<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObserversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->integer('culture_id')->unsigned()->index();
            $table->foreign('culture_id')->references('id')->on('cultures')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('observer_type', ['boss', 'peer', 'direct_report', 'other']);
            $table->boolean('disabled')->default(0);
            $table->integer('quest_observer_id')->unsigned()->nullable()->index();
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
        Schema::dropIfExists('observers');
    }
}
