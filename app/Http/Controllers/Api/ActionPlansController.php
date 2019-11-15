<?php

namespace Momentum\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Momentum\Culture;
use Momentum\ActionPlan;
use Momentum\Notifications\ShareActionPlan;
use Momentum\ActionPlanReminder;
use Momentum\ActionStep;
use Momentum\ApplicationState;
use Momentum\Http\Controllers\Controller;
use Momentum\Notifications\SharePulseSurveyResults;
use Momentum\Observer;
use Momentum\PdfToken;
use Momentum\Services\AnalyticsService;
use Momentum\Services\PDFGeneratorService;
use Momentum\Events\ActionPlan\ActionPlanCreated;
use Momentum\Events\ActionPlan\ActionPlanUpdated;
use Momentum\Events\ActionPlan\ActionPlanBehaviorAdded;
use Momentum\Events\ActionPlan\ActionPlanBehaviorUpdated;
use Momentum\Events\ActionPlan\ActionPlanBehaviorRemoved;
use Momentum\Events\ActionPlan\ActionGoalAdded;
use Momentum\Events\ActionPlan\ActionGoalUpdated;
use Momentum\Events\ActionPlan\ActionGoalRemoved;
use Auth;

/**
 * Handles any AJAX-API requests related to action plans.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlansController extends Controller
{
    /**
     * Returns all action plans for the current report as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\ActionPlan
     */
    public function index()
    {
        return ActionPlan::currentReport()->get();
    }

    /**
     * Attempts to save/create an action plan.
     * Returns a json response with the results.
     * @since 0.2.4
     * @since 0.2.5 Support multiple behaviors, goals.
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $sdt = new Carbon($request->get('starts_at'));
        $edt = new Carbon($request->get('ends_at'));
        
        $action_plan = new ActionPlan();
        $action_plan->name = $request->get('name');
        $action_plan->starts_at = $sdt->toDateString();
        $action_plan->ends_at = $edt->toDateString();

        $action_plan->report_id = Auth::user()->getActiveReport()->id;
        $action_plan->user_id = Auth::user()->id;

        $action_plan->selfValidate();

        //--------------------------------------------
        //               VALIDATIONS
        //--------------------------------------------

        // Business Logic Validation

        if (count($request->get('behaviors')) === 0 ) {
            return response()->json([
                'errors' => ['behaviors' => [trans('action_plan.validation.behaviors.number')]],
            ], 422);
        }

        // if there is not at least one action step
        /*
        if (count($request->get('action_steps')) < 1) {
            return response()->json([
                'errors' => ['action_steps' => [trans('action_plan.validation.action_steps.number')]],
            ], 422);
        }
        */

        // if there is not at least one action step
        if (!Auth::user()->can_create_action_plan) {
            return response()->json([
                'errors' => ['action_plans' => [trans('action_plan.validation.too_many')]],
            ], 422);
        }

        // standard model validation
        if (!$action_plan->isValid()) {
            return response()->json([
                'errors' => $action_plan->getErrors(),
            ], 422);
        }

        $action_plan->save();

        // Get behaviors
        $behaviors = $request->get('behaviors');
        if ($behaviors) {
            foreach ($behaviors as $behavior) {
                if ($behavior['behavior_id'] === null)
                    continue;
                $plan_behavior = $action_plan->behaviors()->create([
                    'action_plan_id'    => $action_plan->id,
                    'behavior_id'       => $behavior['behavior_id'],
                    'emphasis'          => $behavior['emphasis'],
                ]);
                if (isset($behavior['action_steps'])) {
                    foreach ($behavior['action_steps'] as $action_step) {
                        $step = ActionStep::findOrFail($action_step['id']);
                        // we need to remove the temp id and set the action_plan id
                        $step->temp_action_plan_id = null;
                        $step->action_plan_id = $action_plan->id;
                        $step->save();

                        $plan_behavior->action_steps()->attach(
                            $step,
                            [
                                'completed_at' => $action_step['complete'] == true ? Carbon::now() : null,
                                'due_at'       => new Carbon($action_step['due_at']),
                            ]
                        );
                    }
                }
                event(new ActionPlanBehaviorAdded($plan_behavior));
            }
        }

        // Get goals
        $goals = $request->get('goals');
        if ($goals) {
            foreach ($goals as $goal) {
                if ($goal['answer'] === null)
                    continue;
                $goal = $action_plan->goals()->create([
                    'action_plan_id'        => $action_plan->id,
                    'organization_goal_id'  => $goal['organization_goal_id'],
                    'answer'                => $goal['answer'],
                    'custom_question'       => $goal['custom_question'],
                    'custom_type'           => $goal['custom_type'],
                ]);
                event(new ActionGoalAdded($action_plan, $goal));
            }
        }

        $reminders = $request->get('action_plan_reminders');
        if ($reminders) {
            foreach ($reminders as $type => $reminder) {

                if ($type == "action_step" && is_array($reminder)) {
                    foreach ($reminder as $action_step) {
                        $rem = new ActionPlanReminder();
                        $rem->type = $type;
                        $rem->starts_at = new Carbon($action_step['starts_at']);
                        $rem->action_step_id = $action_step['id'];
                        $rem->action_plan_id = $action_plan->id;
                        $rem->frequency = $action_step['frequency'];
                        $rem->save();
                    }
                } else {
                    $rem = new ActionPlanReminder();
                    $rem->type = $type;
                    $rem->starts_at = new Carbon($reminder['starts_at']);
                    $rem->action_plan_id = $action_plan->id;
                    $rem->frequency = $reminder['frequency'];
                    $rem->save();
                }
            }
        }

        // delete any application states for action plans
        ApplicationState::where('user_id', Auth::user()->id)->where('application_key', 'action_plan')->forceDelete();

        // Call event
        event(new ActionPlanCreated($action_plan));

        return response()->json([
            'message'  => trans('action_plan.save.success.message'),
            'redirect' => route('action-plans.index'),
        ]);

    }

    /**
     * Attempts to update an action plan.
     * Returns a json response with the results.
     * @since 0.2.4
     * @since 0.2.5 Support multiple behaviors, goals.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Action plan ID to update.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);
        $action_plan->name = $request->get('name');
        $action_plan->benefits = $request->get('benefits');
        $action_plan->goals = $request->get('goals');
        $action_plan->key_constituents = $request->get('key_constituents');
        $action_plan->obstacles = $request->get('obstacles');
        $action_plan->resources = $request->get('resources');
        $action_plan->risks = $request->get('risks');
        $action_plan->starts_at = new Carbon($request->get('starts_at'));
        $action_plan->ends_at = new Carbon($request->get('ends_at'));

        $action_plan->selfValidate();

        if (!$action_plan->isValid()) {
            return response()->json([
                'errors' => $action_plan->getErrors(),
            ], 422);
        }

        $action_plan->save();

        // Get behaviors
        $behaviors = $request->get('behaviors');
        if ($behaviors) {
            // Updates
            for ($pIndex = $action_plan->behaviors->count()-1; $pIndex >= 0; --$pIndex) {
                $toRemove = true;
                for ($bIndex = count($behaviors)-1; $bIndex >= 0; --$bIndex) {
                    if ($action_plan->behaviors[$pIndex]->behavior_id == $behavior[$bIndex]['behavior_id']) {
                        $toRemove = false;
                        // Update behavior
                        $action_plan->behaviors[$pIndex]->emphasis = $behavior[$bIndex]['emphasis'];
                        $action_plan->behaviors[$pIndex]->save();
                        // Action steps
                        $action_plan->behaviors[$pIndex]->action_steps()->detach();
                        if (isset($behavior[$bIndex]['action_steps'])) {
                            foreach ($behavior[$bIndex]['action_steps'] as $action_step) {
                                $action_plan->behaviors[$pIndex]->action_steps()->attach(
                                    ActionStep::findOrFail($action_step['id']),
                                    [
                                        'completed_at' => $action_step['complete'] == true ? Carbon::now() : null,
                                        'due_at'       => new Carbon($action_step['due_at']),
                                    ]
                                );
                            }
                        }
                        event(new ActionPlanBehaviorUpdated($action_plan->behaviors[$pIndex]));
                        unset($behaviors[$bIndex]);
                        break;
                    }
                }
                if ($toRemove) {
                    $plan_behavior = $action_plan->behaviors[$pIndex];
                    $plan_behavior->delete();
                    event(new ActionPlanBehaviorRemoved($plan_behavior));
                }
            }
            // New
            foreach ($behaviors as $behavior) {
                $plan_behavior = $action_plan->behaviors()->create([
                    'action_plan_id'    => $action_plan->id,
                    'behavior_id'       => $behavior['behavior_id'],
                    'emphasis'          => $behavior['emphasis'],
                ]);
                if (isset($behavior['action_steps'])) {
                    foreach ($behavior['action_steps'] as $action_step) {
                        $plan_behavior->action_steps()->attach(
                            ActionStep::findOrFail($action_step['id']),
                            [
                                'completed_at' => $action_step['complete'] == true ? Carbon::now() : null,
                                'due_at'       => new Carbon($action_step['due_at']),
                            ]
                        );
                    }
                }
                event(new ActionPlanBehaviorAdded($plan_behavior));
            }
        }

        // Get goals
        $goals = $request->get('goals');
        if ($goals) {
            // Updates
            for ($pIndex = $action_plan->goals->count()-1; $pIndex >= 0; --$pIndex) {
                $toRemove = true;
                for ($bIndex = count($goals)-1; $bIndex >= 0; --$bIndex) {
                    if ($action_plan->goals[$pIndex]->id == $goals[$bIndex]['id']) {
                        $toRemove = false;
                        // Update behavior
                        $action_plan->goals[$pIndex]->custom_question = $goals[$bIndex]['custom_question'];
                        $action_plan->goals[$pIndex]->custom_type = $goals[$bIndex]['custom_type'];
                        $action_plan->goals[$pIndex]->answer = $goals[$bIndex]['answer'];
                        $action_plan->goals[$pIndex]->save();
                        event(new ActionGoalUpdated($action_plan, $action_plan->goals[$pIndex]));
                        unset($goals[$bIndex]);
                        break;
                    }
                }
                if ($toRemove) {
                    $goal = $action_plan->goals[$pIndex];
                    $goal->delete();
                    event(new ActionGoalRemoved($action_plan, $goal));
                }
            }
            // New
            foreach ($goals as $goal) {
                if ($goal['answer'] === null)
                    continue;
                $action_goal = $action_plan->goals()->create([
                    'action_plan_id'        => $action_plan->id,
                    'organization_goal_id'  => $goal['organization_goal_id'],
                    'answer'                => $goal['answer'],
                    'custom_question'       => $goal['custom_question'],
                    'custom_type'           => $goal['custom_type'],
                ]);
                event(new ActionGoalAdded($action_plan, $action_goal));
            }
        }

        $reminders = $request->get('action_plan_reminders');
        // delete all the current reminders and recreate them based on the new data
        $action_plan->action_plan_reminders()->delete();

        if ($reminders) {
            foreach ($reminders as $type => $reminder) {

                if ($type == "action_step" && is_array($reminder)) {
                    foreach ($reminder as $action_step) {
                        $rem = new ActionPlanReminder();
                        $rem->type = $type;
                        $rem->starts_at = new Carbon($action_step['starts_at']);
                        $rem->action_step_id = $action_step['id'];
                        $rem->action_plan_id = $action_plan->id;
                        $rem->frequency = $action_step['frequency'];
                        $rem->save();
                    }
                } else {
                    $rem = new ActionPlanReminder();
                    $rem->type = $type;
                    $rem->starts_at = new Carbon($reminder['starts_at']);
                    $rem->action_plan_id = $action_plan->id;
                    $rem->frequency = $reminder['frequency'];
                    $rem->save();
                }
            }
        }

        // Call event
        event(new ActionPlanUpdated($action_plan));

        return response()->json([
            'message'  => trans('action_plan.update.success.message'),
            'redirect' => route('action-plans.index'),
        ]);
    }

    /**
     * Attempts to complete an action plan.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Action plan ID to complete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        $action_plan = ActionPlan::currentReport()->incomplete()->findOrFail($id);

        $action_plan->successes = $request->get('successes');
        $action_plan->failures = $request->get('failures');
        $action_plan->next_focus = $request->get('next_focus');
        $action_plan->helpful = $request->get('helpful');
        $action_plan->completed_at = Carbon::now();

        $action_plan->selfValidate();

        if (!$action_plan->isValid()) {
            return response()->json([
                'errors' => $action_plan->getErrors(),
            ], 422);
        }

        $action_plan->save();

        return response()->json([
            'message'  => trans('action_plan.complete.success.message'),
            'redirect' => route('action-plans.index'),
        ]);

    }

    /**
     * Attempts to sahre an action plan and sends notifications.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Action plan ID to share.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request, $id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        $observers = $request->get('observers');

        if ($observers == null) {
            return response()->json([
                'errors' => trans('action_plan.share.select_observer'),
            ], 422);
        }

        // render/generate the share file for each language type of the observers
        // send the operators emails
        $observers = Observer::with('culture')->findOrFail($observers);

        $observers_by_culture = $observers->groupBy('culture_id');

        foreach ($observers_by_culture as $culture_id => $observers) {

            $culture = Culture::findOrFail($culture_id);

            $token = PdfToken::create([
                'route' => 'pdfs.action-plans.share',
            ]);

            $url = route('pdfs.action-plans.share', [
                'lang'      => $culture->code,
                'id'        => $id,
                'user_id'   => \Auth::user()->id,
                'pdf-token' => $token->token,
            ]);

            $name = 'action-plans-' . $culture->code . Carbon::now()->format('YmdHis');

            $file = PDFGeneratorService::documentToPDF($url, $name);

            foreach ($observers as $observer) {
                $observer->sendNotification(new ShareActionPlan($observer, $action_plan, $file));
            }
        }

        AnalyticsService::trackEvent('action_plan', 'share', 'send_action_plan');

        return response()->json([
            'message'  => trans('action_plan.share.success.message'),
            'redirect' => route('action-plans.index'),
        ]);
    }

    /**
     * Attempts to share the results of an action plan.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Action plan ID to share.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareResults(Request $request, $id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        $observers = $request->get('observers');

        // render/generate the share file for each language type of the observers
        // send the operators emails
        $observers = Observer::with('culture')->findOrFail($observers);

        $observers_by_culture = $observers->groupBy('culture_id');

        foreach ($observers_by_culture as $culture_id => $observers) {

            $culture = Culture::findOrFail($culture_id);

            $token = PdfToken::create([
                'route' => 'pdfs.action-plans.share-results',
            ]);

            $url = route('pdfs.action-plans.share-results', [
                'lang'      => $culture->code,
                'id'        => $id,
                'user_id'   => \Auth::user()->id,
                'pdf-token' => $token->token,
            ]);

            $name = 'pulse-survey-results-' . $culture->code . Carbon::now()->format('YmdHis');

            $file = PDFGeneratorService::documentToPDF($url, $name);

            foreach ($observers as $observer) {
                $observer->sendNotification(new SharePulseSurveyResults($observer, $action_plan, $file));
            }
        }

        AnalyticsService::trackEvent('pulse_survey', 'share', 'send_results');

        return response()->json([
            'message'  => trans('pulse_survey.share_results_successful'),
            'redirect' => route('pulse-surveys.index'),
        ]);
    }

    /**
     * Returns an action plan (for current report) as a json response.
     * @since 0.2.4
     * 
     * @param int $id Action plan ID to get.
     *
     * @return \Momentum\ActionPlan
     */
    public function get($id)
    {
        return ActionPlan::currentReport()->findOrFail($id);
    }

    /**
     * Attempts to delete an action plan.
     * Returns a json response with the results.
     * @since 0.2.4
     * @since 0.2.5 Support multiple behaviors structure.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      Action plan ID to delete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        if (!$action_plan->can_be_deleted) {
            return response()->json([
                'errors' => trans('action_plan.cannot_delete_message'),
            ], 422);
        }

        $action_plan->pulse_surveys()->delete();
        $action_plan->action_plan_reminders()->delete();
        $action_plan->behaviors()->delete();
        $action_plan->delete();

        return response()->json([
            'message'  => trans('action_plan.delete_successful_message'),
            'redirect' => route('action-plans.index'),
        ]);
    }
}
