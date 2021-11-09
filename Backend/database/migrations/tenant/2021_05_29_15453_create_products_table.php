<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('name');
            $table->string('currency');
            $table->double('price');
            $table->integer('category_id')->nullable();
            $table->text('images');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
