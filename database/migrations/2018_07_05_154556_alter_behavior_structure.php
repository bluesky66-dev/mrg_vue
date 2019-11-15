<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration will alter current plans structure
 * in order to support multiple behaviors, and change
 * the steps hierarchy.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class AlterBehaviorStructure extends Migration
{
    /**
     * Run the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function up()
    {
        // Relationship table between plans and behaviors
        Schema::create('action_plan_behaviors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_plan_id')->unsigned()->index();
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->integer('behavior_id')->unsigned()->index();
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
            $table->enum('emphasis', ['more', 'less']);
            $table->timestamps();
            $table->softDeletes();
        });
        // Relationship table between plans and behaviors
        Schema::create('action_plan_behavior_action_step', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_plan_behavior_id')->unsigned()->index();
            $table->foreign('action_plan_behavior_id', 'plan_behavior_steps_behavior_foreign')
                ->references('id')->on('action_plan_behaviors')->onDelete('cascade');
            $table->integer('action_step_id')->unsigned()->index();
            $table->foreign('action_step_id', 'plan_behavior_steps_steps_foreign')
                ->references('id')->on('action_steps')->onDelete('cascade');
            $table->timestamp('due_at')->default('0000-00-00 00:00:00');;
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        // Drop behavior_id column
        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropForeign('action_plans_behavior_id_foreign');
            $table->dropColumn('behavior_id');
            $table->dropColumn('emphasis');
        });
        // Drop action_plan_action_step
        Schema::dropIfExists('action_plan_action_step');
    }

    /**
     * Reverse the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function down()
    {
        // Drop action_plan_behavior and action_plan_behavior_action_step
        Schema::dropIfExists('action_plan_behaviors');
        Schema::dropIfExists('action_plan_behavior_action_step');
        // Restore behavior id column
        Schema::table('action_plans', function (Blueprint $table) {
            $table->integer('behavior_id')->unsigned()->index()->after('user_id');
            $table->enum('emphasis', ['more', 'less'])->after('behavior_id');
        });
        Schema::table('action_plans', function (Blueprint $table) {
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
        });
        // Restore action_plan_action_step
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
}
