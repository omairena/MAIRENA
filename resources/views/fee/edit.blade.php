@extends('layouts.app', ['page' => __('Fact. Elec. Exportacion'), 'pageSlug' => 'crearFactura'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
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
                                <h3 class="mb-0">{{ __('Crear Factura Fiscal') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura">Documento # {{ $sales->numero_documento }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('fee.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('fee.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Factura Fiscal') }}</h6>
                            <div class="pl-lg-4">
                            	<div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                    	<option value="0">-- Seleccione un tipo de documento --</option> 
                                        <option value="09" {{ ($sales->tipo_documento == '09' ? 'selected="selected"' : '') }}>Fáctura Electrónica de Exportación</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                            	<div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required>
                                    	<option value="0">-- Seleccione una Configuración --</option>
                                    @foreach($configuracion as $config) 
                                        <option value="{{ $config->idconfigfact }}" {{ ($sales->idconfigfact == $config->idconfigfact ? 'selected="selected"' : '') }}>{{ $config->nombre_empresa }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
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
                                            <option value="{{ $caja->idcaja }}" {{ ($sales->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required>
                                    	<option value="0">-- Seleccione un Cliente --</option>
                                    @foreach($clientes as $cliente) 
                                    <?php 
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
                                        	case '05':
                                            $tipo_ident = 'Ext-';
                                        	break;
                                    	}

                                    ?>
                                        <option value="{{ $cliente->idcliente }}" {{ ($sales->idcliente == $cliente->idcliente ? 'selected="selected"' : '') }}>{{$tipo_ident}}{{$cliente->num_id }} {{ $cliente->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda') }}" required>
                                        <option value="CRC" {{ ($sales->tipo_moneda == 'CRC' ? 'selected="selected"' : '') }}>Colon Costaricense</option>
                                        <option value="USD" {{ ($sales->tipo_moneda == 'USD' ? 'selected="selected"' : '') }}>Dólar Americano</option>
                                        <option value="EUR" {{ ($sales->tipo_moneda == 'EUR' ? 'selected="selected"' : '') }}>Euro</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" >
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio', $sales->tipo_cambio) }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required> 
                                        <option value="01" {{ ($sales->condición_venta == '01' ? 'selected="selected"' : '') }}>Contado</option>
                                        <option value="02" {{ ($sales->condición_venta == '02' ? 'selected="selected"' : '') }}>Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito', $sales->p_credito) }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required> 
                                        <option value="01" {{ ($sales->medio_pago == '01' ? 'selected="selected"' : '') }}>Efectivo</option>
                                        <option value="02" {{ ($sales->medio_pago == '02' ? 'selected="selected"' : '') }}>Tarjeta</option>
                                        <option value="03" {{ ($sales->medio_pago == '03' ? 'selected="selected"' : '') }}>Cheque</option>
                                        <option value="04" {{ ($sales->medio_pago == '04' ? 'selected="selected"' : '') }}>Transferencia – depósito bancario</option>
                                        <option value="05" {{ ($sales->medio_pago == '05' ? 'selected="selected"' : '') }}>Recaudado por terceros</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago', $sales->referencia_pago) }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                <div class="form-group text-right">
                                	<a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Agregar Producto</a>
                                </div>
                                <div class="table-responsive" style="padding: 40px;margin-bottom: 20px;">
    								<table class="table align-items-center" id="tabla_productos">
    									<thead class="thead-light">
        									<tr>
            									<th scope="col">#</th>
            									<th scope="col">Nombre</th>
                                                <th scope="col">Precio Unitario</th>
            									<th scope="col">Cant</th>
            									<th scope="col">Descuento %</th>
                                                <th scope="col">Neto</th>
                                                <th scope="col">Descuento Monto</th>
            									<th scope="col">Impuesto Monto</th>
                                                <th scope="col">¿Tiene exoneración?</th>
            									<th scope="col">Total</th>
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
                                            @foreach($sales_item as $sale_i)
                                                <?php
                                                    $prod = App\Productos::find($sale_i->idproducto);
                                                    if ($sale_i->existe_exoneracion == '00') {
                                                        $tiene_exoneracion = 'No';
                                                        if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                                                            $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                        }
                                                        $total = $sale_i->valor_neto + ($sale_i->valor_impuesto -  $total_iva_devuelto);
                                                        $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
                                                    }else{
                                                        $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
                                                        $tiene_exoneracion = 'Si '.$exoneracion[0]->porcentaje_exoneracion. ' %';
                                                        $monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
                                                        if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                                                            $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                        }
                                                        $total = ($sale_i->valor_neto+ $monto_imp_exonerado)-$total_iva_devuelto;
                                                        $total_impuesto = $total_impuesto + $monto_imp_exonerado;
                                                    }
                                                    $total_neto = $total_neto + $sale_i->valor_neto;
                                                    $total_descuento = $total_descuento + $sale_i->valor_descuento;
                                                    $total_comprobante = $total_comprobante + $total;
                                                 ?>
                                                <tr>
                                                    <td>{{ $sale_i->codigo_producto }}</td>
                                                    <td>{{ $sale_i->nombre_producto }}</td>
                                                    @if($prod->tipo_producto === 2)
                                                    <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                    @else
                                                        <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                    @endif
                                                    @if($sale_i->existe_exoneracion === '00')
                                                    <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}"></td>
                                                    <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}"></td>
                                                    @else
                                                    <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                    <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                    @endif
                                                    <td>{{ $sale_i->valor_neto }}</td>
                                                    <td>{{ $sale_i->valor_descuento }}</td>
                                                    @if($sale_i->existe_exoneracion === '00')
                                                    <td>{{ $sale_i->valor_impuesto }}</td>
                                                    @else
                                                    <td>{{ $monto_imp_exonerado }}</td>
                                                    @endif
                                                    <td><?php echo $tiene_exoneracion; ?></td>
                                                    <td><?php echo $total; ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                 <a href="#" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item modificar_flotante" data-target="#ModArticulo" data-toggle="modal">{{ __('Modificar Artículo') }}</a>
                                                                @if(count($sales_item) > 1)
                                                                <a href="#" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item eliminar_fila_factura">{{ __('Eliminar Fila') }}</a>
                                                                @endif
                                                                
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
    									</tbody>
									</table>
								</div>
								 <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura de Compra') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control" required="true">{{ $sales->observaciones }}</textarea>
                                    </div>
                                </div>
								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ $total_neto }}</b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ $total_descuento }}</b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ $total_impuesto }}</b></h4>
                                    @if($total_iva_devuelto > 0)
                                    <h4 class="mb-0" id="iva_d" >IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ $total_iva_devuelto }}</b></h4>
                                    @endif
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ $total_comprobante }}</b></h4>
                                	<input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                	<input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                                    <input type="text" name="hidden_observaciones" id="hidden_observaciones" value="{{ old('hidden_observaciones') }}" hidden="true">
                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="{{ old('existe_exoneracion', $sales->tiene_exoneracion) }}" hidden="true">
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                </div>
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
@include('modals.addProducts')
@include('modals.addExoneracion')
@include('modals.modArticulo')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#factura_datatables').DataTable();
       // $('#input-tipo_cambio').val('0.00');
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);

        if ($('#medio_pago').val() === '01') {
            $('#referencia_p').css( "display", "none");
        }else{
            $('#referencia_p').css( "display", "block");
        }



        $('#idconfigfact').change(function() {
            var idconfigfact = $(this).val();
            var tipo_documento = $('#tipo_documento').val();
            var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{idconfigfact:idconfigfact, tipo_documento:tipo_documento},

                dataType: 'json',

                success:function(response){
                    var option = "Documento # " + response['success']['numero_factura'];
                    $("#encabezado_factura").empty();
                    $("#encabezado_factura").append(option);
                    $('#numero_documento').val(response['success']['numero_factura']);
                    var arreglo = response['success']['codigo_actividad'].length;
                    if (arreglo > 0) {
                        $('#combo_actividad').css( "display", "block");
                        $('#combo_caja').css( "display", "block");
                        $('#actividad').find('option').remove();
                        $('#idcaja').find('option').remove();
                        $(response['success']['cajas']).each(function(data) {
                        $("#idcaja").append('<option value="'+response['success']['cajas'][data].idcaja+'">'+ response['success']['cajas'][data].codigo_unico+' - '+ response['success']['cajas'][data].nombre_caja+'</option>');
                        });
                        $(response['success']['codigo_actividad']).each(function(data) {
                        $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                        });
                    }else{
                        $('#actividad').find('option').remove();
                        $('#combo_actividad').css( "display", "none");
                        $('#combo_caja').css( "display", "none");
                        $('#idcaja').find('option').remove();
                    }
                }
            });
        });
        $('#moneda').change(function() {
            switch($(this).val()){
                case 'CRC':
                   $('#input-tipo_cambio').val(response.valor);
                    $('#input-tipo_cambio').val('0.00');
                break;
                case 'USD':
                   // $('#tipo_cambio').css( "display", "block");
                    var URL_USD = 'https://api.hacienda.go.cr/indicadores/tc/dolar';
                    $.ajax({                        
                        type:'get',

                        url: URL_USD,

                        dataType: 'json',

                        success:function(response){
                            if (response == null) {
                                alert('Conexion fallida con Hacienda');
                            }else{
                                $('#input-tipo_cambio').val(response.venta.valor);
                                $("#input-tipo_cambio").prop('readonly', true);
                            }
                        }
                    });
                break;
                case 'EUR':
                    $('#tipo_cambio').css( "display", "block");
                    var URL_EUR = 'https://api.hacienda.go.cr/indicadores/tc/euro';
                    $.ajax({                        
                        type:'get',

                        url: URL_EUR,

                        dataType: 'json',

                        success:function(response){
                            if (response == null) {
                                alert('Conexion fallida con Hacienda');
                            }else{
                                $('#input-tipo_cambio').val(response.valor);
                                $("#input-tipo_cambio").prop('readonly', true);
                            }
                        }
                    });
                break;
            }
        });

        $('[name="seleccion[]"]').click(function() {
      
            var arr = $('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = arr.join(',');
            $('#sales_item').val(arr);
        });

        $('#agregar_producto').click(function(e) {
            e.preventDefault();
            var sales_item = $('#sales_item').val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("click", ".eliminar_fila_factura" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_cantidad_factura" , function(event) {
            var id = $(this).data('id');
            var cantidad = $(this).val();
            var idproducto = $(this).data('producto');
            var URL = {!! json_encode(url('actualizar-cant-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, cantidad:cantidad, idproducto:idproducto},
                success:function(response){
                    location.reload();
                }
            });
        });

$(document).on("blur", "#observaciones" , function(event) {
            event.preventDefault();
            var observacion = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-observacion-fec')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{observacion:observacion, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });
        
        $(document).on("blur", ".update_descuento_factura" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var porcentaje_descuento = $(this).val();
            var URL = {!! json_encode(url('actualizar-desc-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, idproducto: idproducto, porcentaje_descuento:porcentaje_descuento},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_costo_factura" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_utilidad = $(this).val();
            var URL = {!! json_encode(url('actualizar-costo-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, idproducto: idproducto, costo_utilidad:costo_utilidad},
                success:function(response){
                    //console.log(response);
                    location.reload();
                }
            });
        });

        $('#input-numero_exoneracion').on("blur", function( e ) {
            e.preventDefault();
            var valor = $(this).val();
            var identificacion = traerIdentificacion($('#cliente').val());
            var URL = 'https://api.hacienda.go.cr/fe/ex?identificacion='+identificacion;
            var tipo_exoneracion = $('#tipo_exoneracion').val();
            alert(tipo_exoneracion);
            if (tipo_exoneracion === '04') {
                $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                success:function(response){
                    if (response.exoneraciones.length > 0) {
                        for (var i = 0; i < response.exoneraciones.length; i++) {
                            if (response.exoneraciones[i].numeroExoneracion === valor) {
                                $('#input-fecha_exoneracion').val(response.exoneraciones[i].fechaInicio);
                                $('#input-institucion').attr("readonly", false);
                                $('#input-porcentaje_exoneracion').attr("readonly", false);
                                alert('Exoneracion Permitida');
                                break;
                            }else{
                                $('#input-institucion').attr("readonly", true);
                                $('#input-porcentaje_exoneracion').attr("readonly", true);
                                alert('El numero de la exoneracion no existe en la base de datos de hacienda');
                            }
                        }
                    }
                }
                });
            }else{
                $('#input-institucion').attr("readonly", false);
                $('#input-porcentaje_exoneracion').attr("readonly", false);
            }
        });

        $(document).on("click", "#AgregarExoneracion" , function(event) {
            event.preventDefault();
            var datos = $('#form_exoneracion').serialize();
            var URL = {!! json_encode(url('agregar-exoneracion')) !!};
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

 
        $('#AddExoneracion').on('show.bs.modal', function(e) {
            event.preventDefault();
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsaleitem_exo"]').val(id);
        });

        function traerIdentificacion(idcliente) {
            var URL = {!! json_encode(url('traer-cliente')) !!};
            var respuesta = null;
            $.ajax({
                type:'GET',
                url: URL,
                dataType: 'json',
                data:{idcliente:idcliente},
                async: false,
                success:function(response){
                    respuesta = response.success;
                }
            });
            return respuesta;
        }
        
         // ARTICULO FLOTANTE
        $('#ModArticulo').on('show.bs.modal', function(e) {
            event.preventDefault();
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsalesitem_flot"]').val(id);
            var APP_URL = {!! json_encode(url('/infoFlotante')) !!};
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
            var URL = {!! json_encode(url('modificar-flotante')) !!};
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
    });
</script>
@endsection