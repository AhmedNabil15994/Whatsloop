<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('api_key');
            $table->string('api_value');
            $table->integer('status');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });


        \DB::table('api_keys')->insert([
            [
                'api_key' => 'web',
                'api_value' => 'f450c1e62a74ad454a4a1eb86abe2d2d',
                'status' => 1,
            ],
        ]);
        \DB::table('api_keys')->insert([
            [
                'api_key' => 'android',
                'api_value' => 'a01d1c7f13938203f3fbd26fa8850025',
                'status' => 1,
            ],
        ]);
        \DB::table('api_keys')->insert([
            [
                'api_key' => 'IOS',
                'api_value' => 'bf07179e13b6b0e5fd003ca8f629c914',
                'status' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
}
