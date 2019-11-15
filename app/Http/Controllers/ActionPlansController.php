<?php

namespace Momentum\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Momentum\ActionPlan;
use Momentum\ActionStep;
use Momentum\ApplicationState;
use Momentum\Enums\Emphasis;
use Momentum\Enums\ObserverTypes;
use Momentum\Observer;
use Momentum\PdfToken;
use Momentum\ReportScore;
use Momentum\Services\PDFGeneratorService;
use Momentum\Utilities\Statistics;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

/**
 * Handles all view and download requests for action plans.
 * 
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlansController extends Controller
{
    /**
     * Returns and displays an HTML response with the action plan web page view.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $action_plans = ActionPlan::currentReport()->get();
        $application_state = ApplicationState::currentUser()->where('application_key', 'action_plan')->get()->first();

        $data = json_encode(compact('action_plans', 'application_state'));

        return view('action-plans.index', compact('data'));
    }

    /**
     * Returns and displays an HTML response with the create action plan web page view.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $emphasis = Emphasis::optionsWithLabels();
        $report_scores = ReportScore::with('behavior')->currentReport()->get();
        $action_steps = ActionStep::selectable()->get();
        $application_state = ApplicationState::currentUser()->where('application_key', 'action_plan')->get()->first();

        $data = json_encode(compact('emphasis', 'report_scores', 'action_steps', 'application_state'));

        return view('action-plans.create', compact('data'));
    }

    /**
     * Returns and displays an HTML response with the edit action plan web page view.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $action_plan = ActionPlan::with('behaviors.behavior', 'behaviors.action_steps', 'pulse_surveys')
            ->currentReport()
            ->findOrFail($id);
        $emphasis = Emphasis::optionsWithLabels();
        $report_scores = ReportScore::with('behavior')->currentReport()->get();
        $action_steps = ActionStep::selectableEdit($action_plan)->get();

        $data = json_encode(compact('action_plan', 'emphasis', 'report_scores', 'action_steps'));
        return view('action-plans.edit', compact('data'));
    }

    /**
     * Returns and displays an HTML response with the share action plan web page view.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function share($id)
    {
        $action_plan = ActionPlan::with('behaviors.behavior', 'behaviors.action_steps', 'pulse_surveys',
            'action_plan_reminders')->currentReport()->findOrFail($id);
        $observers = Observer::currentReport()->currentUser()->enabled()->get();
        $report_score = ReportScore::with('behavior')->where('behavior_id',
            $action_plan->behavior_id)->currentReport()->get()->first();
        $observer_types = ObserverTypes::optionsWithLabels();


        $data = json_encode(compact('action_plan', 'observers', 'report_score', 'observer_types'));

        return view('action-plans.share', compact('data'));
    }

    /**
     * Returns and displays an HTML response with the to-complete action plan web page view.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function complete($id)
    {
        $action_plan = ActionPlan::with('behaviors.behavior', 'behaviors.action_steps')
            ->currentReport()
            ->incomplete()
            ->findOrFail($id);

        $data = json_encode(compact('action_plan'));

        return view('action-plans.complete', compact('data'));
    }

    /**
     * Shows the action plan results.
     * @since 0.2.4
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function results($id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);
        $pulse_surveys = $action_plan->pulse_surveys()->orderBy('cycle')->with('pulse_survey_results')->get();
        $observers = Observer::currentReport()->currentUser()->enabled()->get();
        $observer_types = ObserverTypes::optionsWithLabels();


        $results = $pulse_surveys->map(function ($pulse_survey) {
            return Statistics::calculateCycleFromCollection($pulse_survey->pulse_survey_results);
        });

        $data = json_encode([
            'results'        => $results,
            'pulse_surveys'  => $action_plan->pulse_surveys,
            'action_plan'    => $action_plan,
            'observers'      => $observers,
            'observer_types' => $observer_types,
        ]);

        return view('action-plans.results', compact('data'));
    }

    /**
     * Downloads the pdf survey results of a pulse survey.
     * @since 0.2.4
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function downloadResults($id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        $token = PdfToken::create([
            'route' => 'pdfs.action-plans.share-results',
        ]);

        $url = route('pdfs.action-plans.share-results', [
            'id'        => $id,
            'user_id'   => \Auth::user()->id,
            'pdf-token' => $token->token,
        ]);

        $name = 'pulse-survey-results-' . Carbon::now()->format('YmdHis');

        $file = PDFGeneratorService::documentToPDF($url, $name);

        return response()->download($file);
    }

    /**
     * Downloads the pdf report of an action plan.
     * @since 0.2.4
     *
     * @param int $id Action plan ID.
     * 
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        $token = PdfToken::create([
            'route' => 'pdfs.action-plans.share',
        ]);

        $url = route('pdfs.action-plans.share', [
            'id'        => $id,
            'user_id'   => \Auth::user()->id,
            'pdf-token' => $token->token,
        ]);

        $name = 'action-plan-' . $action_plan->behavior->name_key_translated . '-' . Carbon::now()->format('YmdHis');

        $file = PDFGeneratorService::documentToPDF($url, $name);

        return response()->download($file);
    }

    /**
     * Downloads the pdf of an action plan.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        // get the entries
        $id = $request->get('id');

        $action_plan = ActionPlan::with('behaviors.behavior', 'behaviors.action_steps', 'pulse_surveys',
            'action_plan_reminders')->findOrFail($id);
        $report_score = ReportScore::with('behavior')->where('behavior_id',
            $action_plan->behavior_id)->where('report_id', $action_plan->report_id)->get()->first();

        $data = json_encode(compact('action_plan', 'report_score'));

        return view('action-plans.pdf', compact('data'));
    }

    /**
     * Downloads the pdf results of an action plan.
     * @since 0.2.4
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function resultsPDF(Request $request)
    {
        // get the entries
        $id = $request->get('id');

        $action_plan = ActionPlan::findOrFail($id);
        $pulse_surveys = $action_plan->pulse_surveys()->orderBy('cycle')->with('pulse_survey_results')->get();

        $results = $pulse_surveys->map(function ($pulse_survey) {
            return Statistics::calculateCycleFromCollection($pulse_survey->pulse_survey_results);
        });

        $data = json_encode([
            'results'       => $results,
            'pulse_surveys' => $action_plan->pulse_surveys,
            'action_plan'   => $action_plan,
        ]);

        return view('action-plans.results-pdf', compact('data'));
    }
}
