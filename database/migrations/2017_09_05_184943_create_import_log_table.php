<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->text('error')->nullable();
            $table->timestamp('started_at')->default('0000-00-00 00:00:00');;
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('error_at')->nullable();
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
        Schema::dropIfExists('import_logs');
    }
}
