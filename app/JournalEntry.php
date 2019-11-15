<?php

namespace Momentum;

use Momentum\Traits\BelongsToReportTrait;
use Momentum\Traits\BelongsToUserTrait;

/**
 * Journal entry model.
 *
 * @author ATOM team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class JournalEntry extends BaseModel
{
    use BelongsToUserTrait, BelongsToReportTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'description',
        'user_id',
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
            'description' => 'required',
            'user_id'     => 'required|exists:users,id',
            'report_id'   => 'required|exists:reports,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $appends = [
        'behavior_tags',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `Media` model.
     * This will return a collection of attachments(media) associated with the entry.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attachments()
    {
        return $this->belongsToMany(Media::class);
    }

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behaviors associated with the entry.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function behaviors()
    {
        return $this->belongsToMany(Behavior::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the report associated with the entry.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the entry.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns dynamic attribute `behaviorTags`.
     * Returns a list (separated by commas) of all behaviors associated with entry.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getBehaviorTagsAttribute()
    {
        return implode(', ', $this->behaviors->pluck('name_key_translated')->toArray());
    }
}
