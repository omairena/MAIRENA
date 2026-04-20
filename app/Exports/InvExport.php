<?php

namespace App\Exports;

use App\Inv;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class InvExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AN1'; // All headers
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
            'Codigo',  
            'Descripcion',
            'Cabys', 
            '% Impuesto',  
            'Costo', 
            '% Utilidad',
            'Precio Sin Imp',
            'Precio Final',    
            'Stock', 
             ];
    }
    public function array(): array
    {

        $clientes = DB::table('productos')
        ->where([
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        $estructura = [];
        foreach ($clientes as $cli) {
               $datos = array(
                '#' => $cli->idproducto,
                'codigo_producto' => $cli->codigo_producto,
                'nombre_producto' => $cli->nombre_producto,
                'codigo_cabys' => $cli->codigo_cabys,
                'porcentaje_imp' => $cli->porcentaje_imp,
                'costo' => $cli->costo,
                'utilidad_producto' => $cli->utilidad_producto,
                'precio_sin_imp' => $cli->precio_sin_imp,
                'precio_final' => $cli->precio_final,
                'cantidad_stock' => $cli->cantidad_stock,
                
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}