@extends('layouts.app', ['page' => __('Reporte de Ventas'), 'pageSlug' => 'reportesSales'])
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
                <h4 class="card-title">{{ __('Descargar Ventas en EXCEL') }}</h4>
                <form method="post" action="{{ route('filtro.reportes') }}" autocomplete="off" id="filtro_factura_excel">
                    @csrf
                    <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_excel" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_excel" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte en Excel.') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body"> 
                <h4 class="card-title">{{ __('Descargar Solo PDF Masivamente') }}</h4>
                <form method="post" action="{{ route('filtroPDF.reportes') }}" autocomplete="off" id="filtro_factura_pdf">
                    @csrf
                    <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_pdf" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_pdf" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Descargar PDFs.') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body"> 
                <h4 class="card-title">{{ __('Descargar PDF y XML Masivamente') }}</h4>
                <form method="post" action="{{ route('filtroPDFXML.reportes') }}" autocomplete="off" id="filtro_factura_pdf_xml">
                    @csrf
                    <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_xml" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_xml" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Descargar PDF y XML.') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">  
                <h4 class="card-title">{{ __('Descargar Ventas en EXCEL Colonizado BETA') }}</h4>
                <form method="post" action="{{ route('filtro.reportescolon') }}" autocomplete="off" id="filtro_factura_excel_colon">
                    @csrf
                    <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                    <input type="date" name="fecha_desde" id="input-fecha_desde_colon" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                    @include('alerts.feedback', ['field' => 'fecha_desde'])
                    <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                    <input type="date" name="fecha_hasta" id="input-fecha_hasta_colon" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                    @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                    <div class="col-12">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte en Excel.') }}</button>
                        </div>
                        <p>Este Reporte exporta la ventas ACEPTADAS colonizando las emitidas en moneda extrangera y con las Notas de Credito en (-) Negativo.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        
        // Calcular el primer y último día del mes anterior
        const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        
        // Formatear las fechas como YYYY-MM-DD
        const formattedFirstDay = firstDayOfLastMonth.toISOString().split('T')[0];
        const formattedLastDay = lastDayOfLastMonth.toISOString().split('T')[0];

        // Asignar las fechas a todos los inputs (manteniendo nombres para el controlador)
        document.getElementById('input-fecha_desde_excel').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_excel').value = formattedLastDay;
        
        document.getElementById('input-fecha_desde_pdf').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_pdf').value = formattedLastDay;

        document.getElementById('input-fecha_desde_xml').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_xml').value = formattedLastDay;

        document.getElementById('input-fecha_desde_colon').value = formattedFirstDay;
        document.getElementById('input-fecha_hasta_colon').value = formattedLastDay;
    });
</script>
@endsection