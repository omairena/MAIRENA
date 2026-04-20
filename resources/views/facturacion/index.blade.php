@extends('layouts.app', ['page' => __('Facturas del Sistema'), 'pageSlug' => 'allfacturas'])
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
                            <h4 class="card-title">{{ __('Filtro de Busquedas') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <form method="post" action="{{ route('filtro.factura') }}" autocomplete="off" id="filtro_factura">
    @csrf
<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
        <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required>
        @include('alerts.feedback', ['field' => 'fecha_desde'])
    </div>

    <div class="col-md-3">
        <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
        <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required>
        @include('alerts.feedback', ['field' => 'fecha_hasta'])
    </div>
</div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-control-label" for="cliente">{{ __('Filtrar por Cliente') }}</label>
            <select class="form-control" id="cliente" name="cliente" required>
                <option value="0">{{ __('-- Clientes --') }}</option>
                @foreach($cxcobrar as $cxc)
                    <option value="{{ $cxc->idcliente }}" {{ old('cliente') == $cxc->idcliente ? 'selected' : '' }}>{{ $cxc->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-control-label" for="estado">{{ __('Estado Documento') }}</label>
            <select class="form-control" id="estado" name="estado" required>
                <option value="0">{{ __('-- Todos --') }}</option>
                <option value="1" {{ old('estado') == 1 ? 'selected' : '' }}>{{ __('-- Anulados --') }}</option>
                <option value="2" {{ old('estado') == 2 ? 'selected' : '' }}>{{ __('-- Sin Anular --') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-control-label" for="tipo_doc">{{ __('Tipo Documento') }}</label>
            <select class="form-control" id="tipo_doc" name="tipo_doc" required>
                <option value="0">{{ __('-- Todos --') }}</option>
                <option value="01" {{ old('tipo_doc') == "01" ? 'selected' : '' }}>{{ __('Factura Electronica') }}</option>
                <option value="02" {{ old('tipo_doc') == "02" ? 'selected' : '' }}>{{ __('Nota Debito') }}</option>
                <option value="03" {{ old('tipo_doc') == "03" ? 'selected' : '' }}>{{ __('Nota Credito') }}</option>
                <option value="04" {{ old('tipo_doc') == "04"? 'selected' : '' }}>{{ __('Tiquete') }}</option>
                <option value="08" {{ old('tipo_doc') == "08" ? 'selected' : '' }}>{{ __('Factura Compra') }}</option>
                <option value="09" {{ old('tipo_doc') == "09" ? 'selected' : '' }}>{{ __('Factura Exportacion') }}</option>

            </select>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-success mt-4">{{ __('Filtrar') }}</button>
    </div>
</form>
 <div class="col-md-12">
<form method="post" action="{{ route('filtro.numero_factura') }}" autocomplete="off" id="filtro_factura">  
    @csrf  
    <div class="row mb-2">  
        <div class="col-mb-6">  
            <label class="form-control-label" for="input-numero_factura">{{ __('Filtrar Por Numero de Documento') }}</label>  
            <input type="numero_factura" name="numero_factura" id="input-numero_factura" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('numero_factura') }}" required>  
            @include('alerts.feedback', ['field' => 'numero_factura'])  
        </div>  
 

    <div class="text-center">  
        <button type="submit" class="btn btn-info mt-4">{{ __('Consultar') }}</button>  
    </div> 
    </div>
</form> 
 </div>
                </div>
                 <div class="col-md-6">
                     
                     
                     </div>  
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Facturas Construidas') }}</h4>
                              <a href="{{ url('limpiar') }}" class="btn btn-sm btn-primary">{{ __('Limpiar Facturas En Proceso') }}</a>
                        </div>
                        
                        <div class="col-4 text-right">
                            
                            <div class="dropdown">
                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b style="color: white;">Acciones</b> &nbsp;
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a href="{{ route('facturar.create') }}" class="dropdown-item">{{ __('Nueva Factura') }}</a>
                                    <a href="{{ route('consultardoc.index') }}" class="dropdown-item">{{ __('Consultar Documentos') }}</a>
                                    <a href="{{ route('reenviar.correo') }}" class="dropdown-item">{{ __('Enviar Correos Pendientes') }}</a>
                                    <!-- <a href="{{ url('limpiar') }}" class="dropdown-item">{{ __('Limpiar Facturas') }}</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_facturas_datatable">
                            <thead class=" text-primary">
                                	<th scope="col">{{ __('Id Interno') }}</th>
                            	<th scope="col">{{ __('Doc #') }}</th>

                                <th scope="col">{{ __('Fecha Creaci贸n') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Condici贸n Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                 <th scope="col">{{ __('Total Otros Cargos ') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                <th scope="col">{{ __('Estado Hacienda') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                 	<?php
                                 //	dd($sale);
                                 	if($sale->tipo_documento == '01'){
                                 	    $tipo_doc="Fact.";
                                 	}
                                 		if($sale->tipo_documento == '02'){
                                 	    $tipo_doc="N.D.";
                                 	}
                                 	if($sale->tipo_documento == '03'){
                                 	    $tipo_doc="N.C.";
                                 	}
                                 	if($sale->tipo_documento == '04'){
                                 	    $tipo_doc="Tckte";
                                 	}
                                 	if($sale->tipo_documento == '08'){
                                 	    $tipo_doc="FEC";
                                 	}
                                 	if($sale->tipo_documento == '09'){
                                 	    $tipo_doc="FEE.";
                                 	}
                                 	if($sale->tipo_documento == '95'){
                                 	    $tipo_doc="Devol. Ord. Ped";
                                 	}
                                 	if($sale->tipo_documento == '96'){
                                 	    $tipo_doc="Orden Pedido";
                                 	}
                                 		if($sale->tipo_documento == '10'){
                                 	    $tipo_doc="REP.";
                                 	}

                                 	if ($sale->referencia_sale > 0 && ($sale->tipo_documento == '01' || $sale->tipo_documento == '04')) {
                                 	    //dd($sale->idsale );
                                  $nc = App\Sales::where('idsale', $sale->referencia_sale)->get();
                                  $nota='Anulado por NC#: '. $nc[0]->numero_documento;
                                 // dd($nc[0]->numero_documento);
                                 	}else if($sale->referencia_sale > 0 && ($sale->tipo_documento == '02' || $sale->tipo_documento == '03')){
                                 	      $nc = App\Sales::where('idsale', $sale->referencia_sale)->get();
                                 	     $nota='Anula a Doc#: '. $nc[0]->numero_documento;
                                 	}else{
                                 	   $nota="";
                                 	}



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
                                                    case 'pendiente':
                                                          $estatus ="<button  type='button' class='btn btn-sm btn-warning'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                }

                                            }
                                        }else{
                                            if($sale->tipo_documento == '95' or $sale->tipo_documento == '96'){
                                                $estatus ="<button  type='button' class='btn btn-sm btn-danger'>No Aplica</button>";
                                            }else{
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Proceso</button>";
                                        }
                                        }


                                      $total_impuesto =  number_format($sale->total_impuesto, 2, '.', '');
                                      
                                      
                                      //dd($sale->idsale);
                                    ?>
                                    <tr>
                                        <td>{{$sale->idsale  }}</td>
                                    	<td>{{ $tipo_doc }} - {{$sale->numero_documento  }}</td>
                                        <td>{{ $sale->fecha_creada }}</td>
                                        <td>{{ $usuario->nombre }}   {{ $nota }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        <td class="text-right">{{ number_format($sale->total_neto + $sale->total_descuento , 2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($sale->total_descuento,  2, '.', ',') }}</td>
                                        <td class="text-right" >{{ number_format($sale->total_impuesto,  2, '.', ',') }}</td>
                                         <td class="text-right" >{{ number_format($sale->total_otros_cargos,  2, '.', ',') }}</td>
                                        <td class="text-right" >{{ number_format($sale->total_comprobante,  2, '.', ',') }}</td>
                                        <td><?php echo $estatus; ?></td>
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if(count($valor))
                                                    @if(is_null($valor[0]->mensajehacienda))
                                                        <a href="{{ url('reenviar-doc', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Reenviar Documento') }}</a>
                                                    @endif

                                                    <a href="{{ url('pdf-factura', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                     @if($valor[0]->estatushacienda=='aceptado')
                                                     @if(!in_array($sale->tipo_documento, ['02', '03', '08']))
                                                    <a href="{{ url('notaCredito/create', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar Nota de Cr茅dito') }}</a>
                                                   <a href="{{ url('notaDebito/create', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar Nota de D茅bito') }}</a>
                                                    <a href="{{ url('convertir-fr', $sale->idsale) }}" class="dropdown-item">{{ __('Re-Facturar') }}</a>
                                                    <a href="{{ url('convertir-ver', $sale->idsale) }}" class="dropdown-item">{{ __('Ver') }}</a>
                                                      @endif
                                                    @endif
                                                      @if($valor[0]->estatushacienda=='rechazado')
                                                      <a href="{{ url('convertir-fr', $sale->idsale) }}" class="dropdown-item">{{ __('Re-Facturar') }}</a>
                                                      @endif
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#mensajeHacienda" data-id="{{ $sale->idsale }}" id="Haciendamodal">{{ __('Mensaje Hacienda') }}</a>
                                                    <a href="{{ url('envia-xml', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>
                                                    <a href="{{ url('donwload-xml', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Descargar XML') }}</a>

                                                    @if(!is_null($valor[0]->respuesta_xml))
                                                    <a href="{{ url('donwload-xml-respuesta', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Descargar Respuesta MH XML') }}</a>
                                                    @endif

                                                      @if($valor[0]->estatushacienda=='pendiente')
                                                    <a href="{{ url('reenviar-doc', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Reenviar Documento') }}</a>

                                                     @endif
                                                    @endif
                                                      @if(empty ($valor[0]->estatushacienda))
                                                     <a href="{{ url('convertir-fr', $sale->idsale) }}" class="dropdown-item">{{ __('Re-Facturar') }}</a>
                                                     <a href="{{ url('reenviar-doc', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Reenviar Documento') }}</a>
                                                     <a href="{{ url('deletefac', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Eliminar') }}</a>

                                                     @endif
                                                    <a href="{{ route('facturar.imprimir', ['id' => $sale->idsale]) }}" class="dropdown-item">{{ __('Imprimir') }}</a>
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
        $('#ver_facturas_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "ASC" ]]
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
                }
            });
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        // Primer día del mes anterior
        const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        // Último día del mes anterior
        const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        
        // Formatear las fechas como YYYY-MM-DD para el input de tipo date
        const formattedFirstDay = firstDayOfLastMonth.toISOString().split('T')[0];
        const formattedLastDay = lastDayOfLastMonth.toISOString().split('T')[0];

        // Asignar las fechas a los inputs
        document.getElementById('input-fecha_desde').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta').value = formattedLastDay;
    });
</script>
@endsection
