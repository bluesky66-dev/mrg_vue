<?php

namespace Momentum;

/**
 * Behavior group model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class BehaviorGroup extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'name_key',
        'order',
        'quest_behavior_group_id',
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
            'name_key'                => 'required',
            'quest_behavior_group_id' => 'required|integer',
            'order'                   => 'required|integer',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'name_key_translated',
    ];

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behaviors associated with the group.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function behaviors()
    {
        return $this->hasMany(Behavior::class);
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
        return trans($this->name_key);
    }
}
