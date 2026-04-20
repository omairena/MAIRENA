@extends('layouts.app', ['page' => __('Egresos'), 'pageSlug' => 'receptor'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
<div class="row">
        <div class="col-md-12">
           
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Registro de Egresos.') }}</h4>
                        </div>
                       
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_receptor_datatable">
                            <thead class=" text-primary">
                              <th scope="col">{{ __('Consecutivo') }}</th>
                                 <th scope="col">{{ __('Doc Recibido') }}</th>
                                <th scope="col">{{ __('Identificacion Emisor') }}</th>
                                <th scope="col">{{ __('Nombre Emisor') }}</th>
                                
                               
                                <th scope="col">{{ __('Fecha XML') }}</th>
                              
                                <th scope="col">{{ __('Total Comprobante') }}</th>
                               
                                <th scope="col">Acciones</th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                 	<?php
                                    $num_receptor = substr($sale->consecutivo, 10,10);
                                    $num_clave = substr($sale->clave, 31,10);
                                    switch ($sale->estatus_hacienda) {
                                        case 'aceptado':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-success'>Aceptado</button>";
                                        break;
                                        case 'recibido':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-success'>Recibido</button>";
                                        break;
                                        case 'rechazado':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-danger'>Rechazado</button>";
                                        break;
                                        case 'procesando':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>procesando</button>";
                                        break;
                                        default:
                                            $estatus = 'sin enviar';
                                        break;
                                    }
                                    ?>
                                    <tr>
                                      <td><?php echo $num_clave; ?></td>
                                        @switch($sale->tipo_documento_recibido)
                                            @case(01)
                                                <td>Fact</td>
                                            @break
                                            @case(02)
                                                <td>N.D.</td>
                                            @break
                                            @case(03)
                                                <td>N.C.</td>
                                            @break
                                        @endswitch
                                    	<td>{{ $sale->cedula_emisor }}</td>
                                        <td>{{ $sale->nombre_emisor }}</td>

                                        
                                      
                                        <td>{{ $sale->fecha_xml_envio }}</td>
                                      
                                        <td class="text-right">
    {{ number_format($sale->total_comprobante, 2, '.', ',') }}
</td>
                                        
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                  
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#showDocument" data-id="{{ $sale->idreceptor }}" id="clickshowDocument">{{ __('Ver documento recibido') }}</a>
                                                    
                                                    
                                                    <a href="{{ route('egresos.procesar', ['id' => $sale->idreceptor]) }}" class="dropdown-item">{{ __('Procesar') }}</a>
                                                    <a href="{{ route('egresos.rechazar', ['id' => $sale->idreceptor]) }}" class="dropdown-item">{{ __('Rechazar') }}</a>
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
@include('modals.mensajeHaciendaReceptor')
@include('modals.verDocumentoRecibido')


@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_receptor_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 5, "desc" ]]
            }
        );
        var table = $('#recibido_data').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );

        $('body').on('click', '#clickModalReceptor', function () {
            var saleid = $(this).data('id');
            var APP_URL = {!! json_encode(url('/haciendaReceptor')) !!};
             $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idsales:saleid},

                dataType: 'json',

                success:function(response){
                    var num_doc = $('#input-clave').val(response['success'].clave);
                    var sale = $('#idsale_hacienda').val(response['success'].idsales);
                    num_doc.prop("disabled",true);
                    var estatus = $('#hacienda-title').empty();
                    if (response['success'].estatus_hacienda === 'aceptado') {
                        estatus.append('Consecutivo #' + response['success'].consecutivo + '<button  type="button" class="btn btn-sm btn-success">' + response['success'].estatus_hacienda + '</button>');
                    }else{
                           estatus.append('Consecutivo #' + response['success'].consecutivo + '<button  type="button" class="btn btn-sm btn-danger">' + response['success'].estatus_hacienda + '</button>');
                    }
                    var respuesta = $('#respuesta_h').empty();
                    respuesta.append(response['success'].respuesta_hacienda);
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
        $('body').on('click', '#clickcambiarClasif', function () {
            var idreceptor = $(this).data('id');
            var APP_URL = {!! json_encode(url('/showClasificacion')) !!};
            $('#idreceptor_modal').val(idreceptor);
             $.ajax({

                type:'GET',

                url: APP_URL,


                data:{idreceptor:idreceptor},

                dataType: 'json',

                success:function(response){
                    //console.log(response.clasifica_d151);
                    $('#tipo_clasificacion').val(response.clasifica_d151);
                }
            });
        });
        //submitClasificacion
        $('#submitClasificacion').on('click', function () {
            var clasificacion = $('#tipo_clasificacion').val();
            var idreceptor = $('#idreceptor_modal').val();
            var APP_URL = {!! json_encode(url('/editModalClasificacion')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,


                data:{idreceptor:idreceptor, clasificacion:clasificacion},

                dataType: 'json',

                success:function(response){
                    console.log(response);
                    location.reload();
                }
            });
        });
    });
</script>
@endsection
