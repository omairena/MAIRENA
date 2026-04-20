<?php

namespace App\Exports;

use App\Sales;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DcomprasExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $fecha_desde;
    protected $fecha_hasta;

        public function __construct($fecha_desde, $fecha_hasta)
    {
            $this->fecha_desde = $fecha_desde;
            $this->fecha_hasta = $fecha_hasta;
    }
        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AB1'; // All headers
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
            'Empresa',
            'Razón Social',
            'Identificación',
            'Correo Electrónico',
            'Teléfono',
            'Clasificación',
            'Total Gravado (CRC)',
            'Total Exento (CRC)',
            'Total Nota de Crédito (CRC)',
            'Total General (CRC)',
        ];
    }
    public function array(): array
    {
        $sales = Sales::query()
                ->select('sales.idcliente','sales.idsale', 'clientes.razon_social', 'clientes.num_id', 'clientes.email', 'clientes.telefono', 'clientes.nombre')
                ->selectRaw("SUM(sales.total_neto) as total_valor")
                ->selectRaw("SUM(sales.total_exento) as total_ex")
                ->selectRaw("SUM(sales.total_comprobante) as total_comp")
                ->Join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
                ->where([
                ['fecha_creada', '>=', $this->fecha_desde],
                ['fecha_creada', '<=', $this->fecha_hasta],
                ['tipo_documento', '=', '08'],
                ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
                ])
                ->groupBy('sales.idcliente')
                ->get();
            $estructura = [];
        foreach ($sales as $sale) {
            $datos = array(
                '#' => $sale->idsale,
                'empresa' => $sale->nombre,
                'nombre_cliente' => $sale->razon_social,
                'num_id_cliente' => $sale->num_id,
                'email' => $sale->email,
                'telefono' => $sale->telefono,
                'total_gravado' => $sale->total_valor,
                'total_exento' => $sale->total_ex,
                'total_nc' => $sale->total_neto,
                'total_comprobante' => $sale->total_comp
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}