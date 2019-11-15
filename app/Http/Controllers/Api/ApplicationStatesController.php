<?php

namespace Momentum\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use Momentum\ActionStep;
use Momentum\ApplicationState;
use Momentum\Events\ApplicationStateUpdated;
use Momentum\Http\Controllers\Controller;

/**
 * Handles any AJAX-API requests related to the application state.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ApplicationStatesController extends Controller
{
    /**
     * Returns the latest recorded state based on a provided application key.
     * @since 0.2.4
     * 
     * @param string $application_key Application key to filter.
     * 
     * @return \Momentum\ApplicationState
     */
    public function get($application_key)
    {
        $state = ApplicationState::currentUser()->where('application_name', $application_key)->first();
        if (!$state) {
            return response()->json([
                'message' => 'Application state not found.',
            ], 404);
        }

        return $state;
    }

    /**
     * Creates or updates an application state.
     * @since 0.2.4
     * @since 0.2.5 Dispatches event.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function save(Request $request)
    {
        $state = ApplicationState::updateOrCreate([
            'application_key' => $request->get('application_key'),
            'user_id'         => Auth::user()->id,
        ],[
            'application_key' => $request->get('application_key'),
            'data'            => $request->get('data'),
            'user_id'         => Auth::user()->id,
        ]);
        event(new ApplicationStateUpdated($state));
    }
}
