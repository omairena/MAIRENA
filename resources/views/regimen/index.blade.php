@extends('layouts.app', ['page' => __('Facturas Regimen Simplificado'), 'pageSlug' => 'simplificado'])
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
                    <form method="post" action="{{ route('filtro.regimen') }}" autocomplete="off" id="filtro_factura">
                    @csrf
                        <div class="row">
                            <div class="col">
                                <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                                <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde', $fecha_desde) }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                                @include('alerts.feedback', ['field' => 'fecha_desde'])
                                <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                                <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta', $fecha_hasta) }}" required style="display: inline !important; width: 40% !important;">
                                @include('alerts.feedback', ['field' => 'fecha_hasta'])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                @if(Auth::user()->config_u[0]->usa_op > 0)

                                    <label class="form-control-label" for="seleccion_regimen">{{ __('Selección de Documento') }}</label>
                                    <select class="form-control" id="seleccion_regimen" name="seleccion_regimen" value="{{ old('seleccion_regimen') }}" required style="display: inline !important; width: 30% !important; margin-right: 40px;">
                                        <option value="2">-- Todos los Documentos --</option>
                                        <option value="0">Regimen Simplificado</option>
                                        <option value="1">Orden de Pedido</option>
                                    </select>
                                    <label class="form-control-label" for="estatus_op">{{ __('Se Envio a factura electronica?') }}</label>
                                    <select class="form-control" id="estatus_op" name="estatus_op" value="{{ old('estatus_op') }}" required style="display: inline !important; width: 10% !important;">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                @endif

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Filtrar') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Facturas de Regimen Simplificado Construidas') }}</h4>
                        </div>
                    </div>
                    
                </div>
                 <div class="col-4 text-left">
                            <div class="dropdown">
                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b style="color: white;">Acciones</b> &nbsp;
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    
                                    <a href="{{ route('consultardoc.index') }}" class="dropdown-item">{{ __('Consultar Documentos') }}</a>
                                    <a href="{{ route('reenviar.correo') }}" class="dropdown-item">{{ __('Enviar Correos Pendientes') }}</a>
                                    <a href="{{ url('limpiar') }}" class="dropdown-item">{{ __('Limpiar Facturas') }}</a>
                                </div>
                            </div>
                        </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_facturas_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Fecha Creación') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Condición Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                @if(Auth::user()->config_u[0]->usa_op > 0)
                                    <th scope="col">{{ __('Se convirtio a FE?') }}</th>
                                    <th scope="col">{{ __('#Doc Factura') }}</th>
                                    <th scope="col">{{ __('¿Se envia Correo?') }}</th>
                                @endif
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                 	<?php
                                 		$usuario = App\Cliente::find($sale->idcliente);
                                        if ($sale->condicion_venta === '01') {
                                        	$condicion = 'Contado';
                                        }else{
                                        	$condicion = 'Credito';
                                        }
                                    ?>
                                    <tr>
                                    	<td>{{ $sale->numero_documento }}</td>
                                         <td>{{ $sale->fecha_creada }}</td>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        <td class="text-right">{{ $sale->total_neto }}</td>
                                        <td class="text-right">{{ $sale->total_descuento }}</td>
                                        <td class="text-right">{{ $sale->total_impuesto }}</td>
                                        <td class="text-right">{{ $sale->total_comprobante }}</td>
                                        @if(Auth::user()->config_u[0]->usa_op > 0)

                                            @if($sale->estatus_op > 0)

                                                <td class="text-center">Si</td>
                                            @else

                                                <td class="text-center">No</td>
                                            @endif
                                            <td class="text-center">{{ $sale->num_documento_convertido }}</td>
                                            @if($sale->desea_enviarcorreo > 0)

                                                <td class="text-center">Si</td>
                                            @else

                                                <td class="text-center">No</td>
                                            @endif
                                        @endif
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                  @if($sale->estatus_sale === 2)
                                                    <a href="{{ url('pdf-regimen', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                    @if($sale->estatus_op === 0)
                                                        <a href="{{ url('regimen/notaCredito/create', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar Nota de Crédito') }}</a>
                                                    @endif
                                                    <a href="{{ url('convertir-fr', $sale->idsale) }}" class="dropdown-item">{{ __('Editar y Re-Facturar') }}</a>
                                                    <a href="{{ url('convertir-automatica-fr', $sale->idsale) }}" class="dropdown-item">{{ __('Re-Facturar') }}</a>
                                                    <a href="{{ route('facturar.imprimir', ['id' => $sale->idsale]) }}" class="dropdown-item">{{ __('Imprimir') }}</a>
                                                  @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_facturas_datatable').DataTable(
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
