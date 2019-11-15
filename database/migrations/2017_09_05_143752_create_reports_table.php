<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('quest_report_id')->unsigned()->index();
            $table->integer('quest_pqp_id')->unsigned()->index();
            $table->enum('status', ['active', 'inactive', 'deleted']);
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
        Schema::dropIfExists('reports');
    }
}
