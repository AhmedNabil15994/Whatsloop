<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateChatEmpLogsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_emp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('chatId');
            $table->integer('user_id');
            $table->integer('type');
            $table->integer('ended')->default(0);
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_emp_logs');
    }
}
