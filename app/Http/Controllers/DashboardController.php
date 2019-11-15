<?php

namespace Momentum\Http\Controllers;

use Illuminate\Http\Request;
use Momentum\ActionPlan;
use Jenssegers\Agent\Agent;

/**
 * Handles requests related with the dashboard web page.
 * 
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class DashboardController extends Controller
{
    /**
     * Returns and displayes the dashboard's view.
     * @since 0.2.4
     * @since 0.2.5 Changes to support new model structure.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $agent = new Agent();
        if($agent->isMobile()){
            if($agent->is('iPhone')){
                return view('errors.iphone'); 
            }
                return view('errors.mobile');            
        }

        $action_plans = ActionPlan::with(
            'behaviors.behavior',
            'behaviors.action_steps',
            'pulse_surveys',
            'pulse_surveys.pulse_survey_results',
            'complete_pulse_surveys'
        )->currentReport()->get();

        $data = json_encode(compact('action_plans'));

        return view('dashboard.dashboard', compact('data'));
    }
}
