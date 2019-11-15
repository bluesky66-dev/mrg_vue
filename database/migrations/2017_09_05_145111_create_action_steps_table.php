<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_key')->nullable();
            $table->string('description_key')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('behavior_id')->unsigned()->index();
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
            $table->enum('emphasis', ['more', 'less']);
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('quest_action_step_id')->unsigned()->index();
            $table->integer('report_id')->unsigned()->nullable()->index();
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
        Schema::dropIfExists('action_steps');
    }
}
