<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldToExoneracion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items_exonerados', function (Blueprint $table) {
            $table->string('articulo')->nullable();
            $table->string('inciso')->nullable();
            $table->string('tipo_exoneracion_otro')->nullable();
            $table->string('institucion_otro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_exonerados', function (Blueprint $table) {
            $table->dropColumn('articulo');
            $table->dropColumn('inciso');
            $table->dropColumn('tipo_exoneracion_otro');
            $table->dropColumn('institucion_otro');
        });
    }
}
