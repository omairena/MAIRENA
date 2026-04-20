<?php

namespace App\Exports;

use App\Sales;
use DB;
use Auth;
use App\Sales_item;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OpExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
                $cellRange = 'A1:AE1'; // All headers
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
            'Numero Documento',
            'Fecha Documento',
            'Condición Venta',
            'Identificacion Cliente',
            'Nombre Cliente',
            'Documento de Referencia',
            'Tipo Pago',                       
            'Total IVA (CRC)',            
            'Total Comprobante',            
            '¿Enviado a MH?',
           
            '¿Solicito Envio?',
        ];
    }
    public function array(): array
    {

        $sales = DB::table('sales')
        ->select('sales.*', 'clientes.nombre', 'clientes.num_id', 'codigo_actividad.codigo_actividad', 'configuracion.sucursal')
        ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
        ->whereIn('sales.tipo_documento', ["96","95"])
        ->where([
            ['sales.fecha_creada', '>=', $this->fecha_desde],
            ['sales.fecha_creada', '<=', $this->fecha_hasta],
            ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
             ['sales.estatus_op', '=',  0],
              ['sales.tipo_documento', '=',  96],
        ])
        ->get();
        $estructura = [];
        foreach ($sales as $sale) {
            switch ($sale->tipo_documento) {
                case '96':
                    $nombre_doc = 'Fáctura Regimen Simplificado';
                break;
                case '95':
                    $nombre_doc = 'Nota de Crédito Regimen Simplificado';
                break;
            }
            switch ($sale->condicion_venta) {
                case '01':
                    $condicion_venta = 'Contado';
                break;
                case '02':
                    $condicion_venta = 'Crédito';
                break;
            }
            switch ($sale->medio_pago) {
                case '01':
                    $medio_pago ="Efectivo";                                        
                break;
                case '02':
                    $medio_pago = "Tarjeta";
                break;
                case '03':
                    $medio_pago = "Cheque";
                break;
                case '04':
                    $medio_pago = "Transferencia – depósito bancario";
                break;
                case '05':
                    $medio_pago = "Recaudado por terceros";
                break;
            }
            if ($sale->tiene_exoneracion === '01') {
                $tiene_exoneracion = 'Si';
            }else{
                $tiene_exoneracion = 'No';
            }
            
            if ($sale->estatus_op > 0) {
                $convertido = 'Si';
            }else{
                 $convertido = 'No';
            }
            if ($sale->desea_enviarcorreo > 0) {
                $correoe = 'Si';
            }else{
                 $correoe = 'No';
            }
            
            $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
            $imp_0 = 0;
            $imp_2 = 0;
            $imp_3 = 0;
            $imp_4 = 0;
            $imp_5 = 0;
            $imp_6 = 0;
            $imp_7 = 0;
            $imp_13 = 0;
            $imp_99 = 0;

            $impx_0 = 0;
            $impx_2 = 0;
            $impx_3 = 0;
            $impx_4 = 0;
            $impx_5 = 0;
            $impx_6 = 0;
            $impx_7 = 0;
            $impx_13 = 0;
            $impx_99 = 0;

            foreach ($sales_item as $item) {
                switch ($item->tipo_impuesto) {
                    case '01':
                        $imp_0 += $item->valor_neto;
                         $impx_0 += $item->exo_monto;
                    break;
                    case '02':
                        $imp_2 += $item->valor_neto;
                         $impx_2 += $item->exo_monto;
                    break;
                    case '03':
                        $imp_3 += $item->valor_neto;
                         $impx_3 += $item->exo_monto;
                    break;
                     case '04':
                        $imp_4 += $item->valor_neto;
                         $impx_4 += $item->exo_monto;
                    break;
                     case '05':
                        $imp_5 += $item->valor_neto;
                         $impx_5 += $item->exo_monto;
                    break;
                    case '06':
                        $imp_6 += $item->valor_neto;
                         $impx_6 += $item->exo_monto;
                    break;
                    case '07':
                        $imp_7 += $item->valor_neto;
                         $impx_7 += $item->exo_monto;
                    break;
                    case '08':
                        $imp_13 +=  $item->valor_neto;
                         $impx_13 += $item->exo_monto;
                    break;
                    case '99':
                        $imp_99 +=  $item->valor_neto;
                         $impx_99 += $item->exo_monto;
                    break;

                }
            }
            $datos = array(
                '#' => $sale->idsale,
                'tipo_documento' => $nombre_doc,
                'numero_documento' => $sale->numero_documento,
                'fechahora' => $sale->fecha_creada,
                'condicion_venta' => $condicion_venta,
                'num_id_cliente' => $sale->num_id,
                'nombre_cliente' => $sale->nombre,
                'documento_referencia' => $sale->referencia_sale,
                'medio_pago' => $medio_pago,
                //'referencia_pago' => $sale->referencia_pago,
               // 'p_credito' => $sale->p_credito,
               // 'sucursal' => $sale->sucursal,
               // 'codigo_actividad' => $sale->codigo_actividad,
               // 'tipo_cambio' => $sale->tipo_cambio,
                // 'tiene_exoneracion' => $tiene_exoneracion,
                // 'total_descuento' => $sale->total_descuento,
                // 'total_exento' => $imp_0,
                // 'total_red_1' => $imp_2,
                // 'total_red_2' => $imp_3,
                // 'total_red_4' => $imp_4,
                // 'total_trans_0' => $imp_5,
                // 'total_trans_4' => $imp_6,
                // 'total_trans_8' => $imp_7,
                // 'total_gravado' => $imp_13,
                // 'total_no_sujeto' => $imp_99,
                'total_iva' => $sale->total_impuesto,
                // 'total_otros_cargos' => $sale->total_otros_cargos,
                // 'total_iva_devuelto' => $sale->total_iva_devuelto,
                'total_comprobante' => $sale->total_comprobante,
                // 'total_iva_exonerado' => ($impx_0 + $impx_2 + $impx_3 + $impx_4 + $impx_5 + $impx_6 + $impx_7 + $impx_13 + $impx_99),
                 //'tipo_moneda' => $sale->tipo_moneda,
                '¿Enviado a MH?'=> $convertido,
                
                 '¿Solicito Envio?'=>$correoe,
                
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}