@extends('layouts.app', ['page' => __('Cajas Creadas'), 'pageSlug' => 'verCajas'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Cajas') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            @if (Auth::user()->es_vendedor == 0)
                                <a href="{{ route('cajas.create') }}" class="btn btn-sm btn-primary">{{ __('Crear Caja') }}</a>
                            @endif
                            <a href="{{ route('cajas.resumen') }}" class="btn btn-sm btn-primary">{{ __('Resumen por dia') }}</a>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="cajas_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Empresa Caja') }}</th>
                                 <th scope="col">{{ __('Usuario') }}</th>
                                <th scope="col">{{ __('Código Único') }}</th>
                                <th scope="col">{{ __('Nombre Caja') }}</th>
                                <th scope="col">{{ __('Fecha Apetura') }}</th>
                                <th scope="col">{{ __('Fecha Ult Cierre') }}</th>
                                <th scope="col">{{ __('Monto de Fondo Inicial') }}</th>
                                <th scope="col">{{ __('Estatus') }}</th>
                                <th scope="col">{{ __('Accion') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($cajas as $caja)
                                    <?php
                                        switch ($caja->estatus) {
                                            case '0':
                                                $estatus = "<button  type='button' class='btn btn-sm btn-danger'>Cerrada</button>";
                                            break;
                                            case '1':
                                                $estatus = "<button  type='button' class='btn btn-sm btn-success'>Abierta</button>";
                                            break;
                                        }
                                        
                                        
                                        if(empty($caja->idcaja)){
           $caja_idcaja=0;
       }else{
            $caja_idcaja=$caja->idcaja;
       }
        //dd($caja_idcaja);
       $consulta = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $caja_idcaja)
        //->where('idusuario', '=', Auth::user()->id)
        ->where('estado', '=', 1)
        ->get();
        $consult = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $caja_idcaja)
        ->where('idusuario', '=', Auth::user()->id)
        ->where('estado', '=', 1)
        ->get();
        //dd($consult);
        if ($consult->isNotEmpty() && !empty($consult[0]->idusuario)) {  
             $consultd = DB::table('users')
        ->select('users.*')
        ->where('id', '=', $consult[0]->idusuario)
            ->get();
        
         
        }else{
           $consultd = DB::table('users')
        ->select('users.*')
        ->where('id', '=', 2)
        //->where('idusuario', '=', Auth::user()->id)
        ->get();
        }
        //dd($consultd);
                                    ?>
                                    <tr>
                                        <td>{{ $caja->caja_emp[0]->nombre_emisor }}</td>
                                        <td>{{ $consultd[0]->name }}</td>
                                        <td>{{ str_pad($caja->codigo_unico, 3, "0", STR_PAD_LEFT) }}</td>
                                        <td>{{ $caja->nombre_caja }}</td>
                                        <td>{{ $caja->fecha_apertura }}</td>
                                        <td>{{ $caja->fecha_cierre }}</td>
                                        <td>{{ $caja->monto_fondo }}</td>
                                        <td><?php echo $estatus; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="{{ route('cajas.edit', $caja->idcaja) }}">{{ __('Editar') }}</a>
                                                    @if($caja->estatus === 0)
                                                        <a class="dropdown-item"  href="#" data-target="#AddCajero" data-toggle="modal" data-id="{{ $caja->idcaja }}" id="asignar_cajero">{{ __('Abrir Caja') }}</a>
                                                    @else
                                                        <a class="dropdown-item"  href="{{ route('cajas.cerrar', $caja->idcaja) }}">{{ __('Cerrar Caja') }}</a>
                                                    @endif
                                                    @if (Auth::user()->es_vendedor == 0)
                                                        <a class="dropdown-item" href="{{ route('consecutivo.show', $caja->idcaja) }}">{{ __('Ver Consecutivos') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('modals.addCajero')

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#cajas_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );

        $(document).on("click", "#asignar_cajero" , function(event) {
            event.preventDefault();
            var idcaja = $(this).data('id');
            var APP_URL = {!! json_encode(url('/consultaCajeros')) !!};
            $.ajax({
                type:'GET',
                url: APP_URL,
                data:{idcaja:idcaja},
                dataType: 'json',
                success:function(response){
                    $('#user_cajero').find('option').not(':first').remove();
                    $('#idcaja_modal').val(idcaja);
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                    // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var option = "<option value='"+response['success'][i].id+"'>"+response['success'][i].name+" - Correo:  "+response['success'][i].email+"</option>";
                            $("#user_cajero").append(option);
                        }
                    }
                }

            });
        });
        $(document).on("click", "#add_cajero" , function(event) {
            event.preventDefault();
            var idcaja = $('#idcaja_modal').val();
            var idusuario = $('#user_cajero').val();
            var APP_URL = {!! json_encode(url('/guardarCajeros')) !!};
            if (idusuario > 0) {
                $.ajax({
                    type:'GET',
                    url: APP_URL,
                    data:{idcaja:idcaja, idusuario:idusuario},
                    dataType: 'json',
                    success:function(response){
                        location.reload();
                    }
                });
            } else {

                alert('Por favor seleccione un usuario para proceder!.');

            }

        });
    });
</script>
@endsection

