<?php

namespace App\Exports;

use App\Clientes;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class ClientesExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
            'Identificación',  
            'Tipo Identificación',
            'Código Actividad', 
            'Razón Social',  
            'Razón Comercial', 
            'Email',
            'Provincia',
            'Cantón',    
            'Distrito', 
            'Barrio', 
            'Dirección',    
            'Código Pais', 
            'Teléfono',
        ];
    }
    public function array(): array
    {

        $clientes = DB::table('clientes')->select('clientes.*', 'cantones.nombre as cant_name', 'distritos.nombre as dist_name')
        ->join('cantones', 'clientes.canton', '=', 'cantones.idcanton')
        ->join('distritos', 'clientes.distrito', '=', 'distritos.iddistrito')
        ->where([
            ['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        $estructura = [];
        foreach ($clientes as $cli) {
            switch ($cli->tipo_id) {
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
            switch ($cli->condicionventa) {
                case '01':
                    $condicionventa = 'Contado';
                break;
                case '02':
                    $condicionventa = 'Crédito';
                break;
            }
            $datos = array(
                '#' => $cli->idcliente,
                'num_id' => $cli->num_id,
                'tipo_id' => $identificacion,
                'codigo_actividad' => $cli->codigo_actividad,
                'razon_social' => $cli->razon_social,
                'nombre_contribuyente' => $cli->nombre_contribuyente,
                'email' => $cli->email,
                'provincia' => $cli->provincia,
                'canton' => $cli->cant_name,
                'distrito' => $cli->dist_name,
                'barrio' => $cli->barrio,
                'direccion' => $cli->direccion,
                'codigo_pais' => $cli->codigo_pais,
                'telefono' => $cli->telefono,
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }

}