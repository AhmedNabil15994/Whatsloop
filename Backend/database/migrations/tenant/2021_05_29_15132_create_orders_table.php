<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->double('subtotal');
            $table->double('tax');
            $table->double('total');
            $table->string('message_id');
            $table->text('products');
            $table->string('client_id');
            $table->integer('status');
            $table->string('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
