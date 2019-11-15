<?php

namespace Momentum;

use Momentum\Traits\BelongsToUserTrait;

/**
 * Application state model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ApplicationState extends BaseModel
{
    use BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'application_key',
        'data',
        'user_id',
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
            'application_key' => 'required',
            'data'            => 'required',
            'user_id'         => 'required|exists:users,id',
        ],
    ];

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the application state.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
