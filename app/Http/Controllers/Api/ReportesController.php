<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Receptor;
use App\Sales;
use App\Sales_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Artisan;
use Log; 
class ReportesController extends Controller
{
public function ejecutarReceptor()  
    {  
        try {  
            Artisan::call('aby:receptor'); // Llama al comando Artisan  
            return redirect()->back()->with('success', 'Comando ejecutado correctamente.');  
        } catch (\Exception $e) {  
            return redirect()->back()->with('error', 'Error al ejecutar el comando: ' . $e->getMessage());  
        }  
    }  
    
       public function salesColonizado() : void
    {
        $fecha_desde = '2025-05-01';
        $fecha_hasta = now()->format('Y-m-d');
        $sales = DB::table('sales')->select('sales.*', 'clientes.*', 'facelectron.*', 'codigo_actividad.codigo_actividad', 'configuracion.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
        ->where([
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta],
            ['facelectron.estatushacienda', '=', 'aceptado'],
            ['sales.tipo_doc_ref' ,'!=', 17],
            ['sales.tipo_documento' ,'!=', '08'],
            ['sales.api_aby', '=', 0],
        ])
         ->limit(100)
         ->get();
        $estructura = [];
        foreach ($sales as $sale) {
            switch ($sale->tipo_documento) {
                case '01':
                    $nombre_doc = 'FACTURA';
                break;
                case '02':
                    $nombre_doc = 'FACTURA';
                break;
                case '03':
                    $nombre_doc = 'NC';
                break;
                case '04':
                    $nombre_doc = 'FACTURA';
                break;
                case '08':
                    $nombre_doc = 'Fáctura Electrónica de Compra';
                break;
                case '09':
                    $nombre_doc = 'FACTURA';
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

                // Consulta a la base de datos para obtener tod los medios de pago asociados con el sale_id
                $medio_pagos = DB::table('medio_pago_sale')->select('medio_pago_sale.*')
                ->where('medio_pago_sale.sale_id', '=', $sale->idsale)->get();

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
            if ($sale->tiene_exoneracion === '01') {
                $tiene_exoneracion = 'Si';
            }else{
                $tiene_exoneracion = 'No';
            }
            $descuentousd = 0;
            $total_impusd = 0;
            $total_otros_cargosusd = 0;
            $total_iva_devusd = 0;
            $total_comprobanteusd = 0;
            $descuento = 0;
            $total_imp = 0;
            $total_otros_cargos = 0;
            $total_iva_dev = 0;
            $total_comprobante = 0;
            $imp_0usd = 0;
            $imp_05usd = 0;
            $imp_2usd = 0;
            $imp_3usd = 0;
            $imp_4usd = 0;
            $imp_5usd = 0;
            $imp_6usd = 0;
            $imp_7usd = 0;
            $imp_13usd = 0;
            $imp_99usd = 0;

            $impx_0usd = 0;
            $impx_05usd = 0;
            $impx_2usd = 0;
            $impx_3usd = 0;
            $impx_4usd = 0;
            $impx_5usd = 0;
            $impx_6usd = 0;
            $impx_7usd = 0;
            $impx_13usd = 0;
            $impx_99usd = 0;
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

            if($sale->tipo_documento == '03'){
                if($sale->tipo_moneda != 'CRC'){

                    $sales_item            = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuentousd          = -$sale->total_descuento * $sale->tipo_cambio;
                    $total_impusd          = -$sale->total_impuesto * $sale->tipo_cambio;
                    $total_otros_cargosusd = -$sale->total_otros_cargos * $sale->tipo_cambio;
                    $total_iva_devusd      = -$sale->total_iva_devuelto * $sale->tipo_cambio;
                    $total_comprobanteusd  = -$sale->total_comprobante * $sale->tipo_cambio;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_0usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '02':
                                $imp_2usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_2usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '03':
                                $imp_3usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_3usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '04':
                                $imp_4usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_4usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '05':
                                $imp_5usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_5usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '06':
                                $imp_6usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_6usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '07':
                                $imp_7usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_7usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '08':
                                $imp_13usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_13usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '09':
                                $imp_05usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_05usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '01':
                            case '11':
                                $imp_99usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_99usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;

                        }
                    }
                }else{
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuento = -$sale->total_descuento;
                    $total_imp = -$sale->total_impuesto;
                    $total_otros_cargos= -$sale->total_otros_cargos;
                    $total_iva_dev = -$sale->total_iva_devuelto;
                    $total_comprobante= -$sale->total_comprobante;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0 += -$item->valor_neto;
                                $impx_0 += -$item->exo_monto;
                            break;
                            case '02':
                                $imp_2 += -$item->valor_neto;
                                $impx_2 += -$item->exo_monto;
                            break;
                            case '03':
                                $imp_3 += -$item->valor_neto;
                                $impx_3 += -$item->exo_monto;
                            break;
                            case '04':
                                $imp_4 += -$item->valor_neto;
                                $impx_4 += -$item->exo_monto;
                            break;
                            case '05':
                                $imp_5 += -$item->valor_neto;
                                $impx_5 += -$item->exo_monto;
                            break;
                            case '06':
                                $imp_6 += -$item->valor_neto;
                                $impx_6 += -$item->exo_monto;
                            break;
                            case '07':
                                $imp_7 += -$item->valor_neto;
                                $impx_7 += -$item->exo_monto;
                            break;
                            case '08':
                                $imp_13 +=  -$item->valor_neto;
                                $impx_13 += -$item->exo_monto;
                            break;
                            case '09':
                                $imp_05 +=  -$item->valor_neto;
                                $impx_05 += -$item->exo_monto;
                            break;
                            case '01':
                            case '11':
                                $imp_99 +=  -$item->valor_neto;
                                $impx_99 += -$item->exo_monto;
                            break;
                        }
                    }
                }
            }else{
                if($sale->tipo_moneda != 'CRC'){
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuentousd = $sale->total_descuento * $sale->tipo_cambio;
                    $total_impusd = $sale->total_impuesto * $sale->tipo_cambio;
                    $total_otros_cargosusd = $sale->total_otros_cargos * $sale->tipo_cambio;
                    $total_iva_devusd = $sale->total_iva_devuelto * $sale->tipo_cambio;
                    $total_comprobanteusd = $sale->total_comprobante * $sale->tipo_cambio;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_0usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '02':
                                $imp_2usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_2usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '03':
                                $imp_3usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_3usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '04':
                                $imp_4usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_4usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '05':
                                $imp_5usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_5usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '06':
                                $imp_6usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_6usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '07':
                                $imp_7usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_7usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '08':
                                $imp_13usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_13usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '09':
                                $imp_05usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_05usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '01':
                            case '11':
                                $imp_99usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_99usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                        }
                    }
                }else{
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuento = $sale->total_descuento;
                    $total_imp = $sale->total_impuesto;
                    $total_otros_cargos= $sale->total_otros_cargos;
                    $total_iva_dev = $sale->total_iva_devuelto;
                    $total_comprobante= $sale->total_comprobante;


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
                }
            }
            $datos = array(
                'idsale'            => $sale->idsale,
                'idconfigfact'      => $sale->numero_id_emisor,
                'tipo_documento'    => $nombre_doc,
                'numero_documento'  => (string ) $sale->numero_documento,
                'fechahora'         => $sale->fechahora,
                'condicion_venta'   => $condicion_venta,
                'num_id_cliente'    => (string) $sale->num_id,
                'nombre_cliente'    => $sale->nombre,
                'estatus_doc'       => $sale->estatushacienda,
                'clave_doc'         => $sale->clave,
                'documento_referencia' => $sale->referencia_sale,
                'medio_pago'        => $medio_pago,
                'referencia_pago'   => $sale->referencia_pago,
                'p_credito'         => (string) $sale->p_credito,
                'sucursal'          => $sale->sucursal,
                'codigo_actividad'  => $sale->codigo_actividad,
                'tipo_moneda'       => $sale->tipo_moneda,
                'tipo_cambio'       => (string) $sale->tipo_cambio,
                'tiene_exoneracion' => $tiene_exoneracion,
                'total_descuento'   => (string) $descuentousd + $descuento,
                'total_exento'      => (string) $imp_0 + $imp_0usd,
                'total_red_05'      => (string) $imp_05 + $imp_05usd,
                'total_red_1'       => (string) $imp_2 + $imp_2usd,
                'total_red_2'       => (string) $imp_3 + $imp_3usd,
                'total_red_4'       => (string) $imp_4 + $imp_4usd,
                'total_trans_0'     => (string) $imp_5 + $imp_5usd,
                'total_trans_4'     => (string) $imp_6 + $imp_6usd,
                'total_trans_8'     => (string) $imp_7 + $imp_7usd,
                'total_gravado'     => (string) $imp_13 + $imp_13usd,
                'total_no_sujeto'   => (string) $imp_99 + $imp_99usd,
                'total_iva'         => (string) $total_impusd + $total_imp,
                'total_otros_cargos'=> (string) $total_otros_cargosusd + $total_otros_cargos,
                'total_iva_devuelto'=> (string) $total_iva_devusd + $total_iva_dev,
                'total_comprobante' => (string) $total_comprobanteusd + $total_comprobante,
                'total_iva_exonerado' => (string)$impx_0 + $impx_2 + $impx_3 + $impx_4 + $impx_5 + $impx_6 + $impx_7 + $impx_13 + $impx_99 + $impx_05 + $impx_0usd + $impx_2usd + $impx_3usd + $impx_4usd + $impx_5usd + $impx_6usd + $impx_7usd + $impx_13usd + $impx_99usd + $impx_05usd,
                'Observaciones'     => $sale->observaciones,
            );
            array_push($estructura, $datos);
            $sale_aby = Sales::find($sale->idsale);
            $sale_aby->update(['api_aby' => 1]);
        }
        DB::connection('external_mysql')->table('reporte_ventas')->insert($estructura);
    }

  
    public function receptorAby() : void
    {
        $fecha_desde = '2025-04-01';
        $fecha_hasta = now()->format('Y-m-d');
        $sales = DB::table('receptor')->select('receptor.*', 'configuracion.*')
        ->join('configuracion', 'receptor.idconfigfact', '=', 'configuracion.idconfigfact')
        ->where([
                ['receptor.fecha_xml_envio', '>=', $fecha_desde],
                ['receptor.fecha_xml_envio', '<=', $fecha_hasta],
                ['receptor.estatus_hacienda', '=', 'aceptado'],
                ['receptor.api_aby', '=', 0],
            ])
            ->limit(100)
            ->get();
        $estructura = [];

        foreach ($sales as $sale) {
            $imp_0 = 0;
            $imp_05 = 0;
            $imp_2 = 0;
            $imp_3 = 0;
            $imp_4 = 0;
            $imp_5 = 0;
            $imp_6 = 0;
            $imp_7 = 0;
            $imp_13 = 0;
            $imp_otros = 0;
            $moneda = 0;
            $no_sujeto=0;
            $no_sujetos=0;
            $iva_devuelto = 0;
            $tc=0;
            $otros_cargos=0;
            $mto_imp_exonerado=0;
            $detalle='';
            $version=$sale->version;
            if($sale->version === '4.4'){
                $codigo_tarifa='CodigoTarifaIVA';
            }else{
                $codigo_tarifa='CodigoTarifa';
            }
            
            $basePath = '/home/okgmfvzr/public_html/sistema/public';
            $pathRelative = ltrim($sale->ruta_carga, '.');
            $fullPath = $basePath . $pathRelative;
            $strContents = file_get_contents($fullPath);
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
               // case '04':
                 //   $nombre_doc = 'Tiquete Electrónico';
                //break;
            }

            $n_recep=$strDatas[$documento]['Receptor']['Nombre'];
            if (isset($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'])) {
                $moneda=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'];
                $tc = floatval($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio']);
            }

            if(isset($strDatas[$documento]['Receptor']['Identificacion']['Numero'])){
                $id_recep=$strDatas[$documento]['Receptor']['Identificacion']['Numero'];
            }else{
                $id_recep=$strDatas[$documento]['Receptor']['IdentificacionExtranjero'];
            }

            if (isset($strDatas[$documento]['ResumenFactura']['TotalComprobante'])) {   ///cambie total impuesto x total de comprobante, pues hay docs que no traen el nodo de total impuesto
                if (isset($strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'])) {
                    $otros_cargos =$strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'];
                }else{
                    $otros_cargos=0;
                }
                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'])) {// puede existir otros cargos entonces se valida.

                    if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'])) { //si no existe nodo de imp, entra aqui (22-02-2024 se cambio la validacion de Impuesto a SubTotal)

                        for ($i=0; $i < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']); $i++) {
                            $detalle .= $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Detalle'] . ' <> '; // Concatenar con un espacio

                            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'])) {
                                if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'])) {

                                    for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']); $im++) {

                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa])) {
                                            switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa]) {
                                                case '10':
                                                    if ($version == '4.4') {
                                                        $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                    }
                                                break;
                                                case '11':
                                                    if ($version == '4.4') {
                                                        $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                    }
                                                break;
                                                case '01':
                                                    if ($version != '4.4') {
                                                        $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                    }else{
                                                        $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                    }
                                                break;
                                                case '02':
                                                    $imp_2 +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '03':
                                                    $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '04':
                                                    $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '05':
                                                    $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '06':
                                                    $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '07':
                                                    $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '08':
                                                    $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                                case '09':
                                                    $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                break;
                                            }
                                            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion'])) {
                                                $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];
                                            }

                                        } else {
                                            $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];
                                        }
                                    }
                                } else {

                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa])) {
                                        switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa]) {
                                            case '10':
                                                if ($version == '4.4') {
                                                    $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                }
                                            break;
                                            case '11':
                                                if ($version == '4.4') {
                                                    $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                }
                                            break;
                                            case '01':
                                                if ($version != '4.4') {
                                                    $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                }else{
                                                    $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                }
                                            break;
                                            case '02':
                                                $imp_2 +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '03':
                                                $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '04':
                                                $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '05':
                                                $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '06':
                                                $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '07':
                                                $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '08':
                                                // dd(  $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']);
                                                $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '09':
                                                $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                        }
                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion'])) {
                                            $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                    } else {
                                        $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];//se agrego [$im] 21-02-2024
                                    }
                                }
                            }else{
                                $no_sujeto +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                            }
                        }
                        $detalle = trim($detalle);
                    } else {

                        if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {
                            $no_sujeto +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                        } else{
                            $no_sujeto=0;
                        }
                        $detalle=$strDatas[$documento]['DetalleServicio']['LineaDetalle']['Detalle'];
                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {
                            if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'])) {
                                for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']); $im++) {
                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa])) {
                                        switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa]) {
                                            case '10':
                                                if ($version == '4.4') {
                                                    $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                }
                                            break;
                                            case '11':
                                                if ($version == '4.4') {
                                                    $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                }
                                            break;
                                            case '01':
                                                if ($version != '4.4') {
                                                    $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                }else{
                                                    $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                }
                                            break;
                                            case '02':
                                                $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '03':
                                                $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '04':
                                                $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '05':
                                                $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '06':
                                                $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '07':
                                                $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                            case '08':
                                                $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;

                                            case '09':
                                                $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            break;
                                        }
                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion'])) {
                                            $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                    } else {

                                        $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                                    }
                                }
                            } else {
                                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa])) {
                                    switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa]) {
                                        case '10':
                                            if ($version == '4.4') {
                                                $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            }
                                        break;
                                        case '11':
                                            if ($version == '4.4') {
                                                $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            }
                                        break;
                                        case '01':
                                            if ($version != '4.4') {
                                                $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            }else{
                                                $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                            }
                                        break;
                                        case '02':
                                            $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '03':
                                            $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '04':
                                            $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '05':
                                            $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '06':
                                            $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '07':
                                            $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '08':
                                            $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;
                                        case '09':
                                            $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                        break;


                                    }
                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion'])) {
                                        $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];
                                    }
                                } else {

                                    $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];
                                }
                            }
                        }else{
                            //$imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                        }
                    }

                }

                switch ($sale->condicion_impuesto) {
                    case '0':
                        $condicion_impuesto = 'Sin Condicion';
                        if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {
                            $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                        }else{
                            $total_impuesto = '0.00000';
                        }
                    break;
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
                    case '8':
                        $clasifica_d151 = 'Otros Gastos';
                    break;
                }

                if (isset($strDatas[$documento]['InformacionReferencia']['Numero'])) {
                $documento_referencia = $strDatas[$documento]['InformacionReferencia']['Numero'];

                }else{
                    $documento_referencia ='0';
                }
                $documento_proveedor = substr($strDatas[$documento]['NumeroConsecutivo'], 10, 20);

                if(is_null($no_sujeto)){
                    if (isset($strDatas[$documento]['ResumenFactura']['TotalNoSujeto'])) {
                        $no_sujeto = $strDatas[$documento]['ResumenFactura']['TotalNoSujeto'];
                    }
                }
                if (isset($strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'])) {
                        $iva_devuelto = $strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'];
                    }
                
                if ($moneda == 'CRC') {
                    if ($sale->tipo_documento_recibido == '03') {
                        $datos = array(
                            'idreceptor'          => $sale->idreceptor,
                            'idconfigfact'        => $id_recep,
                            'tipo_documento'      => $nombre_doc,
                            'numero_doc_receptor' => (string) $sale->numero_documento_receptor,
                            'estatus_hacienda'    => $sale->estatus_hacienda,
                            'detalle_mensaje'     => $sale->detalle_mensaje,
                            'documento_proveedor' => $documento_proveedor,
                            'cedula_emisor'       => (string) $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                            'nombre_emisor'       => $strDatas[$documento]['Emisor']['Nombre'],
                            'fecha_recepcion_doc' => $sale->fecha,
                            'fecha_emite_doc'     => $sale->fecha_xml_envio,
                            'clave_doc'           => $sale->clave,
                            'codigo_act'          => $sale->codigo_act_xml,
                            'no_sujeto'           => (string) -$no_sujetos-$no_sujeto,
                            'total_exento'        => (string) -$imp_0,
                            'total_red_05'        => (string) -$imp_05,
                            'total_red_1'         => (string) -$imp_2,
                            'total_red_2'         => (string) -$imp_3,
                            'total_red_4'         => (string) -$imp_4,
                            'total_trans_0'       => (string) -$imp_5,
                            'total_trans_4'       => (string) -$imp_6,
                            'total_trans_8'       => (string) -$imp_7,
                            'total_gravado'       => (string) -$imp_13,
                            'total_imp'           => (string) -$sale->total_impuesto,
                            'total_imp_exonerados'=> (string) $mto_imp_exonerado,
                            'total_otros'         => (string) -$imp_otros,
                            'otros_cargos'        => (string) -$otros_cargos,
                            'total_recepcion'     => (string) -$sale->total_comprobante,
                            'total_imp_creditar'  => (string) -$sale->imp_creditar,
                            'total_gasto_ap'      => (string) -$sale->gasto_aplica,
                            'condicion_imp'       => $condicion_impuesto,
                            'clasificacion'       => $clasifica_d151,
                            'Id_Receptor'         => (string) $id_recep,
                            'Nombre_Receptor'     => $n_recep,
                            'Moneda'              => $moneda,
                            'TC'                  => (string) $tc,
                            'Doc_Referencia'      => $documento_referencia,
                            'Detalle'             => $detalle,
                            'Version'             => $sale->version,
                            'iva_devuelto'        => (string) -$iva_devuelto, 
                        );
                    } else {
                        $datos = array(
                            'idreceptor'          => $sale->idreceptor,
                            'idconfigfact'        => $id_recep,
                            'tipo_documento'      => $nombre_doc,
                            'numero_doc_receptor' => (string) $sale->numero_documento_receptor,
                            'estatus_hacienda'    => $sale->estatus_hacienda,
                            'detalle_mensaje'     => $sale->detalle_mensaje,
                            'documento_proveedor' => $documento_proveedor,
                            'cedula_emisor'       => (string) $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                            'nombre_emisor'       => $strDatas[$documento]['Emisor']['Nombre'],
                            'fecha_recepcion_doc' => $sale->fecha,
                            'fecha_emite_doc'     => $sale->fecha_xml_envio,
                            'clave_doc'           => $sale->clave,
                            'codigo_act'          => $sale->codigo_act_xml,
                            'no_sujeto'           => (string) $no_sujetos + $no_sujeto,
                            'total_exento'        => (string) $imp_0,
                            'total_red_05'        => (string) $imp_05,
                            'total_red_1'         => (string) $imp_2,
                            'total_red_2'         => (string) $imp_3,
                            'total_red_4'         => (string) $imp_4,
                            'total_trans_0'       => (string) $imp_5,
                            'total_trans_4'       => (string) $imp_6,
                            'total_trans_8'       => (string) $imp_7,
                            'total_gravado'       => (string) $imp_13,
                            'total_imp'           => (string) $sale->total_impuesto,
                            'total_imp_exonerados'=> (string) $mto_imp_exonerado,
                            'total_otros'         => (string) $imp_otros,
                            'otros_cargos'        => (string) $otros_cargos,
                            'total_recepcion'     => (string) $sale->total_comprobante,
                            'total_imp_creditar'  => (string) $sale->imp_creditar,
                            'total_gasto_ap'      => (string) $sale->gasto_aplica,
                            'condicion_imp'       => $condicion_impuesto,
                            'clasificacion'       => $clasifica_d151,
                            'Id_Receptor'         => (string) $id_recep,
                            'Nombre_Receptor'     => $n_recep,
                            'Moneda'              => $moneda,
                            'TC'                  => (string) $tc,
                            'Doc_Referencia'      => $documento_referencia,
                            'Detalle'             => $detalle,
                            'Version'             => $sale->version,
                            'iva_devuelto'        => (string) $iva_devuelto, 
                        );
                    }
                } else {
                    if ($sale->tipo_documento_recibido == '03') {
                        $datos = array(
                            'idreceptor'          => $sale->idreceptor,
                            'idconfigfact'        => $id_recep,
                            'tipo_documento'      => $nombre_doc,
                            'numero_doc_receptor' => (string) $sale->numero_documento_receptor,
                            'estatus_hacienda'    => $sale->estatus_hacienda,
                            'detalle_mensaje'     => $sale->detalle_mensaje,
                            'documento_proveedor' => $documento_proveedor,
                            'cedula_emisor'       => (string) $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                            'nombre_emisor'       => $strDatas[$documento]['Emisor']['Nombre'],
                            'fecha_recepcion_doc' => $sale->fecha,
                            'fecha_emite_doc'     => $sale->fecha_xml_envio,
                            'clave_doc'           => $sale->clave,
                            'codigo_act'          => $sale->codigo_act_xml,
                            'no_sujeto'           => (string) (-$no_sujetos-$no_sujeto) * $tc,
                            'total_exento'        => (string) -$imp_0 * $tc,
                            'total_red_05'        => (string) -$imp_05 * $tc,
                            'total_red_1'         => (string) -$imp_2 * $tc,
                            'total_red_2'         => (string) -$imp_3 * $tc,
                            'total_red_4'         => (string) -$imp_4 * $tc,
                            'total_trans_0'       => (string) -$imp_5 * $tc,
                            'total_trans_4'       => (string) -$imp_6 * $tc,
                            'total_trans_8'       => (string) -$imp_7 * $tc,
                            'total_gravado'       => (string) -$imp_13 * $tc,
                            'total_imp'           => (string) -$sale->total_impuesto * $tc,
                            'total_imp_exonerados'=> (string) -$mto_imp_exonerado * $tc,
                            'total_otros'         => (string) -$imp_otros * $tc,
                            'otros_cargos'        => (string) -$otros_cargos * $tc,
                            'total_recepcion'     => (string) -$sale->total_comprobante * $tc,
                            'total_imp_creditar'  => (string) -$sale->imp_creditar * $tc,
                            'total_gasto_ap'      => (string) -$sale->gasto_aplica * $tc,
                            'condicion_imp'       => $condicion_impuesto,
                            'clasificacion'       => $clasifica_d151,
                            'Id_Receptor'         => (string) $id_recep,
                            'Nombre_Receptor'     => $n_recep,
                            'Moneda'              => $moneda,
                            'TC'                  => (string) $tc,
                            'Doc_Referencia'      => $documento_referencia,
                            'Detalle'             => $detalle,
                            'Version'             => $sale->version,
                            'iva_devuelto'        => (string) -$iva_devuelto * $tc, 
                        );
                    } else {
                        $datos = array(
                            'idreceptor'          => $sale->idreceptor,
                            'idconfigfact'        => $id_recep,
                            'tipo_documento'      => $nombre_doc,
                            'numero_doc_receptor' => (string) $sale->numero_documento_receptor,
                            'estatus_hacienda'    => $sale->estatus_hacienda,
                            'detalle_mensaje'     => $sale->detalle_mensaje,
                            'documento_proveedor' => $documento_proveedor,
                            'cedula_emisor'       => (string) $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                            'nombre_emisor'       => $strDatas[$documento]['Emisor']['Nombre'],
                            'fecha_recepcion_doc' => $sale->fecha,
                            'fecha_emite_doc'     => $sale->fecha_xml_envio,
                            'clave_doc'           => $sale->clave,
                            'codigo_act'          => $sale->codigo_act_xml,
                            'no_sujeto'           => (string) ($no_sujetos + $no_sujeto) * $tc,
                            'total_exento'        => (string) $imp_0 * $tc,
                            'total_red_05'        => (string) $imp_05 * $tc,
                            'total_red_1'         => (string) $imp_2 * $tc,
                            'total_red_2'         => (string) $imp_3 * $tc,
                            'total_red_4'         => (string) $imp_4 * $tc,
                            'total_trans_0'       => (string) $imp_5 * $tc,
                            'total_trans_4'       => (string) $imp_6 * $tc,
                            'total_trans_8'       => (string) $imp_7 * $tc,
                            'total_gravado'       => (string) $imp_13 * $tc,
                            'total_imp'           => (string) $sale->total_impuesto * $tc,
                            'total_imp_exonerados'=> (string) $mto_imp_exonerado * $tc,
                            'total_otros'         => (string) $imp_otros * $tc,
                            'otros_cargos'        => (string) $otros_cargos * $tc,
                            'total_recepcion'     => (string) $sale->total_comprobante * $tc,
                            'total_imp_creditar'  => (string) $sale->imp_creditar * $tc,
                            'total_gasto_ap'      => (string) $sale->gasto_aplica * $tc,
                            'condicion_imp'       => $condicion_impuesto,
                            'clasificacion'       => $clasifica_d151,
                            'Id_Receptor'         => (string) $id_recep,
                            'Nombre_Receptor'     => $n_recep,
                            'Moneda'              => $moneda,
                            'TC'                  => (string) $tc,
                            'Doc_Referencia'      => $documento_referencia,
                            'Detalle'             => $detalle,
                            'Version'             => $sale->version,
                            'iva_devuelto'        => (string) $iva_devuelto * $tc,
                        );
                    }
                }
                array_push($estructura, $datos);
                $sale_aby = Receptor::find($sale->idreceptor);
                $sale_aby->update(['api_aby' => 1]);
            }
        }
        //dd($estructura);
        DB::connection('external_mysql')->table('reporte_receptor')->insert($estructura);
        \Log::info("Método receptorAby se ejecutó.");  
    }
public function salesfec() : void
    {
        $fecha_desde = '2025-04-01';
        $fecha_hasta = now()->format('Y-m-d');
        $sales = DB::table('sales')->select('sales.*', 'clientes.*', 'facelectron.*', 'codigo_actividad.codigo_actividad', 'configuracion.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
        ->where([
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta],
            ['facelectron.estatushacienda', '=', 'aceptado'],
            ['sales.tipo_doc_ref' ,'!=', 17],
            ['sales.tipo_documento' ,'=', '08'],
            ['sales.api_aby', '=', 0],
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

                // Consulta a la base de datos para obtener tod los medios de pago asociados con el sale_id
                $medio_pagos = DB::table('medio_pago_sale')->select('medio_pago_sale.*')
                ->where('medio_pago_sale.sale_id', '=', $sale->idsale)->get();

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
            if ($sale->tiene_exoneracion === '01') {
                $tiene_exoneracion = 'Si';
            }else{
                $tiene_exoneracion = 'No';
            }
            $descuentousd = 0;
            $total_impusd = 0;
            $total_otros_cargosusd = 0;
            $total_iva_devusd = 0;
            $total_comprobanteusd = 0;
            $descuento = 0;
            $total_imp = 0;
            $total_otros_cargos = 0;
            $total_iva_dev = 0;
            $total_comprobante = 0;
            $imp_0usd = 0;
            $imp_05usd = 0;
            $imp_2usd = 0;
            $imp_3usd = 0;
            $imp_4usd = 0;
            $imp_5usd = 0;
            $imp_6usd = 0;
            $imp_7usd = 0;
            $imp_13usd = 0;
            $imp_99usd = 0;

            $impx_0usd = 0;
            $impx_05usd = 0;
            $impx_2usd = 0;
            $impx_3usd = 0;
            $impx_4usd = 0;
            $impx_5usd = 0;
            $impx_6usd = 0;
            $impx_7usd = 0;
            $impx_13usd = 0;
            $impx_99usd = 0;
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

            if($sale->tipo_documento == '03'){
                if($sale->tipo_moneda != 'CRC'){

                    $sales_item            = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuentousd          = -$sale->total_descuento * $sale->tipo_cambio;
                    $total_impusd          = -$sale->total_impuesto * $sale->tipo_cambio;
                    $total_otros_cargosusd = -$sale->total_otros_cargos * $sale->tipo_cambio;
                    $total_iva_devusd      = -$sale->total_iva_devuelto * $sale->tipo_cambio;
                    $total_comprobanteusd  = -$sale->total_comprobante * $sale->tipo_cambio;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_0usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '02':
                                $imp_2usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_2usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '03':
                                $imp_3usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_3usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '04':
                                $imp_4usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_4usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '05':
                                $imp_5usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_5usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '06':
                                $imp_6usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_6usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '07':
                                $imp_7usd += -$item->valor_neto * $sale->tipo_cambio;
                                $impx_7usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '08':
                                $imp_13usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_13usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '09':
                                $imp_05usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_05usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '01':
                            case '11':
                                $imp_99usd +=  -$item->valor_neto * $sale->tipo_cambio;
                                $impx_99usd += -$item->exo_monto * $sale->tipo_cambio;
                            break;

                        }
                    }
                }else{
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuento = -$sale->total_descuento;
                    $total_imp = -$sale->total_impuesto;
                    $total_otros_cargos= -$sale->total_otros_cargos;
                    $total_iva_dev = -$sale->total_iva_devuelto;
                    $total_comprobante= -$sale->total_comprobante;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0 += -$item->valor_neto;
                                $impx_0 += -$item->exo_monto;
                            break;
                            case '02':
                                $imp_2 += -$item->valor_neto;
                                $impx_2 += -$item->exo_monto;
                            break;
                            case '03':
                                $imp_3 += -$item->valor_neto;
                                $impx_3 += -$item->exo_monto;
                            break;
                            case '04':
                                $imp_4 += -$item->valor_neto;
                                $impx_4 += -$item->exo_monto;
                            break;
                            case '05':
                                $imp_5 += -$item->valor_neto;
                                $impx_5 += -$item->exo_monto;
                            break;
                            case '06':
                                $imp_6 += -$item->valor_neto;
                                $impx_6 += -$item->exo_monto;
                            break;
                            case '07':
                                $imp_7 += -$item->valor_neto;
                                $impx_7 += -$item->exo_monto;
                            break;
                            case '08':
                                $imp_13 +=  -$item->valor_neto;
                                $impx_13 += -$item->exo_monto;
                            break;
                            case '09':
                                $imp_05 +=  -$item->valor_neto;
                                $impx_05 += -$item->exo_monto;
                            break;
                            case '01':
                            case '11':
                                $imp_99 +=  -$item->valor_neto;
                                $impx_99 += -$item->exo_monto;
                            break;
                        }
                    }
                }
            }else{
                if($sale->tipo_moneda != 'CRC'){
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuentousd = $sale->total_descuento * $sale->tipo_cambio;
                    $total_impusd = $sale->total_impuesto * $sale->tipo_cambio;
                    $total_otros_cargosusd = $sale->total_otros_cargos * $sale->tipo_cambio;
                    $total_iva_devusd = $sale->total_iva_devuelto * $sale->tipo_cambio;
                    $total_comprobanteusd = $sale->total_comprobante * $sale->tipo_cambio;

                    foreach ($sales_item as $item) {
                        switch ($item->tipo_impuesto) {
                            case '10':
                                $imp_0usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_0usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '02':
                                $imp_2usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_2usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '03':
                                $imp_3usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_3usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '04':
                                $imp_4usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_4usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '05':
                                $imp_5usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_5usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '06':
                                $imp_6usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_6usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '07':
                                $imp_7usd += $item->valor_neto * $sale->tipo_cambio;
                                $impx_7usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '08':
                                $imp_13usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_13usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '09':
                                $imp_05usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_05usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                            case '01':
                            case '11':
                                $imp_99usd +=  $item->valor_neto * $sale->tipo_cambio;
                                $impx_99usd += $item->exo_monto * $sale->tipo_cambio;
                            break;
                        }
                    }
                }else{
                    $sales_item = Sales_item::where('idsales', $sale->idsale)->get();
                    $descuento = $sale->total_descuento;
                    $total_imp = $sale->total_impuesto;
                    $total_otros_cargos= $sale->total_otros_cargos;
                    $total_iva_dev = $sale->total_iva_devuelto;
                    $total_comprobante= $sale->total_comprobante;


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
                }
            }
     
            $datos = array(
                            'idreceptor'          => $sale->idsale,
                            'idconfigfact'        => $sale->numero_id_emisor,
                            'tipo_documento'      => $nombre_doc,
                            'numero_doc_receptor' => (string ) $sale->numero_documento,
                            'estatus_hacienda'    => $sale->estatushacienda,
                            'detalle_mensaje'     => 'FEC',
                            'documento_proveedor' => $sale->referencia_sale,
                            'cedula_emisor'       => (string) $sale->num_id,
                            'nombre_emisor'       => $sale->nombre,
                            'fecha_recepcion_doc' => $sale->fechahora,
                            'fecha_emite_doc'     => $sale->fechahora,
                            'clave_doc'           => $sale->clave,
                            'codigo_act'          => $sale->codigo_actividad,
                            'no_sujeto'           => (string) $imp_99 + $imp_99usd,
                            'total_exento'        => (string) $imp_0 + $imp_0usd,
                            'total_red_05'        => (string) $imp_05 + $imp_05usd,
                            'total_red_1'         => (string) $imp_2 + $imp_2usd,
                            'total_red_2'         => (string) $imp_3 + $imp_3usd,
                            'total_red_4'         => (string) $imp_4 + $imp_4usd,
                            'total_trans_0'       => (string) $imp_5 + $imp_5usd,
                            'total_trans_4'       => (string) $imp_6 + $imp_6usd,
                            'total_trans_8'       => (string) $imp_7 + $imp_7usd,
                            'total_gravado'       => (string) $imp_13 + $imp_13usd,
                            'total_imp'           => (string) $total_impusd + $total_imp,
                            'total_imp_exonerados'=> 0,
                            'total_otros'         => 0,
                            'otros_cargos'        => (string) $total_otros_cargosusd + $total_otros_cargos,
                            'total_recepcion'     => (string) $total_comprobanteusd + $total_comprobante,
                            'total_imp_creditar'  => (string) $total_impusd + $total_imp,
                            'total_gasto_ap'      => 0,
                            'condicion_imp'       => 'N/A',
                            'clasificacion'       => 'N/A',
                            'Id_Receptor'         => $sale->numero_id_emisor,
                            'Nombre_Receptor'     => $sale->nombre_emisor,
                            'Moneda'              => $sale->tipo_moneda,
                            'TC'                  => (string) $sale->tipo_cambio,
                            'Doc_Referencia'      => $sale->referencia_pago,
                            'Detalle'             => $sale->observaciones,
                            'Version'             => 'N/A',
                        );
            array_push($estructura, $datos);
            $sale_aby = Sales::find($sale->idsale);
            $sale_aby->update(['api_aby' => 1]);
        }
        DB::connection('external_mysql')->table('reporte_receptor')->insert($estructura);
    }
    public function Xml2Array($contents, $get_attributes=1, $priority = 'tag')
    {
        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if(!$xml_values) return;//Hmm...

            //Initializations
            $xml_array = array();
            $parents = array();
            $opened_tags = array();
            $arr = array();

            $current = &$xml_array; //Refference

            //Go through the tags.
            $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
            foreach($xml_values as $data) {
                unset($attributes,$value);//Remove existing values, or there will be trouble

                //This command will extract these variables into the foreach scope
                // tag(string), type(string), level(int), attributes(array).
                extract($data);//We could use the array by itself, but this cooler.

                $result = array();
                $attributes_data = array();

                if(isset($value)) {
                    if($priority == 'tag') $result = $value;
                    else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
                }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                        $repeated_tag_index[$tag.'_'.$level] = 1;

                        $current = &$current[$tag];

                    } else { //There was another element with the same tag name

                        if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                            $repeated_tag_index[$tag.'_'.$level]++;
                        } else {//This section will make the value an array if multiple tags with the same name appear together
                            $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                            $repeated_tag_index[$tag.'_'.$level] = 2;

                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                        }
                        $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                        $current = &$current[$tag][$last_item_index];
                    }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }
        return($xml_array);
    }

}
