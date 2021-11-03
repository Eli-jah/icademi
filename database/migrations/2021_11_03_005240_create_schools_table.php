<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable(false)->comment('founder user id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('name')->unique();

            $table->timestamp('approved_at')->nullable(true);
            $table->timestamp('rejected_at')->nullable(true);

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
        Schema::dropIfExists('schools');
    }
}
