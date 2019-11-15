<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalyticsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->string('action')->nullable();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->text('data')->nullable();
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('analytics_events');
    }
}
