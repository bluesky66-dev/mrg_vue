<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionPlanIdsToActionSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_steps', function (Blueprint $table) {
            $table->integer('action_plan_id')->unsigned()->nullable()->index()->after('report_id');
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->string('temp_action_plan_id')->nullable()->after('report_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action_steps', function (Blueprint $table) {
            $table->dropColumn('action_plan_id');
            $table->dropColumn('temp_action_plan_id');
        });
    }
}
