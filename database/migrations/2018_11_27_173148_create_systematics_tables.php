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

        foreach($connections as $name => $value){

            $table_name = $value['table_prefix'].$value['table_name']['relations'];
            Schema::connection($name)->create($table_name, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('source_id');
                $table->integer('target_id');
                $table->integer('type_id');
                //$table->dropPrimary('type_id');
            });

            $table_name = $value['table_prefix'].$value['table_name']['types'];
            Schema::connection($name)->create($table_name, function (Blueprint $table) {
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

        foreach($connections as $name => $value){
            $table_name = $value['table_prefix'].$value['table_name']['relations'];
            Schema::connection($name)->dropIfExists( $table_name );

            $table_name = $value['table_prefix'].$value['table_name']['types'];
            Schema::connection($name)->dropIfExists( $table_name );
        }
    }
}
