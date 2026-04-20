@extends('layouts.app', ['page' => __('Factura de Compra'), 'pageSlug' => 'crearCompras'])
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
                                <h3 class="mb-0">{{ __('Factura de Compra') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('fec.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('fec.guardar') }}" autocomplete="off" enctype="multipart/form-data" id="form_fec">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Factura de Compra Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                        <option value="08">Fáctura Electrónica de Compra</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                      <label class="form-control-label" for="input-cliente">{{ __('Proveedor a Facturar') }}</label>

                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ '' }}">
                                       <input type="text" name="cedula_id_cliente" id="cedula_id_cliente" class="form-control form-control-alternative{{ $errors->has('cedula_id_cliente') ? ' is-invalid' : '' }}" value="{{'' }}" readonly>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>



                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">



                                  <!-- <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required>
                                    	<option value="0">-- Seleccione un Proveedor --</option>
                                    @foreach($clientes as $cliente)
                                    <?php
                                       $tipo_ident = 0;
                                    	switch ($cliente->tipo_id) {
                                        	case '01':
                                        	   $tipo_ident = 'CN-';
                                        	break;
                                        	case '02':
                                            $tipo_ident = 'CJ-';
                                        	break;
                                        	case '03':
                                            $tipo_ident = 'DIME-';
                                        	break;
                                        	case '04':
                                            $tipo_ident = 'NITE-';
                                        	break;
                                    	}

                                    ?>
                                        <option value="{{ $cliente->idcliente }}">{{ $cliente->nombre }} {{$tipo_ident}}{{$cliente->num_id }} </option>
                                    @endforeach
                                    </select>-->
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                <input type="button" class="btn btn-sm btn-success" value="+" data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>
                                <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda') }}" required>
                                        <option value="CRC">Colon Costaricense</option>
                                        <option value="USD">Dólar Americano</option>
                                        <option value="EUR">Euro</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio') }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required>
                                        <option value="01">Contado</option>
                                        <option value="02">Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito') }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
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
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago') }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                <div class="form-group text-right">
                                	<a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto" style="display: none;">Agregar Producto</a>
                                </div>
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
            									<th scope="col">Total</th>
        									</tr>
    									</thead>
    									<tbody class="tabla_productos">
    									</tbody>
									</table>
								</div>
								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto"></b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento"></b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto"></b></h4>
                                    <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto"></b></h4>
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento"></b></h4>
                                	<input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                	<input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                	<input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                                    <input type="text" name="productos_fec" id="productos_fec" value="{{ old('productos_fec') }}" hidden="true">
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                     <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $clientes[0]->idcliente) }}"  hidden="true" >
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura de Compra') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control" required="true"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('referencia_compra') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="referencia_compra">{{ __('Número de Factura emisora de Factura de Compra') }}</label>
                                        <input type="text" id="referencia_compra" name="referencia_compra" class="form-control" max="50">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4 text-center" id="guardar_factura" style="display: none;">{{ __('Guardar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@include('modals.addProducts')
@include('modals.newfcCliente')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('#input-tipo_cambio').val('0.00');
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);
         $('#idcaja').change(function() {
           traerNumFactura(APP_URL,o2);
        });

        $('#tipo_documento').change(function() {
            traerNumFactura(APP_URL,o2);
        });
        $('#moneda').change(function() {
            valorMoneda($(this).val());
        });

        $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
            $('#sales_item').val(data);
        });

        $('#agregar_producto').click(function(e) {
            e.preventDefault();
            var datos = $('#form_fec').serialize();
            $('#AddProductos').modal('hide');
            $("#form_fec").submit();
        });

        $('#cliente').change(function() {
            if ($(this).val() > 0) {
                $('#Agregar_producto').css( "display","");
            }else{
                $('#Agregar_producto').css( "display", "none");
            }
        });


    });

    $(document).on("blur", "#ced_receptor" , function(event) {
            event.preventDefault();
            var num_id = $(this).val();
            var URL = {!! json_encode(url('buscar-identificacion')) !!};
            if ($(this).val().length > 0) {
                $.ajax({
                    type:'GET',
                    url: URL,
                    dataType: 'json',
                    data:{num_id:num_id},
                    success:function(response){
                        //console.log(response);
                        if (response['success'] === true) {
                            alert('Cliente ya registrado en el sistema.');
                            $('#cliente_serch').val(response['default'][0]['nombre']);
                            $('#datos_internos').val(1);
                            $('#cliente').val(response['default'][0]['idcliente']);


                            $('#tipo_documento').val('01');
                            $('#newUsuario').modal('hide');
                            $('#cliente_serch').focus();
                        }else{
                            alert('Identificación No Encontrada en nuestra base de datos 3');
                            $('#cliente_serch').val('');

                            $('#cliente').val('0');
                            var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + num_id;
                             //var api = 'https://apis.gometa.org/cedulas/' + num_id;

                            $.ajax({
                                type:'GET',
                                url: api,
                                dataType: 'json',
                                data:{num_id:num_id},
                                success:function(response){
                                    //console.log(response);
                                    $('#cliente_hacienda').val(JSON.stringify(response));
                                    $('#datos_internos').val(0);
                                    if (typeof response =='object') {
                                        $('#cliente_serch_modal').val(response.nombre);
                                        $('#tipo_id_modal').val(response.tipoIdentificacion);
                                        if ($.isArray([response.actividades])) {
                                            $('#codigo_actividad_modal').find('option').remove();
                                           if (response.actividades.length > 0) {
                                                response.actividades.forEach(function(act, index) {
                                                    $("#codigo_actividad_modal").append('<option value="'+ act.codigo+'">'+  act.codigo +' - '+ act.descripcion+'</option>');
                                                });
                                            }else{
                                                $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                            }
                                        }else{
                                            $('#codigo_actividad_modal').find('option').remove();
                                            $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                        }
                                    }
                                },
                                error:function(response){
                                    alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación3');
                                    $('#cliente_serch').val('');
                                }
                            });

                            // Consulta nueva Hacienda Marzo 2022
                            var settings = {
                                "url": "https://api.hacienda.go.cr/fe/mifacturacorreo?identificacion=" + num_id,
                                "method": "GET",
                                "timeout": 0,
                                "headers": {
                                    "access-user": "206410122",
                                    "access-token": "hQXs4KNNs8HPZ6aRC5oX",
                                    "Content-Type": "application/json",
                                    "Cookie": "TS01d94531=0120156b28a33842b0975df1c1170f626694e5d4c555793b626be216ec5e19637b13a6765c419143ba945cca5258abb36dfd71f363"
                                },
                            };

                            $.ajax(settings).done(function (response) {
                                //console.log(response);
                                if (response['Resultado']['Correos'].length > 0) {
                                    $('#input-email').val(response['Resultado']['Correos'][0]['Correo']);
                                }

                            });
                        }
                    },
                    error:function(response){
                        alert('Identificación No Encontrada en nuestra base de datos4');
                        $('#cliente_serch').val('');
                        var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
                         // var api = 'https://apis.gometa.org/cedulas/' + id;
                        $.ajax({
                            type:'GET',
                            url: api,
                            dataType: 'json',
                            success:function(response){
                                console.log(response);
                            },
                            error:function(response){
                                alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación4');
                                $('#cliente_serch').val('');
                                $('#ced_receptor').focus();
                            }
                        });
                    }
                });

            }else{

            }

    });
  ///omairena 19-04-2023
   $( "#cliente_serch" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/clientefec')}}",
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
                               // $('#cliente_serch').val('');
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
