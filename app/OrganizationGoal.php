<?php

namespace Momentum;

/**
 * Organization goal model.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class OrganizationGoal extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.5
     *
     * @var array 
     */
    protected $fillable = [
        'organization_id',
        'question_key',
        'type',
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
            'organization_id'   => 'required|integer|exists:organization,id',
            'question_key'      => 'required|textarea',
            'type'              => 'required|in:goal,constituents,benefits,risks,obstacles,resources',
            'sort'              => 'required|integer',
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
        'type_name',
    ];

    /**
     * Returns and creates relationship with `organization` model.
     * This will return the organization associated with the goal.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Returns dynamic attribute `question`.
     * Returns the translation for `question_key`.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getQuestionAttribute()
    {
        return trans($this->question_key);
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
        return $query->where('organization_id', is_object($organization) ? $organization->id : $organization);
    }
}
