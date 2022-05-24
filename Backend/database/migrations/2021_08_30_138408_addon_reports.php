<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddonReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('tenant_id');
            $table->string('instanceId');
            $table->integer('user_id');
            $table->string('name');
            $table->integer('count');
            $table->date('paid_date')->nullable();
            $table->string('total')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addon_reports');
    }
}
