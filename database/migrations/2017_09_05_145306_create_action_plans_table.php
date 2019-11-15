<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->text('goals')->nullable();
            $table->text('key_constituents')->nullable();
            $table->text('benefits')->nullable();
            $table->text('risks')->nullable();
            $table->text('obstacles')->nullable();
            $table->text('resources')->nullable();
            $table->text('successes')->nullable();
            $table->text('failures')->nullable();
            $table->text('next_focus')->nullable();
            $table->boolean('helpful')->nullable();
            $table->timestamp('starts_at')->default('0000-00-00 00:00:00');
            $table->timestamp('ends_at')->default('0000-00-00 00:00:00');
            $table->timestamp('completed_at')->nullable();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('behavior_id')->unsigned()->index();
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
            $table->enum('emphasis', ['more', 'less']);
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
        Schema::dropIfExists('action_plans');
    }
}
