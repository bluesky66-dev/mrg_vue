<?php

use Illuminate\Database\Seeder;

class BehaviorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $behavior_groups = [
            [
                'name_key'                => 'behavior.group.creating_vision.label',
                'quest_behavior_group_id' => 1,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.conservative.label',
                        'low_label_key'                    => 'behavior.conservative.low_label',
                        'high_label_key'                   => 'behavior.conservative.high_label',
                        "report_text_key"                  => "behavior.conservative.report_text",
                        "rating_feedback_question_key"     => "behavior.conservative.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.conservative.additional_feedback_question",
                        'quest_behavior_id'                => 10,
                    ],
                    [
                        'name_key'                         => 'behavior.innovative.label',
                        'low_label_key'                    => 'behavior.innovative.low_label',
                        'high_label_key'                   => 'behavior.innovative.high_label',
                        "report_text_key"                  => "behavior.innovative.report_text",
                        "rating_feedback_question_key"     => "behavior.innovative.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.innovative.additional_feedback_question",
                        'quest_behavior_id'                => 42,
                    ],
                    [
                        'name_key'                         => 'behavior.technical.label',
                        'low_label_key'                    => 'behavior.technical.low_label',
                        'high_label_key'                   => 'behavior.technical.high_label',
                        "report_text_key"                  => "behavior.technical.report_text",
                        "rating_feedback_question_key"     => "behavior.technical.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.technical.additional_feedback_question",
                        'quest_behavior_id'                => 81,
                    ],
                    [
                        'name_key'                         => 'behavior.self.label',
                        'low_label_key'                    => 'behavior.self.low_label',
                        'high_label_key'                   => 'behavior.self.high_label',
                        "report_text_key"                  => "behavior.self.report_text",
                        "rating_feedback_question_key"     => "behavior.self.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.self.additional_feedback_question",
                        'quest_behavior_id'                => 72,
                    ],
                    [
                        'name_key'                         => 'behavior.strategic.label',
                        'low_label_key'                    => 'behavior.strategic.low_label',
                        'high_label_key'                   => 'behavior.strategic.high_label',
                        "report_text_key"                  => "behavior.strategic.report_text",
                        "rating_feedback_question_key"     => "behavior.strategic.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.strategic.additional_feedback_question",
                        'quest_behavior_id'                => 75,
                    ],
                ],
            ],
            [
                'name_key'                => 'behavior.group.followership.label',
                'quest_behavior_group_id' => 2,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.persuasive.label',
                        'low_label_key'                    => 'behavior.persuasive.low_label',
                        'high_label_key'                   => 'behavior.persuasive.high_label',
                        "report_text_key"                  => "behavior.persuasive.report_text",
                        "rating_feedback_question_key"     => "behavior.persuasive.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.persuasive.additional_feedback_question",
                        'quest_behavior_id'                => 59,
                    ],
                    [
                        'name_key'                         => 'behavior.outgoing.label',
                        'low_label_key'                    => 'behavior.outgoing.low_label',
                        'high_label_key'                   => 'behavior.outgoing.high_label',
                        "report_text_key"                  => "behavior.outgoing.report_text",
                        "rating_feedback_question_key"     => "behavior.outgoing.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.outgoing.additional_feedback_question",
                        'quest_behavior_id'                => 56,
                    ],
                    [
                        'name_key'                         => 'behavior.excitement.label',
                        'low_label_key'                    => 'behavior.excitement.low_label',
                        'high_label_key'                   => 'behavior.excitement.high_label',
                        "report_text_key"                  => "behavior.excitement.report_text",
                        "rating_feedback_question_key"     => "behavior.excitement.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.excitement.additional_feedback_question",
                        'quest_behavior_id'                => 29,
                    ],
                    [
                        'name_key'                         => 'behavior.restraint.label',
                        'low_label_key'                    => 'behavior.restraint.low_label',
                        'high_label_key'                   => 'behavior.restraint.high_label',
                        "report_text_key"                  => "behavior.restraint.report_text",
                        "rating_feedback_question_key"     => "behavior.restraint.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.restraint.additional_feedback_question",
                        'quest_behavior_id'                => 67,
                    ],
                ],
            ],
            [
                'name_key'                => 'behavior.group.implementing_vision.label',
                'quest_behavior_group_id' => 3,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.structuring.label',
                        'low_label_key'                    => 'behavior.structuring.low_label',
                        'high_label_key'                   => 'behavior.structuring.high_label',
                        "report_text_key"                  => "behavior.structuring.report_text",
                        "rating_feedback_question_key"     => "behavior.structuring.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.structuring.additional_feedback_question",
                        'quest_behavior_id'                => 77,
                    ],
                    [
                        'name_key'                         => 'behavior.tactical.label',
                        'low_label_key'                    => 'behavior.tactical.low_label',
                        'high_label_key'                   => 'behavior.tactical.high_label',
                        "report_text_key"                  => "behavior.tactical.report_text",
                        "rating_feedback_question_key"     => "behavior.tactical.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.tactical.additional_feedback_question",
                        'quest_behavior_id'                => 79,
                    ],
                    [
                        'name_key'                         => 'behavior.communication.label',
                        'low_label_key'                    => 'behavior.communication.low_label',
                        'high_label_key'                   => 'behavior.communication.high_label',
                        "report_text_key"                  => "behavior.communication.report_text",
                        "rating_feedback_question_key"     => "behavior.communication.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.communication.additional_feedback_question",
                        'quest_behavior_id'                => 7,
                    ],
                    [
                        'name_key'                         => 'behavior.delegation.label',
                        'low_label_key'                    => 'behavior.delegation.low_label',
                        'high_label_key'                   => 'behavior.delegation.high_label',
                        "report_text_key"                  => "behavior.delegation.report_text",
                        "rating_feedback_question_key"     => "behavior.delegation.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.delegation.additional_feedback_question",
                        'quest_behavior_id'                => 16,
                    ],
                ],
            ],
            [
                'name_key'                => 'behavior.group.following_through.label',
                'quest_behavior_group_id' => 4,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.control.label',
                        'low_label_key'                    => 'behavior.control.low_label',
                        'high_label_key'                   => 'behavior.control.high_label',
                        "report_text_key"                  => "behavior.control.report_text",
                        "rating_feedback_question_key"     => "behavior.control.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.control.additional_feedback_question",
                        'quest_behavior_id'                => 11,
                    ],
                    [
                        'name_key'                         => 'behavior.feedback.label',
                        'low_label_key'                    => 'behavior.feedback.low_label',
                        'high_label_key'                   => 'behavior.feedback.high_label',
                        "report_text_key"                  => "behavior.feedback.report_text",
                        "rating_feedback_question_key"     => "behavior.feedback.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.feedback.additional_feedback_question",
                        'quest_behavior_id'                => 33,
                    ],
                ],
            ],
            [
                'name_key'                => 'behavior.achieving_results.label',
                'quest_behavior_group_id' => 5,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.management_focus.label',
                        'low_label_key'                    => 'behavior.management_focus.low_label',
                        'high_label_key'                   => 'behavior.management_focus.high_label',
                        "report_text_key"                  => "behavior.management_focus.report_text",
                        "rating_feedback_question_key"     => "behavior.management_focus.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.management_focus.additional_feedback_question",
                        'quest_behavior_id'                => 50,
                    ],
                    [
                        'name_key'                         => 'behavior.dominant.label',
                        'low_label_key'                    => 'behavior.dominant.low_label',
                        'high_label_key'                   => 'behavior.dominant.high_label',
                        "report_text_key"                  => "behavior.dominant.report_text",
                        "rating_feedback_question_key"     => "behavior.dominant.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.dominant.additional_feedback_question",
                        'quest_behavior_id'                => 18,
                    ],
                    [
                        'name_key'                         => 'behavior.production.label',
                        'low_label_key'                    => 'behavior.production.low_label',
                        'high_label_key'                   => 'behavior.production.high_label',
                        "report_text_key"                  => "behavior.production.report_text",
                        "rating_feedback_question_key"     => "behavior.production.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.production.additional_feedback_question",
                        'quest_behavior_id'                => 62,
                    ],
                ],
            ],
            [
                'name_key'                => 'behavior.group.team_playing.label',
                'quest_behavior_group_id' => 6,
                'behaviors'               => [
                    [
                        'name_key'                         => 'behavior.cooperation.label',
                        'low_label_key'                    => 'behavior.cooperation.low_label',
                        'high_label_key'                   => 'behavior.cooperation.high_label',
                        "report_text_key"                  => "behavior.cooperation.report_text",
                        "rating_feedback_question_key"     => "behavior.cooperation.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.cooperation.additional_feedback_question",
                        'quest_behavior_id'                => 13,
                    ],
                    [
                        'name_key'                         => 'behavior.consensual.label',
                        'low_label_key'                    => 'behavior.consensual.low_label',
                        'high_label_key'                   => 'behavior.consensual.high_label',
                        "report_text_key"                  => "behavior.consensual.report_text",
                        "rating_feedback_question_key"     => "behavior.consensual.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.consensual.additional_feedback_question",
                        'quest_behavior_id'                => 9,
                    ],
                    [
                        'name_key'                         => 'behavior.authority.label',
                        'low_label_key'                    => 'behavior.authority.low_label',
                        'high_label_key'                   => 'behavior.authority.high_label',
                        "report_text_key"                  => "behavior.authority.report_text",
                        "rating_feedback_question_key"     => "behavior.authority.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.authority.additional_feedback_question",
                        'quest_behavior_id'                => 3,
                    ],
                    [
                        'name_key'                         => 'behavior.empathy.label',
                        'low_label_key'                    => 'behavior.empathy.low_label',
                        'high_label_key'                   => 'behavior.empathy.high_label',
                        "report_text_key"                  => "behavior.empathy.report_text",
                        "rating_feedback_question_key"     => "behavior.empathy.rating_feedback_question",
                        "additional_feedback_question_key" => "behavior.empathy.additional_feedback_question",
                        'quest_behavior_id'                => 22,
                    ],
                ],
            ],
        ];

        $order = 0;
        $group_order = 0;

        foreach ($behavior_groups as $behavior_group) {
            $group = \Momentum\BehaviorGroup::updateOrCreate([
                'name_key' => $behavior_group['name_key'],
            ], [
                'quest_behavior_group_id' => $behavior_group['quest_behavior_group_id'],
                'order'                   => $group_order,
            ]);

            foreach ($behavior_group['behaviors'] as $behavior) {
                $data = array_merge($behavior, ['behavior_group_id' => $group->id, 'order' => $order]);
                \Momentum\Behavior::updateOrCreate([
                    'name_key' => $behavior['name_key'],
                ],
                    $data);

                $order++;
            }

            $group_order++;
        }
    }
}
