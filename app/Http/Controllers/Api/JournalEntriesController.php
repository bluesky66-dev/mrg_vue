<?php

namespace Momentum\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Momentum\Culture;
use Momentum\Http\Controllers\Controller;
use Momentum\JournalEntry;
use Momentum\Notifications\ShareJournals;
use Momentum\Observer;
use Momentum\PdfToken;
use Momentum\Services\AnalyticsService;
use Momentum\Services\PDFGeneratorService;
use Auth;

/**
 * Handles any AJAX-API requests related to journal entries.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class JournalEntriesController extends Controller
{
    /**
     * Returns all journal entries for the current report as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\JournalEntry
     */
    public function index()
    {
        return JournalEntry::currentReport()->orderByDesc('created_at')->get();
    }

    /**
     * Attempts to save/create a journal entry.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $journal_entry = new JournalEntry();

        $journal_entry->description = $request->get('description');
        $journal_entry->user_id = \Auth::user()->id;
        $journal_entry->report_id = \Auth::user()->getActiveReport()->id;

        $journal_entry->selfValidate();

        if (!$journal_entry->isValid()) {
            return response()->json([
                'errors'  => $journal_entry->getErrors(),
                'success' => false,
            ], 422);
        }

        $journal_entry->save();

        if ($request->has('behaviors')) {
            $journal_entry->behaviors()->attach($request->get('behaviors'));
        }

        return response()->json([
            'message' => trans('journal.success.message'),
            'success' => true,
        ]);
    }

    /**
     * Attempts to share a journal entry.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request)
    {
        $observers = $request->get('observers');
        $entries = $request->get('entries');

        $this->validate($request, [
            'observers' => 'required|min:1',
            'entries'   => 'required|min:1',
        ]);

        $observers = Observer::with('culture')->findOrFail($observers);
        $entries = JournalEntry::findOrFail($entries);

        $observers_by_culture = $observers->groupBy('culture_id');

        foreach ($observers_by_culture as $culture_id => $observers) {

            $culture = Culture::findOrFail($culture_id);

            $token = PdfToken::create([
                'route' => 'pdfs.journals.share',
            ]);

            $url = route('pdfs.journals.share', [
                'lang'      => $culture->code,
                'entries'   => $entries->pluck('id')->values()->toArray(),
                'user_id'   => \Auth::user()->id,
                'pdf-token' => $token->token,
            ]);

            $name = 'journal-entries-' . $culture->code . Carbon::now()->format('YmdHis');

            $file = PDFGeneratorService::documentToPDF($url, $name);

            foreach ($observers as $observer) {
                $observer->sendNotification(new ShareJournals($observer, Auth::user(), $file));
            }
        }

        AnalyticsService::trackEvent('journal', 'share', 'share_entries');

        return response()->json([
            'message' => trans('journal.share.success.message'),
            'success' => true,
        ]);
    }

    /**
     * Returns a journal entry as a json response.
     * @since 0.2.4
     * 
     * @param int $id Journal entry ID to get.
     *
     * @return \Momentum\JournalEntry
     */
    public function get($id)
    {
        return JournalEntry::currentReport()->findOrFail($id);
    }

    /**
     * Attempts to delete a journal entry.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param int $id Journal entry ID to delete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $entry = JournalEntry::currentReport()->findOrFail($id);
        $entry->behaviors()->detach();
        $entry->delete();

        return response()->json([
            'message' => trans('journal.delete.success.message'),
            'success' => true,
        ]);
    }

    /**
     * Attempts to update a journal entry.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Journal entry ID to get.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $journal_entry = JournalEntry::currentReport()->findOrFail($id);
        $journal_entry->description = $request->get('description');

        $journal_entry->selfValidate();

        if (!$journal_entry->isValid()) {
            return response()->json([
                'errors'  => $journal_entry->getErrors(),
                'success' => false,
            ], 422);
        }

        $journal_entry->save();

        if ($request->has('behaviors')) {
            $journal_entry->behaviors()->sync($request->get('behaviors'));
        }

        return response()->json([
            'message' => trans('journal.success.message'),
            'success' => true,
        ]);
    }
}
