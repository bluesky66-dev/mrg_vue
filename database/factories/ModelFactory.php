<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Momentum\User::class, function (Faker $faker) {
    static $password;

    return [
        'first_name'              => $faker->firstName(),
        'last_name'               => $faker->lastName(),
        'email'                   => $faker->unique()->safeEmail(),
        'password'                => $password ?: $password = bcrypt('secret'),
        'magic_token'             => str_random(60),
        'token_expires_at'        => \Carbon\Carbon::now()->addDays(5),
        'culture_id'              => Momentum\Culture::all()->random()->id,
        'organization_id'         => function () {
            return factory(Momentum\Organization::class)->create()->id;
        },
        'billing_organization_id' => function () {
            return factory(Momentum\Organization::class)->create()->id;
        },
        'quest_user_id'           => $faker->randomNumber(),
    ];
});

$factory->define(Momentum\Organization::class, function (Faker $faker) {
    return [
        'name'                  => $faker->name(),
        'quest_organization_id' => $faker->unique()->randomNumber(5),
    ];
});

$factory->define(Momentum\ActionPlan::class, function (Faker $faker) {
    return [
        'starts_at'        => $faker->dateTimeBetween('-1 years', 'now'),
        'ends_at'          => $faker->dateTimeBetween('now', '+1 years'),
        'user_id'          => function () {
            return factory(Momentum\User::class)->create()->id;
        },
        'report_id'        => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\ActionPlanReminder::class, function (Faker $faker) {
    return [
        'frequency'      => $faker->randomElement(['once', 'daily', 'weekly', 'monthly']),
        'type'           => $faker->randomElement(['review', 'pulse_surveys', 'action_step']),
        'starts_at'      => $faker->dateTimeBetween('-1 years', '+1 years'),
        'action_plan_id' => function () {
            return factory(Momentum\ActionPlan::class)->create()->id;
        },
        'action_step_id' => function () {
            return factory(Momentum\ActionStep::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\ActionStep::class, function (Faker $faker) {
    return [
        'name'                 => $faker->name(),
        'description'          => $faker->text(520),
        'behavior_id'          => \Momentum\Behavior::all()->random()->id,
        'emphasis'             => $faker->randomElement(['more', 'less']),
        'user_id'              => function () {
            return factory(Momentum\User::class)->create()->id;
        },
        'quest_action_step_id' => $faker->randomNumber(),
        'report_id'            => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\JournalEntry::class, function (Faker $faker) {
    return [
        'description' => $faker->paragraph(3),
        'user_id'     => function () {
            return factory(Momentum\User::class)->create()->id;
        },
        'report_id'   => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\Observer::class, function (Faker $faker) {
    return [
        'first_name'        => $faker->firstName(),
        'last_name'         => $faker->lastName(),
        'email'             => $faker->unique()->safeEmail(),
        'culture_id'        => Momentum\Culture::where('enabled', true)->get()->random()->id,
        'quest_observer_id' => $faker->randomNumber(),
        'disabled'          => false,
        'report_id'         => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
        'observer_type'     => $faker->randomElement(['boss', 'peer', 'direct_report']),
        'user_id'           => function () {
            return factory(Momentum\User::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\PulseSurvey::class, function (Faker $faker) {
    return [
        'message'        => $faker->text(520),
        'due_at'         => $faker->dateTimeBetween('-1 years', '+1 years'),
        'completed_at'   => $faker->dateTimeBetween('-1 years', '+1 years'),
        'action_plan_id' => function () {
            return factory(Momentum\ActionPlan::class)->create()->id;
        },
        'user_id'        => function () {
            return factory(Momentum\User::class)->create()->id;
        },
        'cycle'          => 1,
        'report_id'      => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\PulseSurveyResult::class, function (Faker $faker) {
    return [
        'share_code'          => $faker->uuid(),
        'additional_comments' => $faker->text(520),
        'score'               => $faker->numberBetween(1, 7),
        'reminders_sent'      => $faker->numberBetween(1, 4),
        'pulse_survey_id'     => function () {
            return factory(Momentum\PulseSurvey::class)->create()->id;
        },
        'observer_id'         => function () {
            return factory(Momentum\Observer::class)->create()->id;
        },
        'completed_at'        => $faker->dateTimeBetween('-1 years', '+1 years'),
    ];
});

$factory->define(Momentum\Report::class, function (Faker $faker) {
    return [
        'file'            => $faker->name . '.file',
        'user_id'         => function () {
            return factory(Momentum\User::class)->create()->id;
        },
        'quest_report_id' => $faker->randomNumber(),
        'quest_pqp_id'    => $faker->randomNumber(),
        'status'          => 'active',
    ];
});

$factory->define(Momentum\ReportScore::class, function (Faker $faker) {
    return [
        'boss_norm'               => $faker->numberBetween(1, 100),
        'self_norm'               => $faker->numberBetween(1, 100),
        'peer_norm'               => $faker->numberBetween(1, 100),
        'direct_report_norm'      => $faker->numberBetween(1, 100),
        'boss_agreement'          => $faker->randomElement(['Low', 'Medium', 'High']),
        'peer_agreement'          => $faker->randomElement(['Low', 'Medium', 'High']),
        'direct_report_agreement' => $faker->randomElement(['Low', 'Medium', 'High']),
        'report_id'               => function () {
            return factory(Momentum\Report::class)->create()->id;
        },
        'behavior_id'             => \Momentum\Behavior::all()->random()->id,
    ];
});

$factory->define(Momentum\AnalyticsEvent::class, function (Faker $faker) {
    return [
        'category' => $faker->randomElement([
            'get_help',
            'pulse_survey',
            'action_plan',
            '360_results',
            'resource_guide',
            'observer',
        ]),
        'action'   => $faker->randomElement([
            'view',
            'send',
            'resend',
            'share',
            'add',
            'edit',
            'delete',
        ]),
        'label'    => $faker->word,
        'user_id'  => function () {
            return factory(Momentum\User::class)->create()->id;
        },
    ];
});

$factory->define(Momentum\ActionGoal::class, function (Faker $faker) {
    return [
        'custom_question'   => $faker->text(520), 
        'custom_type'       => $faker->randomElement(['goal','constituents','benefits','risks','obstacles','resources']),
        'action_plan_id'    => function () {
            return factory(Momentum\ActionPlan::class)->create()->id;
        },
        'answer'            => $faker->text(520),
        'sort'              => rand(0, 10),
    ];
});