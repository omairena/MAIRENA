@extends('layouts.app', ['page' => __('Reporte de Recepci¨®n'), 'pageSlug' => 'reportesRecepcion'])
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
                <form method="post" action="{{ route('filtro.recepcion') }}" autocomplete="off" id="filtro_factura_recepcion">
                    @csrf
                      <h4 class="card-title">{{ __('Descargar en Excel Documentos Recepcionados') }}</h4>
                    <label class="form-control-label" for="input-fecha_desde_recepcion">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_recepcion" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta_recepcion">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_recepcion" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body"> 
                <h4 class="card-title">{{ __('Descargar XML de Recepciones Masivamente') }}</h4>
                <form method="post" action="{{ route('receptorxml.reportes') }}" autocomplete="off" id="filtro_factura_xml">
                @csrf
                    <label class="form-control-label" for="input-fecha_desde_xml">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_xml" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta_xml">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_xml" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Descargar XML.') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        
        // Calcular el primer y ¨²ltimo d¨ªa del mes anterior
        const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        
        // Formatear las fechas como YYYY-MM-DD
        const formattedFirstDay = firstDayOfLastMonth.toISOString().split('T')[0];
        const formattedLastDay = lastDayOfLastMonth.toISOString().split('T')[0];

        // Asignar las fechas a los inputs de los formularios
        document.getElementById('input-fecha_desde_recepcion').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_recepcion').value = formattedLastDay;

        document.getElementById('input-fecha_desde_xml').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_xml').value = formattedLastDay;
    });
</script>
@endsection