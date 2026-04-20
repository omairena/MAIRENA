@extends('layouts.app', ['page' => __('Cerrar Caja'), 'pageSlug' => 'cerrarCaja'])
@section('content')
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <form action="{{ route('cajas.cierredia', $id) }}" method="POST">
                    @csrf
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Cerrar Caja') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cajas.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('Informaci贸n de cierre del dia ') }}
                        <br>
                        {{ $caja->nombre_caja }}<br>
                          {{ __('Fecha de Cierre:') }} <?php echo date('m-d-Y'); ?> <br>
                          {{ __('Fecha Apertura:') }} {{ str_pad($caja->fecha_apertura, 3, "0", STR_PAD_LEFT) }}<br>
                         {{ __('C贸digo de Caja:') }} {{ str_pad($caja->codigo_unico, 3, "0", STR_PAD_LEFT) }}<br>
                          {{ __('ID EMPRESA:') }}{{ $caja->idconfigfact }}<br> 
                       
                        </h6>
 
                        <div class="card-body">
                            <table class="table tablesorter ">
                                <thead class=" text-primary">
                                    <th scope="col" style="text-align: center;">{{ __('Contado') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Cr茅dito') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Recibos de Dinero') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Efectivo Entrante') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Cobro con Tarjeta') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Pagos del Dia') }}</th>
                                    <th scope="col" style="text-align: center;">{{ __('Abonos con Tarjeta y Transferencia') }}</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: right;">{{ number_format($data['contado'],2,',','.') }}</td>
                                        <td style="text-align: right;">{{ number_format($data['credito'],2,',','.') }}</td>
                                        <td style="text-align: right;">{{ number_format($data['recibos_dinero'],2,',','.') }}</td>
                                        <td style="text-align: right;">{{ number_format($data['efectivo_entrante'],2,',','.') }}</td>
                                        <td style="text-align: right;">{{ number_format($data['cobro_tarjeta'],2,',','.') }}</td>
                                        <td style="text-align: right;"><input type="number" step="any" name="pagos_dia" id="pagos_dia" class="form-control form-control-alternative" required="true" style="display: inline;"></td>
                                        <td style="text-align: right;"><input type="number" step="any" name="abonos_tarjeta" id="abonos_tarjeta" class="form-control form-control-alternative" required="true" style="display: inline;" value="{{ $data['abonos_tarjeta'] }}" readonly="true"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Cerrar Caja') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-user">
                <div class="card-body">
                    <p class="card-text">
                        <div class="author">
                            <div class="block block-one"></div>
                            <div class="block block-two"></div>
                            <div class="block block-three"></div>
                            <div class="block block-four"></div>
                            <a href="#">
                                <img class="avatar" src="{{ asset('black') }}/img/default-avatar.png" alt="">
                                <h5 class="title"></h5>
                            </a>
                            <p class="description">
                                Empresa : {{ $caja->caja_emp[0]->nombre_comercial }}
                                <br>
                                Fecha de Cierre: <?php echo date('m-d-Y'); ?>
                            </p>
                        </div>
                    </p>
                    <div class="card-description">
                            {{ $caja->caja_emp[0]->direccion_emisor }}
                            <br>

                    </div>
                </div>
                <div class="card-footer">
                    <div class="button-container">
                            <button class="btn btn-icon btn-round btn-facebook">
                                <i class="fab fa-facebook"></i>
                            </button>
                            <button class="btn btn-icon btn-round btn-twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-icon btn-round btn-google">
                                <i class="fab fa-google-plus"></i>
                            </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<input type="text" name="idcaja" id="idcaja" value="{{ old('idcaja', $id) }}" hidden="true">

<?php 
$idempresa = $caja->idconfigfact;  
$fecha_inicio = $caja->fecha_apertura;  

// Llamar a la función con parámetros  

                      
function obtenerDatos($fecha_inicio, $idempresa) {
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
            ['sales.fecha_creada', '<=', date('Y-m-d')],  
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
$fecha = '2025-03-24';  
$query = obtenerDatos($fecha_inicio, $idempresa); // Asegúrate de que esta función devuelva un arreglo adecuado  
$resultados = calcularTotales($query);  
generarTabla($resultados['totales_crc'], $resultados['totales_otros'], $resultados['total_credito_crc'], $resultados['total_credito_otros']);  

?>
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("blur", "#pagos_dia" , function(event) {
            var pago_dia = $(this).val();
            var idcaja = $('#idcaja').val();
            var abonos_tarjeta = $('#abonos_tarjeta').val();
            var APP_URL = {!! json_encode(url('/ajaxCajas')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idcaja:idcaja, pago_dia:pago_dia, abonos_tarjeta:abonos_tarjeta},

                dataType: 'json',

                success:function(response){
                    $("#e_a_d").empty();
                    $("#t_e_c").empty();
                    var tec = "Total Efectivo Caja: " + response['success']['total_efectivo'].toFixed(2);
                    var ead = "Efectivo a Depositar: " + response['success']['efectivo_depositar'].toFixed(2);
                    $("#e_a_d").append(ead);
                    $("#t_e_c").append(tec);
                    //console.log(response);
                }
            });
        });
    });
</script>
@endsection
