@extends('layouts.app', ['page' => __('Crear Factura Electronica'), 'pageSlug' => 'crearFactura'])
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
            <form method="post" action="{{ route('pedidos.store') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
            @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                <h3 class="mb-0">{{ __('Crear Cotización') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3><br>
                                <img src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="85" class="logo"/><br>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $contado[0]->nombre }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">

                            </div>
                            <div class="col-4">

                            </div>
                            <div class="col-4">
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Agregar Producto</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table tablesorter ">
                            <thead class=" text-primary">
                                <th scope="col" style="text-align: center;">{{ __('Código') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Nombre Producto') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Cantidad') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Disponible') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody id="buscar_articulo_pos">
                                <tr>
                                    <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                                    <td>
                                        <input type="text" name="codigo_pos" id="codigo_pos" class="form-control form-control-alternative{{ $errors->has('codigo_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td>
                                       <input type="text" name="nombre_pos" id="nombre_pos" class="typeahead form-control form-control-alternative{{ $errors->has('nombre_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" style="width:80px;">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" step="any" name="disponible_pos" id="disponible_pos" class="form-control form-control-alternative{{ $errors->has('disponible_pos') ? ' is-invalid' : '' }}" style="width:80px;" readonly="true">
                                    </td>
                                    <td>
                                        <button class="btn btn-success" type="submit" id="agregar_producto_pos" style="display: none;">Agregar Producto</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="tabla_productos">
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <div class="text-right">
                                <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto"></b></h4>
                                <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento"></b> </h4>
                                <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto"></b></h4>
                                <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto"></b></h4>
                                <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento"></b></h4>
                                <input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}" hidden="true">
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', 97) }}" hidden="true">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
</div>
@include('modals.addProducts')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('#codigo_pos').focus();
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
                            return obj.codigo_producto;
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

        $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
            $('#sales_item').val(data);
        });


        $('#agregar_producto').click(function(e) {
            e.preventDefault();
            var datos = $('#form_factura').serialize();
            $('#AddProductos').modal('hide');
            $("#form_factura").submit();
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

        $(document).on("click", "#eliminar_fila" , function(event) {
            event.preventDefault();
            var valor = $('#exoneracion').val();
        });

        $(document).on("blur", "#codigo_pos" , function(event) {
            event.preventDefault();
            var codigo_pos = $(this).val();
            var lector = $('#usa_lector').val();
            var URL = {!! json_encode(url('buscar-producto-pos')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{codigo_pos:codigo_pos},
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
                                $('#cantidad_pos_envia').val(1);
                                $('#agregar_producto_pos').css( "display", "block");
                                $("#agregar_producto_pos").trigger("click");
                            }else{
                                $('#idproducto_pos').val(response['success'][0]['idproducto']);
                                $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                                $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                                $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                                $('#cantidad_pos').prop('readonly', false);
                                $('#descuento_pos').prop('readonly', false);
                            }
                        }else{
                            $('#disponible_pos').prop('readonly', true);
                            $('#cantidad_pos').prop('readonly', true);
                            $('#descuento_pos').prop('readonly', true);
                        }
                    },
                    complete : function(xhr, status) {
                        if (lector > 0) {

                        }else{
                            $('#cantidad_pos_envia').focus();
                        }
                    }
                });
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

        $(document).on("blur", "#cliente_serch" , function(event) {
            event.preventDefault();
            var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
            var dataItem = {
                nombre_cli: $(this).val(),
                desde:"PED",
                URL: URL,
                APP_URL: APP_URL,
                o2: o2,
            }
            traerNombreCliente(dataItem);
        });

        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            var cantidad_pos = $('#cantidad_pos_envia').val();
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

    });
</script>
@endsection
