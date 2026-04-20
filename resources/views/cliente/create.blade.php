@extends('layouts.app', ['page' => __('Nuevo Cliente'), 'pageSlug' => 'newcliente'])


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
                        <form method="post" action="{{ route('cliente.store') }}" autocomplete="off">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Cliente') }}</h6>
                            <div class="pl-lg-4">
<div class="form-group{{ $errors->has('num_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_id">{{ __('Número de Identificación') }}</label>
                                   <input type="text" 
       name="num_id" 
       id="input-num_id"
       pattern="[A-Za-z0-9]+"
       class="form-control form-control-alternative"
       placeholder="Número de Identificación"
       required>
                                    @include('alerts.feedback', ['field' => 'num_id'])
<button  class="btn btn-success mt-4">{{ __('Buscar') }}</button>
                                </div>

                            	<div class="form-group{{ $errors->has('tipo_cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_cliente">{{ __('Tipo de Cliente') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_cliente" name="tipo_cliente" value="{{ old('tipo_cliente') }}" required>
                                    	<option value="1">Cliente</option>
                                    	<option value="2">Proveedor</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_cliente'])
                                </div>
                            	<div class="form-group{{ $errors->has('tipo_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_id">{{ __('Tipo Identificación') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_id" name="tipo_id" value="{{ old('tipo_id') }}" required>
                                    	<option value="01">Cédula Física</option>
                                    	<option value="02">Cédula Júridica</option>
                                    	<option value="03">DIMEX</option>
                                    	<option value="04">NITE</option>
                                        <option value="05">Extranjero No Domiciliado</option>
                                    	<option value="06">NO CONTRIBUYENTE</option>

                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_id'])
                                </div>

                                <!--<div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad') }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a>
                                    </label>
                                    <select class="form-control form-control-alternative" id="actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>-->
                                <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
  <label class="form-control-label" for="input-actividad">Código Actividad</label>
  <input type="text" list="actividad-list" id="input-actividad" name="codigo_actividad" class="form-control form-control-alternative"  required>
  <datalist id="actividad-list">
    <!-- Opcional: un valor por defecto para guiar al usuario -->
    <option value="0">112233-Actividad por defecto</option>
  </datalist>
  @include('alerts.feedback', ['field' => 'codigo_actividad'])
</div>
                                <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}" id="div_tipoclasi" style="display: none;">
                                    <label class="form-control-label" for="tipo_clasificacion">{{ __('Clasificación') }}</label>
                                    <select name="tipo_clasificacion" id="tipo_clasificacion" class="form-control form-control-alternative">
                                        @foreach($clasificaciones as $clasifica)
                                            <option value="{{ $clasifica->idclasifica }}">{{ $clasifica->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre">{{ __('Nombre Cliente/Proveedor') }}</label>
                                    <input type="text" name="nombre" id="input-nombre" class="form-control form-control-alternative{{ $errors->has('nombre') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Completo') }}" value="{{ old('nombre') }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre'])
                                </div>
                                <div class="form-group{{ $errors->has('razon_social') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-razon_social">{{ __('Razon Social') }}</label>
                                    <input type="text" name="razon_social" id="input-razon_social" class="form-control form-control-alternative{{ $errors->has('razon_social') ? ' is-invalid' : '' }}" placeholder="{{ __('Razon Social') }}" value="{{ old('razon_social') }}" required>
                                    @include('alerts.feedback', ['field' => 'razon_social'])
                                </div>
                                <div class="form-group{{ $errors->has('condicionventa') ? ' has-danger' : '' }}" id="condicion" style="display: none;">
                                    <label class="form-control-label" for="input-condicionventa">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condicionventa" name="condicionventa" value="{{ old('condicionventa') }}">
                                        <option value="01">Contado</option>
                                        <option value="02">Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condicionventa'])
                                </div>
                                <div class="form-group{{ $errors->has('plazocredito') ? ' has-danger' : '' }}" id="plazos" style="display: none;">
                                    <label class="form-control-label" for="input-plazocredito">{{ __('Plazo Crédito') }}</label>
                                    <input type="text" name="plazocredito" id="input-plazocredito" class="form-control form-control-alternative{{ $errors->has('plazocredito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Crédito') }}" value="{{ old('plazocredito') }}">
                                    @include('alerts.feedback', ['field' => 'plazocredito'])
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}"  required>
                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_pais') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_pais">{{ __('Código País') }}</label>
                                    <input type="number" name="codigo_pais" id="input-codigo_pais" class="form-control form-control-alternative{{ $errors->has('codigo_pais') ? ' is-invalid' : '' }}" placeholder="{{ __('Código País') }}" value="506" required>
                                    @include('alerts.feedback', ['field' => 'codigo_pais'])
                                </div>
                                <div class="form-group{{ $errors->has('telefono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-telefono">{{ __('Teléfono') }}</label>
                                    <input type="number" name="telefono" id="input-telefono" class="form-control form-control-alternative{{ $errors->has('telefono') ? ' is-invalid' : '' }}" placeholder="{{ __('Teléfono') }}"  required>
                                    @include('alerts.feedback', ['field' => 'telefono'])
                                </div>
                                <div class="form-group{{ $errors->has('provincia') ? ' has-danger' : '' }} ubicacion-cliente" id="ubicacion">
                                    <label class="form-control-label" for="input-provincia">{{ __('Provincia') }}</label>
                                    <select name="provincia" id="provincia" class="form-control form-control-alternative" >
                                        <option value="">-- Seleccione una Provincia --</option>
                                        <option value="1">San José</option>
                                        <option value="2">Alajuela</option>
                                        <option value="3">Cartago</option>
                                        <option value="4">Heredia</option>
                                        <option value="5">Guanacaste</option>
                                        <option value="6">Puntarenas</option>
                                        <option value="7">Limón</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('canton') ? ' has-danger' : '' }} ubicacion-cliente" id="canton-div">
                                    <label class="form-control-label" for="input-canton">{{ __('Cantones') }}</label>
                                    <select name="canton" id="canton" class="form-control form-control-alternative" >
                                        <option value=''>-- Seleccionar un Canton--</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('distrito') ? ' has-danger' : '' }} ubicacion-cliente" id="distrito-div">
                                    <label class="form-control-label" for="input-distrito">{{ __('Distrito') }}</label>
                                    <select name="distrito" id="distrito" class="form-control form-control-alternative" >
                                        <option value=''>-- Seleccionar un Distrito--</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('direccion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-direccion">{{ __('Dirección') }}</label>
                                    <textarea name="direccion" id="input-direccion" class="form-control form-control-alternative{{ $errors->has('direccion') ? ' is-invalid' : '' }}" placeholder="{{ __('Dirección') }}" value="{{ old('direccion') }}" required max="150"></textarea>
                                    @include('alerts.feedback', ['field' => 'direccion'])
                                </div>
                                <div class="form-group{{ $errors->has('notas') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-notas">{{ __('Notas') }}</label>
                                    <textarea name="notas" id="input-notas" class="form-control form-control-alternative{{ $errors->has('notas') ? ' is-invalid' : '' }}" placeholder="{{ __('Nota') }}" value="{{ old('notas') }}" ></textarea>
                                    @include('alerts.feedback', ['field' => 'notas'])
                                </div>
                                <div class="form-group{{ $errors->has('es_contado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_contado">{{ __('¿Es Contado?') }}</label>
                                    <select class="form-control form-control-alternative" id="es_contado" name="es_contado" value="{{ old('es_contado') }}" required>
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_vendedor'])
                                </div>
                                <input type="number" name="idconfigfact" value="{{ Auth::user()->idconfigfact }}" hidden="true">
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
          $(document).on("blur", "#input-num_id", function(event) {
    var id = $(this).val();
    // Usa la URL de la API real que mencionaste
    var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
     //var URL = 'https://apis.gometa.org/cedulas/' + id;

    // Limpiar opciones antes de la solicitud
  $('#actividad-list').empty();

    $.ajax({
        type: 'GET',
        url: URL,
        dataType: 'json',
        success: function(response) {
            // Asegúrate de que response es objeto
            if (response && typeof response === 'object') {
                // Asignaciones básicas si existen
                if (response.nombre) {
                    $('#input-nombre').val(response.nombre);
                    $('#input-razon_social').val(response.nombre);
                }
                if (response.tipoIdentificacion) {
                    $('#tipo_id').val(response.tipoIdentificacion);
                }

                // Cargar actividades si existen
                var actividades = [];
                if (response.actividades && Array.isArray(response.actividades)) {
                    actividades = response.actividades;
                } else if (response.results && Array.isArray(response.results)) {
                    // En caso de que la API devuelva datos en otra estructura
                    // intentamos mapear actividades desde results si procede
                    // Este bloque es opcional y depende de tu API real
                    // actividades = response.results.map(r => ({ codigo: r.guess_type_num, descripcion: r.fullname || '' }));
                }

               if (actividades.length > 0) {
  actividades.forEach(function(act) {
    var cod = act.codigo || '';
    var des = act.descripcion || '';
    // Usamos <option> dentro de <datalist> con solo value
    $('#actividad-list').append('<option value="' + cod + '">' + cod + (des ? ' - ' + des : '') + '</option>');
  });
} else {
  // Caso sin actividades: dejar una opción por defecto
  $('#actividad-list').append('<option value="0">112233-Actividad por defecto</option>');
  // Opcional: rellenar el input con el valor por defecto para guiar al usuario
  $('#input-actividad').val('112233');
}
            }
        },
    error: function(xhr, status, error) {
  console.error('AJAX Error:', status, error);
  alert('Identificación No Encontrada');
  // Limpiar campos relevantes
  $('#input-nombre').val('');
  $('#input-tipo_id').val('');
  $('#input-razon_social').val('');
  // Opcional: permitir escribir manualmente en la actividad
  // Mantener/datfill del datalist y establecer valor por defecto
  $('#input-actividad').val('0');
  $('#actividad-list').empty();
  $('#actividad-list').append('<option value="0">112233-Actividad por defecto</option>');
}
    });
});




        $('#tipo_cliente').change(function() {

            if ($(this).val() === 1) {

                $('#div_tipoclasi').css( "display", "none");

            } else {

                $('#div_tipoclasi').css( "display", "block");
            }
        });
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
