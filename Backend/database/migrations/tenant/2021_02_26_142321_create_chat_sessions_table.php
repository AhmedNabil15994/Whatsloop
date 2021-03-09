<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateChatSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('chatId');
            $table->integer('langId')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_sessions');
    }
}
