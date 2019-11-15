<?php

namespace Momentum;

use Momentum\Traits\BelongsToReportTrait;

/**
 * Report score model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ReportScore extends BaseModel
{
    use  BelongsToReportTrait;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'boss_norm',
        'self_norm',
        'peer_norm',
        'direct_report_norm',
        'boss_agreement',
        'peer_agreement',
        'direct_report_agreement',
        'report_id',
        'behavior_id',
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
            'boss_norm'               => 'nullable|integer|between:0,100',
            'self_norm'               => 'nullable|integer|between:0,100',
            'peer_norm'               => 'nullable|integer|between:0,100',
            'direct_report_norm'      => 'nullable|integer|between:0,100',
            'boss_agreement'          => 'nullable|agreement',
            'peer_agreement'          => 'nullable|agreement',
            'direct_report_agreement' => 'nullable|agreement',
            'report_id'               => 'required|exists:reports,id',
            'behavior_id'             => 'required|exists:behaviors,id',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    public $appends = [
        'boss_agreement_translated',
        'peer_agreement_translated',
        'direct_report_agreement_translated',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `Behavior` model.
     * This will return the behavior associated with the report score.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the report associated with the report score.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Returns dynamic attribute `bossAgreementTranslated`.
     * Returns the translated label related to the `boss_agreement` 
     * associated with the report score.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['agreements']
     * 
     * @return string
     */
    public function getBossAgreementTranslatedAttribute()
    {
        return trans('global.enum.agreements.' . $this->boss_agreement . '.label');
    }

    /**
     * Returns dynamic attribute `peerAgreementTranslated`.
     * Returns the translated label related to the `peer_agreement` 
     * associated with the report score.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['agreements']
     * 
     * @return string
     */
    public function getPeerAgreementTranslatedAttribute()
    {
        return trans('global.enum.agreements.' . $this->peer_agreement . '.label');
    }

    /**
     * Returns dynamic attribute `directReportAgreementTranslated`.
     * Returns the translated label related to the `direct_report_agreement` 
     * associated with the report score.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['agreements']
     * 
     * @return string
     */
    public function getDirectReportAgreementTranslatedAttribute()
    {
        return trans('global.enum.agreements.' . $this->direct_report_agreement . '.label');
    }
}
