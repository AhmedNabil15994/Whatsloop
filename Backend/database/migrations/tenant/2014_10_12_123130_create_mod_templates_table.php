<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mod_templates', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->string('statusText');
            $table->integer('mod_id');
            $table->text('content_ar')->nullable();
            $table->text('content_en')->nullable();
            $table->integer('status')->nullable();
            $table->string('type')->nullable()->default(1);
            $table->string('moderator_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('shipment_policy')->nullable();
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
        Schema::dropIfExists('mod_templates');
    }
}
