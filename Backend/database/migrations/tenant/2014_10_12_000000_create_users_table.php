<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('global_id')->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('code')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->integer('notifications')->default(0);
            $table->integer('offers')->default(0);
            $table->integer('duration_type')->default(1);
            $table->integer('pin_code')->nullable(0);
            $table->string('emergency_number')->nullable();
            $table->integer('two_auth')->default(1);
            $table->integer('group_id');
            $table->string('domain')->nullable();
            $table->string('company')->nullable();
            $table->text('image')->nullable();
            $table->text('extra_rules')->nullable();
            $table->text('channels')->nullable();
            $table->integer('membership_id')->nullable();
            $table->text('addons')->nullable();
            $table->integer('status')->nullable();
            $table->integer('sort')->nullable();
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
        Schema::dropIfExists('users');
    }
}
