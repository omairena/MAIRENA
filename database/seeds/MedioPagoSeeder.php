<?php

use App\MedioPago;
use Illuminate\Database\Seeder;

class MedioPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Definimos los datos de los medios de pago
        $mediosPagos = [
            ['nombre' => 'Efectivo', 'codigo' => '01', 'activo' => true, 'nombre_interno' => 'efectivo'],
            ['nombre' => 'Tarjeta', 'codigo' => '02', 'activo' => true, 'nombre_interno' => 'tarjeta'],
            ['nombre' => 'Cheque', 'codigo' => '03', 'activo' => true, 'nombre_interno' => 'cheque'],
            ['nombre' => 'Transferencia – depósito bancario', 'codigo' => '04', 'activo' => true, 'nombre_interno' => 'transferencia'],
            ['nombre' => 'Sinpe Movil', 'codigo' => '06', 'activo' => true, 'nombre_interno' => 'sinpe_movil'],
            ['nombre' => 'Plataforma Digital', 'codigo' => '07', 'activo' => true, 'nombre_interno' => 'plataforma_digital'],
        ];

        // Insertamos los datos en la tabla
        foreach ($mediosPagos as $medioPago) {
            MedioPago::create($medioPago);
        }
    }
}
