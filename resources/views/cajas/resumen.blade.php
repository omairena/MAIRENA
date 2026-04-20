@extends('layouts.app', ['page' => __('Cajas Resumen'), 'pageSlug' => 'resumenCajas'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif 
<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <h4 class="card-title">{{ __('Filtro de Busqueda') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">   
                    <form method="post" action="{{ route('cajas.filtrodaily') }}" autocomplete="off">
                    @csrf
                            <label class="form-control-label" for="input-caja">{{ __('Seleccionar la caja') }}</label>
                            <select class="form-control form-control-alternative" id="idcaja" name="idcaja" value="{{ old('idcaja') }}" required>
                                @if($fecha['caja'] > 0)
                                    <option value="0">-- Seleccionar una caja --</option>
                                    @foreach($cajas as $caja) 
                                        <option value="{{ $caja->idcaja }}" {{ ($caja->idcaja == $fecha['caja'] ? 'selected="selected"' : '') }}>{{ $caja->nombre_caja }} - Codigo: {{ $caja->codigo_unico}}</option>
                                    @endforeach
                                @else
                                    <option value="0">-- Seleccionar una caja --</option>
                                    @foreach($cajas as $caja) 
                                        <option value="{{ $caja->idcaja }}">{{ $caja->nombre_caja }} - Codigo: {{ $caja->codigo_unico}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                            <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde', $fecha['desde']) }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                            @include('alerts.feedback', ['field' => 'fecha_desde'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                            <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta', $fecha['hasta']) }}" required style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Resumen diario de caja') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')
                    @if($callback != 0)
                        <div class="table-responsive">
                            <table class="table" id="documentos_data">
                                <thead class=" text-primary">
                                    <th scope="col" colspan="2" style="text-align: center;">Resumen Ventas del Dia</th>
                                    <th scope="col" style="text-align: center;">Totales</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;" colspan="2">Dia</td>
                                        <td style="text-align: right;" >{{ $callback['dia'] }}</td>
                                    </tr>      
                                    <tr>
                                        <td style="text-align: center;" colspan="2">Caja</td>
                                        <td style="text-align: right;">{{ $callback['caja'] }}</td>
                                    </tr>
                                   <!-- <tr>
                                        <td style="text-align: center;" colspan="2">Ventas de Contado</td>
                                        <td style="text-align: right;">{{ number_format($callback['ventas_contado'],2,'.',',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;" colspan="2">Recibos de Dinero</td>
                                        <td style="text-align: right;" >{{ number_format($callback['recibos_dinero'],2,'.',',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;" colspan="2"><b>Total Efectivo Día</b></td>
                                        <td style="text-align: right;" ><b>{{ number_format($callback['total_efectivo_dia'],2,'.',',') }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;" colspan="2">Ventas a Credito</td>
                                        <td style="text-align: right;">{{ number_format($callback['ventas_credito'],2,'.',',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;" colspan="2"><b>Total del Día</b></td>
                                        <td style="text-align: right;" ><b>{{ number_format($callback['total_venta_dia'],2,'.',',') }}</b></td>
                                    </tr>-->
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</div>
<?php 


                      
function obtenerDatos($fecha_inicio,$fecha_hasta, $idempresa) {
    return \DB::table('sales')  
        ->select('sales.*', 'sales_item.*', 'sales.tipo_documento', 'sales.tipo_moneda',  
                 'sales.tipo_cambio', 'configuracion.factor_receptor',  
                 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto',  
                 'facelectron.estatushacienda', 'medio_pago_sale.monto as monto_pago',   
                 'medio_pagos.codigo as medio_pago_codigo',   
                 'medio_pagos.nombre as medio_pago_nombre')  
        ->leftJoin('sales_item', 'sales.idsale', '=', 'sales_item.idsales')  
        ->leftJoin('medio_pago_sale', 'sales.idsale', '=', 'medio_pago_sale.sale_id')  
        ->leftJoin('medio_pagos', function ($join) {  
            $join->on('medio_pago_sale.medio_pago_id', '=', 'medio_pagos.id')  
                 ->orOn('sales.medio_pago', '=', 'medio_pagos.codigo');   
        })  
        ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')  
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')  
        ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')  
        ->where([  
            ['sales.fecha_creada', '>=', $fecha_inicio],  
            ['sales.fecha_creada', '<=', $fecha_hasta],  
            ['sales.idconfigfact', '=', $idempresa],  
            ['facelectron.estatushacienda', '=', 'aceptado'],  
            ['sales.estatus_sale', '=', 2],  
            ['sales.tipo_doc_ref', '!=', 17],  
        ])  
        ->whereIn('sales.tipo_documento', ['01', '02', '03', '04', '09'])  
        ->get();  
}  
function calcularTotales($query) {  
    $totales_crc = [];        // Acumulador para montos en CRC  
    $totales_otros = [];      // Acumulador para montos en otras monedas  
    $total_credito_crc = 0;   // Acumulador para créditos en CRC  
    $total_credito_otros = 0; // Acumulador para créditos en otras monedas  

    foreach ($query as $item) {  
        $monto_pago = (float)($item->monto_pago ?? 0);  

        // Determinamos el tipo de pago  
        $tipo_pago = null;  

        // Verificación para obtener el nombre del medio de pago  
        if (!empty($item->medio_pago)) {  
            $medio_pago_info = \DB::table('medio_pagos')  
                ->where('codigo', $item->medio_pago)  
                ->first();  
            $tipo_pago = $medio_pago_info->nombre ?? 'Desconocido';  
        } else {  
            $tipo_pago = $item->medio_pago_nombre ?? 'Desconocido';  
        }  

        // Procesar montos en CRC  
        if ($item->tipo_moneda === 'CRC') {  
            // Solo acumular si la condicion_venta es 01  
            if (isset($item->condicion_venta) && $item->condicion_venta === '01') {  
                if (!isset($totales_crc[$tipo_pago])) {  
                    $totales_crc[$tipo_pago] = 0;  
                }  
                // Sumar total_comprobante  
                $totales_crc[$tipo_pago] += $item->total_comprobante;   
            }  
            // Acumular total_credito si la condición de venta es 02  
            if (isset($item->condicion_venta) && $item->condicion_venta == '02') {  
                $total_credito_crc += $item->total_comprobante;  
            }  
        } else { // Procesar montos en otras monedas  
            if (!isset($totales_otros[$tipo_pago])) {  
                $totales_otros[$tipo_pago] = 0;  
            }  
            // Calcular el monto a acumular considerando medio_pago  
            if (!empty($item->medio_pago)) {  
                if (isset($item->condicion_venta) && $item->condicion_venta == '02') {  
                    $total_credito_otros += $item->total_comprobante; // Acumular créditos  
                }  
                $totales_otros[$tipo_pago] += $item->total_comprobante; // Añadir a "Otros"  
            } else {  
                // Consultar monto en medio_pago_sale  
                $monto_asociado = \DB::table('medio_pago_sale')  
                    ->where('sale_id', $item->idsale)  
                    ->sum('monto');  

                if (isset($item->condicion_venta) && $item->condicion_venta == '02') {  
                    $total_credito_otros += $monto_asociado; // Acumular créditos  
                }  
                $totales_otros[$tipo_pago] += $monto_asociado; // Añadir a "Otros"  
            }  
        }  
    }  

    // Retornar los resultados finales  
    return [  
        'totales_crc' => $totales_crc,  
        'totales_otros' => $totales_otros,  
        'total_credito_crc' => $total_credito_crc,  
        'total_credito_otros' => $total_credito_otros // Retorna total_credito en otras monedas también  
    ];  
}  

function generarTabla($totales_crc, $totales_otros, $total_credito_crc, $total_credito_otros) {  
    echo '<table class="table" width="100%">';  
    echo '<thead class="hover:bg-gray-50"><tr><th align="left">Medio de Pago</th><th align="left">Monto (CRC)</th><th align="left">Monto (Otros)</th></tr></thead>';  
    echo '<tbody>';  

    // Mostrar totales por medio de pago en CRC  
    foreach ($totales_crc as $medio_pago => $monto_total_crc) {  
        // Obtener monto en otras monedas  
        $monto_total_otros = $totales_otros[$medio_pago] ?? 0;  
        echo '<tr class="hover:bg-gray-50">';  
        echo '<td align="left">' . htmlspecialchars($medio_pago) . '</td>';  
        echo '<td align="left">' . htmlspecialchars(number_format($monto_total_crc, 2)) . '</td>';  
        echo '<td align="left">' . htmlspecialchars(number_format($monto_total_otros, 2)) . '</td>';  
        echo '</tr>';  
    }  

    // Mostrar totales  
    echo '<tr class="font-bold bg-gray-100"><td align="left"><b>Total</b></td><td align="left"><b>' . htmlspecialchars(number_format(array_sum($totales_crc), 2)) . '</b></td><td align="left"><b>' . htmlspecialchars(number_format(array_sum($totales_otros), 2)) . '</b></td></tr>';  

    // Mostrar fila de créditos  
    if ($total_credito_crc > 0 || $total_credito_otros > 0) {  
        echo '<tr class="hover:bg-gray-50">';  
        echo '<td align="left">Créditos</td>';  
        echo '<td align="left">' . htmlspecialchars(number_format($total_credito_crc, 2)) . '</td>';  
        echo '<td align="left">' . htmlspecialchars(number_format($total_credito_otros, 2)) . '</td>';  
        echo '</tr>';  
    }  

    echo '</tbody>';  
    echo '</table>';  
}  

// Ejecución del código  
$idempresa = $caja->idconfigfact;  
$fecha_inicio = $fecha['desde'];  
$fecha_hasta = $fecha['hasta']; 
// Llamar a la función con parámetros  

$fecha = '2025-03-24';  
$query = obtenerDatos($fecha_inicio,$fecha_hasta, $idempresa); // Asegúrate de que esta función devuelva un arreglo adecuado  
$resultados = calcularTotales($query);  
generarTabla($resultados['totales_crc'], $resultados['totales_otros'], $resultados['total_credito_crc'], $resultados['total_credito_otros']);  

?>
@endsection
@section('myjs')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#documentos_data').DataTable(
            {     
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]]
            }
        );
});
</script>
@endsection