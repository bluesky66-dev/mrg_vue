<?php

namespace Momentum;

/**
 * Email log model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class EmailLog extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'recipient',
        'email_type',
        'sent_at',
        'error_at',
        'error',
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
        'sent_at',
        'error_at',
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
            'recipient'  => 'required|email',
            'email_type' => 'required',
            'sent_at'    => 'required|date',
        ],
    ];
}
