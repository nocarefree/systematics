<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystematicsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connections = config('systematics.connections');

        foreach($connections as $connnect => $value){

            $table_name = ($value['table']['relations']['prefix']?:$value['table_prefix']).$value['table']['relations']['name'];
            Schema::connection($connnect)->create($table_name, function (Blueprint $table) {
                $table->increments('id');
                $table->string('source_id', 60);
                $table->string('target_id', 60);
                $table->integer('type_id', 5);
            });

            $table_name = ($value['table']['types']['prefix']?:$value['table_prefix']).$value['table']['types']['name'];
            Schema::connection($connnect)->create($table_name, function (Blueprint $table) {
                $table->increments('id');
                $table->string('code', 255);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connections = config('systematics.connections');

        foreach($connections as $connnect => $value){
            $table_name1 = ($value['table']['relations']['prefix']?:$value['table_prefix']).$value['table']['relations']['name'];
            $table_name2 = ($value['table']['types']['prefix']?:$value['table_prefix']).$value['table']['types']['name'];
            Schema::connection($connection)->dropIfExists( $table_name1 );
            Schema::connection($connection)->dropIfExists( $table_name2 );
        }
    }
}
