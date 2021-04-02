<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDialogsTable extends Migration
{
    public function up()
    {
        Schema::create('dialogs', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name')->nullable();
            $table->text('image')->nullable();
            $table->text('metadata')->nullable();
            $table->integer('is_pinned')->default(0);
            $table->integer('is_read')->default(0);
            $table->text('modsArr')->nullable();
            $table->string('last_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dialogs');
    }
}
