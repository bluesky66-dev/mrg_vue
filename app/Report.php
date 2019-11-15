<?php

namespace Momentum;

use Momentum\Enums\ReportStatuses;
use Momentum\Traits\BelongsToUserTrait;

/**
 * Report model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Report extends BaseModel
{
    use BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'file',
        'user_id',
        'quest_report_id',
        'quest_pqp_id',
        'status',
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
            'file'            => 'required',
            'user_id'         => 'required|exists:users,id',
            'quest_report_id' => 'required|exists:reports,id',
            'quest_pqp_id'    => 'required|integer',
            'status'          => 'required|report_status',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'status_translated',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the plans associated with the report.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function action_plans()
    {
        return $this->hasMany(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `JournalEntry` model.
     * This will return the journal entries associated with the report.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journal_entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Returns and creates relationship with `Observer` model.
     * This will return the observers associated with the report.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observers()
    {
        return $this->hasMany(Observer::class);
    }

    /**
     * Returns and creates relationship with `PulseSurvey` model.
     * This will return the pulse surveys associated with the report.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_surveys()
    {
        return $this->hasMany(PulseSurvey::class);
    }

    /**
     * Returns and creates relationship with `ReportScore` model.
     * This will return the report scores associated with the report.
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
     * This will return the user associated with the report.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns dynamic attribute `statusTranslated`.
     * Returns the translated label related to the status associated with the report.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['status']
     * 
     * @return string
     */
    public function getStatusTranslatedAttribute()
    {
        return trans('global.enum.status.' . $this->status . '.label');
    }

    /**
     * Returns scoped query, filtered by active records.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', ReportStatuses::ACTIVE);
    }

}
