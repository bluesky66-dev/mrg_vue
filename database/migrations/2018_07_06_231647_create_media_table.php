<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration will create the media table which holds
 * information related to uploaded files, images and other.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('organization_id')->unsigned()->index()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->string('path');
            $table->string('relative');
            $table->string('filename');
            $table->string('mime');
            $table->bigInteger('size');
            $table->string('name')->nullable();
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('media');
    }
}
