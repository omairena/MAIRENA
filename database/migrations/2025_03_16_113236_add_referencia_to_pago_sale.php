<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenciaToPagoSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medio_pago_sale', function (Blueprint $table) {
            $table->string('referencia')->nullable()->after('monto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medio_pago_sale', function (Blueprint $table) {
            $table->dropColumn('referencia');
        });
    }
}
