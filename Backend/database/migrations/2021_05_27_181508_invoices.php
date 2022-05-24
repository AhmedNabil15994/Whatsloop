<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Invoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->date('due_date');
            $table->dateTime('paid_date')->nullable();
            $table->double('total');
            $table->text('items');
            $table->integer('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->text('transaction_id')->nullable();
            $table->string('payment_gateaway')->nullable();
            $table->integer('sort');
            $table->integer('main')->default(0);
            $table->integer('status');
            $table->integer('whmcs_order_id')->nullable();
            $table->integer('whmcs_invoice_id')->nullable();
            $table->integer('discount_type')->nullable();
            $table->string('discount_value')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
