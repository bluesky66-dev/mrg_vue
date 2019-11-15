<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration will create a relationship table between media
 * and journal entries.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class CreateJournalEntryMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('journal_entry_id')->unsigned()->index();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->integer('media_id')->unsigned()->index();
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_media');
    }
}
