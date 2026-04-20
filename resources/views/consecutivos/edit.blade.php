@extends('layouts.app', ['page' => __('Editar Consecutivo'), 'pageSlug' => 'editConsecutivo'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Editar Consecutivo') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('config.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('consecutivo.update', $consecutivo[0]->idconsecutivo) }}" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Información del Consecutivo') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <input type="text" name="tipo_documento" id="tipo_documento" class="form-control form-control-alternative{{ $errors->has('tipo_documento') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Documento') }}" value="{{ $consecutivo[0]->tipo_documento }}" required>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('numero_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="numero_documento">{{ __('Número de Documento') }}</label>
                                    <input type="number" name="numero_documento" id="numero_documento" class="form-control form-control-alternative{{ $errors->has('numero_documento') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Documento') }}" value="{{ $consecutivo[0]->numero_documento }}" required>
                                    @include('alerts.feedback', ['field' => 'numero_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('doc_desde') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="doc_desde">{{ __('Factura Desde') }}</label>
                                    <input type="number" name="doc_desde" id="doc_desde" class="form-control form-control-alternative{{ $errors->has('doc_desde') ? ' is-invalid' : '' }}" placeholder="{{ __('Factura Desde') }}" value="{{ $consecutivo[0]->doc_desde }}" required>
                                    @include('alerts.feedback', ['field' => 'doc_desde'])
                                </div>
                                <div class="form-group{{ $errors->has('doc_hasta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="doc_hasta">{{ __('Factura Hasta') }}</label>
                                    <input type="number" name="doc_hasta" id="doc_hasta" class="form-control form-control-alternative{{ $errors->has('doc_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Factura Hasta') }}" value="{{ $consecutivo[0]->doc_hasta }}" required>
                                    @include('alerts.feedback', ['field' => 'doc_hasta'])
                                </div>
                                <!--<div class="form-group{{ $errors->has('tipo_compra') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_compra">{{ __('Tipo de Compra') }}</label>
                                    <input type="number" name="tipo_compra" id="tipo_compra" class="form-control form-control-alternative{{ $errors->has('tipo_compra') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Compra') }}" value="{{ $consecutivo[0]->tipo_compra }}" required>
                                    @include('alerts.feedback', ['field' => 'tipo_compra'])
                                </div>-->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Editar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
