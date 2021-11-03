<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('school_id')->nullable(false)->comment('school id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->unsignedInteger('user_id')->nullable(false)->comment('email sender user id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('email')->nullable(false);
            $table->string('recipient_name')->nullable(true);
            $table->string('random_code')->nullable(false)->unique();

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
        Schema::dropIfExists('invitations');
    }
}
