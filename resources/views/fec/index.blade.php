@extends('layouts.app', ['page' => __('Facturas de Compras'), 'pageSlug' => 'feCompras'])
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
                    <form method="post" action="{{ route('filtro.feci') }}" autocomplete="off" id="filtro_factura">
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
                            <h4 class="card-title">{{ __('Facturas de Compras') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('fec.create') }}" class="btn btn-sm btn-primary">{{ __('Nueva Factura Compra') }}</a>
                            <a href="{{ route('consultardoc.fec') }}" class="btn btn-sm btn-primary">{{ __('Consultar Documentos') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_fec_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Fact.Ref') }}</th>
                                <th scope="col">{{ __('Nombre Proveedor') }}</th>
                                <th scope="col">{{ __('Condicion Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                <th scope="col">{{ __('Estado Hacienda') }}</th>
                                <th scope="col">{{ __('Acciones') }}</th>
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
                                        $valor = App\Facelectron::where('idsales', $sale->idsale)->get();
                                        if (count($valor)) {
                                            if(is_null($valor[0]->estatushacienda)){
                                                $estatus = "<button  type='button' class='btn btn-sm btn-warning'>Sin Consultar</button>";
                                            }else{
                                                switch ($valor[0]->estatushacienda) {
                                                    case 'aceptado':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-success'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'recibido':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-success'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'procesando':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-warning'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'rechazado':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-danger'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                }
                                            }
                                        }else{
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Proceso</button>";
                                        }

                                    ?>
                                    <tr>
                                    	<td>{{ $sale->numero_documento }}</td>
                                        <td>{{ $sale->referencia_compra }}</td>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        <td class="text-right">{{ number_format($sale->total_neto, 2, ',', '.') }}</td>  
                                        <td class="text-right">{{ number_format($sale->total_descuento, 2, ',', '.') }}</td>  
                                        <td class="text-right">{{ number_format($sale->total_impuesto, 2, ',', '.') }}</td>  
                                        <td class="text-right">{{ number_format($sale->total_comprobante, 2, ',', '.') }}</td>  
                                        <td><?php echo $estatus; ?></td>
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if(count($valor))
                                                        @if(is_null($valor[0]->estatushacienda))
                                                            <a href="{{ url('reenviar-doc', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Reenviar Documento') }}</a>
                                                        @endif
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#mensajeHacienda" data-id="{{ $sale->idsale }}" id="Haciendamodal">{{ __('Mensaje Hacienda') }}</a>
                                                        <a href="{{ url('donwload-xml', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Descargar XML') }}</a>
                                                        @if(!is_null($valor[0]->respuesta_xml))
                                                            <a href="{{ url('donwload-xml-respuesta', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Descargar Respuesta XML') }}</a>
                                                             <a href="{{ url('envia-xml', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>
                                                             <a href="{{ url('pdf-factura', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('fec.edit', $sale->idsale) }}" class="dropdown-item">{{ __('Editar FEC') }}</a>
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
@include('modals.mensajeHacienda')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">


    $(document).ready(function() {
        $('#ver_fec_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]]
            }
        );
        
        $('body').on('click', '#Haciendamodal', function () {
            var saleid = $(this).data('id');
            var APP_URL = {!! json_encode(url('/hacienda')) !!};
             $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idsales:saleid},

                dataType: 'json',

                success:function(response){
                    var num_doc = $('#input-clave').val(response['success'][0].clave);
                    var sale = $('#idsale_hacienda').val(response['success'][0].idsales);
                    num_doc.prop("disabled",true);
                    var estatus = $('#hacienda-title').empty();
                    if (response['success'][0].estatushacienda === 'aceptado') {
                        estatus.append('Documento #' + response['success'][0].numdoc + '<button  type="button" class="btn btn-sm btn-success">' + response['success'][0].estatushacienda + '</button>');
                    }else{
                           estatus.append('Documento #' + response['success'][0].numdoc + '<button  type="button" class="btn btn-sm btn-danger">' + response['success'][0].estatushacienda + '</button>');
                    }
                    var respuesta = $('#respuesta_h').empty();
                    respuesta.append(response['success'][0].mensajehacienda);
                }            });
        });

        $('#descarga_respuesta').on('click', function () {
            var saleid = $('#idsale_hacienda').val();
            var URL = {!! json_encode(url('donwload-xml-respuesta')) !!} + '/' + saleid;
            $.ajax({

                type:'get',

                url: URL,

                dataType: 'json',

                success:function(response){
                    alert(response);
                    //console.log(response);
                }
            });
        });

    });
</script>
@endsection
