<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActionPlansActionSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plans = \Momentum\ActionPlan::all();
        foreach ($plans as $action_plan) {
            foreach ($action_plan->action_steps as $action_step) {
                $action_step->action_plan_id = $action_plan->id;
                $action_step->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
