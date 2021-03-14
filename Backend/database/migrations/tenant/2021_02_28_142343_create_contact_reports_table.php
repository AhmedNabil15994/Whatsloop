<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateContactReportsTable extends Migration
{
    public function up()
    {
        Schema::create('contact_reports', function (Blueprint $table) {
            $table->id();
            $table->string('contact');
            $table->integer('group_id')->nullable();
            $table->integer('group_message_id')->nullable();
            $table->string('message_id')->nullable();
            $table->integer('status');
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_reports');
    }
}
