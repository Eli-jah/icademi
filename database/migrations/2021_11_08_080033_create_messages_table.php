<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('conversation_id')->nullable(false)->comment('conversation id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');

            $table->unsignedInteger('sender_id')->nullable(false);
            $table->string('sender_type')->nullable(false)->default('teacher')->comment('sender type: teacher | student');

            $table->string('content')->nullable(true)->comment('text content');
            $table->string('image')->nullable(true)->comment('image content');

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
        Schema::dropIfExists('messages');
    }
}
