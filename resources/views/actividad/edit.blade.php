@extends('layouts.app', ['page' => __('Nueva Actividad'), 'pageSlug' => 'newActividad'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Modificar Actividad') }} {{ $config->nombre_empresa }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('actividad.show', $actividad->idconfigfact) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('actividad.update',  $actividad->idcodigoactv) }}" autocomplete="off">
                            @csrf
                            @method('put')
                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Actividad') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                        <option value="{{ $config->idconfigfact }}">{{ $config->nombre_empresa }}</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                            	<div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-descripcion">{{ __('Descripción Actividad') }}</label>
                                    <input type="text" name="descripcion" id="input-descripcion" class="form-control form-control-alternative{{ $errors->has('descripcion') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripción Actividad') }}" value="{{ $actividad->descripcion }}" required>
                                    @include('alerts.feedback', ['field' => 'descripcion'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad') }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a>
                                    </label>
                                    <input type="number" name="codigo_actividad" id="codigo_actividad" class="form-control form-control-alternative{{ $errors->has('codigo_actividad') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Actividad') }}" value="{{ $actividad->codigo_actividad }}" required>
                                       <input type="hidden" name="codigo_actividad_id" id="codigo_actividad_id" class="form-control form-control-alternative{{ $errors->has('codigo_actividad') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Actividad') }}" value="{{ $actividad->idcodigoactv }}" required>
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_impresion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="principal">{{ __('¿Actividad Principal?') }}</label>
                                    <select class="form-control form-control-alternative" id="principal" name="principal"  required>
                                            <option value="0">No</option>
                                            <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_impresion'])
                                </div>
                                 <div class="form-group{{ $errors->has('usa_impresion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="principal">{{ __('Estado') }}</label>
                                    <select class="form-control form-control-alternative" id="estado" name="estado"  required>
                                            <option value="0">Activo</option>
                                            <option value="1">Inactivo</option>
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

