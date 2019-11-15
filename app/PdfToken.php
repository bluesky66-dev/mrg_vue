<?php

namespace Momentum;

use Illuminate\Support\Str;

/**
 * PDF token model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class PdfToken extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'token',
        'used_at',
        'route',
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
        'used_at',
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
            'token' => 'required',
        ],
    ];

    /**
     * Initializes a random `token` of 60 characters when a new model is created.
     * @since 0.2.4
     */
    public static function boot()
    {
       static::creating(function ($model) {
           $model->token = Str::random(60);
       });
    }

}
