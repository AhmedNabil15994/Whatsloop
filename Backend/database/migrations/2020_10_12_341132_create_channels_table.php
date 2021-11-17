<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('global_user_id')->nullable();
            $table->string('name');
            $table->string('token');
            $table->string('instanceId');
            $table->string('instanceToken');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('global_user_id')
                ->references('global_id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        \DB::table('channels')->insert([
            'id' => '139624',
            'token' => '72pb2371l07ur177',
            'name' => '139624 - WhatsLoop Demo',
            'instanceId' => 10001,
            'instanceToken' => '72pb2371l07ur177',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d',strtotime('+5 years')),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels');
    }
}
