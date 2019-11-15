<?php

namespace Momentum;

use Carbon\Carbon;
use Momentum\Utilities\Dates;

/**
 * Action plan behavior model.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlanBehavior extends BaseModel
{

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'action_plan_id',
        'behavior_id',
        'emphasis',
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
            'action_plan_id'   => 'required|exists:action_plans,id',
            'behavior_id'      => 'required|exists:behaviors,id',
            'emphasis'         => 'required|emphasis',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'label',
        'can_be_completed',
        'emphasis_translated',
        'additional_feedback_question_key_translated',
        'rating_feedback_question_key_translated',
    ];

    /**
     * Hidden from casting.
     * @since 0.2.5
     *
     * @var array 
     */
    public $hidden = [
        'user',
        'users',
        'action_plan_id',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the action plan associated with the plan behavior.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_plan()
    {
        return $this->belongsTo(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behavior associated with the plan behavior.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
    }

    /**
     * Returns and creates relationship with `ActionStep` model.
     * This will return the collection of all the steps associated with the plan behavior.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function action_steps()
    {
        return $this->belongsToMany(ActionStep::class)->withPivot('due_at', 'completed_at');
    }

    /**
     * Returns and creates relationship with `User` model through the `ActionPlan` model.
     * This will return the users associated with the reminder.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            ActionPlan::class,
            'user_id',
            'id'
        );
    }

    /**
     * Returns completed steps.
     * @since 0.2.5
     * 
     * @see \Momentum\ActionPlan::action_steps
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function action_steps_complete()
    {
        return $this->action_steps()
            ->wherePivot('completed_at', '!=', null);
    }

    /**
     * Returns incompleted steps.
     * @since 0.2.5
     * 
     * @see \Momentum\ActionPlan::action_steps
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function action_steps_incomplete()
    {
        return $this->action_steps()
            ->wherePivot('completed_at', null);
    }

    /**
     * Returns action plan's user.
     * @since 0.2.5
     * 
     * @return \Momentum\User
     */
    public function getUserAttribute()
    {
        return $this->users()->first();
    }

    /**
     * Returns dynamic attribute `emphasisTranslated`. Returns the localized label
     * related to emphasis associated with the plan.
     * @since 0.2.5
     * 
     * @see {resources}/lang/global.php['enum']['emphasis']
     * 
     * @return string
     */
    public function getEmphasisTranslatedAttribute()
    {
        return trans('global.enum.emphasis.' . $this->emphasis . '.label');
    }

    /**
     * Returns dynamic attribute `status`. Returns the localized name
     * for the status associated with the plan.
     * @since 0.2.5
     * 
     * @see {resources}/lang/action_plan.php['card']['status']
     * 
     * @return string
     */
    public function getStatusAttribute()
    {
        if ($this->isComplete()) {
            return trans('action_plan.card.status.complete');
        }

        return trans('action_plan.card.status.in_progress');
    }

    /**
     * Returns dynamic attribute `progressPercent`.
     * Returns the progress percentage of completed steps against total steps.
     * @since 0.2.5
     * 
     * @return float
     */
    public function getProgressPercentAttribute()
    {
        if ($this->action_steps->count() <= 0) {
            return 0;
        }
        return floor($this->action_steps_complete->count() / $this->action_steps->count() * 100);
    }

    /**
     * Returns dynamic attribute `timelinePercent`.
     * Returns the progress percentage of remaining days left against total days.
     * @since 0.2.5
     * 
     * @return float
     */
    public function getTimelinePercentAttribute()
    {
        if ($this->total_days <= 0) {
            return 0;
        }

        return floor(($this->total_days - $this->days_remaining) / $this->total_days * 100);
    }

    /**
     * Returns dynamic attribute `additionalFeedbackQuestionKeyTranslated`.
     * Returns translation for the behavior's `additional_feedback_question_key` attribute.
     * @since 0.2.5
     * 
     * @see {resources}/lang/{behavior->additional_feedback_question_key}
     * 
     * @return string
     */
    public function getAdditionalFeedbackQuestionKeyTranslatedAttribute()
    {
        if ($this->behavior == null || $this->user == null) {
            return null;
        }
        return trans($this->behavior->additional_feedback_question_key, ['first_name' => $this->user->first_name]);
    }

    /**
     * Returns dynamic attribute `ratingFeedbackQuestionKeyTranslated`.
     * Returns translation for the behavior's `rating_feedback_question_key` attribute.
     * @since 0.2.5
     * 
     * @see {resources}/lang/{behavior->rating_feedback_question_key}
     * 
     * @return string
     */
    public function getRatingFeedbackQuestionKeyTranslatedAttribute()
    {
        if ($this->behavior == null || $this->user == null) {
            return null;
        }
        return trans($this->behavior->rating_feedback_question_key, ['first_name' => $this->user->first_name]);
    }

    /**
     * Returns dynamic attribute `label`.
     * Alias for behavior's name_key_translated.
     * @since 0.2.5
     * 
     * @return string
     */
    public function getLabelAttribute()
    {
        return $this->behavior->name_key_translated;
    }

    /**
     * Returns dynamic attribute `canBeCompleted`.
     * Returns flag indicating if plan can be completed or not, this is done 
     * by comparing the count of steps completed against the count of steps available.
     * @since 0.2.5
     * 
     * @return boolean
     */
    public function getCanBeCompletedAttribute()
    {
        return $this->action_steps_complete->count() == $this->action_steps->count();
    }
}
