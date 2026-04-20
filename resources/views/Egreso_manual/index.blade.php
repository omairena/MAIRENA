@extends('layouts.app', ['page' => __('Crear Registro Egresos'), 'pageSlug' => 'newMovimiento'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>
    
     
</head>
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Registro Egreso Manual') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('egresos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        <form method="post" action="{{ route('Egreso_manual.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Registro Egresos') }}</h6>
                            <div class="pl-lg-4">
                                 <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Proveedor') }}</label>
                                     <input type="button" class="btn btn-sm btn-success" value="+" data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>
                                   
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="" >
                                    <input type="hidden" name="cliente" id="cliente" class="form-control form-control-alternative{{ $errors->has('cliente') ? ' is-invalid' : '' }}" value="" >
                                 
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                
                                <div class="form-group{{ $errors->has('num_recibo_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_recibo_abono">{{ __('Número Referencia') }}</label>
                                    <input type="text" name="id_referencia" id="input-id_referencia" class="form-control form-control-alternative{{ $errors->has('id_referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="" >
                                    @include('alerts.feedback', ['field' => 'num_recibo_abono'])
                                </div>
    <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Clasificacion de Gasto') }}</label>
              
                   <input type="true" name="calsifica" id="calsifica" class="form-control form-control-alternative{{ $errors->has('calsifica') ? ' is-invalid' : '' }}" value="" >
               
               
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
                 
               
               
     
     
     
                <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Cuenta Bancaria') }}</label>
                <select name="banco" id="banco" class="form-control form-control-alternative" required >
                    <option value="">--Seleccione--</option>
                    @foreach($bancos as $banco)
                         
                        <option value="{{ $banco->id_bancos }}">{{ $banco->cuenta}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
                                
                            
                                <div class="form-group{{ $errors->has('monto_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_abono">{{ __('Monto a Abonar') }}</label>
                                    <input type="text" step="any" name="monto_abono" id="input-monto_abono" class="form-control form-control-alternative{{ $errors->has('monto_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Abonar') }}" value="" >
                                    @include('alerts.feedback', ['field' => 'monto_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Referencia Banco') }}</label>
                                    <input type="text" name="referencia" id="input-referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}" required>
                                    
                                     <input type="text" name="obs" id="input-obs" class="form-control form-control-alternative{{ $errors->has('obs') ? ' is-invalid' : '' }}" placeholder="{{ __('Observaciones') }}" value="{{ old('obs') }}">
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="" hidden="true">
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
@include('modals.newtCliente')
@endsection

@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
   
      $( "#calsifica" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/clasificaciontr')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.descripcion;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
        $( "#cliente_serch" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/cliente')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.nombre;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
    $(document).on("blur", "#cliente_serch" , function(event) {
            event.preventDefault();
            var nombre_cli = $(this).val();
            var URL = {!! json_encode(url('buscar-cliente-posfe')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{nombre_cli:nombre_cli},
                    success:function(response){
                        //console.log(response);
                        var arreglo = response['success'].length;
                        var tipo_documento = $('#tipo_documento').val();
                        if (arreglo > 0) {
                            if (response['success'][0]['num_id'] != 100000000 && tipo_documento === '04') {
                                $('#tipo_documento').val('01');
                                traerNumFactura(APP_URL,o2);
                            }
                            if (response['success'][0]['num_id'] === 100000000 && tipo_documento === '01') {
                                alert('seleccionar otro tipo de documento');
                                $('#tipo_documento').focus();
                            }else{
                                $('#cliente_serch').val(response['success'][0]['nombre']);
                                $('#cliente').val(response['success'][0]['idcliente']);
                                $('#cedula_id_cliente').val(response['success'][0]['num_id']);
                                $('#Agregar_producto').css( "display","");
                                $('#ced_receptor').val(response['success'][0]['num_id']);
                                $('#datos_internos').val(1);
                                traerNumFactura(APP_URL,o2);
                            }
                        }else{
                            $('#cliente_serch').val('No se encontraron resultados en Base de Datos Interna');
                            $('#cliente').val(1);
                             $('#cedula_id_cliente').val('');
                             $('#Agregar_producto').css( "display", "none");
                        }
                    }
                });
            }
        });
      
          $(document).on("blur", "#ced_receptor" , function(event) {
            event.preventDefault();
            var num_id = $(this).val();
            var URL = {!! json_encode(url('buscar-identificacion')) !!};
            if ($(this).val().length > 0) {
                $.ajax({
                    type:'GET',
                    url: URL,
                    dataType: 'json',
                    data:{num_id:num_id},
                    success:function(response){
                        //console.log(response);
                        if (response['success'] === true) {
                            alert('Cliente ya registrado en el sistema.');
                            $('#cliente_serch').val(response['default'][0]['nombre']);
                            $('#datos_internos').val(1);
                            $('#cliente').val(response['default'][0]['idcliente']);
                            $('#tipo_documento').val('01');
                            $('#newUsuario').modal('hide');
                            $('#cliente_serch').focus();
                        }else{
                            alert('Identificación No Encontrada en nuestra base de datos 33');
                            $('#cliente_serch').val('');
                            $('#cliente').val('0');
                            var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + num_id;
                            // var api = 'https://apis.gometa.org/cedulas/' + num_id;
                           
                            $.ajax({
                                type:'GET',
                                url: api,
                                dataType: 'json',
                                data:{num_id:num_id},
                                success:function(response){
                                    //console.log(response);
                                    $('#cliente_hacienda').val(JSON.stringify(response));
                                    $('#datos_internos').val(0);
                                    if (typeof response =='object') {
                                        $('#cliente_serch_modal').val(response.nombre);
                                        $('#tipo_id_modal').val(response.tipoIdentificacion);
                                        
                                            $('#codigo_actividad_modal').find('option').remove();
                                            $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                        
                                    }
                                },
                                error:function(response){
                                    alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación3');
                                    $('#cliente_serch').val('');
                                }
                            });

                            // Consulta nueva Hacienda Marzo 2022
                            var settings = {
                                "url": "https://api.hacienda.go.cr/fe/mifacturacorreo?identificacion=" + num_id,
                                "method": "GET",
                                "timeout": 0,
                                "headers": {
                                    "access-user": "206410122",
                                    "access-token": "hQXs4KNNs8HPZ6aRC5oX",
                                    "Content-Type": "application/json",
                                    "Cookie": "TS01d94531=0120156b28a33842b0975df1c1170f626694e5d4c555793b626be216ec5e19637b13a6765c419143ba945cca5258abb36dfd71f363"
                                },
                            };

                            $.ajax(settings).done(function (response) {
                                //console.log(response);
                                if (response['Resultado']['Correos'].length > 0) {
                                    $('#input-email').val(response['Resultado']['Correos'][0]['Correo']);
                                }

                            });
                        }
                    },
                    error:function(response){
                        alert('Identificación No Encontrada en nuestra base de datos4');
                        $('#cliente_serch').val('');
                        //var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
                          var api = 'https://apis.gometa.org/cedulas/' + id;
                        $.ajax({
                            type:'GET',
                            url: api,
                            dataType: 'json',
                            success:function(response){
                                console.log(response);
                            },
                            error:function(response){
                                alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación4');
                                $('#cliente_serch').val('');
                                $('#ced_receptor').focus();
                            }
                        });
                    }
                });

            }else{

            }
        })

</script>
@endsection

