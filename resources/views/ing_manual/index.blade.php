@extends('layouts.app', ['page' => __('Crear Registro'), 'pageSlug' => 'newMovimiento'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
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
                                <h3 class="mb-0">{{ __('Crear Registro Manual') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('ingresos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        <form method="post" action="{{ route('ing_manual.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Registro Ingresos') }}</h6>
                            <div class="pl-lg-4">
                                 <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Cliente') }}</label>
                                   
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="" >
                                    <input type="hidden" name="cliente" id="cliente" class="form-control form-control-alternative{{ $errors->has('cliente') ? ' is-invalid' : '' }}" value="" >
                                 
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                
                                <div class="form-group{{ $errors->has('num_recibo_abono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-num_recibo_abono">{{ __('Número Referencia') }}</label>
                                    <input type="text" name="id_referencia" id="input-id_referencia" class="form-control form-control-alternative{{ $errors->has('id_referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Recibo') }}" value="" >
                                    @include('alerts.feedback', ['field' => 'num_recibo_abono'])
                                </div>
    
                 
               
               
     
     
     
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
                                    <input type="text" step="any" name="monto_abono" id="input-monto_abono" class="form-control form-control-alternative{{ $errors->has('monto_abono') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Abonar') }}" value="" >
                                    @include('alerts.feedback', ['field' => 'monto_abono'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-referencia">{{ __('Referencia Banco') }}</label>
                                    <input type="text" name="referencia" id="input-referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}" required>
                                    
                                     <input type="text" name="obs" id="input-obs" class="form-control form-control-alternative{{ $errors->has('obs') ? ' is-invalid' : '' }}" placeholder="{{ __('Observaciones') }}" value="{{ old('referencia') }}">
                                    @include('alerts.feedback', ['field' => 'referencia'])
                                </div>
                                <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="" hidden="true">
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
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
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
            var nombre_cli = $(this).val();
            var URL = {!! json_encode(url('buscar-cliente-posfe')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{nombre_cli:nombre_cli},
                    success:function(response){
                        //console.log(response);
                        var arreglo = response['success'].length;
                        var tipo_documento = $('#tipo_documento').val();
                        if (arreglo > 0) {
                            if (response['success'][0]['num_id'] != 100000000 && tipo_documento === '04') {
                                $('#tipo_documento').val('01');
                                traerNumFactura(APP_URL,o2);
                            }
                            if (response['success'][0]['num_id'] === 100000000 && tipo_documento === '01') {
                                alert('seleccionar otro tipo de documento');
                                $('#tipo_documento').focus();
                            }else{
                                $('#cliente_serch').val(response['success'][0]['nombre']);
                                $('#cliente').val(response['success'][0]['idcliente']);
                                $('#cedula_id_cliente').val(response['success'][0]['num_id']);
                                $('#Agregar_producto').css( "display","");
                                $('#ced_receptor').val(response['success'][0]['num_id']);
                                $('#datos_internos').val(1);
                                traerNumFactura(APP_URL,o2);
                            }
                        }else{
                            $('#cliente_serch').val('No se encontraron resultados');
                            $('#cliente').val(1);
                             $('#cedula_id_cliente').val('');
                             $('#Agregar_producto').css( "display", "none");
                        }
                    }
                });
            }
        });
</script>
@endsection

