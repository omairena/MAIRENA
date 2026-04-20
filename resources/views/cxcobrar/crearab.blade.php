@extends('layouts.app', ['page' => __('Crear Nuevo Abono'), 'pageSlug' => 'newMovimiento'])
<head>
        <script src="{{ asset('black') }}/js/nucleo_app.js"></script>
</head>
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nuevo Abono') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cxcobrar.show', $idcxcobrar) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        <form method="post" action="{{ route('logcxcobrar.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Abono') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('num_recibo_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_recibo_abono">{{ __('Número Recibo') }}</label>
                                    <input type="text" name="num_recibo_abono" id="input-num_recibo_abono" class="form-control form-control-alternative{{ $errors->has('num_recibo_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="{{ old('num_recibo_abono') }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'num_recibo_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01">Efectivo</option>
                                        <option value="02">Tarjeta</option>
                                        <option value="03">Cheque</option>
                                        <option value="04">Transferencia – depósito bancario</option>
                                        <option value="05">Recaudado por terceros</option>
                                        <option value="06">Sinpe Movil</option>
                                        <option value="07">Plataforma Digital</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                            	<div class="form-group{{ $errors->has('tipo_mov') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_mov">{{ __('Tipo de Abono') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_mov" name="tipo_mov" value="{{ old('tipo_mov') }}" required>
                                        <option value="1">Abono</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_mov'])
                                </div>
                                <div class="form-group{{ $errors->has('monto_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_abono">{{ __('Monto a Abonar') }}</label>
                                    <input type="text" step="any" name="monto_abono" id="input-monto_abono" class="form-control form-control-alternative{{ $errors->has('monto_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Abonar') }}" value="{{number_format( $saldo_pend,  5, '.', ',') }}">
                                    @include('alerts.feedback', ['field' => 'monto_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Referencia') }}</label>
                                    <input type="text" name="referencia" id="input-referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}">
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="{{ old('idmovcxcobrar', $id) }}" hidden="true">
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
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumAbono')) !!};
        traerNumAbono(APP_URL);
        $('#idcaja').change(function() {
           traerNumAbono(APP_URL);
        });
    });
</script>
@endsection

