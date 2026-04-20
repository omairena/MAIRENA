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

class SalesExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
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
                $cellRange = 'A1:AG1'; // All headers
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
            'Estado Documento',
            'Clave',
            'Documento de Referencia',
            'Tipo Pago',
            'Documento Pago',
            'Plazo',
            'Sucursal',
            'Código Actividad',
            'Tipo Cambio (CRC - USD)',
            'Exoneración (S/N)',
            'Total Descuentos (CRC)',
            'Total Excento 0% (CRC)',
            'Total Reducida 0.5% (CRC)',
            'Total Reducida 1% (CRC)',
            'Total Reducida 2% (CRC)',
            'Total Reducida 4% (CRC)',
            'Total Transitorio 0% (CRC)',
            'Total Transitorio 4% (CRC)',
            'Total Transitorio 8% (CRC)',
            'Total Gravado 13% (CRC)',
            'Total No Sujeto (CRC)',
            'Total IVA (CRC)',
            'Total Otros Cargos (CRC)',
            'Total IVA Devuelto (CRC)',
            'Total Comprobante',
            'Total IVA EXONERADO',
            'Moneda',
            'Observaciones',
        ];
    }
    public function array(): array
    {

        $sales = DB::table('sales')->select('sales.*', 'clientes.*', 'facelectron.*', 'codigo_actividad.codigo_actividad', 'configuracion.*')
            ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
            ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
            ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
            ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
            ->where([
                ['fecha_creada', '>=', $this->fecha_desde],
                ['fecha_creada', '<=', $this->fecha_hasta],
                ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
                ['sales.tipo_doc_ref', '!=', 17],
            ])->get();
            $estructura = [];
        foreach ($sales as $sale) {
            switch ($sale->tipo_documento) {
                case '01':
                    $nombre_doc = 'Fáctura Electrónica';
                break;
                case '02':
                    $nombre_doc = 'Nota de Débito Electrónica';
                break;
                case '03':
                    $nombre_doc = 'Nota de Crédito Electrónica';
                break;
                case '04':
                    $nombre_doc = 'Tiquete Electrónico';
                break;
                case '08':
                    $nombre_doc = 'Fáctura Electrónica de Compra';
                break;
                case '09':
                    $nombre_doc = 'Fáctura Electrónica de Exportacion';
                break;
            }
            switch ($sale->condicion_venta) {
                case '01':
                    $condicion_venta = 'Contado';
                break;
                case '02':
                    $condicion_venta = 'Crédito';
                break;
                 case '10':
                     $condicion_venta = 'Venta crédito IVA - 90 días (Art27 LIVA)';
                break;
                case '11':
                     $condicion_venta = 'Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA) ';
                break;
            }
            if(!is_null($sale->medio_pago) && !empty($sale->medio_pago)){
          switch ($sale->medio_pago) {
            case '01':
              $medio_pago = 'Efectivo';
              break;
              case '02':
              $medio_pago = 'Tarjeta';
              break;
              case '03':
              $medio_pago = 'Cheque';
              break;
              case '04':
              $medio_pago = 'Transferencia – depósito bancario';
              break;
              case '05':
               $medio_pago = 'Recaudado por terceros';
              break;
              case '06':
                $medio_pago = 'Sinpe Movil';
               break;
               case '07':
                $medio_pago = 'Plataforma Digital';
               break;
          }
      } else {
      
         $medio_pago = [];  

// Consulta a la base de datos para obtener todos los medios de pago asociados con el sale_id  
$medio_pagos = DB::table('medio_pago_sale')->select('medio_pago_sale.*')  
                ->where('medio_pago_sale.sale_id', '=', $sale->idsale)  
                ->get();  

// Itera sobre los resultados de la consulta  
foreach ($medio_pagos as $medio_pag) {  
    switch ($medio_pag->medio_pago_id) {  
        case '1':  
            $medio_pago[] = 'Efectivo';  
            break;  
        case '2':  
            $medio_pago[] = 'Tarjeta';  
            break;  
        case '3':  
            $medio_pago[] = 'Cheque';  
            break;  
        case '4':  
            $medio_pago[] = 'Transferencia – depósito bancario';  
            break;  
        case '5':  
            $medio_pago[] = 'Recaudado por terceros';  
            break;  
        case '6':  
            $medio_pago[] = 'Sinpe Movil';  
            break;  
        case '7':  
            $medio_pago[] = 'Plataforma Digital';  
            break;  
        default:  
            $medio_pago[] = 'Método de pago desconocido';  
            break;  
    }  
}  

// Unir los nombres en una cadena, si es necesario  
$medio_pago = implode(', ', $medio_pago);  
      }
   //  dd($medio_pago);
            if ($sale->tiene_exoneracion === '01') {
                $tiene_exoneracion = 'Si';
            }else{
                $tiene_exoneracion = 'No';
            }
            $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
            $imp_0 = 0;
            $imp_05 = 0;
            $imp_2 = 0;
            $imp_3 = 0;
            $imp_4 = 0;
            $imp_5 = 0;
            $imp_6 = 0;
            $imp_7 = 0;
            $imp_13 = 0;
            $imp_99 = 0;

            $impx_0 = 0;
            $impx_05 = 0;
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
                    case '10':
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
                     case '09':
                        $imp_05 +=  $item->valor_neto;
                         $impx_05 += $item->exo_monto;
                    break;
                    case '01':  
                    case '11':
                        $imp_99 +=  $item->valor_neto;
                         $impx_99 += $item->exo_monto;
                    break;

                }
            }
            $datos = array(
                '#' => $sale->idsale,
                'tipo_documento' => $nombre_doc,
                'numero_documento' => $sale->numero_documento,
                'fechahora' => $sale->fechahora,
                'condicion_venta' => $condicion_venta,
                'num_id_cliente' => $sale->num_id,
                'nombre_cliente' => $sale->nombre,
                'estatus_doc' => $sale->estatushacienda,
                'clave_doc' => $sale->clave,
                'documento_referencia' => $sale->referencia_sale,
                'medio_pago' => $medio_pago,
                'referencia_pago' => $sale->referencia_pago,
                'p_credito' => $sale->p_credito,
                'sucursal' => $sale->sucursal,
                'codigo_actividad' => $sale->codigo_actividad,
                                'tipo_cambio' => $sale->tipo_cambio,
                'tiene_exoneracion' => $tiene_exoneracion,
                'total_descuento' => $sale->total_descuento,
                'total_exento' => $imp_0,
                'total_red_05' => $imp_05,
                'total_red_1' => $imp_2,
                'total_red_2' => $imp_3,
                'total_red_4' => $imp_4,
                'total_trans_0' => $imp_5,
                'total_trans_4' => $imp_6,
                'total_trans_8' => $imp_7,
                'total_gravado' => $imp_13,
                'total_no_sujeto' => $imp_99,
                'total_iva' => $sale->total_impuesto,
                'total_otros_cargos' => $sale->total_otros_cargos,
                'total_iva_devuelto' => $sale->total_iva_devuelto,
                'total_comprobante' => $sale->total_comprobante,
                'total_iva_exonerado' => ($impx_0 + $impx_2 + $impx_3 + $impx_4 + $impx_5 + $impx_6 + $impx_7 + $impx_13 + $impx_99+$impx_05),
                'tipo_moneda' => $sale->tipo_moneda,
                'Observaciones' => $sale->observaciones,
                
            );
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}