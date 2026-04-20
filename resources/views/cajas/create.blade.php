@extends('layouts.app', ['page' => __('Nueva Caja'), 'pageSlug' => 'crearCajas'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Nueva Caja') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cajas.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cajas.store') }}" autocomplete="off">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Caja') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                        @foreach($configuracion as $config)
                                            <option value="{{ $config->idconfigfact }}">{{ $config->nombre_empresa }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                            	<div class="form-group{{ $errors->has('nombre_caja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_caja">{{ __('Nombre Caja') }}</label>
                                    <input type="text" name="nombre_caja" id="input-nombre_caja" class="form-control form-control-alternative{{ $errors->has('nombre_caja') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Caja') }}" value="{{ old('nombre_caja') }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_caja'])
                                </div>
                                <div class="form-group{{ $errors->has('monto_fondo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_fondo">{{ __('Monto de Fondo') }}
                                    </label>
                                    <input type="number" step="any" name="monto_fondo" id="monto_fondo" class="form-control form-control-alternative{{ $errors->has('monto_fondo') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto de Fondo') }}" required>
                                    @include('alerts.feedback', ['field' => 'monto_fondo'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_unico') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_unico">{{ __('Código Único de Caja') }}</label>
                                    <input type="number" name="codigo_unico" id="input-codigo_unico" class="form-control form-control-alternative{{ $errors->has('codigo_unico') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Único de Caja') }}" value="{{ old('codigo_unico') }}" required>
                                    @include('alerts.feedback', ['field' => 'codigo_unico'])
                                </div>
                             <div class="form-group{{ $errors->has('usa_impresion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="ip_imp">{{ __('¿Caja Principal?') }}</label>
                                    <select class="form-control form-control-alternative" id="ip_imp" name="ip_imp" value="{{ old('ip_imp') }}" required>
                                            <option value="0">No</option>
                                            <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_impresion'])
                                </div>
                               <!-- <div class="form-group{{ $errors->has('nombre_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_imp">{{ __('Nombre Impresora') }}</label>
                                    <input type="text" name="nombre_imp" id="input-nombre_imp" class="form-control form-control-alternative{{ $errors->has('nombre_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Impresora') }}" value="{{ old('nombre_imp') }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_imp'])
                                </div>
                                <div class="form-group{{ $errors->has('ip_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-ip_imp">{{ __('IP de la Impresora') }}</label>
                                    <input type="text" name="ip_imp" id="input-ip_imp" class="form-control form-control-alternative{{ $errors->has('ip_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('IP de la Impresora') }}" value="{{ old('ip_imp') }}" required>
                                    @include('alerts.feedback', ['field' => 'ip_imp'])
                                </div> -->
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

