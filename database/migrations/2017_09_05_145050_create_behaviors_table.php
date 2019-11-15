<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behaviors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_key');
            $table->string('report_text_key');
            $table->string('rating_feedback_question_key');
            $table->string('additional_feedback_question_key');
            $table->string('low_label_key');
            $table->string('high_label_key');
            $table->integer('behavior_group_id')->unsigned()->index();
            $table->foreign('behavior_group_id')->references('id')->on('behavior_groups')->onDelete('cascade');
            $table->integer('quest_behavior_id')->unsigned()->index();
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('behaviors');
    }
}
