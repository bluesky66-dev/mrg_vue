<?php

namespace Momentum;

use Illuminate\Notifications\Notifiable;
use Momentum\Interfaces\CanReceiveNotifications;
use Momentum\Traits\BelongsToReportTrait;
use Momentum\Traits\BelongsToUserTrait;
use Momentum\Traits\ReceivesNotificationsTrait;

/**
 * Observer model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Observer extends BaseModel implements CanReceiveNotifications
{
    use BelongsToReportTrait, BelongsToUserTrait, Notifiable, ReceivesNotificationsTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'culture_id',
        'user_id',
        'observer_type',
        'disabled',
        'quest_observer_id',
        'report_id',
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
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email',
            'culture_id'    => 'required|exists:cultures,id',
            'user_id'       => 'required|exists:users,id',
            'report_id'     => 'required|exists:reports,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'observer_type_translated',
        'full_name',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `Culture` model.
     * This will return the culture associated with the observer.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function culture()
    {
        return $this->belongsTo(Culture::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the report associated with the observer.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Returns and creates relationship with `ReportScore` model.
     * This will return the report scores associated with the observer.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function report_scores()
    {
        return $this->hasMany(ReportScore::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the observer.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns dynamic attribute `fullName`.
     * Returns a concatenation between attributes `first_name` and `last_name`.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns dynamic attribute `observerTypeTranslated`.
     * Returns the translated label related to the type associated with the observer.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['observer_types']
     * 
     * @return string
     */
    public function getObserverTypeTranslatedAttribute()
    {
        return trans('global.enum.observer_types.' . $this->observer_type  . '.label');
    }

    /**
     * Returns scoped query, filtered by enabled records.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
       return $query->where('disabled', false);
    }
}
