<?php

namespace Momentum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Momentum\ActionPlanReminder;
use Momentum\Http\Controllers\Controller;

/**
 * Handles any AJAX-API requests related to action plan reminders.
 * NOTE: This API was not designed to provide integration with external applications.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ActionPlanRemindersController extends Controller
{
    /**
     * Returns filtered action plan reminders as a json response.
     * @since 0.2.4
     *
     * @return \Momentum\ActionPlanReminder
     */
    public function index()
    {
        return ActionPlanReminder::whereHas('action_plan', function($query){
            $query->currentReport();
        })->get();
    }

    /**
     * Saves an action plan reminder.
     * @since 0.2.4
     *
     * @todo Check if this is being used.
     */
    public function save()
    {
        // TODO
    }

    /**
     * Returns an action plan reminders as a json response.
     * @since 0.2.4
     * 
     * @param int $id Action plan reminder ID.
     *
     * @return \Momentum\ActionPlanReminder
     */
    public function get($id)
    {
        return ActionPlanReminder::currentReport()->findOrFail($id);
    }
}
