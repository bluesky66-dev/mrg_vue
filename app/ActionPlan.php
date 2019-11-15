<?php

namespace Momentum;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Momentum\Traits\BelongsToReportTrait;
use Momentum\Traits\BelongsToUserTrait;
use Momentum\Utilities\Dates;

/**
 * Action plan model.
 *
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionPlan extends BaseModel
{
    use BelongsToReportTrait, BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     * @since 0.2.5 Added name.
     *
     * @var array 
     */
    protected $fillable = [
        'name',
        'successes',
        'failures',
        'next_focus',
        'helpful',
        'starts_at',
        'ends_at',
        'completed_at',
        'user_id',
        'report_id',
    ];

    /**
     * List of date attributes.
     * @since 0.2.4
     *
     * @see \Momentum\BaseModel
     *
     * @var array 
     */
    protected $dates = [
        'starts_at',
        'ends_at',
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
            'starts_at'        => 'required|date|before_or_equal:ends_at',
            'ends_at'          => 'required|date|after_or_equal:ends_at',
            'completed_at'     => 'nullable|date',
            'user_id'          => 'required|exists:users,id',
            'report_id'        => 'required|exists:reports,id',
            'successes'        => 'nullable|textarea',
            'failures'         => 'nullable|textarea',
            'next_focus'       => 'nullable|textarea',
            'helpful'          => 'nullable|boolean',
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
        'can_create_pulse_survey',
        'can_edit_behavior',
        'can_be_deleted',
        'status',
        'progress_percent',
        'timeline_percent',
        'days_remaining',
        'total_days',
        'is_complete',
        'current_pulse_survey',
        'formatted_dates',
        'action_steps',
        'action_steps_complete',
    ];

    /**
     * Returns and creates relationship with `ActionPlanBehavior` model.
     * This will return the behaviors associated with the plan.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function behaviors()
    {
        return $this->hasMany(ActionPlanBehavior::class);
    }

    /**
     * Returns and creates relationship with `ActionGoal` model.
     * This will return the collection of goals associated with the plan.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goals()
    {
        return $this->hasMany(ActionGoal::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the report associated with the plan.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Returns and creates relationship with `PulseSurvey` model.
     * This will return the collection of all the behaviors associated with the plan.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_surveys()
    {
        return $this->hasMany(PulseSurvey::class);
    }

    /**
     * Returns opened pulse surveys.
     * @since 0.2.4
     * 
     * @see \Momentum\ActionPlan::pulse_surveys
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function open_pulse_surveys()
    {
        return $this->pulse_surveys()->whereNull('completed_at');
    }

    /**
     * Returns completed pulse surveys.
     * @since 0.2.4
     * 
     * @see \Momentum\ActionPlan::pulse_surveys
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function complete_pulse_surveys()
    {
        return $this->pulse_surveys()->whereNotNull('completed_at');
    }

    /**
     * Returns and creates relationship with `ActionPlanReminder` model.
     * This will return the collection of all the reminders associated with the plan.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function action_plan_reminders()
    {
        return $this->hasMany(ActionPlanReminder::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the plan.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns dynamic attribute `label`.
     * Returns the action plan's name or the list of behavior labels
     * separated by commas.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getLabelAttribute()
    {
        if ($this->name)
            return $this->name;
        if ($this->behaviors->count() > 0) {
            $labels = [];
            for ($i = 0; $i < $this->behaviors->count(); ++$i) {
                $labels[] = $this->behaviors[$i]->label;
            }
            return implode(config('momentum.labels_separator'), $labels);
        }
        return null;
    }

    /**
     * Returns a collection of all the steps found in behaviors.
     * @since 0.2.5
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getActionStepsAttribute()
    {
        $steps = new Collection;
        for ($bIndex = 0; $bIndex < $this->behaviors->count(); ++$bIndex) {
            for ($sIndex = 0; $sIndex < $this->behaviors[$bIndex]->action_steps->count(); ++$sIndex) {
                $steps[] = $this->behaviors[$bIndex]->action_steps[$sIndex];
            }
        }
        return $steps;
    }

    /**
     * Returns a collection of all the steps found in behaviors.
     * @since 0.2.5
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getActionStepsCompleteAttribute()
    {
        $steps = new Collection;
        for ($bIndex = 0; $bIndex < $this->behaviors->count(); ++$bIndex) {
            for ($sIndex = 0; $sIndex < $this->behaviors[$bIndex]->action_steps_complete->count(); ++$sIndex) {
                $steps[] = $this->behaviors[$bIndex]->action_steps_complete[$sIndex];
            }
        }
        return $steps;
    }

    /**
     * Returns dynamic attribute `emphasisTranslated`. Returns the localized label
     * related to emphasis associated with the plan.
     * @since 0.2.4
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
     * Returns the date in which the next reminder should be sent.
     * @since 0.2.4
     *
     * @return string
     */
    public function getNextReminderDate()
    {
        // logic for determining when the next reminder should be sent
        $reminders = $this->action_plan_reminders;

        $dates = [];

        foreach ($reminders as $reminder) {
            $date = $reminder->getNextReminderDateFromEndDate($this->ends_at);
            if ($date !== null) {
                $dates[] = $date;
            }
        }

        $dates = new Collection($dates);

        return $dates->first();
    }

    /**
     * Returns dynamic attribute `formattedDates`. Returns list of formatted dates.
     * Overrides `BaseModel` method to take inconsideration next reminder date.
     * @since 0.2.4
     * 
     * @see \Momentum\BaseModel
     *
     * @return array
     */
    public function getFormattedDatesAttribute()
    {
        $dates = parent::getFormattedDatesAttribute();

        $dates['next_reminder_date'] = Dates::returnFormattedDates($this->getNextReminderDate());

        return $dates;
    }

    /**
     * Returns dynamic attribute `status`. Returns the localized name
     * for the status associated with the plan.
     * @since 0.2.4
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
     * Returns dynamic attribute `daysRemaining`. Returns the amount of days
     * remaining based on the value set in attribute `ends_at` and now.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getDaysRemainingAttribute()
    {
        $days = Carbon::now()->diffInDays($this->ends_at);

        if (Carbon::now() < $this->starts_at) {
            return $this->total_days;
        }

        return $days < 0 ? 0 : $days;
    }

    /**
     * Returns dynamic attribute `totalDays`. Returns the amount of days found
     * between the attributes `starts_at` and `ends_at`.
     * @since 0.2.4
     * 
     * @return int
     */
    public function getTotalDaysAttribute()
    {
        return $this->starts_at->diffInDays($this->ends_at);
    }

    /**
     * Returns dynamic attribute `currentPulseSurvey`.
     * Alias for method `getCurrentPulseSurvey()`.
     * @since 0.2.4
     * 
     * @return \Momentum\PulseSurvey
     */
    public function getCurrentPulseSurveyAttribute()
    {
        return $this->getCurrentPulseSurvey();
    }

    /**
     * Returns the first pulse survey found, ordered descendingly by its cycle.
     * @since 0.2.4
     * 
     * @see \Momentum\ActionStep::pulse_surveys
     * 
     * @return \Momentum\PulseSurvey
     */
    public function getCurrentPulseSurvey()
    {
        return $this->pulse_surveys()->orderByDesc('cycle')->first();
    }

    /**
     * Returns dynamic attribute `progressPercent`.
     * Returns the progress percentage of completed steps against total steps.
     * @since 0.2.4
     * @since 0.2.5 Change to take in consideration behaviors.
     * 
     * @return float
     */
    public function getProgressPercentAttribute()
    {
        if ($this->behaviors->count() <= 0)
            return 0;
        $counts = ['completed' => 0, 'total' => 0];
        for ($i = $this->behaviors->count() - 1; $i >= 0; --$i) {
            $counts['completed'] += $this->behaviors[$i]->action_steps_complete->count();
            $counts['total'] += $this->behaviors[$i]->action_steps->count();
        }
        return $counts['total'] > 0
            ? floor($counts['completed'] / $counts['total'] * 100)
            : 0;
    }

    /**
     * Returns dynamic attribute `timelinePercent`.
     * Returns the progress percentage of remaining days left against total days.
     * @since 0.2.4
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
     * Returns dynamic attribute `canCreatePulseSurvey`.
     * Returns flag indicating if pulse survey can be created or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanCreatePulseSurveyAttribute()
    {
        return $this->open_pulse_surveys()->count() == 0 && !$this->isComplete();
    }

    /**
     * Returns scoped query, filtered by completed records.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Returns scoped query, filtered by incompleted records.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Returns flag indicating if plan is in progress and has not been completed.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function isInProgress()
    {
        return $this->completed_at === null;
    }

    /**
     * Returns flag indicating if plan has been completed.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function isComplete()
    {
        return !$this->isInProgress();
    }

    /**
     * Returns dynamic attribute `isCompleted`.
     * Alias for `isCompleted()`.
     * @since 0.2.4
     * 
     * @see \Momentum\ActionPlan::isComplete
     * 
     * @return boolean
     */
    public function getIsCompleteAttribute()
    {
        return $this->isComplete();
    }

    /**
     * Returns dynamic attribute `canBeCompleted`.
     * Returns flag indicating if plan can be completed or not, this is done 
     * by comparing the count of steps completed against the count of steps available.
     * @since 0.2.4
     * @since 0.2.5 Takes in consideration behaviors instead. 
     * 
     * @return boolean
     */
    public function getCanBeCompletedAttribute()
    {
        $can = true;
        for ($i = $this->behaviors->count() - 1; $i >= 0; --$i) {
            $can = ($this->behaviors[$i]->action_steps_complete->count() === $this->behaviors[$i]->action_steps->count())
                && $can;
        }
        return $can;
    }

    /**
     * Returns dynamic attribute `canBeDeleted`.
     * Returns flag indicating if plan can be deleted or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanBeDeletedAttribute()
    {
        return $this->pulse_surveys()->count() === 0;
    }

    /**
     * Returns dynamic attribute `canEditBehavior`.
     * Returns flag indicating if the behavior can be edited or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanEditBehaviorAttribute()
    {
        return $this->pulse_surveys()->count() === 0;
    }
}
