<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Variables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->string('var_key');
            $table->text('var_value')->nullable();
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        \DB::table('variables')->insert([
            'var_key' => 'API_KEY',
            'var_value' => 'rokKW1f5J6XGtIgUjP3mHatF4lH2',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'INSTANCES_URL',
            'var_value' => 'https://us-central1-app-chat-api-com.cloudfunctions.net/',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'SECRET_KEY',
            'var_value' => 'b46fa9ebae2f7f049e8b4db88e9cfd64',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'SallaURL',
            'var_value' => 'https://api.salla.dev/admin/v2',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'ZidURL',
            'var_value' => 'https://api.zid.sa/v1',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'ZidMerchantToken',
            'var_value' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxMTYiLCJqdGkiOiJiNWQ4M2Y0MTc5Y2I5YWVjMjg3MjEzZjQxNDdlYmNhMmJkN2QyNzMyYzY3YzA2ODRhYzc2MDk3ZTI5MGFhODM1NGQ3MDI5NmFkOTdkYjJlZSIsImlhdCI6MTYzMDIyNjgyOS4wMDEzMjgsIm5iZiI6MTYzMDIyNjgyOS4wMDEzMzEsImV4cCI6MTY2MTc2MjgyOC45ODYwMjcsInN1YiI6IjE0Mjk3MyIsInNjb3BlcyI6WyJ0aGlyZC1wYXJ0aWVzLWFwaXMiXX0.RtFoYZ5aRnH3hVt6N80E9Oo88MlyUf4Psgm0pNylrofvRsAVkZKIdXW-bhqH4C8B9LA1tT3zYebaVMLYZAARU5Ppz1Dvyr_itwAzqjdiIQTURk0YN52_LOU05u1X5Z_2hftBrou0RSwt8ZumSSDYIznLEPy4LndT7UdCpKevA0byYBx6kwtdS8I-sOURpBv89UkyTdcSQzbI_JyralTemsX4UEzEiw9twpkP51qlJOd07uBgIyXV8PXrxVsgbC53xE17cPY-HVxeXfIVZyAXDCjwSY-sH8_okzDL8qc1MzxJJsjuV15fG18w8x5UvTGEJ0aik8qDJBOS_LLMipnx88oWyhlXa2Fys63snFx1TZQQ5t_88J6NQMlbdQt8YB5Czf-fFZt2LPF9VhRB2pn1wge1zglnIb6LNOBb4MK-l1v89Zhz2Okjl6Qz_x3xFS29TZSDeZUCEIr9ibjIg_2jZsyq4i7zuJSHgkjVi3Kl8mmZYULxn039VHbxil6RcMbGjUxT2WJajj_UaY6wyq-pgYOqWIfKlZTYuJEp7usd780fZ8TRyR9gLFwjhWyzxeLeK0LWyYYTL_2bSxrF8tPhJkmJEPRhugK9a-IaOJC7SUVgOO2OKWPbq57KGj_74Mve8fsG_moLRncQTw_y6ZbjGGDgwL61Hr4eRFDGKbPvMKU',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'ONESIGNALAPPID',
            'var_value' => 'c3f0f166-2cc7-4f29-bd07-a735b4559481',
        ]);
        \DB::table('variables')->insert([
            'var_key' => 'ONESIGNALAPPKEY',
            'var_value' => 'ODU0MDQ5NjEtYTZlOS00MThlLTk5YWEtN2Q5NWQ2MjQ3Zjk0',
        ]);

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variables');
    }
}
