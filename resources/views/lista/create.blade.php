@extends('layouts.app', ['page' => __('Configuración Fiscal'), 'pageSlug' => 'createconfig'])
@section('content')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Listas de Precio de la Empresa') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('list.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('list.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Descripcion de la Lista') }}</h6>
                            <div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="descripcion">{{ __('Descripcion de la Lista') }}</label>
                                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-alternative{{ $errors->has('descripcion') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripcion de la Lista') }}" value="{{ old('descripcion') }}" required>
                                @include('alerts.feedback', ['field' => 'descripcion'])
                             </div>
                            <div class="form-group{{ $errors->has('porcentaje') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="porcentaje">{{ __('Porcentaje de la Lista') }}</label>
                                <input type="text" name="porcentaje" id="porcentaje" class="form-control form-control-alternative{{ $errors->has('porcentaje') ? ' is-invalid' : '' }}" placeholder="{{ __('Porcentaje de la Lista') }}" value="{{ old('porcentaje') }}" required>
                                @include('alerts.feedback', ['field' => 'porcentaje'])
                            </div>
                            <div class="form-group{{ $errors->has('estatus') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="estatus">{{ __('Estatus') }}</label>
                                <select class="form-control form-control-alternative" id="estatus" name="estatus" value="{{ old('estatus') }}" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                @include('alerts.feedback', ['field' => 'estatus'])
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
