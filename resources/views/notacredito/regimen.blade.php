@extends('layouts.app', ['page' => __('Notas de Crédito Simplificada'), 'pageSlug' => 'notacreditoregimen'])
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
                    <form method="post" action="{{ route('filtro.notaregimen') }}" autocomplete="off">
                    @csrf
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                            <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                            @include('alerts.feedback', ['field' => 'fecha_desde'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                            <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Filtrar') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Notas de Créditos') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('consultarnc.index') }}" class="btn btn-sm btn-primary">{{ __('Consultar Documentos') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_notac_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Condición Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
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
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        <td class="text-right">{{ $sale->total_neto }}</td>
                                        <td class="text-right">{{ $sale->total_descuento }}</td>
                                        <td class="text-right">{{ $sale->total_impuesto }}</td>
                                        <td class="text-right">{{ $sale->total_comprobante }}</td>
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ url('pdf-regimen', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
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
@include('modals.mensajeHacienda')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_notac_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
    });
</script>
@endsection
