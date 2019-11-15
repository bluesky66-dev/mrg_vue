<?php

use Illuminate\Database\Seeder;
use Momentum\ActionPlan;
use Momentum\Culture;
use Illuminate\Support\Collection;

class FakeDataSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Culture::create([
            'code'             => 'en_PL',
            'english_name'     => 'Pig Latin',
            'quest_culture_id' => 99999,
            'name_key'         => 'global.culture.pig_latin',
        ]);

        Culture::create([
            'code'             => 'en_PS',
            'english_name'     => 'Pirate Speak',
            'quest_culture_id' => 888888,
            'name_key'         => 'global.culture.pirate_speak',
        ]);

        $user = factory(\Momentum\User::class)->create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.com',
            'culture_id' => \Momentum\Culture::where('code', 'en_US')->first()->id,
        ]);

        $this->createDataForUser($user);

        $user = factory(\Momentum\User::class)->create([
            'first_name' => 'igpay',
            'last_name'  => 'atinlay',
            'email'      => 'piglatin@example.com',
            'culture_id' => \Momentum\Culture::where('code', 'en_PL')->first()->id,
        ]);

        $this->createDataForUser($user);

        $user = factory(\Momentum\User::class)->create([
            'first_name' => 'Calico',
            'last_name'  => 'Jack',
            'email'      => 'pirate@example.com',
            'culture_id' => \Momentum\Culture::where('code', 'en_PS')->first()->id,
        ]);

        $this->createDataForUser($user);

        $user = factory(\Momentum\User::class)->create([
            'first_name' => 'No',
            'last_name'  => 'Password',
            'email'      => 'nopassword@example.com',
            'culture_id' => \Momentum\Culture::where('code', 'en_US')->first()->id,
        ]);

        $user->password = null;

        $user->save();

        $this->createDataForUser($user);
    }

    private function createDataForUser(\Momentum\User $user)
    {
        // Create organization goals
        if ($user->organization->goals()->count() === 0)
            event(new \Momentum\Events\Organization\OrganizationCreated($user->organization));

        $report = factory(\Momentum\Report::class)->create([
            'user_id' => $user->id,
        ]);

        $observers = factory(\Momentum\Observer::class, 10)->create([
            'user_id'   => $user->id,
            'report_id' => $report->id,
        ]);

        $report_scores = new \Illuminate\Support\Collection();

        foreach (\Momentum\Behavior::all() as $behavior) {
            $scores = factory(\Momentum\ReportScore::class)->create([
                'behavior_id' => $behavior->id,
                'report_id'   => $report->id,
            ]);
            $report_scores->merge($scores);
        }


        $action_plans = factory(\Momentum\ActionPlan::class, 3)->create([
            'user_id'   => $user->id,
            'report_id' => $report->id,
        ]);

        $complete = $action_plans->first();
        $complete->completed_at = $this->faker->dateTimeBetween($complete->starts_at, $complete->ends_at);
        $complete->save();

        // requery the action plans so we have the freshest data
        $action_plans = ActionPlan::find($action_plans->pluck('id')->toArray());

        foreach ($action_plans as $action_plan) {

            $behaviors = new Collection;
            for ($i = rand(1, 3); $i > 0; --$i) {
                $behaviors[] = \Momentum\ActionPlanBehavior::create([
                    'action_plan_id'    => $action_plan->id,
                    'behavior_id'       => \Momentum\Behavior::all()->random()->id,
                    'emphasis'          => $this->faker->randomElement(['more', 'less']),
                ]);
            }

            foreach ($behaviors as $behavior) {
                $steps = factory(\Momentum\ActionStep::class, rand(1, 3))->create([
                    'behavior_id' => $behavior->behavior_id,
                    'user_id'     => $user->id,
                    'report_id'   => $report->id,
                    'emphasis'    => $behavior->emphasis,
                ]);
                if ($action_plan->isComplete()) {
                    $behavior->action_steps()->attach($steps->pluck('id')->toArray(),
                        [
                            'due_at'       => $this->faker->dateTimeBetween($action_plan->starts_at, $action_plan->ends_at),
                            'completed_at' => $this->faker->dateTimeBetween($action_plan->starts_at, $action_plan->ends_at),
                        ]);
                } else {
                    $behavior->action_steps()->attach($steps->take(2)->pluck('id')->toArray(),
                        ['due_at' => $this->faker->dateTimeBetween($action_plan->starts_at, $action_plan->ends_at)]);

                    $behavior->action_steps()->attach($steps->take(-1)->pluck('id')->toArray(),
                        [
                            'due_at'       => $this->faker->dateTimeBetween($action_plan->starts_at, $action_plan->ends_at),
                            'completed_at' => $this->faker->dateTimeBetween($action_plan->starts_at, $action_plan->ends_at),
                        ]);
                }
            }

            factory(\Momentum\ActionPlanReminder::class, 3)->create([
                'action_plan_id' => $action_plan->id,
                'action_step_id' => \Momentum\ActionStep::all()->random()->id,
            ]);

            $num = $this->faker->numberBetween(1, 6);

            for ($cycle = 1; $cycle <= $num; $cycle++) {
                $pulse_survey = factory(\Momentum\PulseSurvey::class)->create([
                    'user_id'        => $user->id,
                    'action_plan_id' => $action_plan->id,
                    'report_id'      => $report->id,
                    'cycle'          => $cycle,
                ]);

                foreach ($observers as $observer) {
                    factory(\Momentum\PulseSurveyResult::class)->create([
                        'pulse_survey_id' => $pulse_survey->id,
                        'observer_id'     => $observer->id,
                    ]);
                }
            }

            // Goals
            foreach ($user->organization->goals as $goal) {
                \Momentum\ActionGoal::create([
                    'action_plan_id'        => $action_plan->id,
                    'organization_goal_id'  => $goal->id,
                    'answer'                => $this->faker->text(520),
                    'sort'                  => $goal->sort,
                ]);
            }
            for ($i = rand(0, 4); $i > 0; --$i) {
                factory(\Momentum\ActionGoal::class, 1)->create([
                    'action_plan_id'        => $action_plan->id,
                    'custom_question'       => $this->faker->text(500),
                    'answer'                => $this->faker->text(520),
                    'custom_type'           => $this->faker->randomElement(['goal','constituents','benefits','risks','obstacles','resources']),
                    'sort'                  => $i + 6,
                ]);
            }
        }

        $entries = factory(Momentum\JournalEntry::class, 50)->create([
            'user_id'   => $user->id,
            'report_id' => $report->id,
        ]);

        foreach ($entries as $entry) {
            $behaviors = \Momentum\Behavior::all()->take(random_int(1, 10));
            $entry->behaviors()->attach($behaviors->pluck('id')->toArray());
        }

        factory(Momentum\AnalyticsEvent::class, 150)->create([
            'user_id' => $user->id,
        ]);
    }
}
