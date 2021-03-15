<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->text('body');
            $table->integer('fromMe');
            $table->integer('isForwarded')->nullable();
            $table->string('author');
            $table->string('time');
            $table->string('chatId');
            $table->integer('messageNumber');
            $table->string('type');
            $table->integer('type_id')->nullable();
            $table->string('senderName');
            $table->string('chatName');
            $table->string('quotedMsgBody')->nullable();
            $table->string('quotedMsgId')->nullable();
            $table->string('quotedMsgType')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
