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
            <form method="post" action="{{ route('boletas.store') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
            @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                <h3 class="mb-0">{{ __('Crear Boleta Reparacion') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3><br>
                                <img src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="85" class="logo"/><br>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                            <div class="card-body">
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label> <input type="button" class="btn btn-sm btn-success" value="+ Cliente." data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $contado[0]->nombre }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                    
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                         
                        
                        </div>
                    </div>
                   
                    <div class="card-body">
                        <!-- Informaci車n de los art赤culos a reparar -->
                        <h4 class="mb-3">Informacion del Articulo a Reparar</h4>
                        
                        <div class="form-group">
                            <label for="descripcion">{{ __('Descripcion') }}</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="marca">{{ __('Marca') }}</label>
                            <input type="text" name="marca" id="marca" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="modelo">{{ __('Modelo') }}</label>
                            <input type="text" name="modelo" id="modelo" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="serie">{{ __('# Serie') }}</label>
                            <input type="text" name="serie" id="serie" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="factura">{{ __('# Factura') }}</label>
                            <input type="text" name="factura" id="factura" class="form-control" >
                        </div>
                        
                        <div class="form-group">
                            <label for="num_servicio">{{ __('# de Servicio') }}</label>
                            <input type="text" name="num_servicio" id="num_servicio" class="form-control" >
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_venta">{{ __('Fecha de Venta') }}</label>
                            <input type="date" name="fecha_venta" id="fecha_venta" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="tiene_garantia">{{ __('Tiene Garantia?') }}</label>
                            <select name="tiene_garantia" id="tiene_garantia" class="form-control" required>
                                 <option value="">--</option>
                                 
                                <option value="SI">Si</option>
                                <option value="NO">No</option>
                                <option value="NA">N/A</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="accesorios">{{ __('Accesorios') }}</label>
                            <textarea name="accesorios" id="accesorios" class="form-control" rows="3"  placeholder="Especificar accesorios"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="falla">{{ __('Falla') }}</label>
                            <textarea name="falla" id="falla" class="form-control" rows="3" placeholder="Descripcion de la falla" ></textarea>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">{{ __('Observaciones') }}</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"  placeholder="Observaciones adicionales"></textarea>
                        </div>
   <div class="form-group">
                            <div class="text-right">
                                <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                                <input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}" hidden="true">
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', 97) }}" hidden="true">
                                
                            </div>
                        </div>
                        <!-- Bot車n para guardar la boleta -->
                        <div class="form-group">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Guardar Boleta</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('modals.addProducts')
@include('modals.newboletaCliente')
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
    });
</script>
@endsection
