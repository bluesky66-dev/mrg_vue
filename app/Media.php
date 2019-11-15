<?php

namespace Momentum;

use Auth;
use Momentum\Traits\BelongsToUserTrait;

/**
 * Media model.
 * 
 * @link https://laravel.com/docs/5.6/filesystem#the-public-disk
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class Media extends BaseModel
{
    use BelongsToUserTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.5
     *
     * @var array 
     */
    protected $fillable = [
        'user_id',
        'organization_id',
        'path',
        'relative',
        'filename',
        'mime',
        'size',
        'name',
        'caption',
        'description',
    ];

    /**
     * Validation rules per attribute.
     * @since 0.2.5
     *
     * @see \Momentum\Traits\ValidatableTrait
     *
     * @var array 
     */
    protected $rules = [
        'default' => [
            'user_id'           => 'required|exists:users,id',
            'organization_id'   => 'nullable|exists:organizations,id',
            'path'              => 'required',
            'relative'          => 'required',
            'filename'          => 'required',
            'mime'              => 'required',
            'size'              => 'required',
            'name'              => 'nullable',
            'caption'           => 'nullable',
            'description'       => 'nullable',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.5
     *
     * @var array 
     */
    public $appends = [
        'url',
        'is_image',
        'is_video',
        'is_pdf',
        'is_owner',
    ];

    /**
     * Hidden from casting.
     * @since 0.2.5
     *
     * @var array 
     */
    public $hidden = [
        'path',
        'relative',
        'user',
        'organization',
        'pivot',
        'size',
        'deleted_at',
        'user_id',
        'organization_id',
    ];

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the event.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the user associated with the event.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Returns dynamic attribute `url`.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getUrlAttribute()
    {
        return route('media.render', ['id' => $this->id]);
    }

    /**
     * Returns dynamic attribute `isStorage` | `is_storage`.
     * Returns flag indicating if media is stored using laravel's storage system.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsStorageAttribute()
    {
        return preg_match('/disk\:/', $this->path);
    }

    /**
     * Returns dynamic attribute `storageDisk` | `storage_disk`.
     * Returns laravel's storage disk where this file is located.
     * @since 0.2.5
     * 
     * @return string
     */
    protected function getStorageDiskAttribute()
    {
        return $this->isStorage ? trim(str_replace('disk:', '', $this->path)) : null;
    }

    /**
     * Returns dynamic attribute `isOwner` | `is_owner`.
     * Returns flag indicating if current user is owner of the media.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsOwnerAttribute()
    {
        return Auth::user() && Auth::user()->id === $this->user_id;
    }

    /**
     * Returns dynamic attribute `isImage` | `is_image`.
     * Returns flag indicating if media is an image.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsImageAttribute()
    {
        return preg_match('/image/', $this->mime);
    }

    /**
     * Returns dynamic attribute `isVideo` | `is_video`.
     * Returns flag indicating if media is a video.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsVideoAttribute()
    {
        return preg_match('/video/', $this->mime);
    }

    /**
     * Returns dynamic attribute `isPdf` | `is_pdf`.
     * Returns flag indicating if media is a pdf.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getIsPdfAttribute()
    {
        return preg_match('/pdf/', $this->mime);
    }

    /**
     * Returns dynamic attribute `canForceDownload` | `can_force_download`.
     * Returns flag indicating if media can be forced downloaded during rendering.
     * @since 0.2.5
     * 
     * @return bool
     */
    protected function getCanForceDownloadAttribute()
    {
        return !$this->is_image && !$this->is_video && !$this->is_pdf;
    }

    /**
     * Returns scoped query, filtered by current user, or current user's organization.
     * @since 0.2.5
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCurrentUserOrOrganization($query)
    {
        $user = Auth::user();
        if ($user) {
            return $query->where(function($query) use(&$user) {
                $query->where('user_id', $user->id)
                    ->orWhere('organization_id', $user->organization_id);
            });
        }
        return $query;
    }
}
