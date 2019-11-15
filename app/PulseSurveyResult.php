<?php

namespace Momentum;

/**
 * Pulse survey result model.
 *
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class PulseSurveyResult extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'share_code',
        'additional_comments',
        'score',
        'reminders_sent',
        'pulse_survey_id',
        'observer_id',
        'completed_at',
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
            'share_code'      => 'required',
            'score'           => 'nullable|integer',
            'pulse_survey_id' => 'required|exists:pulse_surveys,id',
            'observer_id'     => 'required|exists:observers,id',
            'completed_at'    => 'nullable|date',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $appends = [
        'status',
        'is_complete',
        'additional_feedback_question_key_translated',
        'rating_feedback_question_key_translated',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `Observer` model.
     * This will return the observer associated with the survey result.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function observer()
    {
        return $this->belongsTo(Observer::class);
    }

    /**
     * Returns and creates relationship with `PulseSurvey` model.
     * This will return the pulse survey associated with the survey result.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pulse_survey()
    {
        return $this->belongsTo(PulseSurvey::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the survey result.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->pulse_survey->belongsTo(User::class);
    }

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the plan associated with the survey result.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_plan()
    {
        return $this->pulse_survey->belongsTo(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behavior associated with the survey result.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function behavior()
    {
        return $this->action_plan->belongsTo(Behavior::class);
    }

    /**
     * Returns flag indicating if result is open or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function isOpen()
    {
        return $this->completed_at === null;
    }

    /**
     * Returns flag indicating if result has been completed or not.
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
     * @see \Momentum\PulseSurveyResult::isComplete
     * 
     * @return boolean
     */
    public function getIsCompleteAttribute()
    {
        return $this->isComplete();
    }

    /**
     * Returns dynamic attribute `status`.
     * Returns translation for survey status.
     * @since 0.2.4
     * 
     * @see {resources}/lang/pulse_survey.php['card']
     * 
     * @return string
     */
    public function getStatusAttribute()
    {
        if ($this->isComplete()) {
            return trans('pulse_survey.card.complete.status');
        }

        return trans('pulse_survey.card.open.status');
    }

    /**
     * Returns dynamic attribute `AdditionalFeedbackQuestionKeyTranslated`.
     * Returns translation found for behavior's `additional_feedback_question_key` attribute.
     * @since 0.2.4
     * @since 0.2.5 Handling empty properties.
     * 
     * @see {resources}/lang/{behavior->additional_feedback_question_key}
     * 
     * @return string
     */
    public function getAdditionalFeedbackQuestionKeyTranslatedAttribute()
    {
        return $this->behavior && $this->user
            ? trans($this->behavior->additional_feedback_question_key, ['first_name' => $this->user->first_name])
            : null;
    }

    /**
     * Returns dynamic attribute `RatingFeedbackQuestionKeyTranslated`.
     * Returns translation found for behavior's `rating_feedback_question_key` attribute.
     * @since 0.2.4
     * @since 0.2.5 Handling empty properties.
     * 
     * @see {resources}/lang/{behavior->rating_feedback_question_key}
     * 
     * @return string
     */
    public function getRatingFeedbackQuestionKeyTranslatedAttribute()
    {
        return $this->behavior && $this->user
            ? trans($this->behavior->rating_feedback_question_key, ['first_name' => $this->user->first_name])
            : null;
    }

    /**
     * Overrides method to prevent recursion killing toJson and toArray due to all of the appends properties
     * we hide some relations from serialization that we don't need.
     * @since 0.2.4
     * 
     * @return array
     */
    public function toArray()
    {
        $this->makeHidden(['pulse_survey', 'action_plan', 'user']);
        return parent::toArray();
    }

    /**
     * Increments reminders sent count and saves model.
     * @since 0.2.4
     */
    public function incrementRemindersSent()
    {
        $this->reminders_sent = $this->reminders_sent + 1;
        $this->save();
    }
}
