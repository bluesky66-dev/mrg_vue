<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('boss_norm')->nullable();
            $table->integer('self_norm')->nullable();
            $table->integer('peer_norm')->nullable();
            $table->integer('direct_report_norm')->nullable();
            $table->enum('boss_agreement', ['low', 'medium', 'high'])->nullable();
            $table->enum('peer_agreement', ['low', 'medium', 'high'])->nullable();
            $table->enum('direct_report_agreement', ['low', 'medium', 'high'])->nullable();
            $table->integer('report_id')->unsigned()->index();
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->integer('behavior_id')->unsigned()->index();
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
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
        Schema::dropIfExists('report_scores');
    }
}
