<?php

namespace Momentum;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Momentum\Enums\Frequencies;
use Momentum\Utilities\Dates;

/**
 * Action plan reminder model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ActionPlanReminder extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'frequency',
        'starts_at',
        'action_plan_id',
        'action_step_id',
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
        'starts_at',
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
            'frequency'      => 'required|frequency',
            'starts_at'      => 'required|date',
            'action_plan_id' => 'required|exists:action_plans,id',
            'action_step_id' => 'nullable|exists:action_steps,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'frequency_translated',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the plan associated with the reminder.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_plan()
    {
        return $this->belongsTo(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `ActionStep` model.
     * This will return the step associated with the reminder.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_step()
    {
        return $this->belongsTo(ActionStep::class);
    }

    /**
     * Returns and creates relationship with `User` model through the `ActionPlan` model.
     * This will return the users associated with the reminder.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function user()
    {
        return $this->hasManyThrough(User::class, ActionPlan::class);
    }

    /**
     * Returns dynamic attribute `frequencyTranslated`. Returns the localized label
     * related to the frequency associated with the step.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['frequencies']
     * 
     * @return string
     */
    public function getFrequencyTranslatedAttribute()
    {
        return trans('global.enum.frequencies.' . $this->frequency . '.label');
    }

    /**
     * Returns flag indicating if frequency is once.
     * @since 0.2.4
     *
     * @see \Momentum\Enums\Frequencies
     * 
     * @return boolean
     */
    public function frequencyIsOnce()
    {
        return $this->frequency == Frequencies::ONCE;
    }

    /**
     * Returns flag indicating if frequency is daily.
     * @since 0.2.4
     *
     * @see \Momentum\Enums\Frequencies
     * 
     * @return boolean
     */
    public function frequencyIsDaily()
    {
        return $this->frequency == Frequencies::DAILY;
    }

    /**
     * Returns flag indicating if frequency is weekly.
     * @since 0.2.4
     *
     * @see \Momentum\Enums\Frequencies
     * 
     * @return boolean
     */
    public function frequencyIsWeekly()
    {
        return $this->frequency == Frequencies::WEEKLY;
    }

    /**
     * Returns flag indicating if frequency is monthly.
     * @since 0.2.4
     *
     * @see \Momentum\Enums\Frequencies
     * 
     * @return boolean
     */
    public function frequencyIsMonthly()
    {
        return $this->frequency == Frequencies::MONTHLY;
    }

    /**
     * Returns the date in which the next reminder should be sent.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getNextReminderDate()
    {
        $dates = $this->getScheduledDates();

        return Dates::getNextDate($dates);
    }

    /**
     * Returns the date in which the next reminder should be sent.
     * Taking in consideration an end date passed as parameter.
     * @since 0.2.4
     * 
     * @param string $end_date End date reference.
     * 
     * @return string
     */
    public function getNextReminderDateFromEndDate($end_date)
    {
        $dates = $this->getScheduledDatesFromEndDate($end_date);

        return Dates::getNextDate($dates);
    }

    /**
     * Returns the collection of scheduled dates in which the reminder should be sent.
     * @since 0.2.4
     * 
     * @return Illuminate\Support\Collection
     */
    public function getScheduledDates()
    {
        return $this->getScheduledDatesFromEndDate($this->action_plan->ends_at);
    }

    /**
     * Returns the collection of scheduled dates in which the reminder should be sent.
     * Taking in consideration an end date passed as parameter.
     * @since 0.2.4
     * 
     * @param string $end_date End date reference.
     * 
     * @return Illuminate\Support\Collection
     */
    public function getScheduledDatesFromEndDate($end_date)
    {
        $start_date = $this->starts_at;

        if ($this->frequencyIsOnce()) {
            // there's only one date if it's once, and it's the start date
            return new Collection([$start_date]);
        }

        if ($this->frequencyIsDaily()) {
            $dates = [];
            for($date = $start_date; $date->lte($end_date); $date->addDay()) {
                $dates[] = $date->copy();
            }

            return new Collection($dates);
        }

        if ($this->frequencyIsWeekly()) {
            $dates = [];
            for($date = $start_date; $date->lte($end_date); $date->addWeek()) {
                $dates[] = $date->copy();
            }

            return new Collection($dates);
        }

        if ($this->frequencyIsMonthly()) {
            $dates = [];
            for($date = $start_date; $date->lte($end_date); $date->addMonth()) {
                $dates[] = $date->copy();
            }

            return new Collection($dates);
        }

        // there's only one date if it's once, and it's the start date
        return new Collection([$start_date]);
    }
}
