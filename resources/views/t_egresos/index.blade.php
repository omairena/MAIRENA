@extends('layouts.app', ['page' => __('Transacciones Egresos'), 'pageSlug' => 'tiquetes'])
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
                    <form method="post" action="{{ route('filtro.tr') }}" autocomplete="off" id="filtro_factura">
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
                            <h4 class="card-title">{{ __('Transacciones Construidas') }}</h4>
                          
                             <a href="{{ route('Egreso_manual.index') }}" class="btn btn-sm btn-primary">{{ __('Nuevo Egreso') }}</a>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_tiquetes_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Factura') }}</th>
                                <th scope="col">{{ __('Fecha Creación') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                
                                <th scope="col">{{ __('Total Documento') }}</th>
                                <th scope="col">{{ __('Cuenta') }}</th>
                                <th scope="col">{{ __('Clasificacion') }}</th>
                                <th scope="col">{{ __('Ref. Bancos') }}</th>
                                 <th scope="col">User</th>
                                <th scope="col">Obs</th>
                                <th scope="col">Acciones</th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <?php
                                        $usuario = App\Cliente::find($sale->idcliente);
                                       
                                        $banco = App\Bancos::find($sale->id_bancos);
                                      
                                        if ($sale->condicion_venta === '01') {
                                            $condicion = 'Contado';
                                        }else{
                                            $condicion = 'Credito';
                                        }
                                       


                                    ?>
                                
                                    <tr>
                                    	<td>{{ $sale-> factura  }}</td>
                                        <td>{{ $sale-> fecha }}</td>
                                        <td>{{ $usuario->nombre }}</td>
                                      
                                        <td >( {{ $sale-> signo  }} ) {{ number_format($sale->monto, 2, '.', ',') }}</td>
                                        <td>{{ $banco-> cuenta }}</td>
                                        <td>{{ $sale->clasificacion_recep }}</td>
                                         <td>{{ $sale->referencia }}</td>
                                           <td>{{ $sale->user }}</td>
                                       <td > {{ $sale->obs }}</td>
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                   <?php
                                                  if($sale->idreceptor>0){
                                                   ?>
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#showDocument" data-id="{{ $sale->idreceptor }}" id="clickshowDocument">{{ __('Ver documento recibido') }}</a>
                                                         <?php
                                                  }
                                                   ?>
                                                        
                                                        <a href="{{ url('t_egresos/deleted', ['idsales' => $sale->id_tr_bancos]) }}" class="dropdown-item">{{ __('Eliminar') }}</a>
                                                       
                                                        
                                                    

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
@include('modals.verDocumentoRecibido')
@endsection

@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_tiquetes_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
       $('body').on('click', '#HaciendamodalTiq', function () {
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
        
         $('body').on('click', '#clickshowDocument', function () {
            var idreceptor = $(this).data('id');
            var APP_URL = {!! json_encode(url('/documentReceptor')) !!};

             $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idreceptor:idreceptor},

                dataType: 'json',

                success:function(response){
                    //console.log(response);
                    //tipo documento
                    var tipo = $('#modalDocumento').empty();
                    tipo.append(response['success'].documento);
                    //Clave documento
                    var clave = $('#modalClavedoc').empty();
                    clave.append(response['success'].clave);
                    //Fecha documento
                    var fecha = $('#modalFechadoc').empty();
                    fecha.append(response['success'].fecha);
                    //Codigo Actividad

                    var codigo_activ= $('#modalActividad').empty();
                    codigo_activ.append(response['success'].codigo_activ);
                    //Tipo de Cambio
                    var cambio = $('#modalTipoCambio').empty();
                    cambio.append(response['success'].cambio);
                    //Identificacion Emisor
                    var identificacion = $('#modalIdentificacion').empty();
                    identificacion.append(response['success'].identificacion);
                    //Nombre Emisor
                    var nombre = $('#modalNombre').empty();
                    nombre.append(response['success'].nombre_emisor);
                    //Correo Emisor
                    var correo = $('#modalCorreo').empty();
                    correo.append(response['success'].correo_emisor);
                    //Seccion de detalle de documento
                    var content_body = '';
                    var recorrido = response['success'].detalle;
                    for (var i = 0; i < recorrido.length; i++) {
                        content_body += '<tr>';
                        content_body += '<td>'+recorrido[i]['codigo_comercial']+'</td>';
                        content_body += '<td>'+recorrido[i]['cabys']+'</td>';
                        content_body += '<td>'+recorrido[i]['descripcion']+'</td>';
                        content_body += '<td>'+recorrido[i]['cantidad']+'</td>';
                        content_body += '<td>'+recorrido[i]['unidad_medida']+'</td>';
                        content_body += '<td>'+recorrido[i]['subtotal']+'</td>';
                        content_body += '<td>'+recorrido[i]['tarifa']+'</td>';
                        content_body += '<td>'+recorrido[i]['impuesto_mto']+'</td>';
                        content_body += '<td>'+recorrido[i]['total_linea']+'</td>';
                        content_body += '</tr>';
                    }
                    $('#recibido_data tbody').html(content_body);
                    //Seccion de Totales
                    var content = '';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Gravado:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_gravado+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Exento:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_exento+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Venta:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_venta+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Descuento:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_descuento+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Venta Neta:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_neta+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Impuesto:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_impuesto+'</th>';
                    content += '</tr>';
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Iva Devuelto:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_iva_devuelto+'</th>';
                    content += '</tr>'
                    content += '<tr>';
                    content += '<th style="color:black; text-align:right;" colspan="8">Total Comprobante:</th>';
                    content += '<th style="color:black; text-align:right;" colspan="2">'+response['success'].total_comprobante+'</th>';
                    content += '</tr>'
                    $('#recibido_data tfoot').html(content);
                }
            });
        });
        
        
    });
</script>
@endsection

