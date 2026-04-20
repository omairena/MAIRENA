@extends('layouts.app', ['page' => __('Reporte de FEC'), 'pageSlug' => 'reportesSales'])
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
                    <form method="post" action="{{ route('filtro.fec') }}" autocomplete="off" id="filtro_factura">
                    @csrf
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