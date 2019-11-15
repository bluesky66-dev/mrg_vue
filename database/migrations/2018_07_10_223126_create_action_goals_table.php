<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration will create action goals table and the
 * relationships between this and other tables.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class CreateActionGoalsTable extends Migration
{
    /**
     * Run the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_goals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned()->index()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->text('question_key')->nullable();
            $table->enum('type', ['goal', 'constituents', 'benefits', 'risks', 'obstacles', 'resources']);
            $table->tinyInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('action_goals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_plan_id')->unsigned()->index();
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->integer('organization_goal_id')->unsigned()->index()->nullable();
            $table->foreign('organization_goal_id')->references('id')->on('organization_goals')->onDelete('cascade');
            $table->text('custom_question')->nullable();
            $table->enum('custom_type', ['goal', 'constituents', 'benefits', 'risks', 'obstacles', 'resources'])->nullable();
            $table->text('answer');
            $table->tinyInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        // Drop goal columns from plan
        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropColumn('goals');
            $table->dropColumn('key_constituents');
            $table->dropColumn('benefits');
            $table->dropColumn('risks');
            $table->dropColumn('obstacles');
            $table->dropColumn('resources');
        });
    }

    /**
     * Reverse the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function down()
    {
        // Restore goal columns from plan
        Schema::table('action_plans', function (Blueprint $table) {
            $table->text('goals')->nullable()->after('name');
            $table->text('key_constituents')->nullable()->after('goals');
            $table->text('benefits')->nullable()->after('key_constituents');
            $table->text('risks')->nullable()->after('benefits');
            $table->text('obstacles')->nullable()->after('risks');
            $table->text('resources')->nullable()->after('obstacles');
        });
        // Drop tables
        Schema::dropIfExists('action_goals');
        Schema::dropIfExists('organization_goals');
    }
}
