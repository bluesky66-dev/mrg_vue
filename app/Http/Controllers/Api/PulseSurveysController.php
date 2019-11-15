<?php

namespace Momentum\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Momentum\ActionPlan;
use Momentum\Http\Controllers\Controller;
use Momentum\Notifications\SendPulseSurvey;
use Momentum\Notifications\SharePulseSurveyResults;
use Momentum\Observer;
use Momentum\PulseSurvey;
use Momentum\PulseSurveyResult;
use Momentum\Services\AnalyticsService;

/**
 * Handles any AJAX-API requests related to pulse surveys.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class PulseSurveysController extends Controller
{
    /**
     * Returns all surveys in the current report a json response.
     * @since 0.2.4
     *
     * @return \Momentum\PulseSurvey
     */
    public function index()
    {
        return PulseSurvey::currentReport()->with('pulse_survey_results', 'pulse_survey_results.observer',
            'action_plan',
            'action_plan.behavior')->get();
    }

    /**
     * Attempts to save/create a pulse survey.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($request->get('action_plan_id'));

        $pulse_survey = new PulseSurvey();
        $pulse_survey->due_at = new Carbon($request->get('due_at'));
        $pulse_survey->message = $request->get('message');
        $pulse_survey->action_plan_id = $request->get('action_plan_id');
        $pulse_survey->report_id = \Auth::user()->getActiveReport()->id;
        $pulse_survey->user_id = \Auth::user()->id;
        $pulse_survey->cycle = $action_plan->pulse_surveys()->count() + 1;

        $pulse_survey->selfValidate();

        //--------------------------------------------
        //               VALIDATIONS
        //--------------------------------------------

        // Business Logic Validation

        // if there are not at least three observers
        if (count($request->get('observers')) < 1) {
            return response()->json([
                'errors' => ['selectedObservers' => [trans('pulse_survey.validation.send')]],
            ], 422);
        }

        // if there is already an open pulse survey for this action plan
        if (!$action_plan->can_create_pulse_survey) {
            return response()->json([
                'errors' => ['action_plan_id' => [trans('pulse_survey.validation.open_pulse_survey')]],
            ], 422);
        }

        // standard model validation
        if (!$pulse_survey->isValid()) {
            return response()->json([
                'errors' => $pulse_survey->getErrors(),
            ], 422);
        }

        $pulse_survey->save();

        foreach ($request->get('observers') as $observer) {
            $result = new PulseSurveyResult();
            $result->pulse_survey_id = $pulse_survey->id;
            $result->observer_id = $observer;
            $result->share_code = Str::random(60);

            $result->selfValidate();

            if (!$result->isValid()) {
                return response()->json([
                    'errors' => $result->getErrors(),
                ], 422);
            }

            $result->save();

            $result->observer->sendNotification(new SendPulseSurvey($result->observer, $pulse_survey->user,
                $pulse_survey, $result));
        }

        AnalyticsService::trackEvent('pulse_survey', 'send', 'send_surveys');

        return response()->json([
            'message'  => trans('pulse_survey.complete.success.message'),
            'redirect' => route('pulse-surveys.index'),
        ]);
    }

    /**
     * Returns a pulse survey as a json response.
     * @since 0.2.4
     * 
     * @param int $id Pulse survey ID to get.
     *
     * @return \Momentum\PulseSurvey
     */
    public function get($id)
    {
        return PulseSurvey::currentReport()->findOrFail($id);
    }

    /**
     * Attempts to resend a pulse survey.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Pulse survey ID to resend.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request, $id)
    {
        $pulse_survey = PulseSurvey::currentReport()->findOrFail($id);

        if ($request->get('type') == "open") {
            $pulse_survey->pulse_survey_results_open->each(function ($result) {
                $result->observer->sendNotification(new SendPulseSurvey($result->observer, $result->pulse_survey->user,
                    $result->pulse_survey, $result));
                $result->incrementRemindersSent();
            });

            AnalyticsService::trackEvent('pulse_survey', 'resend', 'bulk');
        }

        $observer_id = $request->get('observer_id');

        if ($observer_id) {
            $observer = Observer::findOrFail($observer_id);
            $pulse_survey_result = $pulse_survey->pulse_survey_results_open()->where('observer_id',
                $observer->id)->get()->first();
            if ($pulse_survey_result) {
                $observer->sendNotification(new SendPulseSurvey($observer, $pulse_survey->user, $pulse_survey,
                    $pulse_survey_result));
                $pulse_survey_result->incrementRemindersSent();
            }

            AnalyticsService::trackEvent('pulse_survey', 'resend', 'individual');
        }

        return response()->json([
            'message' => trans('pulse_survey.resend_successful'),
            'data'    => ['pulse_survey_results' => $pulse_survey->pulse_survey_results->load('observer')],
            'success' => true,
        ]);
    }

    /**
     * Attempts to complete a pulse survey.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param int $id Pulse survey ID to complete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete($id)
    {
        $pulse_survey = PulseSurvey::currentReport()->findOrFail($id);

        // if the pulse survey has already been complete, don't complete it again
        if ($pulse_survey->isComplete()) {
            return;
        }

        // the user has to have completed at lease
        if (!$pulse_survey->canBeCompleted()) {
            return response()->json([
                'message' => trans('pulse_survey.cannot_complete'),
                'success' => false,
            ], 422);
        }

        $pulse_survey->completed_at = Carbon::now();
        $pulse_survey->save();

        return response()->json([
            'redirect' => route('action-plans.results', ['id' => $pulse_survey->action_plan_id]),
        ]);

    }

    /**
     * Attempts to add a result to a pulse survey.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Pulse survey ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request, $id)
    {
        $pulse_survey = PulseSurvey::currentReport()->findOrFail($id);

        // the user has to have completed at lease
        if ($pulse_survey->isComplete()) {
            return response()->json([
                'message' => trans('pulse_survey.cannot_add_observers'),
                'success' => false,
            ], 422);
        }

        foreach ($request->get('observers') as $observer) {
            // if this observer has already been added to this pulse survey, skip them
            if ($pulse_survey->pulse_survey_results()->where('observer_id', $observer)->count() > 0) {
                continue;
            }

            $result = new PulseSurveyResult();
            $result->pulse_survey_id = $pulse_survey->id;
            $result->observer_id = $observer;
            $result->share_code = Str::random(60);

            $result->selfValidate();

            if (!$result->isValid()) {
                return response()->json([
                    'errors' => $result->getErrors(),
                ], 422);
            }

            $result->save();

            $result->observer->sendNotification(new SendPulseSurvey($result->observer, $pulse_survey->user,
                $pulse_survey, $result));
        }

        $pulse_survey->load('pulse_survey_results');

        return response()->json([
            'message'  => trans('pulse_survey.contacts_added_successfully'),
            'data'     => ['pulse_survey_results' => $pulse_survey->pulse_survey_results],
            'redirect' => route('pulse-surveys.index'),
        ]);

    }
}
