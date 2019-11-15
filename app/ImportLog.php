<?php

namespace Momentum;

/**
 * Import log model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ImportLog extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'file_name',
        'started_at',
        'completed_at',
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
        'started_at',
        'completed_at',
        'error_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
