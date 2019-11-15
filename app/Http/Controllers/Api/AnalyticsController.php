<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\Http\Controllers\Controller;
use Momentum\Services\AnalyticsService;

/**
 * Handles any AJAX-API requests related to analytics.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class AnalyticsController extends Controller
{
    /**
     * Process the request to add and track a new event for analytics purposes.
     * @since 0.2.4
     * 
     * @param Illuminate\Http\Request $request
     */
    public function track(Request $request)
    {
        AnalyticsService::trackEvent($request->get('category'), $request->get('action'), $request->get('label'),
            $request->get('value'), $request->get('data'));
    }
}
