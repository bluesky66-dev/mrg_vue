<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionPlanActionStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_plan_action_step', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_plan_id')->unsigned()->index();
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->integer('action_step_id')->unsigned()->index();
            $table->foreign('action_step_id')->references('id')->on('action_steps')->onDelete('cascade');
            $table->timestamp('due_at')->default('0000-00-00 00:00:00');;
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_plan_action_step');
    }
}
