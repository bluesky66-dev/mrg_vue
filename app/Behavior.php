<?php

namespace Momentum;

/**
 * Behavior model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class Behavior extends BaseModel
{
    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'name_key',
        'report_text_key',
        'rating_feedback_question_key',
        'additional_feedback_question_key',
        'low_label_key',
        'high_label_key',
        'behavior_group_id',
        'quest_behavior_id',
        'order',
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
            'name_key'                         => 'required',
            'report_text_key'                  => 'required',
            'additional_feedback_question_key' => 'required',
            'rating_feedback_question_key'     => 'required',
            'low_label_key'                    => 'required',
            'high_label_key'                   => 'required',
            'behavior_group_id'                => 'required|exists:behavior_groups,id',
            'quest_behavior_id'                => 'required',
            'order'                            => 'required|integer',
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
        'report_text_key_translated',
        'additional_feedback_question_key_translated',
        'rating_feedback_question_key_translated',
        'low_label_key_translated',
        'high_label_key_translated',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the behaviors associated with the plan.
     * @since 0.2.5
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function action_plans()
    {
        return $this->belongsToMany(ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `BehaviorGroup` model.
     * This will return the group associated with the behavior.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function behavior_group()
    {
        return $this->belongsTo(BehaviorGroup::class);
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
     * Returns dynamic attribute `reportTextKeyTranslated`.
     * Returns translation found for `report_text_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->report_text_key}
     * 
     * @return string
     */
    public function getReportTextKeyTranslatedAttribute()
    {
        return trans($this->report_text_key);
    }

    /**
     * Returns dynamic attribute `AdditionalFeedbackQuestionKeyTranslated`.
     * Returns translation found for `additional_feedback_question_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->additional_feedback_question_key}
     * 
     * @return string
     */
    public function getAdditionalFeedbackQuestionKeyTranslatedAttribute()
    {
        return trans($this->additional_feedback_question_key);
    }

    /**
     * Returns dynamic attribute `RatingFeedbackQuestionKeyTranslated`.
     * Returns translation found for `rating_feedback_question_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->rating_feedback_question_key}
     * 
     * @return string
     */
    public function getRatingFeedbackQuestionKeyTranslatedAttribute()
    {
        return trans($this->rating_feedback_question_key);
    }

    /**
     * Returns dynamic attribute `LowLabelKeyTranslated`.
     * Returns translation found for `low_label_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->low_label_key}
     * 
     * @return string
     */
    public function getLowLabelKeyTranslatedAttribute()
    {
        return trans($this->low_label_key);
    }

    /**
     * Returns dynamic attribute `HighLabelKeyTranslated`.
     * Returns translation found for `high_label_key` attribute.
     * @since 0.2.4
     * 
     * @see {resources}/lang/{this->high_label_key}
     * 
     * @return string
     */
    public function getHighLabelKeyTranslatedAttribute()
    {
        return trans($this->high_label_key);
    }

    /**
     * Returns model (front-end ready) mapped with translations.
     * @since 0.2.4
     * 
     * @return \Momentum\Behavior
     */
    public static function getForFrontend()
    {
        return self::with('behavior_group')->get()->map(function ($item) {
            return [
                'id'                         => $item->id,
                'name_key'                   => $item->name_key,
                'name_key_translated'        => $item->name_key_translated,
                'report_text_key'            => $item->report_text_key,
                'report_text_key_translated' => $item->report_text_key_translated,
                'low_label_key'              => $item->low_label_key,
                'high_label_key'             => $item->high_label_key,
                'low_label_key_translated'   => $item->low_label_key_translated,
                'high_label_key_translated'  => $item->high_label_key_translated,
                'group_name_key'             => $item->behavior_group->name_key,
                'group_name_key_translated'  => $item->behavior_group->name_key_translated,
            ];
        });
    }
}
