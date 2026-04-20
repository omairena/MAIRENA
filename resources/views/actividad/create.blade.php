@extends('layouts.app', ['page' => __('Nueva Actividad'), 'pageSlug' => 'newActividad'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Nueva Actividad para la empresa') }} {{ $config->nombre_empresa }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('actividad.show', $config->idconfigfact) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('actividad.store') }}" autocomplete="off">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Actividad') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                        <option value="{{ $config->idconfigfact }}">{{ $config->nombre_empresa }}</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('num_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_id">{{ __('Número de Identificación') }}</label>
                                    <input type="text" name="num_id" id="num_id" class="form-control form-control-alternative{{ $errors->has('num_id') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ old('num_id', $config->numero_id_emisor) }}" required readonly="true">
                                    @include('alerts.feedback', ['field' => 'num_id'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad') }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a>
                                    </label>
                                    <select class="form-control form-control-alternative chosen-select" id="actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" required>
                                    </select>
                                     <input type="text" name="hidden_codigo" id="hidden_codigo" class="form-control form-control-alternative{{ $errors->has('hidden_codigo') ? ' is-invalid' : '' }}" value="{{ old('hidden_codigo') }}" required >
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>
                            	<div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="descripcion">{{ __('Descripción Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="descripcion" name="descripcion" value="{{ old('descripcion') }}"   >
                                    </select>
                                    @include('alerts.feedback', ['field' => 'descripcion'])
                                </div>
                               
                                <input type="text" name="hidden_descripcion" id="hidden_descripcion" class="form-control form-control-alternative{{ $errors->has('hidden_descripcion') ? ' is-invalid' : '' }}" value="{{ old('hidden_descripcion') }}" required >
                                  <div class="form-group{{ $errors->has('usa_impresion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="principal">{{ __('¿Actividad Principal?') }}</label>
                                    <select class="form-control form-control-alternative" id="principal" name="principal" value="{{ old('principal') }}" required>
                                            <option value="0">No</option>
                                            <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_impresion'])
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
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        var id = $('#num_id').val();
        var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
        $.ajax({
            type:'GET',
            url: URL,
            dataType: 'json',
            success:function(response){
                //console.log(response);
                if (typeof response =='object') {
                    if ($.isArray([response.actividades])) {
                        $('#codigo_actividad').find('option').remove();
                        if (response.actividades.length > 0) {
                            response.actividades.forEach(function(act, index) {
                                $("#actividad").append('<option value="'+ act.codigo+'">'+  act.codigo +'</option>');
                                $("#descripcion").append('<option value="'+ act.codigo+'">'+ act.descripcion+'</option>');
                            });
                        }else{
                            $("#actividad").append('<option value="112233">112233-Actividad por defecto</option>');
                            $("#descripcion").append('<option value=""112233">112233-Actividad por defecto</option>');
                        }
                    }else{
                       // $('#actividad').find('option').remove();
                        $("#actividad").append('<option value="112233">112233-Actividad por defecto</option>');
                            $("#descripcion").append('<option value=""112233">112233-Actividad por defecto</option>');
                    }
                }
            },
            error:function(response){
                alert('Identificación No Encontrada');
               // $('#input-nombre_emisor').val('');
               // $('#tipo_id_emisor').val('');
                
                 $("#actividad").append('<option value="112233">112233-Actividad por defecto</option>');
                            $("#descripcion").append('<option value=""112233">112233-Actividad por defecto</option>');
            }
        });

        $(function () {
            var selectMenus = $('#actividad, #descripcion');
            $.each(selectMenus, function () {
                $(this).click(function () {
                    //var selectedVal =
                    $('#descripcion').val($('#actividad option:selected').val());
                    $('#hidden_descripcion').val($('#descripcion option:selected').text());
                    $('#hidden_codigo').val($('#actividad option:selected').text());
                    hidden_codigo
                });
            });
        });
    });
</script>
@endsection
