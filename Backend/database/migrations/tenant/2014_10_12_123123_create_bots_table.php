<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->integer('message_type');
            $table->text('message');
            $table->integer('reply_type');
            $table->text('reply')->nullable();
            $table->string('file_name')->nullable();
            $table->string('https_url')->nullable();
            $table->string('url_title')->nullable();
            $table->string('url_desc')->nullable();
            $table->string('url_image')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('address')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('templates')->nullable();
            $table->integer('status')->nullable();
            $table->integer('sort')->nullable();
            $table->integer('lang')->default(0);
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bots');
    }
}
