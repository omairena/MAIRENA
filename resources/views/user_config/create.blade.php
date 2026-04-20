@extends('layouts.app', ['page' => __('Nueva Actividad'), 'pageSlug' => 'newActividad'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Asignar Usuario a empresa') }} {{ $config->nombre_empresa }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('userconfig.show', $config->idconfigfact) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('userconfig.store') }}" autocomplete="off">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Información de Usuario') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                        <option value="{{ $config->idconfigfact }}">{{ $config->nombre_empresa }}</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                            	<div class="form-group{{ $errors->has('idusuario') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idusuario">{{ __('Configuracion Usuario') }}</label>
                                    <select class="form-control form-control-alternative" id="idusuario" name="idusuario" value="{{ old('idusuario') }}" required>
                                        @foreach($usuariosd as $users)
                                            <option value="{{ $users->id }}">{{ $users->name }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_pos') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-usa_pos">{{ __('¿Usa Punto de Venta?') }}</label>
                                    <select class="form-control form-control-alternative" id="usa_pos" name="usa_pos" value="{{ old('usa_pos') }}" required>
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_pos'])
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

