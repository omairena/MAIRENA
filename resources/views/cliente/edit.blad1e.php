@extends('layouts.app', ['page' => __('Nuevo Cliente'), 'pageSlug' => 'newcliente'])
<head>
    <style>
        span.email-ids {
            float: left;
            /* padding: 4px; */
            border: 1px solid #ccc;
            margin-right: 5px;
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 5px;
            background: #f5f5f5;
            padding-top: 3px;
            padding-bottom: 3px;
            border-radius: 5px;
        }
        span.cancel-email {
            border: 1px solid #ccc;
            width: 18px;
            display: block;
            float: right;
            text-align: center;
            margin-left: 20px;
            border-radius: 49%;
            height: 18px;
            line-height: 15px;
            margin-top: 1px;    cursor: pointer;
        }
        .col-sm-12.email-id-row {
            border: 1px solid #ccc;
        }
        .col-sm-12.email-id-row input {
            border: 0px; outline:0px;
        }
        span.to-input {
            display: block;
            float: left;
            padding-right: 11px;
        }
        .col-sm-12.email-id-row {
            padding-top: 6px;
            padding-bottom: 7px;
            margin-top: 23px;
        }
    </style>
</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Nuevo Cliente') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cliente.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cliente.update', $cliente->idcliente) }}" autocomplete="off">
                            @csrf
                            @method('put')

                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Cliente') }}</h6>
                            <div class="pl-lg-4">
                            	<div class="form-group{{ $errors->has('tipo_cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_cliente">{{ __('Tipo de Cliente') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_cliente" name="tipo_cliente" value="{{ old('tipo_cliente', $cliente->tipo_cliente) }}" required>
                                    	<option value="1" {{ ($cliente->tipo_cliente == 1 ? 'selected="selected"' : '') }}>Cliente</option>
                                    	<option value="2" {{ ($cliente->tipo_cliente == 2 ? 'selected="selected"' : '') }}>Proveedor</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_cliente'])
                                </div>
                            	<div class="form-group{{ $errors->has('tipo_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_id">{{ __('Tipo Identificación') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_id" name="tipo_id" value="{{ old('tipo_id', $cliente->tipo_id) }}" required>
                                    	<option value="01" {{ ($cliente->tipo_id == 01 ? 'selected="selected"' : '') }}>Cédula Física</option>
                                    	<option value="02" {{ ($cliente->tipo_id == 02 ? 'selected="selected"' : '') }}>Cédula Júridica</option>
                                    	<option value="03" {{ ($cliente->tipo_id == 03 ? 'selected="selected"' : '') }}>DIMEX</option>
                                    	<option value="04" {{ ($cliente->tipo_id == 04 ? 'selected="selected"' : '') }}>NITE</option>
                                        <option value="05" {{ ($cliente->tipo_id == 05 ? 'selected="selected"' : '') }}>Extranjero No Domiciliado</option>
                                        <option value="06" {{ ($cliente->tipo_id == 06 ? 'selected="selected"' : '') }}>NO CONTRIBUYENTE</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_id'])
                                </div>
                                <div class="form-group{{ $errors->has('num_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_id">{{ __('Número de Identificación') }}</label>
                                    <input type="number" name="num_id" id="input-num_id" class="form-control form-control-alternative{{ $errors->has('num_id') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ $cliente->num_id }}" required>
                                    @include('alerts.feedback', ['field' => 'num_id'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad') }} &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a></label>
                                    <select class="form-control form-control-alternative" id="actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" required>
                                        <option value="{{ $cliente->codigo_actividad }}"> {{ $cliente->codigo_actividad }} </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre">{{ __('Nombre Cliente/Proveedor') }}</label>
                                    <input type="text" name="nombre" id="input-nombre" class="form-control form-control-alternative{{ $errors->has('nombre') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Completo') }}" value="{{ $cliente->nombre }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre'])
                                </div>
                                <div class="form-group{{ $errors->has('razon_social') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-razon_social">{{ __('Razon Social') }}</label>
                                    <input type="text" name="razon_social" id="input-razon_social" class="form-control form-control-alternative{{ $errors->has('razon_social') ? ' is-invalid' : '' }}" placeholder="{{ __('Razon Social') }}" value="{{ $cliente->razon_social }}" required>
                                    @include('alerts.feedback', ['field' => 'razon_social'])
                                </div>
                                <div class="form-group{{ $errors->has('condicionventa') ? ' has-danger' : '' }}" id="condicion" style="display: none;">
                                    <label class="form-control-label" for="input-condicionventa">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condicionventa" name="condicionventa" value="{{ $cliente->condicionventa }}">
                                        <option value="01" {{ ($cliente->condicionventa == 01
                                         ? 'selected="selected"' : '') }}>Contado</option>
                                        <option value="02" {{ ($cliente->condicionventa == 02
                                         ? 'selected="selected"' : '') }}>Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condicionventa'])
                                </div>
                                <div class="form-group{{ $errors->has('plazocredito') ? ' has-danger' : '' }}" id="plazos" style="display: none;">
                                    <label class="form-control-label" for="input-plazocredito">{{ __('Plazo Crédito') }}</label>
                                    <input type="text" name="plazocredito" id="input-plazocredito" class="form-control form-control-alternative{{ $errors->has('plazocredito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Crédito') }}" value="{{ $cliente->plazocredito }}">
                                    @include('alerts.feedback', ['field' => 'plazocredito'])
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ $cliente->email }}" required>
                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                <div class="form-group{{ $errors->has('additional_email') ? ' has-danger' : '' }}">
                                    <div class="col-sm-12 email-id-row">
                                        <span class="to-input">Emails Adicionales</span>
                                        <div class="all-mail">
                                            @if(!is_null($cliente->additional_email) and !empty($cliente->additional_email))
                                                <?php
                                                    $emails = explode(',', $cliente->additional_email);
                                                ?>
                                                @foreach($emails as $email)
                                                    <span class="email-ids">{{ $email }}<span class="cancel-email" data-email='{{ $email }}'>x</span></span>
                                                @endforeach
                                            @endif
                                        </div>
                                        <input type="text" name="additional_email[]" id="additional_email" class="form-control form-control-alternative{{ $errors->has('additional_email') ? ' is-invalid' : '' }} enter-mail-id" placeholder="Ingrese los correos electronicos"/>

                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('codigo_pais') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_pais">{{ __('Código País') }}</label>
                                    <input type="number" name="codigo_pais" id="input-codigo_pais" class="form-control form-control-alternative{{ $errors->has('codigo_pais') ? ' is-invalid' : '' }}" placeholder="{{ __('Código País') }}" value="{{ $cliente->codigo_pais }}" required>
                                    @include('alerts.feedback', ['field' => 'codigo_pais'])
                                </div>
                                <div class="form-group{{ $errors->has('telefono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-telefono">{{ __('Teléfono') }}</label>
                                    <input type="number" name="telefono" id="input-telefono" class="form-control form-control-alternative{{ $errors->has('telefono') ? ' is-invalid' : '' }}" placeholder="{{ __('Teléfono') }}" value="{{ $cliente->telefono }}" required>
                                    @include('alerts.feedback', ['field' => 'telefono'])
                                </div>
                                <div class="form-group{{ $errors->has('provincia') ? ' has-danger' : '' }}" id="ubicacion">
                                    <label class="form-control-label" for="input-provincia">{{ __('Provincia') }}</label>
                                    <select name="provincia" id="provincia" class="form-control form-control-alternative" value="{{ old('provincia', $cliente->provincia) }}">
                                        <option value="0">-- Seleccione una Provincia --</option>
                                        <option value="1" {{ ($cliente->provincia == 1
                                         ? 'selected="selected"' : '') }}>San José</option>
                                        <option value="2" {{ ($cliente->provincia == 2
                                         ? 'selected="selected"' : '') }}>Alajuela</option>
                                        <option value="3" {{ ($cliente->provincia == 3
                                         ? 'selected="selected"' : '') }}>Cartago</option>
                                        <option value="4" {{ ($cliente->provincia == 4
                                         ? 'selected="selected"' : '') }}>Heredia</option>
                                        <option value="5" {{ ($cliente->provincia == 5
                                         ? 'selected="selected"' : '') }}>Guanacaste</option>
                                        <option value="6" {{ ($cliente->provincia == 6
                                         ? 'selected="selected"' : '') }}>Puntarenas</option>
                                        <option value="7" {{ ($cliente->provincia == 7
                                         ? 'selected="selected"' : '') }}>Limón</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('canton') ? ' has-danger' : '' }}" id="canton-div">
                                    <label class="form-control-label" for="input-canton">{{ __('Cantones') }}</label>
                                    <select name="canton" id="canton" class="form-control form-control-alternative" value="{{ old('canton', $cliente->canton) }}">
                                        <option value='0'>-- Seleccionar un Canton--</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('distrito') ? ' has-danger' : '' }}" id="distrito-div">
                                    <label class="form-control-label" for="input-distrito">{{ __('Distrito') }}</label>
                                    <select name="distrito" id="distrito" class="form-control form-control-alternative" value="{{ old('distrito', $cliente->distrito) }}">
                                        <option value='0'>-- Seleccionar un Distrito--</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('direccion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-direccion">{{ __('Dirección') }}</label>
                                    <textarea name="direccion" id="input-direccion" class="form-control form-control-alternative{{ $errors->has('direccion') ? ' is-invalid' : '' }}" placeholder="{{ __('Dirección') }}" required max="150">{{ $cliente->direccion }}</textarea>
                                    @include('alerts.feedback', ['field' => 'direccion'])
                                </div>
                                 <div class="form-group{{ $errors->has('exocli') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-notas">{{ __('Exoneración Hacienda') }}</label>
                                    <input name="exocli" id="input-exocli" class="form-control form-control-alternative{{ $errors->has('exocli') ? ' is-invalid' : '' }}" placeholder="{{ __('Exoneración Hacienda') }}" value="{{ $cliente->exocli }}"></input>
                                    @include('alerts.feedback', ['field' => 'notas'])
                                </div>
                                <div class="form-group{{ $errors->has('notas') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-notas">{{ __('Notas') }}</label>
                                    <textarea name="notas" id="input-notas" class="form-control form-control-alternative{{ $errors->has('notas') ? ' is-invalid' : '' }}" placeholder="{{ __('Notas') }}" >{{ $cliente->notas }}</textarea>
                                    @include('alerts.feedback', ['field' => 'notas'])
                                </div>
                                <div class="form-group{{ $errors->has('es_contado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_contado">{{ __('¿Es Contado?') }}</label>
                                    <select class="form-control form-control-alternative" id="es_contado" name="es_contado" value="{{ old('es_contado') }}" required>
                                        <option value="0" {{ ($cliente->es_contado == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($cliente->es_contado == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_contado'])
                                </div>
                                <input type="number" name="idconfigfact" value="{{ $cliente->idconfigfact }}" hidden="true">
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
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        var provincia = $('#provincia').val();
        var canton_asignado = pad({!! $cliente->canton !!}, 2);
        // Empty the dropdown
        $('#canton').find('option').not(':first').remove();
        $('#distrito').find('option').not(':first').remove();
        var APP_URL = {!! json_encode(url('/ajaxCantones')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{provincia:provincia},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var idcanton = response['success'][i].idcanton;
                            var nombre = response['success'][i].nombre;
                            var codigo_canton = response['success'][i].codigo_canton;
                            if (codigo_canton+'' === ''+canton_asignado) {
                                var option = "<option value='"+idcanton+"' selected='selected'>"+codigo_canton+"-"+nombre+"</option>";
                            }else{
                                var option = "<option value='"+idcanton+"'>"+codigo_canton+"-"+nombre+"</option>";
                            }
                            $("#canton").append(option);
                        }
                    }
                    var canton = $('#canton').val();
                    $('#distrito').find('option').not(':first').remove();
                    var distrito_asignado = pad({!! $cliente->distrito !!}, 2);
                    var APP_URL2 = {!! json_encode(url('/ajaxDistritos')) !!};
                    $.ajax({

                        type:'GET',

                        url: APP_URL2,

                        data:{canton:canton},

                        dataType: 'json',

                        success:function(response){
                            //console.log(response);
                            var len = 0;
                            if(response['success'] != null){
                                len = response['success'].length;
                            }
                            if(len > 0){
                                // Read data and create <option >
                                for(var i=0; i<len; i++){
                                    var iddistrito = response['success'][i].iddistrito;
                                    var nombre = response['success'][i].nombre;
                                    var codigo_distrito = response['success'][i].codigo_distrito;
                                    if (codigo_distrito+'' === ''+distrito_asignado) {
                                        var option = "<option value='"+iddistrito+"' selected='selected'>"+codigo_distrito+"-"+nombre+"</option>";
                                    }else{
                                        var option = "<option value='"+iddistrito+"'>"+codigo_distrito+"-"+nombre+"</option>";
                                    }
                                    $("#distrito").append(option);
                                }
                            }
                        }

                    });
                }

            });
        $('#provincia').change(function() {
            var provincia = $(this).val();
            // Empty the dropdown
            $('#canton').find('option').not(':first').remove();
            $('#distrito').find('option').not(':first').remove();
            var APP_URL = {!! json_encode(url('/ajaxCantones')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{provincia:provincia},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                    // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var idcanton = response['success'][i].idcanton;
                            var nombre = response['success'][i].nombre;
                            var codigo_canton = response['success'][i].codigo_canton;
                            var option = "<option value='"+idcanton+"'>"+codigo_canton+"-"+nombre+"</option>";
                            $("#canton").append(option);
                        }
                    }
                }

            });
        });

        $('#canton').change(function() {
            var canton = $(this).val();
            $('#distrito').find('option').not(':first').remove();
            var APP_URL2 = {!! json_encode(url('/ajaxDistritos')) !!};
            $.ajax({

                type:'GET',

                url: APP_URL2,

                data:{canton:canton},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                    // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var iddistrito = response['success'][i].iddistrito;
                            var nombre = response['success'][i].nombre;
                            var codigo_distrito = response['success'][i].codigo_distrito;
                            var option = "<option value='"+iddistrito+"'>"+codigo_distrito+"-"+nombre+"</option>";
                            $("#distrito").append(option);
                        }
                    }
                }

            });
        });
        $(document).on("blur", "#input-num_id" , function(event) {
            var id = $(this).val();
            //var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
            var URL = 'https://apis.gometa.org/cedulas/' + id;
           
            $.ajax({
                type:'GET',
                url: URL,
                dataType: 'json',
                success:function(response){
                    if (typeof response =='object') {
                        $('#input-nombre').val(response.nombre);
                        $('#input-razon_social').val(response.nombre);
                        $('#tipo_id').val(response.tipoIdentificacion);
        $("#actividad").append('<option value="'+ act.codigo+'">'+  act.codigo +' - '+ act.descripcion+'</option>');
                        if ($.isArray([response.actividades])) {
                            $('#actividad').find('option').remove();
                            response.actividades.forEach(function(act, index) {
                                $("#actividad").append('<option value="'+ act.codigo+'">'+  act.codigo +' - '+ act.descripcion+'</option>');
                            });
                        }else{
                            $('#actividad').find('option').remove();
                        }
                    }
                },
                error:function(response){
                    alert('Identificación No Encontrada');
                    $('#input-nombre').val('');
                    $('#input-tipo_id').val('');
                    $('#input-razon_social').val('');
                    $('#actividad').find('option').remove();
                }
            });
        });

        $(".enter-mail-id").keydown(function (e) {
            if (e.keyCode == 13 || e.keyCode == 32 || e.keyCode == 9) {
                //alert('You Press enter');
	            var getValue = $(this).val();
	            $('.all-mail').append('<span class="email-ids">'+ getValue +' <span class="cancel-email">x</span></span>');
                var URL = {!! json_encode(url('/saveAdicionalEmail')) !!};
                var idcliente = {!! $cliente->idcliente !!};
                $.ajax({
                    type:'GET',
                    url: URL,
                    data:{email:getValue,idcliente:idcliente},
                    dataType: 'json',
                    success:function(response){
                        location.reload();
                    }
                });
                //$(this).val('');
            }
        });


        /// Cancel

        $(document).on('click','.cancel-email',function(){
            var email = $(this).data('email');
	        $(this).parent().remove();
            var URL = {!! json_encode(url('/deleteAdicionalEmail')) !!};
            var idcliente = {!! $cliente->idcliente !!};
            $.ajax({
                type:'GET',
                url: URL,
                data:{email:email,idcliente:idcliente},
                dataType: 'json',
                success:function(response){
                    location.reload();

                }
            });

	    });

        // $('.enter-mail-id').click()

        function pad(n, width, z) {
            var z = z || '0';
            var n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        }
    });
</script>

<script>
$(document).ready(function() {
    // Función para mostrar u ocultar los campos de ubicación
    function toggleUbicacion() {
        var tipoId = $('#tipo_id').val();
        if (tipoId === '05') { // '05' es para "Extranjero No Domiciliado"
            $('#ubicacion').hide();
            $('#canton-div').hide();
            $('#distrito-div').hide();
        } else {
            $('#ubicacion').show();
            $('#canton-div').show();
            $('#distrito-div').show();
        }
    }

    // Agregar evento al cambio del select
    $('#tipo_id').change(toggleUbicacion);

    // Ejecutar la función una vez al cargar para ajustar la vista
    toggleUbicacion();
});
</script>

@endsection
