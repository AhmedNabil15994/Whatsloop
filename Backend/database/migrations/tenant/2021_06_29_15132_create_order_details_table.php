<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('address')->nullable();
            $table->integer('shipping_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->dateTime('transfer_date')->nullable();
            $table->integer('transfer_status')->nullable();
            $table->string('image')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('paymentGateaway')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
