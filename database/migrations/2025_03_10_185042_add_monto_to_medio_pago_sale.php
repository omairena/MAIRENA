<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMontoToMedioPagoSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medio_pago_sale', function (Blueprint $table) {
            $table->double('monto', 18, 5)->nullable()->after('sale_id');

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
            $table->dropColumn('monto');
        });
    }
}
