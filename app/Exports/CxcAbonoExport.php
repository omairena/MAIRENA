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

class CxcAbonoExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
            'Fecha Recibo',
            'Número de Identificación',
            'Cliente',  
            'Documento Abono',
            'Monto Abono',
            '# Documento de Factura',
        ];
    }
    public function array(): array
    {
        if ($this->cuenta != 0) {
            $cuenta_cxc = DB::table('mov_cxcobrar')->select('cxcobrar.idcliente', 'clientes.num_id', 'clientes.nombre', 'mov_cxcobrar.num_documento_mov','mov_cxcobrar.fecha_mov', 'log_cxcobrar.*')
            ->join('cxcobrar', 'cxcobrar.idcxcobrar', '=', 'mov_cxcobrar.idcxcobrar')
            ->join('clientes', 'cxcobrar.idcliente', '=', 'clientes.idcliente')
            ->join('log_cxcobrar', 'log_cxcobrar.idmovcxcobrar', '=', 'mov_cxcobrar.idmovcxcobrar')
            ->where([
                ['mov_cxcobrar.idcxcobrar', '=', $this->cuenta],
                ['log_cxcobrar.fecha_rec_mov', '>=', $this->fecha_desde],
                ['log_cxcobrar.fecha_rec_mov', '<=', $this->fecha_hasta],
                ['cxcobrar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }else{
            $cuenta_cxc = DB::table('mov_cxcobrar')->select('cxcobrar.idcliente', 'clientes.num_id', 'clientes.nombre', 'mov_cxcobrar.num_documento_mov','mov_cxcobrar.fecha_mov', 'log_cxcobrar.*')
            ->join('cxcobrar', 'cxcobrar.idcxcobrar', '=', 'mov_cxcobrar.idcxcobrar')
            ->join('clientes', 'cxcobrar.idcliente', '=', 'clientes.idcliente')
            ->join('log_cxcobrar', 'log_cxcobrar.idmovcxcobrar', '=', 'mov_cxcobrar.idmovcxcobrar')
            ->where([
                ['log_cxcobrar.fecha_rec_mov', '>=', $this->fecha_desde],
                ['log_cxcobrar.fecha_rec_mov', '<=', $this->fecha_hasta],
                ['cxcobrar.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }
       
        $estructura = [];
        foreach ($cuenta_cxc as $cx_c) {
            $datos = array(
                '#' => $cx_c->idlogcxcobrar,
                'fecha_mov_doc' => $cx_c->fecha_rec_mov,
                'num_id' => $cx_c->num_id,
                'nombre' => $cx_c->nombre,
                'documento_abono' => $cx_c->num_recibo_abono,
                'monto_abono' => $cx_c->monto_abono,
                'documento_referencia' => $cx_c->num_documento_mov,
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}