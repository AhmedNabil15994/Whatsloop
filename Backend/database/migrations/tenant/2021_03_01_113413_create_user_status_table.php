<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUserStatusTable extends Migration
{
    public function up()
    {
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_status');
    }
}
