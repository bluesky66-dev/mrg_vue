<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionPlanRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_plan_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly']);
            $table->enum('type', ['review', 'pulse_surveys', 'action_step']);
            $table->timestamp('starts_at')->default('0000-00-00 00:00:00');
            $table->integer('action_plan_id')->unsigned()->index();
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->integer('action_step_id')->unsigned()->index()->nullable();
            $table->foreign('action_step_id')->references('id')->on('action_steps')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_plan_reminders');
    }
}
