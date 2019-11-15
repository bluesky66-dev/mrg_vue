<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePulseSurveyResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pulse_survey_results', function (Blueprint $table) {
            $table->increments('id');
            $table->string('share_code');
            $table->text('additional_comments')->nullable();
            $table->tinyInteger('score')->nullable();
            $table->tinyInteger('reminders_sent')->default(0);
            $table->integer('pulse_survey_id')->unsigned()->index();
            $table->foreign('pulse_survey_id')->references('id')->on('pulse_surveys')->onDelete('cascade');
            $table->integer('observer_id')->unsigned()->index();
            $table->foreign('observer_id')->references('id')->on('observers')->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('pulse_survey_results');
    }
}
