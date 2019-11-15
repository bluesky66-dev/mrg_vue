<?php

namespace Momentum;

use Carbon\Carbon;
use Momentum\Utilities\Dates;

/**
 * Action goal model.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ActionGoal extends BaseModel
{

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.5
     *
     * @var array 
     */
    protected $fillable = [
        'action_plan_id',
        'organization_goal_id',
        'custom_question',
        'custom_type',
        'answer',
        'sort',
    ];

    /**
     * Validation rules per attribute.
     * @since 0.2.5
     *
     * @see \Momentum\Traits\ValidatableTrait
     *
     * @var array 
     */
    protected $rules = [
        'default' => [
            'action_plan_id'        => 'required|integer|exists:action_plans,id',
            'organization_goal_id'  => 'required_if:custom_question,null|integer|exists:organization_goals,id',
            'custom_question'       => 'required_if:organization_goal_id,null|textarea',
            'custom_type'           => 'required_if:organization_goal_id,null|in:goal,constituents,benefits,risks,obstacles,resources',
            'answer'                => 'required|textarea',
            'sort'                  => 'required|integer',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.5
     *
     * @var array 
     */
    protected $appends = [
        'question',
        'question_key',
        'type',
        'type_name',
        'is_custom',
        'is_organizational',
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
     * Returns and creates relationship with `OrganizationGoal` model.
     * This will return the organization goal associated with the plan goal.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization_goal()
    {
        return $this->belongsTo(OrganizationGoal::class);
    }

    /**
     * Returns dynamic attribute `is_organizational`.
     * Returns flag indicating if this is an organizational question.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsOrganizationalAttribute()
    {
        return !empty($this->organization_goal_id);
    }

    /**
     * Returns dynamic attribute `is_custom`.
     * Returns flag indicating if this is a custom question.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsCustomAttribute()
    {
        return !$this->is_organizational;
    }

    /**
     * Returns dynamic attribute `question`.
     * Returns custom or organizational question.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getQuestionAttribute()
    {
        return $this->is_organizational ? $this->organization_goal->question : $this->custom_question;
    }

    /**
     * Returns dynamic attribute `question_key`.
     * Returns organizational question key.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getQuestionKeyAttribute()
    {
        return $this->is_organizational ? $this->organization_goal->question_key : null;
    }

    /**
     * Returns dynamic attribute `type`.
     * Returns custom or organizational question type.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getTypeAttribute()
    {
        return $this->is_organizational ? $this->organization_goal->type : $this->custom_type;
    }

    /**
     * Returns dynamic attribute `type_name`.
     * Returns the translation for `type`.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getTypeNameAttribute()
    {
        return trans('action_plan.goals.'.$this->type.'.label');
    }

    /**
     * Returns scoped query, filtered by current organization or organization ID.
     * @since 0.2.5
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query        Query builder.
     * @param mixed                                 $organization Organization model or organization ID.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereOrganization($query, $organization)
    {
        if ($organization === null || is_array($organization))
            return $query;
        return $query->select('action_goals.*')
            ->join('organization_goals', 'organization_goals.id', '=', 'action_goals.organization_goal_id')
            ->where('organization_goals.organization_id', is_object($organization) ? $organization->id : $organization);
    }

    /**
     * Returns scoped query, filtered by NON organizational goals.
     * @since 0.2.5
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotOrganization($query)
    {
        return $query->whereNull('organization_goal_id');
    }

    /**
     * Returns scoped query, filtered by organizational goals only.
     * @since 0.2.5
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereIsOrganization($query)
    {
        return $query->whereNotNull('organization_goal_id');
    }
}
