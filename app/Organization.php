<?php

namespace Momentum;

/**
 * Organization model.
 *
 * @author ATOM team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class Organization extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'name',
        'logo',
        'quest_organization_id',
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
            'name'                  => 'required',
            'quest_organization_id' => 'required|integer',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $appends = [
        'formatted_dates',
        'logo_path',
    ];

    /**
     * Returns and creates relationship with `User` model.
     * This will return the users associated with the organization.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Returns and creates relationship with `OrganizationGoal` model.
     * This will return the collection of goals associated with the organization.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goals()
    {
        return $this->hasMany(OrganizationGoal::class);
    }

    /**
     * Returns and creates relationship with `User` model.
     * This will return the users associated with the organization
     * that have billing capabilities.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billing_users()
    {
        return $this->hasMany(User::class, 'billing_organization_id');
    }

    /**
     * Returns dynamic attribute `logoPath`. Returns organization's logo URL.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getLogoPathAttribute()
    {
        if ($this->logo === null) {
           return null;
        }

        if (!$this->logoFileExists()) {
            return null;
        }

        return asset(config('momentum.logos_asset_path') . DIRECTORY_SEPARATOR . $this->logo);
    }

    /**
     * Returns flag indicating if logo file exists.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function logoFileExists()
    {
        return file_exists(config('momentum.logos_path') . DIRECTORY_SEPARATOR . $this->logo);
    }

}
