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
          
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Facturas para Env√≠o') }}</h4>
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
                            	<th scope="col">{{ __('# ID') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Total a Facturar') }}</th>
                                
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                               
                                @foreach($log_masivo as $lm)
                                    <?php
                                     // dd($lm);
                                        $config_masivo = App\Config_masivo::where([
                                            ['idlogmasivo', $lm->idlogmasivo]
                                        ])->get();
                                        $cliente = App\Cliente::where([
                                            ['idcliente', $lm->idclientes ]
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
                                        <td>{{ $lm->idconfigmasivo }}</td>
                                        <td>{{ $cliente[0]->nombre }}</td>
                                        
                                        <td>{{ number_format($lm->total_comprobante, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                              <a class="dropdown-item update_lista" href="#" data-id="{{ $lm->idconfigmasivo }}">{{ __('Crear') }}</a>  
                                              
                                                   
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
            
    $('.update_lista').click(function(e) {  
    e.preventDefault(); // Evita la acci®Æn por defecto del enlace  

    var id = $(this).data('id'); // Obtiene el ID desde el atributo data-id  
    var APP_URL = {!! json_encode(url('/update_lista')) !!}; // Obtiene la URL para la solicitud AJAX  

    $.ajax({  
        type: 'POST', // Cambia a 'PUT' si la ruta usa PUT  
        url: APP_URL,  
        dataType: 'json',  
        data: {  
            idlogmasivo: id, // Aseg®≤rate de que el campo coincide con lo que espera tu controlador  
            _token: $('meta[name="csrf-token"]').attr('content') // Inclusi®Æn del token CSRF  
        },  
        beforeSend: function(response) {  
            $('.loader').css('display', 'block'); // Muestra una carga de espera  
        },  
        success: function(response) {  
            if (response.success) {  
                // Aseg®≤rate de usar response.url  
                window.location.href = response.url; // Redirige a la URL si fue exitoso  
            } else {  
                alert('Error: ' + response.message); // Muestra un mensaje de error si no fue exitoso  
            }  
        },  
        complete: function(response) {  
            $('.loader').css('display', 'none'); // Oculta el loader al finalizar  
        },  
        error: function(response) {  
            console.error('Error AJAX:', response); // Registra cualquier error  
            alert('Ocurri®Æ un error al intentar enviar.'); // Muestra un mensaje general de error  
        }  
    });  
});  
        
    
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
 