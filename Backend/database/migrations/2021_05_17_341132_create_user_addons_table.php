<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addons', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('global_user_id')->nullable();
            $table->integer('user_id');
            $table->integer('addon_id');
            $table->integer('duration_type');
            $table->integer('setting_pushed')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('status')->nullable();
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

            // $table->foreign('user_id')
            //     ->references('id')
            //     ->on('users')
            //     ->onUpdate('cascade')
            //     ->onDelete('cascade');

            // $table->foreign('addon_id')
            //     ->references('id')
            //     ->on('addons')
            //     ->onUpdate('cascade')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addons');
    }
}
