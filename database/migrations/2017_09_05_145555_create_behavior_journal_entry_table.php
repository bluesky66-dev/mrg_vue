<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorJournalEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behavior_journal_entry', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('behavior_id')->unsigned()->index();
            $table->foreign('behavior_id')->references('id')->on('behaviors')->onDelete('cascade');
            $table->integer('journal_entry_id')->unsigned()->index();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('behavior_journal_entry');
    }
}
