<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserTheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_theme', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('theme')->nullable();
            $table->string('width')->nullable();
            $table->string('menus_position')->nullable();
            $table->string('sidebar_color')->nullable();
            $table->string('sidebar_size')->nullable();
            $table->string('user_info')->nullable();
            $table->string('top_bar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_theme');
    }
}
