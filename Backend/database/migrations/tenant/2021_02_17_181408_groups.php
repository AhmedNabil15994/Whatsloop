<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Groups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('rules')->nullable();
            $table->integer('sort');
            $table->integer('status');
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        \DB::table('groups')->insert([
            [
                'name_ar' => 'مالك الحساب',
                'name_en' => 'Account Owner',
                'status' => 1,
                'sort' => 1,
            ],
        ]);
        \DB::table('groups')->insert([
            [
                'name_ar' => 'المشرفين',
                'name_en' => 'Mods',
                'status' => 1,
                'sort' => 2,
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
