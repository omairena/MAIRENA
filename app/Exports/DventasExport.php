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

class DventasExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
            'Razón Social',
            'Identificación',
            'Correo Electrónico',
            'Teléfono',
            'Total Neto (CRC)',
            'Total Impuesto IVA (CRC)',
           
            'Total Comprobantes (CRC)',
        ];
    }
   public function array(): array  
{  
    $sales = Sales::query()  
            ->select('sales.idcliente', 'sales.idsale', 'clientes.razon_social', 'clientes.num_id', 'clientes.email', 'clientes.telefono')  
            ->selectRaw("SUM(CASE   
                            WHEN sales.tipo_documento IN ('01', '02', '04') THEN sales.total_neto   
                            WHEN sales.tipo_documento = '03' THEN -sales.total_neto   
                            ELSE 0   
                        END) as total_valor")  
            ->selectRaw("SUM(CASE   
                            WHEN sales.tipo_documento IN ('01', '02', '04') THEN sales.total_impuesto   
                            WHEN sales.tipo_documento = '03' THEN -sales.total_impuesto   
                            ELSE 0   
                        END) as total_imp")  
            ->selectRaw("SUM(CASE   
                            WHEN sales.tipo_documento IN ('01', '02', '04') THEN sales.total_comprobante   
                            WHEN sales.tipo_documento = '03' THEN -sales.total_comprobante   
                            ELSE 0   
                        END) as total_comp")  
            ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')  
            ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales') // Join con facelectron  
            ->where([  
                ['sales.fecha_creada', '>=', $this->fecha_desde],  
                ['sales.fecha_creada', '<=', $this->fecha_hasta],  
                ['sales.idconfigfact', '=', Auth::user()->idconfigfact],  
                ['facelectron.estatushacienda', '=', 'aceptado'], // Filtro para estatushacienda  
            ])  
            ->whereNotIn('sales.tipo_documento', ['08']) // Omitir tipo_documento = '08'  
            ->groupBy('sales.idcliente')  
            ->get();  

    $estructura = [];  
    foreach ($sales as $sale) {  
        $datos = array(  
            '#' => $sale->idsale,  
            'nombre_cliente' => $sale->razon_social,  
            'num_id_cliente' => $sale->num_id,  
            'email' => $sale->email,  
            'telefono' => $sale->telefono,  
            'total_gravado' => $sale->total_valor,  
            'total_exento' => $sale->total_imp,  
            'total_comprobante' => $sale->total_comp  
        );  
        array_push($estructura, $datos);  
    }  
    return $estructura;  
} 
}