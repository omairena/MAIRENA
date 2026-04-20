@extends('layouts.app', ['page' => __('Facturas del Sistema'), 'pageSlug' => 'allfacturas'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
.loader {
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
    </style>
</head>
@section('content')
 <div class="loader" style="display: none;"></div>
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
                    <form method="post" action="{{ route('filtro.factura') }}" autocomplete="off" id="filtro_factura">
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
                            <h4 class="card-title">{{ __('Configuraciones Masivas para Env√≠o') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <div class="dropdown">
                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b style="color: white;">Acciones</b> &nbsp;
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a href="{{ route('masivo.create') }}" class="dropdown-item">{{ __('Nueva Configuracion Masiva') }}</a>
                                    <a href="{{ route('consultardoc.index') }}" class="dropdown-item">{{ __('Consultar Documentos') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_masivo_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('# Masivo') }}</th>
                                <th scope="col">{{ __('Nombre Configuraci√≥n') }}</th>
                                <th scope="col">{{ __('Fecha Masivo') }}</th>
                                <th scope="col">{{ __('Estatus Masivo') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach($log_masivo as $lm)
                                    <?php
                                        $config_masivo = App\Config_masivo::where([
                                            ['idlogmasivo', $lm->idlogmasivo]
                                        ])->get();
                                        switch ($lm->estatus_masivo) {
                                            case '0':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Edici√≥n</button>";
                                            break;
                                            case '1':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-info'>Guardado</button>";
                                            break;
                                            case '2':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-success'>Configurado</button>";
                                            break;
                                            default:
                                                $estatus ="<button  type='button' class='btn btn-sm btn-warning'>Editando</button>";
                                            break;
                                        }

                                    ?>
                                    <tr>
                                        <td>{{ $lm->idlogmasivo }}</td>
                                        <td>{{ $lm->nombre_masivo }}</td>
                                        <td>{{ $lm->fecha_masivo }}</td>
                                        <td><?php echo $estatus; ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                   @if (!empty($config_masivo) && count($config_masivo) > 0)  
    <a class="dropdown-item editar_logmasivo"   
       href="#"  
       data-id="{{ $lm->idlogmasivo }}"   
       data-cliente="{{ $config_masivo[0]->idclientes }}">  
       {{ __('Editar') }}  
    </a>  
@else  
    
@endif  
                                                     <a class="dropdown-item eliminar_logmasivo" href="#"  data-id="{{ $lm->idlogmasivo }}">{{ __('Limpiar  Registros para carga Masiva') }}</a>
                                                    @if($lm->estatus_masivo > 0)
                                                        <a class="dropdown-item enviar_logmasivo" href="#"  data-id="{{ $lm->idlogmasivo }}">{{ __('Enviar Masivamente') }}</a>
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
        $('#ver_masivo_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('.editar_logmasivo').click(function(e) {
            var id = $(this).data('id');
            var cliente = $(this).data('cliente');
            var APP_URL = {!! json_encode(url('/ajaxEditarConfig')) !!};
            $.ajax({
                type:'get',
                url: APP_URL,
                dataType: 'json',
                data:{idlogmasivo:id,cliente:cliente},
                success:function(response){
                    if(response.success){
                        window.location = response.url;
                    }
                },
                error:function(response){
                    //console.log(response);
                }
            });

        });
        
     
        
            $('.eliminar_logmasivo').click(function(e) {  
    e.preventDefault(); // Previene la acci®Æn por defecto del enlace  
    var id = $(this).data('id'); // Obtiene el id  
    var APP_URL = {!! json_encode(url('/ajaxeliminarConfig')) !!}; // Define la URL para la solicitud  

    $.ajax({  
        type: 'POST',  
        url: APP_URL,  
        dataType: 'json',  
        data: {  
            idlogmasivo: id, // Aseg®≤rate de que 'idlogmasivo' est®¢ definido  
            _token: '{{ csrf_token() }}' // Inclusi®Æn del token CSRF  
        },  
        beforeSend: function(response) {  
            $('.loader').css('display', 'block'); // Muestra el loader antes de la solicitud  
        },  
        success: function(response) {  
            if (response.success) {  
                window.location = response.url; // Redirigir si la operaci®Æn fue exitosa  
            }  
        },  
        complete: function(response) {  
            $('.loader').css('display', 'none'); // Oculta el loader  
        },  
        error: function(response) {  
            // Aqu®™ puedes imprimir la respuesta para depurar  
            console.log(response);  
        }  
    });  
});
        
        $('.enviar_logmasivo').click(function(e) {
            var id = $(this).data('id');
            var APP_URL = {!! json_encode(url('/ajaxEnviarConfig')) !!};
            $.ajax({
                type:'get',
                url: APP_URL,
                dataType: 'json',
                data:{idlogmasivo:id},
                beforeSend:function(response){
                    $('.loader').css('display', 'block');
                },
                success:function(response){
                    if(response.success){
                        window.location = response.url;
                    }
                },
                complete:function(response){
                     $('.loader').css('display', 'none');
                },
                error:function(response){
                    //console.log(response);
                }
            });

        });
    });
</script>
@endsection
