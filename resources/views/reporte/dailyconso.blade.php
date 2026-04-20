@extends('layouts.app', ['page' => __('Consolidado Farrier'), 'pageSlug' => 'dailySales'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
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
                    <form method="post" action="{{ route('filtro.dailyconso') }}" autocomplete="off">
                    @csrf
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
                             <?php 
                            
                            $confi= App\Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)
               ->get();
          
          if($confi[0]->sum_op == 0){
          
          ?>
          
                     <a href="{{ url('sin_op') }}" class="btn btn-sm btn-primary">{{ __('Reporte Sin OP') }}</a>        
                            
    <?php 
          }else{
           ?>           
           <a href="{{ url('con_op') }}" class="btn btn-sm btn-primary">{{ __('Reporte Con OP') }}</a>
          
               <?php 
          }
           ?>                
                            
                            
                            <h4 class="card-title">{{ __('Consolidado Farrier  ***Los documentos en moneda Extrangera son convertidos a CRC***') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="documentos_data">
                            <thead class=" text-primary">
                                 <th scope="col">{{ __('Sucursal') }}</th>
                                <th scope="col">{{ __('Tipo de Documento') }}</th>
                                <th scope="col">Numero Documento</th>
                                <th scope="col">Condición Venta</th>
                                <th scope="col">Fecha Documento</th>
                                <th scope="col">Identificacion Cliente</th>
                                <th scope="col">Nombre Cliente</th>
                                <th scope="col">Estado Documento</th>
                                <th scope="col">Código Actividad</th>
                                <th scope="col">Tipo Cambio (CRC - USD)</th>
                                <th scope="col">Moneda</th>
                                <th scope="col">Exoneración (S/N)</th>
                                <th scope="col">Suma de Total Descuentos (CRC)</th>
                                <th scope="col">Suma de Total Excento 0% (CRC)</th>
                                <th scope="col">Suma de Total Reducida 0.5% (CRC)</th>
                                <th scope="col">Suma de Total Reducida 1% (CRC)</th>
                                <th scope="col">Suma de Total Reducida 2% (CRC)</th>
                                <th scope="col">Suma de Total Reducida 4% (CRC)</th>
                                <th scope="col">Suma de Total Transitorio 0% (CRC)</th>
                                <th scope="col">Suma de Total Transitorio 4% (CRC)</th>
                                <th scope="col">Suma de Total Transitorio 8% (CRC)</th>
                                <th scope="col">Suma de Total Gravado 13% (CRC)</th>
                                <th scope="col">Suma de Total No Sujeto (CRC)</th>
                                <th scope="col">Suma de Total IVA (CRC)</th>
                                <th scope="col">Suma de Total Otros Cargos (CRC)</th>
                                <th scope="col">Suma de Total IVA Devuelto (CRC)</th>
                                <th scope="col">Suma de Total IVA EXONERADO</th>
                                <th scope="col">Suma de Total Comprobante</th>
                            </thead>
                            <tbody>
                                <?php 
                                    $masivo_exento_0 = 0.00000;
                                    $masivo_transitorio_0 = 0.00000;
                                     
                                    $masivo_reducida_05 = 0.00000;
                                    $masivo_reducida_1 = 0.00000;
                                    $masivo_reducida_2 = 0.00000;
                                    $masivo_reducida_4 = 0.00000;
                                    $masivo_transitorio_4 = 0.00000;
                                    $masivo_transitorio_8 = 0.00000;
                                    $masivo_gravado_13 = 0.00000;
                                    
                                     $masivo_reducida_05e = 0.00000;
                                    $masivo_reducida_1e = 0.00000;
                                    $masivo_reducida_2e = 0.00000;
                                    $masivo_reducida_4e = 0.00000;
                                    $masivo_transitorio_4e = 0.00000;
                                    $masivo_transitorio_8e = 0.00000;
                                    $masivo_gravado_13e = 0.00000;
                                    
                                    
                                    $masivo_no_sujeto = 0.00000;
                                    $masivo_total_iva = 0.00000;

                                    $masivo_total_otroc = 0.00000;
                                    $masivo_descuento = 0.00000;
                                    $masivo_devuelto = 0.00000;
                                    $masivo_exonerado = 0.00000;
                                    $masivo_total_comprobante = 0.00000;
                                    $masivo_total_comprobantet = 0.00000;
                                    $masivo_total_comprobantenc = 0.00000;
                                    
                                    
                                            $masivo_reducida_05eusd = 0.00000;
                                            $masivo_reducida_1eusd = 0.00000;
                                            $masivo_reducida_2eusd = 0.00000;
                                            $masivo_reducida_4eusd = 0.00000;
                                            $masivo_transitorio_4eusd = 0.00000;
                                            $masivo_transitorio_8eusd = 0.00000;
                                            $masivo_gravado_13eusd = 0.00000;       
                                                 
                                            $masivo_exento_0usd = 0.00000;
                                            $masivo_reducida_05usd = 0.00000;
                                            $masivo_reducida_1usd = 0.00000;
                                            $masivo_reducida_2usd = 0.00000;
                                            $masivo_reducida_4usd = 0.00000;
                                            $masivo_transitorio_0usd = 0.00000;
                                            $masivo_transitorio_4usd = 0.00000;
                                            $masivo_transitorio_8usd = 0.00000;
                                            $masivo_gravado_13usd = 0.00000;
                                            $masivo_no_sujetousd = 0.00000;
                                            $masivo_total_ivausd = 0.00000;

                                            $masivo_total_otrocusd = 0.00000;
                                            $masivo_descuentousd = 0.00000;
                                            $masivo_devueltousd = 0.00000;
                                            $masivo_exoneradousd = 0.00000;
                                            $masivo_total_comprobanteusd = 0.00000;
                                            $masivo_total_comprobantencusd = 0.00000;
                                            $masivo_total_comprobantetusd = 0.00000;
                                ?>
                                @foreach($callback as $document)
                                    <?php
                                    if ($document['tipo_documento'] != '03' and $document['tipo_documento'] != '95') {
                                            
                                            if($document['moneda'] === 'CRC'){
                                            if($document['exoneracion']=='Si'){
                                            $masivo_reducida_05e += $document['reducida_05'];
                                            $masivo_reducida_1e += $document['reducida_1'];
                                            $masivo_reducida_2e += $document['reducida_2'];
                                            $masivo_reducida_4e += $document['reducida_4'];
                                            $masivo_transitorio_4e += $document['transitorio_4'];
                                            $masivo_transitorio_8e += $document['transitorio_8'];
                                            $masivo_gravado_13e += $document['gravado_13'];
                                            }
                                            $masivo_exento_0 += $document['excento_0'];
                                            $masivo_transitorio_0 += $document['transitorio_0'];
                                            $masivo_reducida_05 += $document['reducida_05'];
                                            $masivo_reducida_1 += $document['reducida_1'];
                                            $masivo_reducida_2 += $document['reducida_2'];
                                            $masivo_reducida_4 += $document['reducida_4'];
                                            $masivo_transitorio_4 += $document['transitorio_4'];
                                            $masivo_transitorio_8 += $document['transitorio_8'];
                                            $masivo_gravado_13 += $document['gravado_13'];
                                            
                                            $masivo_no_sujeto += $document['no_sujeto'];
                                            $masivo_total_iva += $document['total_iva'];

                                            $masivo_total_otroc += $document['otros_cargos'];
                                            $masivo_descuento += $document['total_descuento'];
                                            $masivo_devuelto += $document['iva_devuelto'];
                                            $masivo_exonerado += $document['total_exonerado'];
                                            $masivo_total_comprobante += $document['total_comprobante'];
                                            $masivo_total_comprobantet += $document['total_comprobante'];
                                            }else{
                                             if($document['exoneracion']=='Si'){
                                            $masivo_reducida_05eusd += $document['reducida_05'] * $document['tipo_cambio'];
                                            $masivo_reducida_1eusd += $document['reducida_1'] * $document['tipo_cambio'];
                                            $masivo_reducida_2eusd += $document['reducida_2'] * $document['tipo_cambio'];
                                            $masivo_reducida_4eusd += $document['reducida_4'] * $document['tipo_cambio'];
                                            $masivo_transitorio_4eusd += $document['transitorio_4'] * $document['tipo_cambio'];
                                            $masivo_transitorio_8eusd += $document['transitorio_8'] * $document['tipo_cambio'];
                                            $masivo_gravado_13eusd += ($document['gravado_13'] * $document['tipo_cambio']);
                                            }
                                            $masivo_exento_0usd += $document['excento_0'] * $document['tipo_cambio'] ;
                                            $masivo_reducida_05usd += $document['reducida_05'] * $document['tipo_cambio'];
                                            $masivo_reducida_1usd += $document['reducida_1'] * $document['tipo_cambio'];
                                            $masivo_reducida_2usd += $document['reducida_2'] * $document['tipo_cambio'];
                                            $masivo_reducida_4usd += $document['reducida_4'] * $document['tipo_cambio'];
                                            $masivo_transitorio_0usd += $document['transitorio_0'] * $document['tipo_cambio'];
                                            $masivo_transitorio_4usd += $document['transitorio_4'] * $document['tipo_cambio'];
                                            $masivo_transitorio_8usd += $document['transitorio_8'] * $document['tipo_cambio'];
                                            $masivo_gravado_13usd += ($document['gravado_13'] * $document['tipo_cambio']);
                                            $masivo_no_sujetousd += $document['no_sujeto'] * $document['tipo_cambio'];
                                            $masivo_total_ivausd += $document['total_iva'] * $document['tipo_cambio'];

                                            $masivo_total_otrocusd += $document['otros_cargos'] * $document['tipo_cambio'];
                                            $masivo_descuentousd += $document['total_descuento'] * $document['tipo_cambio'];
                                            $masivo_devueltousd += $document['iva_devuelto'] * $document['tipo_cambio'];
                                            $masivo_exoneradousd += $document['total_exonerado'] * $document['tipo_cambio'];
                                            $masivo_total_comprobanteusd += $document['total_comprobante'] * $document['tipo_cambio'];
                                            $masivo_total_comprobantetusd += $document['total_comprobante'] * $document['tipo_cambio'];
                                                
                                            }

                                        } else {
                                            if($document['moneda']==='CRC'){
                                            if($document['exoneracion']=='Si'){
                                            $masivo_reducida_05e = $masivo_reducida_05e - $document['reducida_05'];
                                            $masivo_reducida_1e = $masivo_reducida_1e - $document['reducida_1'];
                                            $masivo_reducida_2e = $masivo_reducida_2e - $document['reducida_2'];
                                            $masivo_reducida_4e = $masivo_reducida_4e - $document['reducida_4'];
                                            $masivo_transitorio_4e = $masivo_transitorio_4e - $document['transitorio_4'];
                                            $masivo_transitorio_8e = $masivo_transitorio_8e - $document['transitorio_8'];
                                            $masivo_gravado_13e = $masivo_gravado_13e - $document['gravado_13']; 
                                            }
                                                
                                            $masivo_exento_0 = $masivo_exento_0 - $document['excento_0'];
                                            $masivo_reducida_05 = $masivo_reducida_05 - $document['reducida_05'];
                                            $masivo_reducida_1 = $masivo_reducida_1 - $document['reducida_1'];
                                            $masivo_reducida_2 = $masivo_reducida_2 - $document['reducida_2'];
                                            $masivo_reducida_4 = $masivo_reducida_4 - $document['reducida_4'];
                                            $masivo_transitorio_0 = $masivo_transitorio_0 - $document['transitorio_0'];
                                            $masivo_transitorio_4 = $masivo_transitorio_4 - $document['transitorio_4'];
                                            $masivo_transitorio_8 = $masivo_transitorio_8 - $document['transitorio_8'];
                                            $masivo_gravado_13 = $masivo_gravado_13 - $document['gravado_13'];
                                            $masivo_no_sujeto = $masivo_no_sujeto - $document['no_sujeto'];
                                            $masivo_total_iva = $masivo_total_iva - $document['total_iva'];

                                            $masivo_total_otroc = $masivo_total_otroc - $document['otros_cargos'];
                                            $masivo_descuento = $masivo_descuento - $document['total_descuento'];
                                            $masivo_devuelto = $masivo_devuelto - $document['iva_devuelto'];
                                            $masivo_exonerado = $masivo_exonerado - $document['total_exonerado'];
                                            $masivo_total_comprobante = $masivo_total_comprobante - $document['total_comprobante'];
                                            $masivo_total_comprobantenc = $masivo_total_comprobantenc - $document['total_comprobante'];
                                            }else{
                                                 if($document['exoneracion']=='Si'){
                                            $masivo_reducida_05eusd = ($masivo_reducida_05eusd - $document['reducida_05'])* $document['tipo_cambio'];
                                            $masivo_reducida_1eusd = ($masivo_reducida_1eusd - $document['reducida_1'])* $document['tipo_cambio'];
                                            $masivo_reducida_2eusd = ($masivo_reducida_2eusd - $document['reducida_2'])* $document['tipo_cambio'];
                                            $masivo_reducida_4eusd = ($masivo_reducida_4eusd - $document['reducida_4'])* $document['tipo_cambio'];
                                            $masivo_transitorio_4eusd = ($masivo_transitorio_4eusd - $document['transitorio_4'])* $document['tipo_cambio'];
                                            $masivo_transitorio_8eusd = ($masivo_transitorio_8eusd - $document['transitorio_8'])* $document['tipo_cambio'];
                                            $masivo_gravado_13eusd = ($masivo_gravado_13eusd - $document['gravado_13'])* $document['tipo_cambio'];        
                                                 }
                                            $masivo_exento_0usd = ($masivo_exento_0usd - $document['excento_0'])* $document['tipo_cambio'];
                                            $masivo_reducida_05usd = ($masivo_reducida_05usd - $document['reducida_05'])* $document['tipo_cambio'];
                                            $masivo_reducida_1usd = ($masivo_reducida_1usd - $document['reducida_1'])* $document['tipo_cambio'];
                                            $masivo_reducida_2usd = ($masivo_reducida_2usd - $document['reducida_2'])* $document['tipo_cambio'];
                                            $masivo_reducida_4usd = ($masivo_reducida_4usd - $document['reducida_4'])* $document['tipo_cambio'];
                                            $masivo_transitorio_0usd = ($masivo_transitorio_0usd - $document['transitorio_0'])* $document['tipo_cambio'];
                                            $masivo_transitorio_4usd = ($masivo_transitorio_4usd - $document['transitorio_4'])* $document['tipo_cambio'];
                                            $masivo_transitorio_8usd = ($masivo_transitorio_8usd - $document['transitorio_8'])* $document['tipo_cambio'];
                                            $masivo_gravado_13usd = ($masivo_gravado_13usd - $document['gravado_13'])* $document['tipo_cambio'];
                                            $masivo_no_sujetousd = ($masivo_no_sujetousd - $document['no_sujeto'])* $document['tipo_cambio'];
                                            $masivo_total_ivausd = ($masivo_total_ivausd - $document['total_iva'])* $document['tipo_cambio'];

                                            $masivo_total_otrocusd = ($masivo_total_otrocusd - $document['otros_cargos'])* $document['tipo_cambio'];
                                            $masivo_descuentousd = ($masivo_descuentousd - $document['total_descuento'])* $document['tipo_cambio'];
                                            $masivo_devueltousd = ($masivo_devueltousd - $document['iva_devuelto'])* $document['tipo_cambio'];
                                            $masivo_exoneradousd = ($masivo_exoneradousd - $document['total_exonerado'])* $document['tipo_cambio'];
                                            $masivo_total_comprobanteusd = ($masivo_total_comprobanteusd - $document['total_comprobante'])* $document['tipo_cambio'];
                                            $masivo_total_comprobantencusd = ($masivo_total_comprobantencusd - $document['total_comprobante'])* $document['tipo_cambio'];
                                            }
                                        }
                                     
                                    ?>
                                    <tr>
                                        <td>
                                            {{ $document['nombre_empresa']}}
                                        </td>
                                        <td>
                                            @switch($document['tipo_documento'])
                                                @case('01')
                                                    Factura Electrónica
                                                @break
                                                @case('02')
                                                    Nota Débito Electrónica
                                                @break
                                                @case('03')
                                                    Nota Crédito Electrónica
                                                @break
                                                @case('04')
                                                    Tiquete Electrónico
                                                @break
                                                @case('08')
                                                    Factura electrónica de compra
                                                @break
                                                @case('09')
                                                    Factura electrónica de exportación
                                                @break
                                                
                                                @case('96')
                                                    Orden de Pedido
                                                @break
                                                @case('95')
                                                    Devolucion Orden de Pedido
                                                @break
                                                
                                               
                                               
                                               
                                            @endswitch
                                        </td>
                                        <td>{{ $document['numero_documento']}}</td>
                                        <td>
                                            @switch($document['condicion'])
                                                @case('01')
                                                    Contado
                                                @break
                                                @case('02')
                                                    Crédito
                                                @break
                                                 @case('10')
                                                    Crédito
                                                @break
                                                 @case('11')
                                                    Crédito
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{ $document['fecha_documento']}}</td>
                                        <td>{{ $document['identificacion_cliente']}}</td>
                                        <td>{{ $document['nombre_cliente']}}</td>
                                        <td>{{ $document['estado_doc']}}</td>
                                        <td>{{ $document['codigo_actividad']}}</td>
                                        <td>{{ $document['tipo_cambio']}}</td>
                                        <td>{{ $document['moneda']}}</td>
                                        <td>{{ $document['exoneracion']}}</td>
                                          @if($document['moneda'] === 'CRC')
                                        <td>{{ number_format($document['total_descuento' ],2,'.',',')}}</td>
                                        <td>{{ number_format( $document['excento_0'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['reducida_05'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['reducida_1'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['reducida_2'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['reducida_4'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['transitorio_0'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['transitorio_4'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['transitorio_8'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['gravado_13'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['no_sujeto'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['total_iva'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['otros_cargos'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['iva_devuelto'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['total_exonerado'] ,2,'.',',')}}</td>
                                        <td>{{ number_format($document['total_comprobante'] ,2,'.',',')}}</td>
                                         @else
                                         <td>{{ number_format(($document['total_descuento' ] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format( ($document['excento_0'] * $document['tipo_cambio']) ,2,'.',',')}}</td>
                                        <td>{{ number_format(($document['reducida_05'] * $document['tipo_cambio']) ,2,'.',',')}}</td>
                                        <td>{{ number_format(($document['reducida_1'] * $document['tipo_cambio']) ,2,'.',',')}}</td>
                                        <td>{{ number_format(($document['reducida_2']  * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['reducida_4'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['transitorio_0'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['transitorio_4'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['transitorio_8'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['gravado_13'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['no_sujeto'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['total_iva'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['otros_cargos'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['iva_devuelto'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['total_exonerado'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                        <td>{{ number_format(($document['total_comprobante'] * $document['tipo_cambio']),2,'.',',')}}</td>
                                         @endif
                                    </tr>
                                @endforeach                        
                            </tbody>
                            <tfoot> 
                                <tr>
                                    <th style="color:black;">Totales Ventas Periodo:</td>
                                    
                                   
                                    <th colspan="10"></th>
                                    <th style="color:black;">{{ number_format( ($masivo_descuento) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( $masivo_exento_0 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_reducida_05 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_reducida_1 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_reducida_2 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_reducida_4 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_transitorio_0 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_transitorio_4 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_transitorio_8 ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( ($masivo_gravado_13) ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_no_sujeto ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_total_iva ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_total_otroc ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_devuelto ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_exonerado ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $masivo_total_comprobante ,2,'.',',')}}</th>
                                </tr>
                                <tr>
                                   <th style="color:black;">Total Comprobantes:</td>
                                    <th style="color: black; " colspan="2"><b>{{ number_format($masivo_total_comprobantet,2,'.',',') }}</b></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th style="color:black;">Total Notas de Credito:</th>
                                    <th style="color: black; " colspan="2"><b>{{ number_format($masivo_total_comprobantenc,2,'.',',') }}</b></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                 <tr>
                                    <th style="color:black;">Total IVA:</th>
                                    <th style="color: black; " colspan="2"><b>{{ number_format(($masivo_total_iva ) ,2,'.',',') }}</b></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th style="color:black;">Total Final:</th>
                                    <th style="color: black; " colspan="2"><b>{{ number_format(($masivo_total_comprobante) ,2,'.',',') }}</b></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                 </tfoot>
                        </table>
                       
                        </div>
                          </div>
                         
            </div>
        </div>
</div>
 <div class="card ">
                         <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Resumen por Tarifa de IVA') }}</h4>
                        </div>
                    </div>
                </div>
                         <div class="table-responsive">
                          <table class="table" id="documentos_data1">
                              <thead class=" text-primary">
                                <th colspan="2" scope="col">Tipo Impuesto</th>
                                
                                <th scope="col">Base Imponible</th>
                                <th scope="col">IVA</th>
                                <th scope="col">Total</th>
                            </thead>
                                 <tfoot> 
                                <tr>
                                     <th colspan="2" style="color:black;">Excento</td>
                                    <th style="color:black;">{{number_format( $masivo_exento_0 + $masivo_transitorio_0 ,2,'.',',')}}</th>
                                     <th style="color:black;">{{number_format( (($masivo_exento_0 + $masivo_transitorio_0)*0) ,2,'.',',')}}</th>
                                      <th style="color:black;">{{number_format( (($masivo_exento_0 + $masivo_transitorio_0)*0)+ ($masivo_exento_0 + $masivo_transitorio_0) ,2,'.',',')}}</th>
                                    </tr>
                                     <tr>
                                          <th colspan="2" style="color:black;">No Sujeto</td>
                                    <th style="color:black;">{{ number_format( $masivo_no_sujeto ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( ($masivo_no_sujeto * 0) ,2,'.',',') }}</th>
                                      <th style="color:black;">{{ number_format( (($masivo_no_sujeto * 0)+ $masivo_no_sujeto ) ,2,'.',',') }}</th>
                                     
                                    </tr>
                                    
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 0.5%</td>
                                    <th style="color:black;">{{ number_format(($masivo_reducida_05-$masivo_reducida_05e) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( (($masivo_reducida_05-$masivo_reducida_05e) * 0.005) ,2,'.',',') }}</th>
                                      <th style="color:black;">{{ number_format( ((($masivo_reducida_05-$masivo_reducida_05e) * 0.005) + ($masivo_reducida_05-$masivo_reducida_05e) ) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 1%</td>
                                    <th style="color:black;">{{ number_format(($masivo_reducida_1-$masivo_reducida_1e) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format((($masivo_reducida_1-$masivo_reducida_1e)*0.01) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format(((($masivo_reducida_1-$masivo_reducida_1e)*0.01) + ($masivo_reducida_1-$masivo_reducida_1e)) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 2%</td>
                                    <th style="color:black;">{{ number_format(($masivo_reducida_2 - $masivo_reducida_2e) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format((($masivo_reducida_2 - $masivo_reducida_2e) * 0.02) ,2,'.',',') }}</th>
                                       <th style="color:black;">{{ number_format(((($masivo_reducida_2 - $masivo_reducida_2e) * 0.02) + ($masivo_reducida_2 - $masivo_reducida_2e)) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 4%</td>
                                    <th style="color:black;">{{ number_format(($masivo_reducida_4 + $masivo_transitorio_4 - $masivo_reducida_4e-$masivo_transitorio_4e ) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( ((($masivo_reducida_4 + $masivo_transitorio_4 - $masivo_reducida_4e-$masivo_transitorio_4e ))*0.04) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( ((($masivo_reducida_4 + $masivo_transitorio_4 - $masivo_reducida_4e-$masivo_transitorio_4e )*0.04) + ($masivo_reducida_4 + $masivo_transitorio_4 - $masivo_reducida_4e-$masivo_transitorio_4e )) ,2,'.',',') }}</th>
                                    </tr>
                                
                                
                                <tr>
                                      <th colspan="2"  style="color:black;">IVA Reducido al 8%</td>
                                    <th style="color:black;">{{ number_format(($masivo_transitorio_8-$masivo_transitorio_8e) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( (($masivo_transitorio_8-$masivo_transitorio_8e) *0.08) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( ((($masivo_transitorio_8-$masivo_transitorio_8e) *0.08) + ($masivo_transitorio_8-$masivo_transitorio_8e)) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                      <th colspan="2"  style="color:black;">IVA Reducido al 13%</td>
                                    <th style="color: black; "><i><b>{{ number_format(($masivo_gravado_13-$masivo_gravado_13e) ,2,'.',',') }}</b></i></th>
                                     <th style="color: black; "><i><b>{{ number_format( (($masivo_gravado_13-$masivo_gravado_13e) *.13) ,2,'.',',') }}</b></i></th>
                                       <th style="color: black; "><i><b>{{ number_format( ((($masivo_gravado_13-$masivo_gravado_13e) *.13) + ($masivo_gravado_13-$masivo_gravado_13e))  ,2,'.',',') }}</b></i></th>
                                    </tr>
                               
                                <tr>
                                    <th colspan="2"  style="color:black;">Total IVA</td>
                                    <th style="color:black;">{{ number_format($masivo_total_iva ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total Otros Cargos</td>
                                    <th style="color:black;">{{ number_format($masivo_total_otroc ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total IVA Devuelto</td>
                                    <th style="color:black;">{{ number_format($masivo_devuelto ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total Venta Exonerada DGH</td>
                                    <th style="color:black;">{{ number_format($masivo_exonerado ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total Venta</td>
                                    <th style="color:black;">{{ number_format($masivo_total_comprobante  ,2,'.',',') }}</th>
                           </tfoot>
                        </table>
                    </div>
                </div>
@endsection
@section('myjs')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#documentos_data').DataTable(
            {     
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]],
                dom: 'Bfrtip',
                buttons: {
                    buttons: [
                    {
                    extend: 'excelHtml5',
                    footer: true,
                    customize: (xlsx, config, dataTable) => {
                        let sheet = xlsx.xl.worksheets['sheet1.xml'];
                        let footerIndex = $('sheetData row', sheet).length;
                        let $footerRows = $('tr', dataTable.footer());

                        // If there are more than one footer rows
                        if ($footerRows.length > 1) {
                            // First header row is already present, so we start from the second row (i = 1)
                            for (let i = 1; i < $footerRows.length; i++) {
                                // Get the current footer row
                                let $footerRow = $footerRows[i];

                                // Get footer row columns
                                let $footerRowCols = $('th', $footerRow);

                                // Increment the last row index
                                footerIndex++;

                                // Create the new header row XML using footerIndex and append it at sheetData
                                $('sheetData', sheet).append(`
                                    <row r="${footerIndex}">
                                        ${$footerRowCols.map((index, el) => `
                                            <c t="inlineStr" r="${String.fromCharCode(65 + index)}${footerIndex}" s="2">
                                                <is>
                                                    <t xml:space="preserve">${$(el).text()}</t>
                                                </is>
                                            </c>
                                        `).get().join('')}
                                    </row>
                                `);
                            }
                        }
                    }
                    }],
                    dom:{
                        button:{
                            tag:"button",
                            className:"btn btn-danger mt-4"
                        },
                        buttonLiner: {
                            tag: null
                        } 
                    }
                }
        }
    );
});

</script>
@endsection