@extends('layouts.app', ['page' => __('Crear Configuración Masiva'), 'pageSlug' => 'crearMasiva'])
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
        <div class="col-md-12">
            <form method="post" action="{{ route('masivo.store') }}" autocomplete="off" enctype="multipart/form-data" id="form_masivo">
            @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                <h3 class="mb-0">{{ __('Configuración Masiva') }}</h3><br>
                                <img src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="85" class="logo"/><br>
                                <a href="#" class="btn btn-sm btn-success" data-target="#AtrasModal" data-toggle="modal" id="ir_atras" >{{ __('Atras') }}</a>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('nombre_masivo') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="nombre_masivo">{{ __('Nombre de la Configuración') }}</label>
                                        <input type="text" name="nombre_masivo" id="nombre_masivo" class="form-control form-control-alternative{{ $errors->has('nombre_masivo') ? ' is-invalid' : '' }}" value="{{ $crear_masivo->nombre_masivo }}">
                                        @include('alerts.feedback', ['field' => 'nombre_masivo'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="tipo_documento">{{ __('Tipo de Documento') }}</label>
                                        <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                            <option value="0">-- Seleccione un tipo de documento --</option> 
                                            <option value="01" selected="true">Fáctura Electrónica</option>
                                            <option value="04">Tiquete</option>
                                        </select>
                                        @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                               
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-success" data-target="#AddCliente" data-toggle="modal" id="Agregar_cliente">Agregar Cliente</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center" id="tabla_productos">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Cant</th>
                                        <th scope="col">Neto</th>
                                        <th scope="col">Descuento</th>
                                        <th scope="col">Impuesto</th>
                                        <th scope="col">¿Tiene exoneración?</th>
                                        <th scope="col">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="tabla_productos">
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <div style="text-align: left;">
                                <b>Sección de Cambio</b><br><br>
                                <div class="form-group">
                                    <label for="efectivo_dev">{{ __('Efectivo:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="efectivo_dev" id="efectivo_dev" class="form-control" style="width:80px; display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="tarjeta_dev">{{ __('Tarjeta:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="tarjeta_dev" id="tarjeta_dev" class="form-control" style="width:80px; display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="cambio_dev">{{ __('Cambio:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="cambio_dev" id="cambio_dev" class="form-control" style="width:80px; display: inline !important;">
                                </div>
                            </div>
                            <div class="text-right">
                                <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto"></b></h4>
                                <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento"></b> </h4>
                                <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto"></b></h4>
                                <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto"></b></h4>
                                <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento"></b></h4>
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="idlogmasivo" id="idlogmasivo" value="{{ old('idlogmasivo', $crear_masivo->idlogmasivo) }}" hidden="true">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
</div>
@include('modals.addCliente')
@include('modals.irAtras')

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);
        var table = $('#tabla_productos').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        
        $('#agregar_cliente').click(function(e) {
            e.preventDefault();
            var datos_form = $('#form_add_cliente').serialize();
            var idlogmasivo = $('#idlogmasivo').val();
            var punto_venta = $('#punto_venta').val();
            var idcaja = $('#idcaja').val();
            var idcodigoactv = $('#actividad').val();
            var tipo_documento = $('#tipo_documento').val();
            var nombre_masivo = $('#nombre_masivo').val();
            var APP_URL = {!! json_encode(url('/ajaxGuardarCliente')) !!};
            $.ajax({
                type:'GET',
                url: APP_URL,
                data:{idlogmasivo:idlogmasivo, tipo_documento:tipo_documento, punto_venta:punto_venta, idcaja:idcaja, idcodigoactv:idcodigoactv, datos_form:datos_form, nombre_masivo:nombre_masivo},
                dataType: 'json',
                success:function(response){
                    if(response.success){  
                        window.location = response.url;
                    }
                }
            });
            $('#AddCliente').modal('hide');
        });

         $('#volver_atras').click(function(e) {
            e.preventDefault();
            var idlogmasivo = $('#idlogmasivo').val();
            var APP_URL = {!! json_encode(url('/ajaxBorrarMasivo')) !!};
            $.ajax({
                type:'GET',
                url: APP_URL,
                data:{idlogmasivo:idlogmasivo},
                dataType: 'json',
                success:function(response){
                    if(response.success){  
                        window.location = response.url;
                    }
                }
            });
            $('#AtrasModal').modal('hide');
        });

        $('#condicion_venta_mod').change(function() {
            var condicion = $(this).val();
            if (condicion === '02') {
                $('#p_credito_mod').prop("required", true);
                $('#pl_credito_mod').css( "display", "block");
            }else{
                $('#p_credito_mod').prop("required", false);
                $('#pl_credito_mod').css( "display", "none");
            }
        });

    });
</script>
@endsection