<?php

namespace Momentum\Http\Controllers\Api;

use Momentum\Http\Controllers\Controller;
use Momentum\PulseSurveyResult;

/**
 * Handles any AJAX-API requests related to pulse survey results.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class PulseSurveyResultsController extends Controller
{
    /**
     * Returns all results of all pulse surveys in the current report a json response.
     * @since 0.2.4
     *
     * @return \Momentum\PulseSurveyResult
     */
    public function index()
    {
        return PulseSurveyResult::whereHas('pulse_survey', function($query){
            $query->currentReport();
        })->get();
    }

    /**
     * Returns a pulse survey result as a json response.
     * @since 0.2.4
     * 
     * @param int $id Pulse survey result ID to get.
     *
     * @return \Momentum\PulseSurveyResult
     */
    public function get($id)
    {
        return PulseSurveyResult::whereHas('pulse_survey', function($query){
            $query->currentReport();
        })->findOrFail($id);
    }

    /**
     * Attempts to delete a pulse survey result.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param int $id Pulse survey result ID to delete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $result =  PulseSurveyResult::whereHas('pulse_survey', function($query){
            $query->currentReport();
        })->findOrFail($id);

        // if there are not at least three observers
        if (!$result->pulse_survey->can_delete_results) {
            return response()->json([
                'errors' => ['observers' => [trans('pulse_survey.validation.cannot_delete_survey')]],
            ], 422);
        }

        $result->delete();

        return response()->json([
            'message' => trans('pulse_survey.result.deleted'),
            'success' => true,
        ]);
    }
}
