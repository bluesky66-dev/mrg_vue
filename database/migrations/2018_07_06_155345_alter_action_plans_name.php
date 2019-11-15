<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration will allow action plans to have a custom
 * name/label.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class AlterActionPlansName extends Migration
{
    /**
     * Run the migrations.
     * @since 0.2.5
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_plans', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
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
        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
