@extends('layouts.app', ['page' => __('Nueva Actividad'), 'pageSlug' => 'newActividad'])
@section('content')
@if($errors->any())
    <div class="alert alert-danger">{{$errors->first()}}</div>
@endif
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Editar la caja') }} {{ $caja->nombre_caja }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cajas.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cajas.update', $caja->idcaja) }}" autocomplete="off">
                            @csrf
                            @method('put')
                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Caja') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                        @foreach($configuracion as $config)
                                            <option value="{{ $config->idconfigfact }}" {{ ($caja->idconfigfact == $config->idconfigfact ? 'selected="selected"' : '') }}>{{ $config->nombre_empresa }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_caja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_caja">{{ __('Nombre Caja') }}</label>
                                    <input type="text" name="nombre_caja" id="input-nombre_caja" class="form-control form-control-alternative{{ $errors->has('nombre_caja') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Caja') }}" value="{{ old('nombre_caja', $caja->nombre_caja) }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_caja'])
                                </div>
                                <div class="form-group{{ $errors->has('monto_fondo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_fondo">{{ __('Monto de Fondo') }}
                                    </label>
                                    <input type="number" step="any" name="monto_fondo" id="monto_fondo" class="form-control form-control-alternative{{ $errors->has('monto_fondo') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto de Fondo') }}" value="{{ old('monto_fondo', $caja->monto_fondo) }}"  required>
                                    @include('alerts.feedback', ['field' => 'monto_fondo'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_unico') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_unico">{{ __('Código Único de Caja') }}</label>
                                    <input type="number" name="codigo_unico" id="input-codigo_unico" class="form-control form-control-alternative{{ $errors->has('codigo_unico') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Único de Caja') }}" value="{{ old('codigo_unico', $caja->codigo_unico) }}" required>
                                    @include('alerts.feedback', ['field' => 'codigo_unico'])
                                </div>
                                <!-- <div class="form-group{{ $errors->has('usa_impresion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_impresion">{{ __('¿Usa Impresión?') }}</label>
                                    <select class="form-control form-control-alternative" id="usa_impresion" name="usa_impresion" value="{{ old('usa_impresion') }}" required>
                                            <option value="0" {{ ($caja->usa_impresion == 0 ? 'selected="selected"' : '') }}>No</option>
                                            <option value="1" {{ ($caja->usa_impresion == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_impresion'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_imp">{{ __('Nombre Impresora') }}</label>
                                    <input type="text" name="nombre_imp" id="input-nombre_imp" class="form-control form-control-alternative{{ $errors->has('nombre_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Impresora') }}" value="{{ old('nombre_imp', $caja->nombre_imp) }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_imp'])
                                </div>
                                <div class="form-group{{ $errors->has('ip_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-ip_imp">{{ __('IP de la Impresora') }}</label>
                                    <input type="text" name="ip_imp" id="input-ip_imp" class="form-control form-control-alternative{{ $errors->has('ip_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('IP de la Impresora') }}" value="{{ old('ip_imp', $caja->ip_imp) }}" required>
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

