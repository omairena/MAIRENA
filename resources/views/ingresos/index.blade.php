@extends('layouts.app', ['page' => __('Registro Ingresos Bancarios.'), 'pageSlug' => 'tiquetes'])
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
                            <h4 class="card-title">{{ __('Ingresos por Registrar') }}</h4>
                        </div>
                       
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_tiquetes_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Fecha Creación') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Condición Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                               
                                <th scope="col">{{ __('Total Documento') }}</th>
                                <th scope="col">{{ __('Estado Hacienda') }}</th>
                                 <th scope="col">{{ __('Tipo Doc') }}</th>
                                 
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
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Proceso</button>";
                                        }


                                    ?>
                                    <tr>
                                    	<td>{{ $sale->numero_documento }}</td>
                                        <td>{{ $sale->fecha_creada }}</td>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        
                                        <td class="text-right" >{{ number_format($sale->total_comprobante,  2, '.', ',') }}</td>
                                        <td><?php echo $estatus; ?></td>
                                        @switch($sale->tipo_documento)
                                            @case(01)
                                                <td>Fact</td>
                                            @break
                                            @case(02)
                                                <td>N.D.</td>
                                            @break
                                            @case(03)
                                                <td>N.C.</td>
                                            @break
                                             @case(04)
                                                <td>Tiquete</td>
                                            @break
                                        @endswitch
                                        
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    
                                                        <a href="{{ url('pdf-factura', ['idsales' => $sale->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                       
                                                    <a href="{{ route('ingresos.procesar', ['id' => $sale->idsale]) }}" class="dropdown-item">{{ __('Procesar') }}</a>
                                                    <a href="{{ route('ingresos.rechazar', ['id' => $sale->idsale]) }}" class="dropdown-item">{{ __('Rechazar') }}</a>
                                                     

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
    });
</script>
@endsection

