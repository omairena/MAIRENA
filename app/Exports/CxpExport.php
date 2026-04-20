<?php

namespace App\Exports;

use App\Cxpagar;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class CxpExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $fecha_desde;
    protected $fecha_hasta;
    protected $cuenta;

        public function __construct($fecha_desde, $fecha_hasta, $cuenta)
    {
            $this->fecha_desde = $fecha_desde;
            $this->fecha_hasta = $fecha_hasta;
            $this->cuenta = $cuenta;
    }

        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AH1'; // All headers
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
            'Tipo Identificación',
            'Número de Identificación',
            'Proveedor',  
            'Documento Referencia',
            'Días Promedio',
            'Monto de la Cuenta',
            'Saldo Actual',         
        ];
    }
    public function array(): array
    {
        if ($this->cuenta != 0) {
            $cuenta_cxp = DB::table('mov_cxpagar')->select('cxpagar.idcliente', 'cxpagar.saldo_pendiente as saldo_cxpagar','cxpagar.cantidad_dias as dias_cxpagar', 'clientes.num_id', 'clientes.tipo_id', 'clientes.nombre', 'mov_cxpagar.*')
            ->join('cxpagar', 'cxpagar.idcxpagar', '=', 'mov_cxpagar.idcxpagar')
            ->join('clientes', 'cxpagar.idcliente', '=', 'clientes.idcliente')
            ->where([
                ['mov_cxpagar.idcxpagar', '=', $this->cuenta],
                ['mov_cxpagar.fecha_mov', '>=', $this->fecha_desde],
                ['mov_cxpagar.fecha_mov', '<=', $this->fecha_hasta],
                ['cxpagar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }else{
            $cuenta_cxp = DB::table('mov_cxpagar')->select('cxpagar.idcliente', 'cxpagar.saldo_pendiente as saldo_cxpagar','cxpagar.cantidad_dias as dias_cxpagar', 'clientes.num_id', 'clientes.tipo_id', 'clientes.nombre', 'mov_cxpagar.*')
            ->join('cxpagar', 'cxpagar.idcxpagar', '=', 'mov_cxpagar.idcxpagar')
            ->join('clientes', 'cxpagar.idcliente', '=', 'clientes.idcliente')
            ->where([
                ['mov_cxpagar.fecha_mov', '>=', $this->fecha_desde],
                ['mov_cxpagar.fecha_mov', '<=', $this->fecha_hasta],
                ['cxpagar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }
        $estructura = [];
        foreach ($cuenta_cxp as $cx_p) {
            switch ($cx_p->tipo_id) {
                case '01':
                    $identificacion = 'Cédula Física';
                break;
                case '02':
                    $identificacion = 'Cédula Júridica';
                break;
                case '03':
                    $identificacion = 'DIME';
                break;
                case '04':
                    $identificacion = 'NITE';
                break;

            }
            $datos = array(
                '#' => $cx_p->idmovcxpagar,
                'tipo_id' => $identificacion,
                'num_id' => $cx_p->num_id,
                'nombre' => $cx_p->nombre,
                'documento_referencia' => $cx_p->num_documento_mov,
                'dias' => $cx_p->dias_cxpagar,
                'monto' => $cx_p->monto_mov,
                'saldo' => $cx_p->saldo_pendiente,
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}