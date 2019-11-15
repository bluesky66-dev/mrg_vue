<?php

namespace Momentum;

use Carbon\Carbon;
use Momentum\Traits\BelongsToReportTrait;
use Momentum\Traits\BelongsToUserTrait;
use Momentum\Utilities\Dates;

/**
 * Action plan step model.
 *
 * @author prev-team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionStep extends BaseModel
{
    use BelongsToReportTrait, BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'name_key',
        'description_key',
        'name',
        'description',
        'behavior_id',
        'emphasis',
        'user_id',
        'quest_action_step_id',
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
        'created_at',
        'updated_at',
        'deleted_at',
        'completed_at',
        'due_at',
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
            'description_key' => 'required_without:description',
            'name'            => 'required_without:name_key',
            'description'     => 'required_without:description_key',
            'behavior_id'     => 'required|exists:behaviors,id',
            'emphasis'        => 'required|emphasis',
            'user_id'         => 'required|exists:users,id',
            'report_id'       => 'nullable|exists:reports,id',
            'action_plan_id'  => 'nullable|exists:action_plans,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     * @since 0.2.5 Appended pivot attributes.
     *
     * @var array 
     */
    public $appends = [
        'completed_at',
        'due_at',
        'name_key_translated',
        'description_key_translated',
        'emphasis_translated',
        'name_processed',
        'description_processed',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the collection of all the plans associated with the step.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function action_plans()
    {
        return $this->belongsToMany(ActionPlans::class);
    }

    /**
     * Returns and creates relationship with `ActionPlans` model.
     * This will return the plan associated with the step.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action_plan()
    {
        return $this->belongsTo(ActionPlans::class);
    }

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behavior associated with the plan.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
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
     * Returns dynamic attribute `completedAt` | `completed_at`.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getCompletedAtAttribute()
    {
        return $this->pivot && $this->pivot->completed_at
            ? $this->pivot->completed_at
            : null;
    }

    /**
     * Returns dynamic attribute `dueAt` | `due_at`.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getDueAtAttribute()
    {
        return $this->pivot && $this->pivot->due_at
            ? $this->pivot->due_at
            : null;
    }

    /**
     * Returns dynamic attribute `nameKeyTranslated`.
     * Returns translation found for `name_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->name_key}
     * 
     * @return string
     */
    public function getNameKeyTranslatedAttribute()
    {
        if ($this->name_key == null) {
            return null;
        }

        return trans($this->name_key);
    }

    /**
     * Returns dynamic attribute `DescriptionKeyTranslated`.
     * Returns translation found for `description_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->description_key}
     * 
     * @return string
     */
    public function getDescriptionKeyTranslatedAttribute()
    {
        return trans($this->description_key);
    }

    /**
     * Returns dynamic attribute `emphasisTranslated`. Returns the localized label
     * related to emphasis associated with the step.
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
     * Returns dynamic attribute `nameProcessed`.
     * Returns the translation for the `name` attribute.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getNameProcessedAttribute()
    {
        if ($this->name_key !== null) {
            return $this->name_key_translated;
        }

        return $this->name;
    }

    /**
     * Returns dynamic attribute `descriptionProcessed`.
     * Returns the translation for the `description` attribute.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getDescriptionProcessedAttribute()
    {
        if ($this->description_key !== null) {
            return $this->description_key_translated;
        }

        return $this->description;
    }

    /**
     * Returns dynamic attribute `completedAtFormatted`.
     * NOTE: `completed_at` attribute will only be present if the model is loaded
     * using pivot with the `ActionPlan` model.
     * @since 0.2.5
     *
     * @return string
     */
    protected function getCompletedAtFormattedAttribute()
    {
        return $this->completed_at
            ? Dates::returnFormattedDates(new Carbon($this->completed_at))
            : null;
    }

    /**
     * Returns dynamic attribute `dueAtFormatted`.
     * NOTE: `due_at` attribute will only be present if the model is loaded
     * using pivot with the `ActionPlan` model.
     * @since 0.2.5
     *
     * @return string
     */
    protected function getDueAtFormattedAttribute()
    {
        return $this->due_at
            ? Dates::returnFormattedDates(new Carbon($this->due_at))
            : null;
    }

    /**
     * Returns scoped query, filtered by selectable records.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectable($query)
    {
        return $query->whereNull('user_id')->orWhere(function ($query) {
            $query->currentReport()->whereNull('action_plan_id');
        });
    }

    /**
     * Returns scoped query, filtered by selectable records for edition.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * @param \Momentum\ActionPlan                  $plan  Plan associated.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectableEdit($query, ActionPlan $action_plan)
    {
        return $query->whereNull('user_id')->orWhere(function ($query) use ($action_plan) {
            $query->currentReport()->where('action_plan_id', $action_plan->id);
        });
    }
}
