@extends('layouts.app', ['page' => __('Crear Registro'), 'pageSlug' => 'newMovimiento'])
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
                                <a href="{{ route('ingresos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        <form method="post" action="{{ route('ingresos.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Registro Ingresos') }}</h6>
                            <div class="pl-lg-4">
                                 <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Datos Generales') }}</label>
                                   
                                    <input type="text" name="nombre" id="input-nombre" class="form-control form-control-alternative{{ $errors->has('nombre') ? ' is-invalid' : '' }}" placeholder="{{ __('nombre') }}" value="{{ $cliente[0]->nombre }}">
                                    <input type="hidden" name="idcliente" id="input-idcliente" class="form-control form-control-alternative{{ $errors->has('idcliente') ? ' is-invalid' : '' }}" placeholder="{{ __('idcliente') }}" value="{{ $cliente[0]->idcliente }}">
                                    <input type="text" name="num_id" id="input-num_id" class="form-control form-control-alternative{{ $errors->has('num_id') ? ' is-invalid' : '' }}" placeholder="{{ __('num_id') }}" value="{{ $cliente[0]->num_id }}">
                                   
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                <div class="form-group{{ $errors->has('num_recibo_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_recibo_abono">{{ __('Tipo Doc') }}</label>
                                   
                                     <input type="text" name="t_factura" id="input-t_factura" class="form-control form-control-alternative{{ $errors->has('t_factura') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="{{ old('sales', $sales[0]->tipo_documento) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'num_recibo_abono'])
                                </div>
                                
                                <div class="form-group{{ $errors->has('num_recibo_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_recibo_abono">{{ __('Número Factura') }}</label>
                                    <input type="hidden" name="id_referencia" id="input-id_referencia" class="form-control form-control-alternative{{ $errors->has('id_referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="{{ old('sales', $id) }}" readonly="true">
                                     <input type="text" name="factura" id="input-factura" class="form-control form-control-alternative{{ $errors->has('factura') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="{{ old('sales', $sales[0]->numdoc) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'num_recibo_abono'])
                                </div>
    
                 
                <input type="hidden" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="20" readonly="true">
               
     
     
     
                <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Cuenta Bancaria') }}</label>
                <select name="banco" id="banco" class="form-control form-control-alternative">
                    @foreach($bancos as $banco)
                        <option value="{{ $banco->id_bancos }}">{{ $banco->cuenta}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
                                
                            
                                <div class="form-group{{ $errors->has('monto_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-monto_abono">{{ __('Monto a Abonar') }}</label>
                                    <input type="text" step="any" name="monto_abono" id="input-monto_abono" class="form-control form-control-alternative{{ $errors->has('monto_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Abonar') }}" value="{{ old('sales', $totalc) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'monto_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Referencia') }}</label>
                                    <input type="text" name="referencia" id="input-referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}" required>
                                    
                                     <input type="text" name="obs" id="input-obs" class="form-control form-control-alternative{{ $errors->has('obs') ? ' is-invalid' : '' }}" placeholder="{{ __('Observaciones') }}" value="{{ old('referencia') }}">
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
   
     $( "#cliente_serch" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/cliente')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.nombre;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
     $(document).on("blur", "#cliente_serch" , function(event) {
           event.preventDefault();
           var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
            var dataItem = {
                nombre_cli: $(this).val(),
              
                desde:"POS",
                URL: URL,
                APP_URL: APP_URL,
                o2: o2,
            }
            traerNombreCliente(dataItem);

        });
</script>
@endsection

