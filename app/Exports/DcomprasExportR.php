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

class DcomprasExportR implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
            'Tipo Documento',  
            'Número Documento Recepción',  
            'Estado del documento',  
            'Detalle Mensaje', 
            'Número Documento Proveedor',
            'Identificación Proveedor',
            'Nombre Proveedor',    
            'Fecha Emisión Documento', 
            'Clave Documento', 
            'Código Actividad',    
            'Total Impuestos', 
            'Total Recepción',
            'Total del Impuesto a Acreditar',  
            'Total del Gasto Aplicable',   
            'Condición de Impuesto',   
            'Clasificación d-151', 
        ];
    }
    public function array(): array
    {
         $sales = DB::table('receptor')->select('receptor.*', 'configuracion.*')
        ->join('configuracion', 'receptor.idconfigfact', '=', 'configuracion.idconfigfact')
        ->where([
                ['fecha', '>=', $this->fecha_desde],
                ['fecha', '<=', $this->fecha_hasta],
                ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        $estructura = [];
        foreach ($sales as $sale) {
            $strContents = file_get_contents($sale->ruta_carga);
            $strDatas = $this->Xml2Array($strContents);
            
            switch ($sale->tipo_documento_recibido) {
                case '01':
                    $nombre_doc = 'Fáctura Electrónica';
                    $documento = 'FacturaElectronica';
                break;
                case '02':
                    $nombre_doc = 'Nota de Débito Electrónica';
                    $documento = 'NotaDebitoElectronica';
                break;
                case '03':
                    $nombre_doc = 'Nota de Crédito Electrónica';
                    $documento = 'NotaCreditoElectronica';
                break;
                case '04':
                    $nombre_doc = 'Tiquete Electrónico';
                break;
            }
            switch ($sale->condicion_impuesto) {
                case '01':
                    $condicion_impuesto = 'Genera crédito IVA';
                break;
                case '02':
                    $condicion_impuesto = 'Genera Crédito parcial del IVA';
                break;
                case '03':
                    $condicion_impuesto = 'Bienes de Capital';
                break;
                case '04':
                    $condicion_impuesto = 'Gasto corriente no genera crédito';
                break;
                case '05':
                    $condicion_impuesto = 'Proporcionalidad';
                break;
            }
            switch ($sale->clasifica_d151) {
                case '1':
                    $clasifica_d151 = 'Compras';
                break;
                case '2':
                    $clasifica_d151 = 'Gastos';
                break;
                case '3':
                    $clasifica_d151 = 'Alquileres';
                break;
                case '4':
                    $clasifica_d151 = 'Servicios Profesionales';
                break;
                case '5':
                    $clasifica_d151 = 'Comisiones';
                break;
                case '6':
                    $clasifica_d151 = 'Intereses';
                break;
                case '7':
                    $clasifica_d151 = 'Otros';
                break;
            }
            if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {
                $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
            }else{
                $total_impuesto = '0.00000';
            }
            $documento_proveedor = substr($strDatas[$documento]['NumeroConsecutivo'], 10, 20);
            $datos = array(
                '#' => $sale->idreceptor,
                'tipo_documento' => $nombre_doc,
                'numero_doc_receptor' => $sale->numero_documento_receptor,
                'estatushacienda' => $sale->estatushacienda,
                'detalle_mensaje' => $sale->detalle_mensaje,
                'documento_proveedor' => $documento_proveedor,
                'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                'fecha_emite_doc' => $sale->fecha,
                'clave_doc' => $sale->clave,
                'codigo_act' => $strDatas[$documento]['CodigoActividad'],
                'total_imp' => $total_impuesto,
                'total_recepcion' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                'total_imp_creditar' => $sale->imp_creditar,
                'total_gasto_ap' => $sale->gasto_aplica,
                'condicion_imp' => $condicion_impuesto,
                'clasificacion' => $clasifica_d151
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}