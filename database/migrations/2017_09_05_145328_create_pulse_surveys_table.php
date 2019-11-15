<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePulseSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pulse_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->timestamp('due_at')->default('0000-00-00 00:00:00');;
            $table->timestamp('completed_at')->nullable();
            $table->integer('action_plan_id')->unsigned()->index();
            $table->foreign('action_plan_id')->references('id')->on('action_plans')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('cycle')->default('1');
            $table->integer('report_id')->unsigned()->index();
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
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
        Schema::dropIfExists('pulse_surveys');
    }
}
