@extends('layouts.app', ['page' => __('Transferencias'), 'pageSlug' => 'tiquetes'])
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
              <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nueva Transferencia') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('egresos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        <form method="post" action="{{ route('transferencias.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                           
                            <div class="pl-lg-4">
                               
                       <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Cuenta Bancaria Origen') }}</label>
                <select name="banco_o" id="banco_o" class="form-control form-control-alternative">
                    @foreach($bancos as $banco)
                        <option value="{{ $banco->id_bancos }}">{{ $banco->cuenta}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>          
                                
           
                <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Cuenta Bancaria Destino') }}</label>
                <select name="banco_d" id="banco_d" class="form-control form-control-alternative">
                    @foreach($bancos as $banco)
                        <option value="{{ $banco->id_bancos }}">{{ $banco->cuenta}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
            
          
                                
                            
                                <div class="form-group{{ $errors->has('monto_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_abono">{{ __('Monto Total') }}</label>
                                    <input type="text" step="any" name="monto_abono" id="input-monto_abono" class="form-control form-control-alternative{{ $errors->has('monto_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Abonar') }}" value="" >
                                    @include('alerts.feedback', ['field' => 'monto_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Referencia') }}</label>
                                    <input type="text" name="referencia" id="input-referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}" required>
                                    
                                     <input type="text" name="obs" id="input-obs" class="form-control form-control-alternative{{ $errors->has('obs') ? ' is-invalid' : '' }}" placeholder="{{ __('Observaciones') }}" value="{{ old('referencia') }}">
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Transacciones Construidas') }}</h4>
                          
                          
                            
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_tiquetes_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('ID') }}</th>
                                <th scope="col">{{ __('Fecha Creación') }}</th>
                                <th scope="col">{{ __('Cta Origen') }}</th>
                                
                                <th scope="col">{{ __('Cta Destino') }}</th>
                                <th scope="col">{{ __('Monto') }}</th>
                                <th scope="col">{{ __('Referencia') }}</th>
                                 <th scope="col">User</th>
                                <th scope="col">Obs</th>
                                <th scope="col">Acciones</th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <?php
                                       // $usuario = App\Cliente::find($sale->idcliente);
                                      //  $facturas = App\Facelectron::find($factura[0]->idfacelectron);
                                      
                                        
                                        $banco = App\Bancos::find($sale->origen);
                                         $bancd = App\Bancos::find($sale->destino);
                                        $clasificaciones = App\Clasificaciones::find($sale->clasificacion);
                                        if ($sale->condicion_venta === '01') {
                                            $condicion = 'Contado';
                                        }else{
                                            $condicion = 'Credito';
                                        }
                                       


                                    ?>
                                
                                    <tr>
                                    	<td>{{ $sale-> id_transfer  }}</td>
                                        <td>{{ $sale-> fecha }}</td>
                                        <td>{{  $banco->cuenta }}</td>
                                        <td>{{  $bancd->cuenta }}</td>
                                        <td >{{ number_format($sale->monto, 2, '.', ',') }}</td>
                                        
                                        
                                         <td>{{ $sale->referencia }}</td>
                                        <td > {{ $sale->user }}</td>
                                       <td > {{ $sale->obs }}</td>
                                        <td>
                                        	<div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                   
                                                        
                                                        <a href="{{ url('tranfer/deleted', ['idsales' => $sale->id_transfer]) }}" class="dropdown-item">{{ __('Eliminar') }}</a>
                                                       
                                                        
                                                    

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

