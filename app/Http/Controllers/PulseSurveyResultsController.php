<?php

namespace Momentum\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Momentum\ActionPlan;
use Momentum\PulseSurveyResult;
use Momentum\Culture;
use Momentum\Utilities\Localization;

/**
 * Handles requests related with the pulse survey results web page.
 * 
 * @author Atom team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class PulseSurveyResultsController extends Controller
{
    /**
     * Returns a redirection response after processing a save attempt after
     * successfully retrieving a survey result loaded by a share code.
     * @since 0.2.4
     * 
     * @param \Illuminate\Http\Request $request
     * @param string                   $share_code Result share code.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $share_code)
    {
        $survey = PulseSurveyResult::where('share_code', $share_code)->first();

        if (!$survey) {
            abort(404);
        }

        if ($survey->isComplete()) {
            abort(404);
        }

        // you can't respond to a pulse survey that is already complete
        if ($survey->pulse_survey->isComplete()) {
            abort(404);
        }

        $this->validate($request, [
            'score' => 'required',
        ]);

        $survey->score = $request->get('score');
        $survey->additional_comments = $request->get('additional_comments');
        $survey->completed_at = Carbon::now();

        $survey->save();

        return redirect()->action('PulseSurveyResultsController@thankyou');
    }

    /**
     * Displays pulse survey thank you web page.
     * Returns view response.
     * @since 0.2.4
     *
     * @return \Illuminate\Http\Response
     */
    public function thankyou()
    {
        return view('pulse-survey-results.thankyou');
    }

    /**
     * Displayes the pulse survey result edit form.
     * Returns view response.
     * @since 0.2.4
     * @since 0.2.5 Multiple behaviors support.
     *
     * @param string $share_code Result share code.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($share_code)
    {
        $survey = PulseSurveyResult::with(
                'pulse_survey',
                'pulse_survey.user',
                'pulse_survey.action_plan',
                'pulse_survey.action_plan.behaviors',
                'pulse_survey.action_plan.behaviors.behavior',
                'observer'
            )->where('share_code', $share_code)->first();

        $cultures = Culture::where('enabled', true)->get();

        if (!$survey) {
            abort(404);
        }

        if ($survey->isComplete()) {
            abort(421);
        }

        $observer = $survey->observer;

        $culture = $observer->culture;

        if (!$culture || !$culture->enabled) {
            Localization::setApplicationLocale(Culture::where('code', 'en_US')->get()->first());
        } else {
            Localization::setApplicationLocale($culture);
        }

        // you can't respond to a pulse survey that is already complete
        if ($survey->pulse_survey->isComplete()) {
            abort(404);
        }

        return view('pulse-survey-results.edit', compact('survey', 'cultures'));
    }

    /**
     * Returns redirection response to the result edit page only if culture is present.
     * @since 0.2.4
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $share_code Result share code.
     *
     * @return \Illuminate\Http\Response
     */
    public function culture(Request $request, $share_code)
    {
        $culture_id = $request->get('culture_id');
        $survey = PulseSurveyResult::where('share_code', $share_code)->first();
        $observer = $survey->observer;

        if (!$survey) {
            abort(404);
        }

        $culture = Culture::enabled()->findOrFail($culture_id);

        $observer->culture_id = $culture->id;
        $observer->save();
        return redirect()->route('pulse-survey-result.edit', ['share_code' => $survey->share_code]);

    }
}
