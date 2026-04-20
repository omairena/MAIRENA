@extends('layouts.app', ['page' => __('Boletas de Reparacion'), 'pageSlug' => 'crearFactura'])

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
            <form method="post" action="{{ route('boletas.update', $pedido->idpedido) }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
            @csrf
            @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                <h3 class="mb-0">{{ __('Boleta de Reparacion') }}
                               
                                <img src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="85" class="logo"/><br>
                                <a href="{{ route('boletas.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a></h3><br>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $usuario->nombre }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}" {{ ($pedido->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Descripcion') }}</label>
                                    <input type="text" readonly  name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->descripcion }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Marca') }}</label>
                                    <input type="text" readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->marca }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Modelo') }}</label>
                                    <input type="text"  readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->modelo }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Serie') }}</label>
                                    <input type="text"  readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->serie }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                             <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Factura') }}</label>
                                    <input type="text"  readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->factura }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                             <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Fecha Venta') }}</label>
                                    <input type="text"  readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->fecha_venta }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                             <div class="col-4">
                            <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Tiene Garantia') }}</label>
                                    <input type="text"  readonly name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $pedido->tiene_garantia }}">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                            </div>
                            <a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Agregar Producto</a>
                            
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
                                        <th scope="col">Precio Unitario</th>
                                         <th scope="col">Precio Total <a  class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="Si actualizas el precio desde esta opticion, se tomara como el precio total de la linea, sin hacer calculos de descuento y cantidad, es decir, el valor indicado en este campo, sera el total, de la cantidad indicada para la linea a actualizar.">¿?
                                        <th scope="col">Cant</th>
                                        <th scope="col">Descuento %</th>
                                        <th scope="col">Neto</th>
                                        <th scope="col">Descuento Monto</th>
                                        <th scope="col">Impuesto Monto</th>
                                        <th scope="col">Total</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="tabla_productos">
                                    <?php
                                        $total_neto = 0;
                                        $total_descuento = 0;
                                        $total_comprobante = 0;
                                        $total_impuesto = 0;
                                        $total_iva_devuelto = 0;
                                    ?>
                                    @foreach($pedidos_item as $pedido_i)
                                        <?php
                                            $total = $pedido_i->valor_neto+($pedido_i->valor_impuesto-$total_iva_devuelto);
                                            $total_impuesto = $total_impuesto + $pedido_i->valor_impuesto;
                                            $total_neto = $total_neto + $pedido_i->valor_neto;
                                            $total_descuento = $total_descuento + $pedido_i->valor_descuento;
                                            $total_comprobante = $total_comprobante + $total;
                                        ?>
                                        <tr>
                                            <td>{{ $pedido_i->prod_pedidos[0]->codigo_producto }}</td>
                                            @if(!empty($pedido_i->nombre_proc))
                                                <td>{{ $pedido_i->nombre_proc }}</td>
                                            @else
                                                <td>{{ $pedido_i->prod_pedidos[0]->nombre_producto }}</td>
                                            @endif
                                            
                                                <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $pedido_i->idsalesitem }}" value="{{ $pedido_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $pedido_i->idsalesitem }}" data-producto="{{ $pedido_i->idproducto }}">
                                            <td class="text-right">
                                            <input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $pedido_i->idpedidositem }}" value="{{ $pedido_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" style="width:80px;" data-id="{{ $pedido_i->idpedidositem }}" data-producto="{{ $pedido_i->idproducto }}">
                                            </td>
                                             @if(empty($pedido_i->costo_utilidad))
                                                <td>{{ number_format($pedido_i->costo_utilidad,2,',','.') }}</td>
                                            @endif
                                                <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $pedido_i->idpedidositem }}" value="{{ $pedido_i->cantidad_ped }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_pedido" style="width:80px;" data-id="{{ $pedido_i->idpedidositem }}" data-producto="{{ $pedido_i->idproducto }}"></td>
                                                <td class="text-right"><input type="number" name="descuento" id="descuento{{ $pedido_i->idpedidositem }}" value="{{ $pedido_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_pedido" style="width:80px;" data-id="{{ $pedido_i->idpedidositem }}" data-producto="{{ $pedido_i->idproducto }}"></td>
                                            <td>{{ number_format($pedido_i->valor_neto,2,',','.') }}</td>
                                            <td>{{ number_format($pedido_i->valor_descuento,2,',','.') }}</td>
                                            <td>{{ number_format($pedido_i->valor_impuesto,2,',','.') }}</td>
                                            <td><?php echo number_format($total,2,',','.'); ?></td>
                                            <td class="td-actions text-right">
                                                @if($pedido_i->prod_pedidos[0]->flotante > 0)
                                                    <button type="button" id="modificar_articulo_flotante{{ $pedido_i->idpedidositem }}" data-id="{{ $pedido_i->idpedidositem }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="td-actions text-right">
                                                @if(count($pedidos_item) > 1)
                                                    <button type="button" id="eliminar_fila{{ $pedido_i->idpedidositem }}" data-id="{{ $pedido_i->idpedidositem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_pedido">
                                                        <i class="tim-icons icon-simple-remove"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <div class="text-right">
                                <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ number_format($total_neto,2,',','.') }}</b></h4>
                                <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ number_format($total_descuento,2,',','.') }}</b> </h4>
                                <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ number_format($total_impuesto,2,',','.') }}</b></h4>
                                <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante,2,',','.') }}</b></h4>
                                <input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                <input type="text" name="numero_documento" id="numero_documento" value="{{  $pedido->numero_documento }}"  hidden="true" >
                                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $pedido->idcliente) }}" hidden="true">
                                <input type="text" name="idpedido" id="idpedido" value="{{ old('idpedido', $pedido->idpedido) }}" hidden="true">
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', 97) }}" hidden="true">
                            </div>
                            @if(Auth::user()->config_u[0]->usa_cotizacion_adicional > 0)
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <div class="form-group{{ $errors->has('value_label_aditional_1') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="value_label_aditional_1">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_1) }}</label>
                                        <input type="text" name="value_label_aditional_1" id="value_label_aditional_1" class="form-control form-control-alternative{{ $errors->has('value_label_aditional_1') ? ' is-invalid' : '' }}" value="{{ $pedido->value_label_aditional_1 }}">
                                        @include('alerts.feedback', ['field' => 'value_label_aditional_1'])
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group{{ $errors->has('value_label_aditional_2') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="value_label_aditional_2">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_2) }}</label>
                                        <input type="text" name="value_label_aditional_2" id="value_label_aditional_2" class="form-control form-control-alternative{{ $errors->has('value_label_aditional_2') ? ' is-invalid' : '' }}" value="{{ $pedido->value_label_aditional_2 }}">
                                        @include('alerts.feedback', ['field' => 'value_label_aditional_2'])
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group{{ $errors->has('value_label_aditional_3') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="value_label_aditional_3">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_3) }}</label>
                                        <input type="text" name="value_label_aditional_3" id="value_label_aditional_3" class="form-control form-control-alternative{{ $errors->has('value_label_aditional_3') ? ' is-invalid' : '' }}" value="{{ $pedido->value_label_aditional_3 }}">
                                        @include('alerts.feedback', ['field' => 'value_label_aditional_3'])
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Datos Reparacion') }}</label>
                                        <textarea   id="datos_cierre"  name="datos_cierre" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Historial') }}</label>
                                        <textarea readonly id="historial"  name="historial" class="form-control">{{ $pedido->datos_cierre }}</textarea>
                                    </div>
                                </div>
                            <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Boleta') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control">{{ $pedido->observaciones }}</textarea>
                                    </div>
                                </div>
                                 <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Falla') }}</label>
                                        <textarea id="falla"  name="falla" class="form-control">{{ $pedido->falla }}</textarea>
                                    </div>
                                </div>
                                 <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Accesorios') }}</label>
                                        <textarea id="accesorios"  name="accesorios" class="form-control">{{ $pedido->accesorios }}</textarea>
                                    </div>
                                </div>
                                <div class="col-4">
    <div class="form-group{{ $errors->has('estado_boleta') ? ' has-danger' : '' }}">
        <label class="form-control-label" for="estado_boleta">{{ __('Estado de la Boleta') }}</label>
        <select name="estado_boleta" id="estado_boleta" class="form-control form-control-alternative{{ $errors->has('estado_boleta') ? ' is-invalid' : '' }}">
    <option value="1" {{ $pedido->estatus_doc == 1 ? 'selected' : '' }}>Creada</option>
    <option value="2" {{ $pedido->estatus_doc == 2 ? 'selected' : '' }}>En Proceso</option>
    <option value="3" {{ $pedido->estatus_doc == 3 ? 'selected' : '' }}>Finalizada</option>
    <option value="4" {{ $pedido->estatus_doc == 4 ? 'selected' : '' }}>Anulada</option>
</select>
        @include('alerts.feedback', ['field' => 'estado_boleta'])
    </div>
</div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
</div>
@include('modals.addProducts')
@include('modals.modArticulo')

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        //traerNumFactura(APP_URL,o2);
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );

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
            var sales_item = $('#sales_item').val();
            var idpedido = $('#idpedido').val();
            var cantidad =  null;
            var URL = {!! json_encode(url('agregar-linea-pedido')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idpedido:idpedido, cantidad:cantidad},
                success:function(response){
                    location.reload();
                }
            });
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

         $(document).on("click", ".eliminar_fila_pedido" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-pedido')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedidositem:id},
                success:function(response){
                    location.reload();
                }
            });
        });
//omariena - 26-05-2021
        $(document).on("blur", ".update_costo_con_iva" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_con_iva = $(this).val();
            var URL = {!! json_encode(url('actualizar-pedido-con-iva')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedidositem:id, idproducto: idproducto, costo_con_iva:costo_con_iva},
                success:function(response){
                    //console.log(response);
                    location.reload();
                }
            });
        });
        //
        $(document).on("blur", ".update_cantidad_pedido" , function(event) {
            var id = $(this).data('id');
            var cantidad = $(this).val();
            var idproducto = $(this).data('producto');
            var URL = {!! json_encode(url('actualizar-cant-pedido')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedidositem:id, cantidad:cantidad, idproducto:idproducto},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_descuento_pedido" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var porcentaje_descuento = $(this).val();
            var URL = {!! json_encode(url('actualizar-desc-pedido')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedidositem:id, idproducto: idproducto, porcentaje_descuento:porcentaje_descuento},
                success:function(response){
                    location.reload();
                }
            });
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
            var nombre_cli = $(this).val();
            var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
            if ($(this).val().length <= 0) {
            }else{
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{nombre_cli:nombre_cli},
                    success:function(response){
                        var arreglo = response['success'].length;
                        if (arreglo > 0) {
                            $('#cliente_serch').val(response['success'][0]['nombre']);
                            $('#cliente').val(response['success'][0]['idcliente']);
                        }else{
                            $('#cliente_serch').val('No se encontraron resultados');
                            $('#cliente').val(0);
                        }
                    }
                });
            }
        });

        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            $('#sales_item').val(idproducto);
            var sales_item = $('#sales_item').val();
            var idpedido = $('#idpedido').val();
            var cantidad = $('#cantidad_pos_envia').val();
			var descripcioncot = $('#nombre_pos').val();
            var URL = {!! json_encode(url('agregar-linea-pedido')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idpedido:idpedido, cantidad:cantidad, descripcioncot:descripcioncot},
                success:function(response){
                    location.reload();
                }
            });
        });

        $('#cantidad_pos_envia').change(function() {
            if ($(this).val() > 0) {
                $('#agregar_producto_pos').css( "display", "block");
            }else{
                alert('La cantidad debe ser mayor a 0');
                $('#agregar_producto_pos').css( "display", "none");
            }
        });

        // ARTICULO FLOTANTE
        $('#ModArticulo').on('show.bs.modal', function(e) {
            event.preventDefault();
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsalesitem_flot"]').val(id);
            var APP_URL = {!! json_encode(url('/infoFlotanteCot')) !!};
             $.ajax({

                type:'GET',

                url: APP_URL,

                data:{id:id},

                dataType: 'json',

                success:function(response){
                    //console.log(response);
                    var cod_producto = $('#input-codigo_producto').val(response['success'].codigo_producto);
                    var nom_producto = $('#input-nombre_producto').val(response['success'].nombre_producto);
                    var cost_utl = $('#input-costo_utilidad').val(response['success'].precio_sin_imp);
                    var tip_imp = $('#tipo_impuesto').val(response['success'].impuesto_iva);
                }
            });
        });


        $(document).on("click", "#ModificarFlotanteItem" , function(event) {
            event.preventDefault();
            var datos = $('#form_flotante').serialize();
            var URL = {!! json_encode(url('modificar-flotante-ped')) !!};
            $.ajax({
                type:'GET',
                url: URL,
                dataType: 'json',
                data:{datos:datos},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", "#value_label_aditional_1" , function(event) {
            var idpedido = $('#idpedido').val();
            var label = $(this).val();
            var type = 1;
            var URL = {!! json_encode(url('actualizar-adic-1')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedido:idpedido, label:label, type:type},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", "#value_label_aditional_2" , function(event) {
            var idpedido = $('#idpedido').val();
            var label = $(this).val();
            var type = 2;
            var URL = {!! json_encode(url('actualizar-adic-1')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedido:idpedido, label:label, type:type},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", "#value_label_aditional_3" , function(event) {
            var idpedido = $('#idpedido').val();
            var label = $(this).val();
            var type = 3;
            var URL = {!! json_encode(url('actualizar-adic-1')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idpedido:idpedido, label:label, type:type},
                success:function(response){
                    location.reload();
                }
            });
        });

    });
</script>
@endsection
