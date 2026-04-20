<?php

namespace App\Exports;

use App\Cxcobrar;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class CxcExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
            'Estado Cuenta',
            'Tipo Identificación',
            'Número de Identificación',
            'Cliente',  
            'Documento Referencia',
            'Días Promedio',
            'Monto de la Cuenta',
            'Saldo Actual',
            'Total Cuenta Por Cobrar',     
        ];
    }
    public function array(): array
    {
        if ($this->cuenta != 0) {
            $cuenta_cxc = DB::table('mov_cxcobrar')->select('cxcobrar.idcliente', 'cxcobrar.saldo_cuenta as saldo_cxcobrar','cxcobrar.cantidad_dias as dias_cxcobrar', 'clientes.num_id', 'clientes.tipo_id', 'clientes.nombre', 'mov_cxcobrar.*')
            ->join('cxcobrar', 'cxcobrar.idcxcobrar', '=', 'mov_cxcobrar.idcxcobrar')
            ->join('clientes', 'cxcobrar.idcliente', '=', 'clientes.idcliente')
            ->where([
                ['mov_cxcobrar.idcxcobrar', '=', $this->cuenta],
                ['mov_cxcobrar.fecha_mov', '>=', $this->fecha_desde],
                ['mov_cxcobrar.fecha_mov', '<=', $this->fecha_hasta],
                ['cxcobrar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }else{
            $cuenta_cxc = DB::table('mov_cxcobrar')->select('cxcobrar.idcliente', 'cxcobrar.saldo_cuenta as saldo_cxcobrar','cxcobrar.cantidad_dias as dias_cxcobrar', 'clientes.num_id', 'clientes.tipo_id', 'clientes.nombre', 'mov_cxcobrar.*')
            ->join('cxcobrar', 'cxcobrar.idcxcobrar', '=', 'mov_cxcobrar.idcxcobrar')
            ->join('clientes', 'cxcobrar.idcliente', '=', 'clientes.idcliente')
            ->where([
                ['mov_cxcobrar.fecha_mov', '>=', $this->fecha_desde],
                ['mov_cxcobrar.fecha_mov', '<=', $this->fecha_hasta],
                ['cxcobrar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }
       
        $estructura = [];
        foreach ($cuenta_cxc as $cx_c) {
            switch ($cx_c->tipo_id) {
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
            switch ($cx_c->estatus_mov) {
                case '1':
                    $estatus = 'Pendiente';
                break;
                case '2':
                    $estatus = 'Pagada';
                break;
            }
            $datos = array(
                '#' => $cx_c->idmovcxcobrar,
                'estatus' => $estatus,
                'tipo_id' => $identificacion,
                'num_id' => $cx_c->num_id,
                'nombre' => $cx_c->nombre,
                'documento_referencia' => $cx_c->num_documento_mov,
                'dias' => $cx_c->dias_cxcobrar,
                'monto' => $cx_c->monto_mov,
                'saldo' => $cx_c->saldo_pendiente,
                'saldototal' => $cx_c->saldo_cxcobrar,
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}