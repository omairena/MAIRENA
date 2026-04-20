@extends('layouts.app', ['page' => __('Reporte de Documentos Emitidos PDF'), 'pageSlug' => 'reportesCxc'])
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
                    <form method="post" action="{{ route('filtro.fac') }}" autocomplete="off" id="filtro_factura">
                    @csrf
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Selección de Configuracion') }}</label>
                            <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                @foreach($configuracion as $config) 
                                    <option value="{{ $config->idconfigfact }}">{{ $config->nombre_empresa }}</option>
                                @endforeach
                            </select>
                             <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}" id="combo_actividad" style="display: none;">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>

                            <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                            <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                            @include('alerts.feedback', ['field' => 'fecha_desde'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                            <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
            var idconfigfact = $('#idconfigfact').val();
            var APP_URL = {!! json_encode(url('/consultaEmpresa')) !!};
            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idconfigfact:idconfigfact},

                dataType: 'json',

                success:function(response){
                    var arreglo = response['success']['codigo_actividad'].length;
                    if (arreglo > 0) {
                        $('#combo_actividad').css( "display", "block");
                        $('#actividad').find('option').remove();
                        $(response['success']['codigo_actividad']).each(function(data) {
                        $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                        });
                    }else{
                        $('#actividad').find('option').remove();
                        $('#combo_actividad').css( "display", "none");
                    }
                }
            });
        $('#idconfigfact').change(function() {
            var idconfigfact = $(this).val();
            var APP_URL = {!! json_encode(url('/consultaEmpresa')) !!};
            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idconfigfact:idconfigfact},

                dataType: 'json',

                success:function(response){
                    var arreglo = response['success']['codigo_actividad'].length;
                    if (arreglo > 0) {
                        $('#combo_actividad').css( "display", "block");
                        $('#actividad').find('option').remove();
                        $(response['success']['codigo_actividad']).each(function(data) {
                        $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                        });
                    }else{
                        $('#actividad').find('option').remove();
                        $('#combo_actividad').css( "display", "none");
                    }
                }
            });
        });
    });
</script>
@endsection

