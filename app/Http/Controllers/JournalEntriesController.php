<?php

namespace Momentum\Http\Controllers;

use Illuminate\Http\Request;
use Momentum\ActionPlan;
use Momentum\JournalEntry;
use Momentum\Observer;
use Momentum\PulseSurvey;
use Momentum\Enums\ObserverTypes;

/**
 * Handles requests related with the journal web page.
 * 
 * @author ATOM team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class JournalEntriesController extends Controller
{
    /**
     * Display the journal and its entries.
     * Returns view response.
     * @since 0.2.4
     * @since 0.2.5 Supports attachments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entries = JournalEntry::with('attachments')->currentReport()->orderByDesc('created_at')->get();

        $observers = Observer::currentReport()->currentUser()->enabled()->get();
        $observer_types = ObserverTypes::optionsWithLabels();

        $data = json_encode(compact('entries', 'observers','observer_types'));

        return view('journal.index', compact('data'));
    }

    /**
     * Returns PDF version of the journal's entries.
     * Returns view response.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        // get the entries
        $entries = $request->get('entries');

        // get all the entries requested in the URL
        $entries = JournalEntry::findOrFail($entries);

        return view('journal.pdf', compact('entries'));
    }
}
