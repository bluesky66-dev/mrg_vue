<?php

namespace Momentum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Momentum\Traits\ValidatableTrait;
use Momentum\Utilities\Dates;

/**
 * The base model extends of Laravel's abstract Model to 
 * provide basic functionality related to date handling. 
 * Includes functionality to localize and format date values. 
 * Also base traits for soft deletions and validations.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class BaseModel extends Model
{
    use SoftDeletes, ValidatableTrait;
    /**
     * Model property used to define which attributes have date or datetime values.
     * @since 0.2.4
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * Default appends with formatted dates.
     * @since 0.2.4
     *
     * @var array
     */
    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
        'deleted_at_formatted',
        'formatted_dates',
    ];
    /**
     * Returns list of formatted date values.
     * Alias: formattedDates
     * @since 0.2.4
     *
     * @var array
     */
    public function getFormattedDatesAttribute()
    {
        $dates = [];
        foreach ($this->dates as $field) {
            $dates[$field] = Dates::returnFormattedDates($this->{$field});
        }
        if(isset($this->attributes['formatted_dates'])) {
            return array_merge($dates, $this->attributes['formatted_dates']);
        }
        return $dates;
    }
    /**
     * Returns formatted date value.
     * Alias: createdAtFormatted
     * @since 0.2.4
     *
     * @var string
     */
    public function getCreatedAtFormattedAttribute()
    {
        if ($this->created_at === null) {
            return null;
        }
        return $this->created_at->formatLocalized(config('momentum.long_date_format'));
    }
    /**
     * Returns formatted date value.
     * Alias: updatedAtFormatted
     * @since 0.2.4
     *
     * @var string
     */
    public function getUpdatedAtFormattedAttribute()
    {
        if ($this->updated_at === null) {
            return null;
        }
        return $this->updated_at->formatLocalized(config('momentum.long_date_format'));
    }
    /**
     * Returns formatted date value.
     * Alias: deletedAtFormatted
     * @since 0.2.4
     *
     * @var string
     */
    public function getDeletedAtFormattedAttribute()
    {
        if ($this->deleted_at === null) {
            return null;
        }
        return $this->deleted_at->formatLocalized(config('momentum.long_date_format'));
    }
}
