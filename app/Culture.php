<?php

namespace Momentum;

/**
 * Culture model. (Localization catalog)
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Culture extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'code',
        'enabled',
        'english_name',
        'name_key',
        'quest_culture_id',
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
            'code'             => 'required',
            'enabled'          => 'required',
            'english_name'     => 'required',
            'name_key'         => 'required',
            'quest_culture_id' => 'required|integer',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'name_key_translated',
        'code_unix',
    ];

    /**
     * Returns model (front-end ready) mapped with translations.
     * @since 0.2.4
     * 
     * @return \Momentum\Culture
     */
    public static function getForFrontend()
    {
        return self::enabled()->get()->map(function ($item) {
            return [
                'id'                  => $item->id,
                'code'                => $item->code,
                'name_key'            => $item->code,
                'name_key_translated' => $item->name_key_translated,
            ];
        });
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
        return $query->where('enabled', true);
    }

    /**
     * Returns dynamic attribute `nameKeyTranslated`.
     * Returns translation found for `name_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->name_key}
     * 
     * @return string
     */
    public function getNameKeyTranslatedAttribute()
    {
        return trans($this->name_key);
    }

    /**
     * Returns dynamic attribute `codeUnix`.
     * Returns UNIX code related to culture.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getCodeUnixAttribute()
    {
        return $this->code . '.UTF-8';
    }
}
