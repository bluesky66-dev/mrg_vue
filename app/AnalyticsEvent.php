<?php

namespace Momentum;

use Momentum\Traits\BelongsToUserTrait;

/**
 * Analytics model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class AnalyticsEvent extends BaseModel
{
    use BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'category',
        'label',
        'action',
        'data',
        'user_id',
    ];

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the event.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
