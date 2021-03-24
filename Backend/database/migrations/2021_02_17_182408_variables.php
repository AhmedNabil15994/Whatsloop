<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Variables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('var_key');
            $table->text('var_value')->nullable();
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        \DB::table('variables')->insert([
            'var_key' => 'API_KEY',
            'var_value' => 'rokKW1f5J6XGtIgUjP3mHatF4lH2',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'INSTANCES_URL',
            'var_value' => 'https://us-central1-app-chat-api-com.cloudfunctions.net/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variables');
    }
}
