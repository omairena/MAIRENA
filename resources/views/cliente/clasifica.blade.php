@extends('layouts.app', ['page' => __('Clasificacion del Proveedor'), 'pageSlug' => 'clasificacionProveedor'])
@section('content')
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Nueva Clasificación') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('cliente.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group{{ $errors->has('clasificaciones') ? ' has-danger' : '' }}">
                        <label class="form-control-label" for="clasificaciones">{{ __('Clasificaciones Registradas') }}</label>
                        <div class="d-flex">
                            <select class="form-control form-control-alternative" id="clasificacion" name="clasificacion" value="{{ old('clasificaciones') }}" required>
                                <option value="0"> -- Seleccionar una clasificacion --</option>
                                @if(count($clasificacion) > 0)
                                    @foreach($clasificacion as $clas)
                                        <option value="{{ $clas->idclasificacion}}">{{ $clas->codigo_actividad }} - {{ $clas->razon_social}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="button" class="btn btn-sm btn-success" value="+" data-target="#addClasificacion" data-toggle="modal" id="agregar_actividad"/>
                            @if(count($clasificacion) > 0)
                                <input type="button" class="btn btn-sm btn-info" value="edit" id="editar_clasificacion"/>
                                <input type="button" class="btn btn-sm btn-danger" value="X" id="delete_config"/>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="col-8">
                        <h3 class="mb-0">{{ __('Información de la Clasificacion seleccionada') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group{{ $errors->has('descripcion_actividad') ? ' has-danger' : '' }}">
                        <label class="form-control-label" for="descripcion_actividad">{{ __('Actividad - Descripcion') }}</label>
                        <input type="text" name="descripcion_actividad" id="descripcion_actividad" class="form-control form-control-alternative{{ $errors->has('descripcion_actividad') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripcion') }}" value="{{ old('descripcion_actividad') }}" required readonly="true">
                        @include('alerts.feedback', ['field' => 'descripcion_actividad'])
                    </div>
                    <div class="form-group{{ $errors->has('ticlasificacion') ? ' has-danger' : '' }}">
                        <label class="form-control-label" for="ticlasificacion">{{ __('Clasificación') }}</label>
                        <select name="ticlasificacion" id="ticlasificacion" class="form-control form-control-alternative" disabled="true">
                            @foreach($clasificaciones as $clasifica)
                                <option value="{{ $clasifica->idclasifica }}">{{ $clasifica->descripcion}}</option>
                            @endforeach
                        </select>
                        @include('alerts.feedback', ['field' => 'ticlasificacion'])
                    </div>
                    <div class="form-group{{ $errors->has('default') ? ' has-danger' : '' }}">
                        <label class="form-control-label" for="default">{{ __('¿Clasificación Por Defecto?') }}</label>
                        <select name="default" id="default" class="form-control form-control-alternative" disabled="true">
                            <option value="0">No</option>
                            <option value="1" selected="true">Si</option>
                        </select>
                        @include('alerts.feedback', ['field' => 'ticlasificacion'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('modals.addNewclasificacion')
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {

        const clasificaciones = @json($clasificacion);

        $(function () {
            var selectMenus = $('#actividad, #descripcion');
            $.each(selectMenus, function () {
                $(this).click(function () {
                    //var selectedVal =
                    $('#descripcion').val($('#actividad option:selected').val());
                    $('#hidden_descripcion').val($('#descripcion option:selected').text());
                });
            });
        });

        $('#clasificacion').change(function() {
            var idclasifica = $(this).val();
            var results = clasificaciones.filter(function (nickname) { return nickname.idclasificacion == idclasifica; });
            //console.log(results[0]);
            $('#descripcion_actividad').val(results[0].codigo_actividad +' - '+ results[0].razon_social);
            var id = $('#ticlasificacion').val(results[0].tipo_clasificacion).children(":selected").attr("id");
            var defecto = $('#default').val(results[0].por_defecto).children(":selected").attr("id");

        });

        $('#editar_clasificacion').click(function() {
            var clasificacion = $('#clasificacion').val();
            if (clasificacion > 0) {

                $('#ticlasificacion').prop('disabled', false);
                $('#default').prop('disabled', false);
            } else {

                $('#ticlasificacion').prop('disabled', true);
                $('#default').prop('disabled', true);
            }
        });

        $('#delete_config').click(function() {

            var clasificacion = $('#clasificacion').val();
            if (clasificacion > 0) {

                var URL = {!! json_encode(url('deleteClasificacion')) !!};
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{idclasificacion:clasificacion},
                    success:function(response){
                        location.reload();
                    }
                });
            } else {
                alert('Seleccione una clasificacion valida!');
            }
        });

        $('#ticlasificacion').change(function() {
            var clasificacion = $('#clasificacion').val();
            var por_defecto = $('#default').val();
            if (clasificacion > 0) {
                var ticlasificacion = $(this).val();
                var URL = {!! json_encode(url('updateClasificacion')) !!};
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{idclasificacion:clasificacion, tipo_clasificacion:ticlasificacion, por_defecto:por_defecto},
                    success:function(response){
                        location.reload();
                    }
                });
            } else {
                location.reload();
            }
        });

        $('#default').change(function() {
            var clasificacion = $('#clasificacion').val();
            var ticlasificacion = $('#ticlasificacion').val();
            if (clasificacion > 0) {
                var por_defecto = $(this).val();
                var URL = {!! json_encode(url('updateClasificacion')) !!};
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{idclasificacion:clasificacion, tipo_clasificacion:ticlasificacion, por_defecto:por_defecto},
                    success:function(response){
                        location.reload();
                    }
                });
            } else {
                location.reload();
            }
        });

        $('#agregar_actividad').click(function() {

            var id = $('#num_id').val();
            var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
            $.ajax({
                type:'GET',
                url: URL,
                dataType: 'json',
                success:function(response){

                    console.log(response);
                    if (typeof response =='object') {

                        if ($.isArray([response.actividades])) {

                            $('#codigo_actividad').find('option').not(':first').remove();
                            if (response.actividades.length > 0) {

                                response.actividades.forEach(function(act, index) {
                                    $("#actividad").append('<option value="'+ act.codigo+'">'+  act.codigo +'</option>');
                                    $("#descripcion").append('<option value="'+ act.codigo+'">'+ act.descripcion+'</option>');
                                });
                            } else {

                                $("#actividad").append('<option value="112233">112233-Actividad por defecto</option>');
                                $("#descripcion").append('<option value=""112233">112233-Actividad por defecto</option>');
                            }
                        } else {

                            $('#actividad').find('option').remove();
                        }
                    }
                },
                error:function(response){
                    alert('Identificación No Encontrada');
                    $('#input-nombre_emisor').val('');
                    $('#tipo_id_emisor').val('');
                }
            });
        });
        $('#AgregarClasificacion').click(function() {
            var actividad = $('#actividad').val();
            var num_id = $('#num_id').val();
            var tipo_clasificacion = $('#tipo_clasificacion').val();
            var razon_social = $('#hidden_descripcion').val();
            if (actividad > 0) {

                var URL = {!! json_encode(url('addClasificacion')) !!};
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{codigo_actividad:actividad, num_id:num_id, tipo_clasificacion:tipo_clasificacion, razon_social:razon_social},
                    success:function(response){
                        if (response['success'] > 0) {

                            location.reload();
                        } else {
                            alert('Actividad previamente agregada!');
                        }

                    }
                });
            } else {
                alert('Seleccione un Codigo de actividad valido!');
            }
        });
    });
</script>
@endsection
