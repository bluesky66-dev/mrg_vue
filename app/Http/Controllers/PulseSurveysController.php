<?php

namespace Momentum\Http\Controllers;

use Illuminate\Http\Request;
use Momentum\ActionPlan;
use Momentum\ApplicationState;
use Momentum\Observer;
use Momentum\Enums\ObserverTypes;
use Momentum\PulseSurvey;
use Momentum\Utilities\Statistics;

/**
 * Handles requests related with the pulse survey web page.
 * 
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class PulseSurveysController extends Controller
{
    /**
     * Displays pulse survey web page (shows the survey and information such as results, observers and more).
     * Returns view response.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pulse_surveys =  PulseSurvey::currentReport()
            ->with(
                'pulse_survey_results',
                'pulse_survey_results.observer',
                'action_plan',
                'action_plan.behaviors',
                'action_plan.behaviors.behavior'
            )
            ->orderByDesc('cycle')
            ->get();
        $application_state = ApplicationState::currentUser()->where('application_key', 'pulse_survey')->get()->first();
        $action_plans = ActionPlan::currentReport()->get();
        $observers = Observer::currentReport()->currentUser()->enabled()->get();
        $observer_types = ObserverTypes::optionsWithLabels();

        $data = json_encode(compact('pulse_surveys', 'application_state', 'action_plans', 'observers', 'observer_types'));

        return view('pulse-surveys.index', compact('data'));
    }

    /**
     * Displayes the pulse survey create form.
     * Returns view response.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action_plans = ActionPlan::currentReport()->get();
        $observers = Observer::currentReport()->currentUser()->enabled()->get();
        $observer_types = ObserverTypes::optionsWithLabels();
        $data = json_encode(compact('action_plans', 'observers','observer_types'));

        return view('pulse-surveys.create', compact('data'));
    }

    /**
     * Displayes the pulse survey edit form.
     * Returns view response.
     * @since 0.2.4
     *
     * @param int $id Pulse survey ID.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('pulse-surveys.edit');
    }

}
