<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\Http\Controllers\Controller;
use Momentum\Observer;
use Momentum\Services\AnalyticsService;

/**
 * Handles any AJAX-API requests related to observers.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ObserversController extends Controller
{
    /**
     * Returns all enabled observers for the current report and current user as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\Observer
     */
    public function index()
    {
        return Observer::currentUser()->currentReport()->enabled()->get();
    }

    /**
     * Attempts to save/create an observer.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $observer = $request->has('id') ? Observer::findOrFail($request->get('id')) : new Observer();

        $observer->first_name = $request->get('first_name');
        $observer->last_name = $request->get('last_name');
        $observer->email = $request->get('email');
        $observer->observer_type = $request->get('observer_type');
        $observer->culture_id = $request->get('culture_id');
        $observer->report_id = \Auth::user()->getActiveReport()->id;
        $observer->user_id = \Auth::user()->id;

        $observer->selfValidate();

        if (!$observer->isValid()) {
            return response()->json([
                'errors'  => $observer->getErrors(),
                'success' => false,
            ], 422);
        }

        $observer->save();

        if ($request->has('id')) {
            AnalyticsService::trackEvent('observer', 'add', 'save');
        } else {
            AnalyticsService::trackEvent('observer', 'edit', 'save');
        }

        return response()->json([
            'message'  => trans('profile.contact.saved'),
            'observer' => $observer,
            'success'  => true,
        ]);
    }

    /**
     * Returns an observer as a json response.
     * @since 0.2.4
     * 
     * @param int $id Observer ID to get.
     *
     * @return \Momentum\Observer
     */
    public function get($id)
    {
        return Observer::currentReport()->findOrFail($id);
    }

    /**
     * Attempts to delete an observer.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param int $id Observer ID to get.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $observer = Observer::currentReport()->findOrFail($id);
        $observer->disabled = true;
        $observer->save();

        AnalyticsService::trackEvent('observer', 'delete', 'save');

        return response()->json([
            'message' => trans('profile.contact.deleted'),
            'success' => true,
        ]);
    }
}
