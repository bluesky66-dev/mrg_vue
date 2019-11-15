<?php

namespace Momentum;

use Carbon\Carbon;
use Momentum\Traits\BelongsToReportTrait;
use Momentum\Traits\BelongsToUserTrait;
use Momentum\Utilities\Statistics;

/**
 * Pulse survey model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class PulseSurvey extends BaseModel
{
    use BelongsToReportTrait, BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'message',
        'due_at',
        'completed_at',
        'action_plan_id',
        'user_id',
        'cycle',
        'report_id',
    ];

    /**
     * Date attributes.
     * @since 0.2.4
     * 
     * @see \Momentum\BaseModel
     *
     * @var array
     */
    protected $dates = [
        'due_at',
        'completed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Validation rules per attribute.
     * @since 0.2.4
     *
     * @see \Momentum\Traits\ValidatableTrait
     *
     * @var array 
     */
    protected $rules = [
        'default' => [
            'message'        => 'required',
            'due_at'         => 'required|date',
            'completed_at'   => 'nullable|date',
            'action_plan_id' => 'required|exists:action_plans,id',
            'user_id'        => 'required|exists:users,id',
            'cycle'          => 'required|integer',
            'report_id'      => 'required|exists:reports,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $appends = [
        'total_sent',
        'total_completed',
        'total_open',
        'status_name',
        'can_view_results',
        'can_resend_open_surveys',
        'can_delete_results',
        'has_additional_comments',
        'is_complete',
        'statistics',
        'total_surveys_sent',
        'total_surveys_complete',
        'total_surveys_open',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the plan associated with the survey.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_plan()
    {
        return $this->belongsTo(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the report associated with the survey.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the survey.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns and creates relationship with `PulseSurveyResult` model.
     * This will return the results associated with the survey.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_survey_results()
    {
        return $this->hasMany(PulseSurveyResult::class);
    }

    /**
     * Returns completed pulse survey results.
     * @since 0.2.4
     * 
     * @see \Momentum\PulseSurvey::pulse_survey_results
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_survey_results_completed()
    {
        return $this->pulse_survey_results()->whereNotNull('completed_at');
    }

    /**
     * Returns open pulse survey results.
     * @since 0.2.4
     * 
     * @see \Momentum\PulseSurvey::pulse_survey_results
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_survey_results_open()
    {
        return $this->pulse_survey_results()->whereNull('completed_at');
    }

    /**
     * Returns flag indicating if survey is open or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function isOpen()
    {
        return $this->completed_at === null;
    }

    /**
     * Returns flag indicating if survey has been completed or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function isComplete()
    {
        return !$this->isOpen();
    }

    /**
     * Returns dynamic attribute `isCompleted`.
     * Alias for method `isCompleted`;
     * @since 0.2.4
     * 
     * @see \Momentum\PulseSurvey::isComplete
     * 
     * @return boolean
     */
    public function getIsCompleteAttribute()
    {
        return $this->isComplete();
    }

    /**
     * Returns dynamic attribute `totalCompleted`.
     * Returns count of completed results.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalCompletedAttribute()
    {
        return $this->pulse_survey_results_completed->count();
    }

    /**
     * Returns dynamic attribute `totalOpen`.
     * Returns count of open results.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalOpenAttribute()
    {
        return $this->pulse_survey_results_open->count();
    }

    /**
     * Returns dynamic attribute `totalSent`.
     * Returns total count of results (in theory all of them sent).
     * @since 0.2.4
     * 
     * @todo Where is this being used? May cause a fatal error.
     * 
     * @return int
     */
    public function getTotalSentAttribute()
    {
        return $this->pulse_survey_results->count();
    }

    /**
     * Returns dynamic attribute `canViewResults`.
     * Returns flag indicating if results can be viewed.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanViewResultsAttribute()
    {
        return $this->pulse_survey_results_completed()->count() >= 3;
    }

    /**
     * Returns dynamic attribute `canCanResendOpenSurveys`.
     * Returns flag indicating open results can be resend or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanResendOpenSurveysAttribute()
    {
        return $this->pulse_survey_results_open()->count() > 0 && !$this->isComplete();
    }

    /**
     * Returns dynamic attribute `statistics`.
     * Returns calculated statistics for the survey's results.
     * @since 0.2.4
     * 
     * @see \Momentum\Utilities\Statistics::calculateCycleFromCollection
     * 
     * @return array
     */
    public function getStatisticsAttribute()
    {
        $results = $this->pulse_survey_results;

        return Statistics::calculateCycleFromCollection($results);
    }

    /**
     * Returns dynamic attribute `totalSurveysSent`.
     * Returns total count of results (in theory all of them sent).
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalSurveysSentAttribute()
    {
        return $this->pulse_survey_results()->count();
    }

    /**
     * Returns dynamic attribute `totalSurveysCompleted`.
     * Same as `totalCompleted`.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalSurveysCompleteAttribute()
    {
        return $this->pulse_survey_results_completed()->count();
    }

    /**
     * Returns dynamic attribute `totalSurveysOpen`.
     * Same as `totalOpen`.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalSurveysOpenAttribute()
    {
        return $this->pulse_survey_results_open()->count();
    }

    /**
     * Returns dynamic attribute `statusName`.
     * Returns translation for status name.
     * @since 0.2.4
     * 
     * @see {resources}/lang/pulse_survey.php['card']
     * 
     * @return string
     */
    public function getStatusNameAttribute()
    {
        if ($this->isComplete()) {
            return trans('pulse_survey.card.complete.status');
        }

        return trans('pulse_survey.card.open.status');
    }

    /**
     * Returns dynamic attribute `canDeleteResults`.
     * Returns flag indicating if results can be deleted.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanDeleteResultsAttribute()
    {
        return !$this->isComplete();
    }

    /**
     * Returns dynamic attribute `hasAdditionalComments`.
     * Returns flag indicating if results have additional comments or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getHasAdditionalCommentsAttribute()
    {
        $results = $this->pulse_survey_results;
        return $results->filter(function($result){
           return $result->additional_comments !== null;
        })->count() > 0;
    }

    /**
     * Returns flag indicating if survey can be set as completed or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function canBeCompleted()
    {
        return $this->pulse_survey_results_completed()->count() >= 3;
    }

    /**
     * Returns flag indicating if survey should be completed or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function shouldBeCompleted()
    {
        return $this->pulse_survey_results_open()->count() <= 0;
    }
}
