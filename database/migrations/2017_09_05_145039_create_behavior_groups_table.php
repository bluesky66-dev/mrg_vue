<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behavior_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_key');
            $table->integer('order')->default(0);
            $table->integer('quest_behavior_group_id')->unsigned()->index();
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
        Schema::dropIfExists('behavior_groups');
    }
}
