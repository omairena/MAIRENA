<?php

namespace App\Exports;

use App\Bancos;
use App\Tr_bancos;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class BancosExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $fecha_desde;
    protected $fecha_hasta;
    protected $idproducto;
        public function __construct($idproducto, $fecha_desde, $fecha_hasta)
    {
            $this->idproducto = $idproducto;
            $this->fecha_desde = $fecha_desde;
            $this->fecha_hasta = $fecha_hasta;
    }

        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AJ1'; // All headers
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
            '#',   
            'ID CUENTA',  
            'FECHA',
            'MONTO', 
            'REFERENCIA',
            'CLASIFICACION',  
            'FACTURA', 
            'CLIENTE/PROVEEDOR',
            'USUARIO',
           
        ];
    }
    public function array(): array
    {
        if ($this->idproducto != 0) {
            $sales = DB::table('tr_bancos')->select('tr_bancos.*', 'bancos.*')
            ->join('bancos', 'tr_bancos.id_bancos', '=', 'bancos.id_bancos')
            // >join('clientes', 'tr_bancos.idcliente', '=', 'clientes.idcliente')
            ->where([
                ['tr_bancos.id_bancos', '=', $this->idproducto],
                ['tr_bancos.fecha', '>=', $this->fecha_desde],
                ['tr_bancos.fecha', '<=', $this->fecha_hasta],
                
            ])->get();
        }
        $estructura = [];
        $valor_linea = 0;
        foreach ($sales as $sale) {
           
            $cliente = DB::table('clientes')->select('clientes.*')
             ->where([
                ['idcliente', '=', $sale->idcliente],
                
            ])->get();
         // dd($cliente);
            $datos = array(
                '#' => $sale->id_tr_bancos,
                'id_cuenta' => $sale->id_bancos.'-'.$sale->cuenta,
                'fecha' => $sale->fecha,
                'monto' => $sale->signo.''. $sale->monto,
                'referencia' => $sale->referencia,
                'clasificacion' => $sale->clasificacion_recep,
                'factura' => $sale->factura,
                'cliente' => $cliente[0]->nombre,
                'usuario' => $sale->user,
                
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}