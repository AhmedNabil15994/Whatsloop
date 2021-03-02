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
            [
                'var_key' => 'WHATSLOOP_INSTANCEID',
                'var_value' => '1002',
            ],
            [
                'var_key' => 'WHATSLOOP_TOKEN',
                'var_value' => 'a8924830787bd9c55fb58c1ace37f83d',
            ],
            [
                'var_key' => 'SallaURL',
                'var_value' => 'https://api.salla.dev/admin/v2',
            ],
            [
                'var_key' => 'ZidURL',
                'var_value' => 'https://api.zid.sa/v1',
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
        Schema::dropIfExists('variables');
    }
}
