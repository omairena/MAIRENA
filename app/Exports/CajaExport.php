<?php

namespace App\Exports;

use App\Cajas;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class CajaExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $idcaja;

        public function __construct($idcaja)
    {
            $this->idlogcaja = $idcaja;
    }

        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AK1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'Dia',   
            'Caja',  
            'Fondo de Caja',
            'Ventas de Contado', 
            'Ventas a Credito',  
            'Recibos de Dinero', 
            'Total Efectivo Entrante',
            'Cobros Con Tarjeta',
            'Pagos del Dia',    
            'Total Efectivo en Caja', 
            'Efectivo a Depositar', 
        ];
    }
    public function array(): array
    {

        $logcaja = DB::table('log_cajas')->select('log_cajas.*')
        ->where([
            ['idlogcaja', '=', $this->idlogcaja]
        ])->get();
        $estructura = [];
        foreach ($logcaja as $log) {
            $datos = array(
                'dia' => $log->fecha_cierre_caja,
                'caja' => $log->idcaja,
                'fondo_caja' => $log->fondo_caja,
                'ventas_contado' => $log->ventas_contado,
                'ventas_credito' => $log->ventas_credito,
                'recibo_dinero' => $log->recibo_dinero,
                't_efectivo_entrante' => $log->t_efectivo_entrante,
                'cobro_tarjeta' => $log->cobro_tarjeta,
                'pago_del_dia' => $log->pago_del_dia,
                't_efectivo_caja' => $log->t_efectivo_caja,
                't_efectivo_depositar' => $log->t_efectivo_depositar,
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}