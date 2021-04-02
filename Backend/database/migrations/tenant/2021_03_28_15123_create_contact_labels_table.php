<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateContactLabelsTable extends Migration
{
    public function up()
    {
        Schema::create('contact_labels', function (Blueprint $table) {
            $table->id();
            $table->string('contact');
            $table->integer('category_id');
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_labels');
    }
}
