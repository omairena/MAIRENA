@extends('layouts.pos', ['page' =>"", 'pageSlug' => 'crearFactura'])

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
        @if (Session::has('message'))
            <div class="alert alert-danger">{{ Session::get('message') }}</div>
        @endif
        <div class="col-md-12">
            <form method="post" action="{{ route('punto.guardar') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
            @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="container-fluid mt--7">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="mb-0" id="encabezado_factura"></h3>
                                    </div>

                                </div>


                            </div>
                            <div class="col-6">

                                <label class="form-control-label" for="input-tipo_documento">{{Auth::user()->config_u[0]->nombre_emisor }}</label>
                                  <left><a href="{{ route('facturar.index') }}" class="btn btn-sm btn-warning" >{{ __(' Atras') }}</a></left>
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">

                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>

                                       <option value="0"selected ="true">-- Seleccione un tipo de documento --</option>
                                        @if(Auth::user()->config_u[0]->es_simplificado == 1)

                                            <option value="01">Fáctura Electrónica</option>
                                            <option value="09">Fáctura Electrónica de Exportación</option>


                                            @if(Auth::user()->config_u[0]->usa_op > 0)

                                             <option value="04" > Tiquete</option>
                                             <option value="96" >Orden de Pedido</option>
                                            @else

                                                <option value="96" >Fáctura Regimen Simplificado</option>
                                                <option value="04" > Tiquete</option>
                                            @endif
                                        @else

                                            <option value="01" >Fáctura Electrónica</option>
                                            <option value="09">Fáctura Electrónica de Exportación</option>
                                            <option value="04"  >Tiquete</option>

                                        @endif
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <div class="d-flex">
                                        <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $contado[0]->nombre }}" required>

                                        <input type="button" class="btn btn-sm btn-success" value="+" data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>

                                    </div>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}" id="combo_actividad" style="display: none;">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                               <!-- <div class="form-group{{ $errors->has('telefono') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="telefono">{{ __('Número Telefonico') }}</label>
                                    <input type="number" name="telefono" id="telefono" class="form-control form-control-alternative{{ $errors->has('telefono') ? ' is-invalid' : '' }}" placeholder="{{ __('Número Telefonico') }}"  value="{{ $contado[0]->telefono }}">

                                    @include('alerts.feedback', ['field' => 'telefono'])
                                </div>-->
                                <div class="form-group{{ $errors->has('exoneracion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="telefono">{{ __('Número Exoneracion') }}</label>
                                    <input type="text" name="exocli" id="exocli" class="form-control form-control-alternative{{ $errors->has('exocli') ? ' is-invalid' : '' }}" placeholder="{{ __('Número exocli') }}" readonly value="{{ $contado[0]->exocli }}">
                                    @include('alerts.feedback', ['field' => 'exocli'])
                                </div>

                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                         @php
                                        $cajas_punto=$caja->caja;
                                        @endphp
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($cajas_punto->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $cajas_punto->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('direccion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="direccion">{{ __('Direccion Cliente') }}</label>
                                    <textarea id="direccion" name="direccion" class="form-control">{{ $contado[0]->direccion }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
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
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required>
                                        <option value="01">Contado</option>
                                        <option value="02">Crédito</option>
                                        <option value="10">Venta a crédito en IVA hasta 90 días (Artículo 27, LIVA) </option>
                                        <option value="11">Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA)  </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito') }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                                <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                    <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                                </div>
                                 <div class="col-2">
                                        <div class="form-group text-left">
                                           <!-- <a href="#" class="btn btn-sm btn-success" data-toggle="modal" id="Agregar_producto">Agregar Producto</a>-->
                                             <button type="submit" style="display: none;" id="continuar" class="btn btn-success mt-4">{{ __('Continuar') }}</button>
                                        </div>
                                    </div>
                            </div>


                        </div>

                        </div>
                         <div class="row">
                            <div class="col-4">

                           </div>
                        </div>

                    </div>
                </div>
                <input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="00" hidden="true">
                <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">
                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}"  required>
                <input type="text" name="usa_lector" id="usa_lector" value="{{ old('usa_lector', $configuracion[0]->usa_lector ) }}" hidden="true">
                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                <input type="text" name="usa_balanza" id="usa_balanza" value="{{ old('usa_balanza', Auth::user()->config_u[0]->usa_balanza) }}" hidden="true">
                <!-- nuevos cambios para pasar el POS con el modal-->
                <input type="text" name="datos_internos" id="datos_internos" hidden="true">
            </form>
        </div>
        </div>
</div>



@endsection
@include('modals.addProducts')
@include('modals.addExoneracion')
@include('modals.newCliente')

@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(window).scroll(function() {

        });
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};

        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);
        validaMedioPago();
        validaCondicionVenta();
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                pageLength : 7,
                order: [[ 1, "asc" ]]
            }
        );
        $('#codigo_pos').focus();
        $('#input-tipo_cambio').val('0.00');

        $( "#codigo_pos" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.codigo_producto + '-' + obj.nombre_producto;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });

        $( "#nombre_pos" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/nombre')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.nombre_producto;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });

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


        $('#idcaja').change(function() {
           traerNumFactura(APP_URL,o2);
        });

            $('#tipo_documento').change(function() {
    // Llama a la función traerNumFactura siempre que cambie el tipo de documento
    traerNumFactura(APP_URL, o2);

    // Obtiene el valor del select
    var tipoDocumentoValue = $(this).val();

    // Verifica si el tipo de documento es '04'
    if (tipoDocumentoValue === '04' || tipoDocumentoValue === '96') {
        // Aquí deberías asegurarte de tener la respuesta disponible o llamarla en este contexto
        // Por ejemplo, puedes hacer una llamada AJAX si es necesario para obtener el 'nombre'

                    $('#cliente_serch').val('CLIENTE DE CONTADO');
                    $('#cliente').val(1);

                    $('#continuar').css( "display","");


    }else{

    }
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
            if( $('.select-checkbox').is(':checked') ) {
                enviarDatosProducto();
            } else {
                alert('Debe seleccionar al menos 1 producto.');
            }
        });

        $('#cliente').change(function() {
            if ($(this).val() > 0) {
                $('#Agregar_producto').css( "display","");
                $('#agregar_producto_pos').css( "display","");
            }else{
                $('#Agregar_producto').css( "display", "none");
                $('#agregar_producto_pos').css( "display", "none");
            }
        });

        $(document).on("blur", "#codigo_pos" , function(event) {
            event.preventDefault();
            var codigo_pos = $(this).val();
            var lector = $('#usa_lector').val();
            var balanza = $('#usa_balanza').val();
            if (codigo_pos.length <= 0) {
            }else{
                if (lector > 0) {
                    if (balanza > 0) {
                        var buscar = buscarProducto(codigo_pos, lector, balanza);
                    }else{
                        colocarProducto(codigo_pos, lector, balanza);
                    }
                }else{
                    if (balanza > 0) {
                        var buscar = buscarProducto(codigo_pos, lector, balanza);
                    }else{
                        colocarProducto(codigo_pos, lector, balanza);
                    }
                }
            }
        });

        $(document).on("blur", "#nombre_pos" , function(event) {
            event.preventDefault();
            var nombre_pos = $(this).val();
            var URL = {!! json_encode(url('buscar-nombre-pos')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{nombre_pos:nombre_pos},
                    success:function(response){
                        var arreglo = response['success'].length;
                        if (arreglo > 0) {
                            $('#idproducto_pos').val(response['success'][0]['idproducto']);
                            $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                            $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                            $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                            $('#cantidad_pos').prop('readonly', false);
                            $('#descuento_pos').prop('readonly', false);
                        }else{
                            $('#disponible_pos').prop('readonly', true);
                            $('#cantidad_pos').prop('readonly', true);
                            $('#descuento_pos').prop('readonly', true);
                        }
                    },
                    complete : function(xhr, status) {
                        $('#cantidad_pos_envia').focus();
                    }
                });
            }
        });




        ///omairena 30-03-2022 // se copia de sistema 02// se modifica que no seleccione 0 en cliente sino 1
        $(document).on("blur", "#cliente_serch" , function(event) {
            event.preventDefault();
            var nombre_cli = $(this).val();
            var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
            var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
            var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{nombre_cli:nombre_cli},
                    success:function(response){
                        console.log(response);
                        var arreglo = response['success'].length;
                        var tipo_documento = $('#tipo_documento').val();
                        if (arreglo > 0) {


                            if ( tipo_documento === '01' || tipo_documento === '09') {

    $('#cliente_serch').val(response['success'][0]['nombre']);
    $('#cliente').val(response['success'][0]['idcliente']);
    $('#ced_receptor').val(response['success'][0]['num_id']);
    $('#exocli').val(response['success'][0]['exocli']);
     $('#continuar').css( "display","");
    $('#datos_internos').val(1);

    var numId = response['success'][0]['num_id'];
    var apiUrl = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + numId;

    $.ajax({
        type: 'get',
        url: apiUrl,
        dataType: 'json',
        success: function(apiResponse) {
            // Verifica el estado de la respuesta de la API usando la ruta correcta
            var estado = apiResponse.situacion.estado; // Ahora accedemos al estado correctamente
            if (estado !== 'Inscrito' && estado !== 'Inscrito de Oficio') {
                alert('El estado del cliente no es válido para la emision de Facturas Electronicas, por lo tanto cambiamos el tipo de documento a Tiquete.El  Estado actual Tributario del cliente es: ' + estado);
                $('#tipo_documento').val('04');
                 traerNumFactura(APP_URL, o2);
            }
            // Continuar con la obtención del número de factura
            traerNumFactura(APP_URL, o2);
        },
        error: function() {
            alert('Error al consultar el estado en Hacienda. El estado del cliente no es válido para la emision de Facturas Electronicas, por lo tanto cambiamos el tipo de documento a Tiquete.');
            $('#tipo_documento').val('04');
             traerNumFactura(APP_URL, o2);
        }
    });
}else{

    $('#cliente_serch').val(response['success'][0]['nombre']);
    $('#cliente').val(response['success'][0]['idcliente']);
    $('#ced_receptor').val(response['success'][0]['num_id']);
    $('#exocli').val(response['success'][0]['exocli']);
     $('#continuar').css( "display","");
    $('#datos_internos').val(1);

}
                        }else{
                            $('#cliente_serch').val('');
                            $('#cliente').val(1);
                        }
                    }
                });
            }
        });
        //// fin cambio 30-03-2022


        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            var cantidad_pos = $('#cantidad_pos').val();
            var descuento_pos = $('#descuento_pos').val();
            $('#sales_item').val(idproducto);
            $("#form_factura").submit();
        });

        $('#cantidad_pos_envia').change(function() {
            if ($(this).val() > 0) {
                $('#agregar_producto_pos').css( "display", "block");
            }else{
                alert('La cantidad debe ser mayor a 0');
                $('#agregar_producto_pos').css( "display", "none");
            }
        });

        $( "#telefonos" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/telefono')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.telefono;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
        $(document).on("blur", "#telefono" , function(event) {
            event.preventDefault();
            var telefono = $(this).val();
            var URL = {!! json_encode(url('buscar-telefono')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{telefono:telefono},
                    success:function(response){
                        var arreglo = response['success'].length;
                        var tipo_documento = $('#tipo_documento').val();
                        //console.log(arreglo);
                        if (arreglo > 0) {

                               // $('#tipo_documento').val('01');
                                $('#cliente_serch').val(response['success'][0]['nombre']);
                                $('#cliente').val(response['success'][0]['idcliente']);
                                $('#telefono').val(response['success'][0]['telefono']);
                                $('#direccion').val(response['success'][0]['direccion']);
                                traerNumFactura(APP_URL,o2);

                        } else {

                            $('#cliente_serch').val('No se encontraron resultados');
                            $('#cliente').val(0);
                        }
                    }
                });
            }
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
                           // $('#tipo_documento').val('04');
                            $('#newUsuario').modal('hide');
                            $('#cliente_serch').focus();
                        }else{
                            alert('Identificación No Encontrada en nuestra base de datos');
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
                                        //if ($.isArray([response.actividades])) {
                                          //  $('#codigo_actividad_modal').find('option').remove();
                                          //  if (response.actividades.length > 0) {
                                             //   response.actividades.forEach(function(act, index) {
                                              //      $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                               //     $("#codigo_actividad_modal").append('<option value="'+ act.codigo+'">'+  act.codigo +' - '+ act.descripcion+'</option>');

                                               // });
                                           // }else{
                                               // $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');

                                           // }
                                      // }else{
                                            $('#codigo_actividad_modal').find('option').remove();
                                             $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                      //  }
                                    }
                                },
                                error:function(response){
                                   // alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación');
                                    $('#cliente_serch').val('');
                                     $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
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
                        alert('Identificación No Encontrada en nuestra base de datosS');
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
                                alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación');
                                $('#cliente_serch').val('');
                                $('#ced_receptor').focus();
                                 $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                            }
                        });
                    }
                });

            }else{

            }
        });
    });
    function buscarProducto(codigo_producto, lector, balanza){
        var URL = {!! json_encode(url('buscar-producto-pos')) !!};
        $.ajax({
            type:'get',
            url: URL,
            dataType: 'json',
            data:{codigo_pos:codigo_producto},
            success:function(response){
                var arreglo = response['success'].length;
                if (response['success'].length > 0) {
                    colocarProducto(codigo_producto, lector, balanza);
                }else{
                    var entero = codigo_producto.substring(7,9);
                    var decimal = codigo_producto.substring(9,12);
                    var new_codigo_producto = codigo_producto.substring(2,7);
                    var cantidad = entero + '.'+decimal;
                    $('#cantidad_pos_envia').val(parseFloat(cantidad));
                    $('#codigo_pos').val(new_codigo_producto);
                    colocarProducto(new_codigo_producto, lector, balanza);
                }
            },
            error: function(response){
                //response);
            }
        });

    }

    function colocarProducto(codigo_producto, lector, balanza) {
        var URL = {!! json_encode(url('buscar-producto-pos')) !!};
        $.ajax({
            type:'get',
            url: URL,
            dataType: 'json',
            data:{codigo_pos:codigo_producto},
            success:function(response){
                var arreglo = response['success'].length;
                if (arreglo > 0) {
                    if (lector > 0) {
                        $('#idproducto_pos').val(response['success'][0]['idproducto']);
                        $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                        $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                        $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                        $('#cantidad_pos_envia').focus();

                        if (balanza === 0) {
                            $('#cantidad_pos_envia').val(1);
                            $('#agregar_producto_pos').css( "display", "block");
                            $("#agregar_producto_pos").trigger("click");
                        }else{
                            $('#agregar_producto_pos').css( "display", "block");
                            $("#agregar_producto_pos").trigger("click");
                        }

                    }else{
                        $('#idproducto_pos').val(response['success'][0]['idproducto']);
                        $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                        $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                        $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                    }
                }else{
                    alert('No se encontro el Codigo de Producto, por favor agregelo.');
                    $('#disponible_pos').prop('readonly', true);
                    $('#cantidad_pos').prop('readonly', true);
                    $('#descuento_pos').prop('readonly', true);
                }
            },
            complete : function(xhr, status) {
                if (lector > 0) {
                    if (balanza > 0) {
                        $('#cantidad_pos_envia').focus();
                    }
                }else{
                    $('#cantidad_pos_envia').focus();
                }
            },
            error: function(response){
                alert('No existe el producto en Base de Datos');
            }
        });
    }

       ///omairena 30-03-2022 // se copia de sistema 02// se modifica que no seleccione 0 en cliente sino 1

        //// fin cambio 30-03-2022
</script>
@endsection
