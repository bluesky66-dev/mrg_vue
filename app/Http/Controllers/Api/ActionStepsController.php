<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\ActionPlan;
use Momentum\ActionStep;
use Momentum\Http\Controllers\Controller;
use Auth;

/**
 * Handles any AJAX-API requests related to action steps.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ActionStepsController extends Controller
{
    /**
     * Returns all action steps for the current report and current user as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\ActionStep
     */
    public function index()
    {
        return ActionStep::where(function ($query) {
            $query->currentUser()->currentReport();
        })->orWhereNull('user_id')->get();
    }

    /**
     * Attempts to save/create an action step.
     * Returns a json response with the results.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $action_step = new ActionStep();
        $action_step->name = $request->get('name');
        $action_step->description = $request->get('description');
        $action_step->behavior_id = $request->get('behavior_id');
        $action_step->emphasis = $request->get('emphasis');
        $action_step->report_id = Auth::user()->getActiveReport()->id;
        $action_step->user_id = Auth::user()->id;

        if ($request->has('action_plan_id')) {
            $action_step->action_plan_id = $request->get('action_plan_id');
        }

        if ($request->has('temp_action_plan_id')) {
            $action_step->temp_action_plan_id = $request->get('temp_action_plan_id');
        }

        $action_step->selfValidate();

        // standard model validation
        if (!$action_step->isValid()) {
            return response()->json([
                'errors' => $action_step->getErrors(),
            ], 422);
        }

        $action_step->save();

        $steps = ActionStep::selectable()->get();

        if ($request->has('action_plan_id')) {
            $steps = ActionStep::selectableEdit(ActionPlan::find($request->get("action_plan_id")))->get();
        }

        return response()->json([
            'message' => trans('action_plan.action_step.save_successful'),
            'data'    => [
                'action_steps' => $steps,
                'action_step'  => $action_step,
            ],
            'success' => true,
        ]);

    }

    /**
     * Returns an action step as a json response.
     * @since 0.2.4
     * 
     * @param int $id Action step ID to get.
     *
     * @return \Momentum\ActionStep
     */
    public function get($id)
    {
        return ActionStep::findOrFail($id);
    }
}
