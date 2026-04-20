@extends('layouts.app', ['page' => __('Crear Configuraciones Automaticas'), 'pageSlug' => 'config_automatica'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Nueva Configuracion para Recepcion') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('config_automatica.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('config_automatica.update', $configuracion_autm->idconfigautomatica) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Configuracion para Recepcion Automatica') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="idcaja">{{ __('Seleccione la Caja') }}</label>
                                    <select class="form-control form-control-alternative" id="idcaja" name="idcaja" value="{{ old('idcaja') }}" required>
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}" {{ ($caja->idcaja == $configuracion_autm->idcaja ? 'selected="selected"' : '') }}>{{ $caja->codigo_unico }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('detalle_mensaje') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="detalle_mensaje">{{ __('Detalle Mensaje para la Recepcion') }}</label>
                                    <input type="text" name="detalle_mensaje" id="detalle_mensaje" class="form-control form-control-alternative{{ $errors->has('detalle_mensaje') ? ' is-invalid' : '' }}" placeholder="{{ __('Detalle Mensaje para la Recepcion') }}" value="{{ old('detalle_mensaje', $configuracion_autm->detalle_mensaje) }}" required>
                                    @include('alerts.feedback', ['field' => 'detalle_mensaje'])
                                </div>
                                <div class="form-group{{ $errors->has('estatus') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="estatus">{{ __('Estatus') }}</label>
                                    <select name="estatus" id="estatus" class="form-control form-control-alternative" value="{{ old('estatus') }}" required="true">
                                        <option value="aprobado" {{ ($configuracion_autm->estatus == 'aprobado' ? 'selected="selected"' : '') }}>Aprobado</option>
                                        <option value="rechazado" {{ ($configuracion_autm->estatus == 'rechazado' ? 'selected="selected"' : '') }}>Rechazado</option>
                                        <option value="pendiente" {{ ($configuracion_autm->estatus == 'pendiente' ? 'selected="selected"' : '') }}>Pendiente</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'estatus'])
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

