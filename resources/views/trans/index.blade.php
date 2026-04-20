@extends('layouts.app', ['page' => __('Transacciones Ingresos'), 'pageSlug' => 'tiquetes'])
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
                          
                             <a href="{{ route('ing_manual.index') }}" class="btn btn-sm btn-primary">{{ __('Nuevo Ingreso') }}</a>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_tiquetes_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Factura') }}</th>
                                <th scope="col">{{ __('Fecha Creaci籀n') }}</th>
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
        // Obtener el usuario (cliente) solo si existe
        $usuario = App\Cliente::find($sale->idcliente);
        $nombreCliente = $usuario ? $usuario->nombre : 'N/A'; // Manejo al no encontrar cliente

        // Obtener la factura asociada
        // Asumiendo que $factura es una colecci車n de resultados
        $factura = App\Facelectron::where('idfacelectron', $sale->idfacelectron)->get();
        
        if ($factura->isNotEmpty()) {
            $associatedFactura = App\Facelectron::find($factura[0]->idfacelectron);
        } else {
            $associatedFactura = null; // No se encontr車 ninguna factura
        }

        $banco = App\Bancos::find($sale->id_bancos);
        $clasificaciones = App\Clasificaciones::find($sale->clasificacion);

        // Condici車n de venta
        $condicion = $sale->condicion_venta === '01' ? 'Contado' : 'Credito';
    ?>
                                
                                  <tr>
        <td>{{ $sale->factura }}</td>
        <td>{{ $sale->fecha }}</td>
        <td>{{ $nombreCliente }}</td> <!-- Usa nombreCliente para mostrar -->
        <td >({{$sale->signo}}) {{ number_format($sale->monto, 2, '.', ',') }}</td>
        <td>{{ optional($banco)->cuenta ?? 'N/A' }}</td> <!-- Manejo al no encontrar banco -->
        <td>{{ optional($clasificaciones)->descripcion ?? 'N/A' }}</td> <!-- Manejo al no encontrar clasificaci車n -->
        <td>{{ $sale->referencia }}</td>
        <td>{{ $sale->user }}</td>
        <td> {{ $sale->obs }}</td>
        <td>
            <div class="dropdown">
                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                    <?php if($sale->idsales > 0): ?>
                        <a href="{{ url('pdf-factura', ['idsales' => $sale->idsales]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                    <?php endif; ?>
                    <a href="{{ url('tr/deleted', ['idsales' => $sale->id_tr_bancos]) }}" class="dropdown-item">{{ __('Eliminar') }}</a>
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

