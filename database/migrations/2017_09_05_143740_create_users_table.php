<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email', 200)->unique();
            $table->integer('culture_id')->unsigned()->index();
            $table->foreign('culture_id')->references('id')->on('cultures')->onDelete('cascade');
            $table->integer('organization_id')->unsigned()->index();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->integer('billing_organization_id')->unsigned()->index();
            $table->foreign('billing_organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->integer('quest_user_id')->unsigned()->index();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('magic_token', 100)->nullable();
            $table->timestamp('token_expires_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
