<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_user', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('school_id')->nullable(false)->comment('school id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->unsignedInteger('user_id')->nullable(false)->comment('user id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('is_founder')->nullable(false)->default(false);

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
        Schema::dropIfExists('school_user');
    }
}
