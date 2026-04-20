@extends('layouts.app', ['page' => __('Editar Configuración Masiva'), 'pageSlug' => 'editarMasiva'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
        div.inline { float:left; }
        .clearBoth { clear:both; }

    </style>
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>
</head>
@section('content')
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="{{ route('masivo.update', $idlogmasivo) }}" autocomplete="off" enctype="multipart/form-data" id="form_masivo">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                <h3 class="mb-0">{{ __('Configuración Masiva') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3><br>
                                <img src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="85" class="logo"/><br>
                                <a href="{{ route('masivo.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                            <div class="col-4">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="tipo_documento">{{ __('Tipo de Documento') }}</label>
                                        <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                            <option value="0">-- Seleccione un tipo de documento --</option>
                                            <option value="01" {{ ($config_cliente->tipo_documento_mas == 01 ? 'selected="selected"' : '') }}>Fáctura Electrónica</option>
                                            <option value="04" {{ ($config_cliente->tipo_documento_mas == 04 ? 'selected="selected"' : '') }}>Tiquete</option>
                                        </select>
                                        @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                 <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <div class="d-flex">
                                        <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required>
                                            @foreach($config_masivo as $cmc)
                                                <option value="{{ $cmc->idclientes }}" {{ ($cmc->idclientes == $config_cliente->idclientes ? 'selected="selected"' : '') }}>{{ $cmc->configmas_cli[0]->nombre  }}</option>
                                            @endforeach
                                        </select>
                                        @if(count($clientes) > 0)
                                            <input type="button" class="btn btn-sm btn-success" value="+" data-target="#AddCliente" data-toggle="modal" id="Agregar_cliente"/>
                                        @endif
                                        @if(count($config_masivo) > 1)
                                            <input type="button" class="btn btn-sm btn-danger" value="X" id="delete_config"/>
                                        @endif
                                    </div>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>

                            </div>
                            <div class="col-4">
                             <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}" id="combo_actividad">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                   <!-- <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad', $config_masivo[0]->idcodigoactv) }}" required>
                                         
                                    </select>-->
                                    
                                    <input type="text" name="actividad" readonly id="actividad" value="{{ old('actividad', $config_masivo[0]->idcodigoactv) }}" class="form-control form-control-alternative{{ $errors->has('nombre_pos') ? ' is-invalid' : '' }}"> 
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative" value="{{ old('idcaja', $config_cliente->idcaja) }}">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}" {{ ($config_cliente->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta', $config_cliente->condicion_venta) }}" required>
                                        <option value="01" {{ ($config_cliente->condicion_venta == 01 ? 'selected="selected"' : '') }}>Contado</option>
                                        <option value="02" {{ ($config_cliente->condicion_venta == 02 ? 'selected="selected"' : '') }}>Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito', $config_cliente->p_credito) }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda', $config_cliente->tipo_moneda) }}" required>
                                        <option value="CRC" {{ ($config_cliente->tipo_moneda == 'CRC' ? 'selected="selected"' : '') }}>Colon Costaricense</option>
                                        <option value="USD" {{ ($config_cliente->tipo_moneda == 'USD' ? 'selected="selected"' : '') }}>Dólar Americano</option>
                                        <option value="EUR" {{ ($config_cliente->tipo_moneda == 'EUR' ? 'selected="selected"' : '') }}>Euro</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                 <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio' , $config_cliente->tipo_cambio) }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                            <div class="col-3">
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01"  {{ ($config_cliente->medio_pago == 01 ? 'selected="selected"' : '') }}>Efectivo</option>
                                        <option value="02" {{ ($config_cliente->medio_pago == 02 ? 'selected="selected"' : '') }}>Tarjeta</option>
                                        <option value="03" {{ ($config_cliente->medio_pago == 03 ? 'selected="selected"' : '') }}>Cheque</option>
                                        <option value="04" {{ ($config_cliente->medio_pago == 04 ? 'selected="selected"' : '') }}>Transferencia – depósito bancario</option>
                                        <option value="05" {{ ($config_cliente->medio_pago == 05 ? 'selected="selected"' : '') }}>Recaudado por terceros</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago') }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                            </div>

                            <div class="col-12">
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
                                       <input type="text" name="nombre_pos" id="nombre_pos" class="form-control form-control-alternative{{ $errors->has('nombre_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" style="width:80px;">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" step="any" name="disponible_pos" id="disponible_pos" class="form-control form-control-alternative{{ $errors->has('disponible_pos') ? ' is-invalid' : '' }}" style="width:80px;" readonly="true">
                                    </td>
                                    <td>
                                        <button class="btn btn-success" type="button" id="agregar_producto_pos" style="display: none;">Agregar Producto</button>
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
                                        <th scope="col">Cant</th>
                                        <th scope="col">Descuento %</th>
                                        <th scope="col">Neto</th>
                                        <th scope="col">Descuento Monto</th>
                                        <th scope="col">Impuesto Monto</th>
                                        <th scope="col">¿Tiene exoneración?</th>
                                        <th scope="col">Total</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
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
                                    @foreach($items_masivo as $sale_i)
                                        <?php
                                            if ($sale_i->existe_exoneracion == '00') {
                                                $tiene_exoneracion = 'No';
                                                $total = $sale_i->valor_neto+($sale_i->valor_impuesto-$total_iva_devuelto);
                                                $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
                                            }else{
                                                $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
                                                $tiene_exoneracion = 'Si '.$exoneracion[0]->porcentaje_exoneracion. ' %';
                                                $monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
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
                                            @if($sale_i->prod_masivo[0]->tipo_producto === 2)
                                                <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $sale_i->iditemsmasivo }}" value="{{ $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $sale_i->iditemsmasivo }}" data-producto="{{ $sale_i->idproducto }}">
                                            @else
                                                <td>{{ number_format($sale_i->costo_utilidad,2,',','.') }}</td>
                                            @endif
                                            @if($sale_i->existe_exoneracion === '00')
                                                <td class="text-right"><input type="number" name="cantidad_masivo" id="cantidad_masivo{{ $sale_i->iditemsmasivo }}" value="{{ $sale_i->cantidad_masivo }}" class="form-control form-control-alternative{{ $errors->has('cantidad_masivo') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->iditemsmasivo }}" data-producto="{{ $sale_i->idproducto }}"></td>
                                                <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->iditemsmasivo }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->iditemsmasivo }}" data-producto="{{ $sale_i->idproducto }}"></td>
                                            @else
                                                <td class="text-right"><input type="number" name="cantidad_masivo" id="cantidad_masivo{{ $sale_i->iditemsmasivo }}" value="{{ $sale_i->cantidad_masivo }}" class="form-control form-control-alternative{{ $errors->has('cantidad_masivo') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->iditemsmasivo }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->iditemsmasivo }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->iditemsmasivo }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                            @endif
                                            <td>{{ number_format($sale_i->valor_neto,2,',','.') }}</td>
                                            <td>{{ number_format($sale_i->valor_descuento,2,',','.') }}</td>
                                            @if($sale_i->existe_exoneracion === '00')
                                                <td>{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                                            @else
                                                <td>{{ number_format($monto_imp_exonerado,2,',','.') }}</td>
                                            @endif
                                            <td><?php echo $tiene_exoneracion; ?></td>
                                            <td><?php echo number_format($total,2,',','.'); ?></td>

                                            <td class="td-actions text-right">
                                                @if($sale_i->prod_masivo[0]->flotante > 0)
                                                    <button type="button" id="modificar_articulo_flotante{{ $sale_i->iditemsmasivo }}" data-id="{{ $sale_i->iditemsmasivo }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="td-actions text-right">
                                                @if(count($items_masivo) > 1)
                                                    <button type="button" id="eliminar_fila{{ $sale_i->iditemsmasivo }}" data-id="{{ $sale_i->iditemsmasivo }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
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
                            <div style="text-align: left;">
                                <b>Sección de Cambio</b><br><br>
                                <div class="form-group">
                                    <label for="efectivo_dev" style="font-weight: bold;">{{ __('Efectivo:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="efectivo_dev" id="efectivo_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="tarjeta_dev" style="font-weight: bold;">{{ __('Tarjeta:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="tarjeta_dev" id="tarjeta_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="cambio_dev" style="font-weight: bold;">{{ __('Cambio:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="cambio_dev" id="cambio_dev" class="form-control" style="width:180px; display: inline !important;" readonly="true" value="0">
                                </div>
                            </div>
                            <div class="text-right">
                                <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ number_format($total_neto,2,',','.') }}</b></h4>
                                <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ number_format($total_descuento,2,',','.') }}</b> </h4>
                                <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ number_format($total_impuesto,2,',','.') }}</b></h4>
                                @if($total_iva_devuelto > 0)
                                    <h4 class="mb-0" id="iva_d" >IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ number_format($total_iva_devuelto,2,',','.') }}</b></h4>
                                @endif
                                <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante,2,',','.') }}</b></h4>
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="idlogmasivo" id="idlogmasivo" value="{{ old('idlogmasivo', $idlogmasivo) }}" hidden="true">
                                <input type="text" name="idconfigmasivo" id="idconfigmasivo" value="{{ old('idconfigmasivo', $config_cliente->idconfigmasivo) }}" hidden="true">
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                <input type="text" name="caja" id="caja" value="{{ old('caja', $config_cliente->idcaja) }}" hidden="true">
                                <input type="text" name="codigoactv" id="codigoactv" value="{{ old('codigoactv', $config_cliente->idcodigoactv) }}" hidden="true">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group{{ $errors->has('observaciones_masivo') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="observaciones_masivo">{{ __('Observaciones de Factura') }}</label>
                                <textarea id="observaciones_masivo" name="observaciones_masivo" class="form-control">{{ $config_cliente->observacion_masivo }}</textarea>
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
@include('modals.addCliente')

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumMasivo')) !!};
        traerNumMasivo(APP_URL);
        validaMedioPago();
        validaCondicionVenta();
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('#tabla_productos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "autoWidth": true,
            "processing": true,
            "serverSide": false,
            "deferRender": true,
            order: [[ 1, "asc" ]]
        });

        if ($('#medio_pago').val() === '01') {
            $('#referencia_p').css( "display", "none");
        }else{
            $('#referencia_p').css( "display", "block");
        }

        if ($('#condición_venta').val() === '01') {
            $('#pl_credito').css( "display", "none");
        }else{
            $('#pl_credito').css( "display", "block");
        }
if ($('#moneda').val() === 'CRC') {
            $('#tipo_cambio').css( "display", "block");
           $('#input-tipo_cambio').val(0.00);
        }else{
            $('#tipo_cambio').css( "display", "block");
            $("#input-tipo_cambio").prop('readonly', true);
        }

        $('#cliente').change(function() {
            var cliente = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var APP_URL = {!! json_encode(url('/ajaxEditarConfig')) !!};
            $.ajax({
                type:'get',
                url: APP_URL,
                dataType: 'json',
                data:{idlogmasivo:idlogmasivo,cliente:cliente},
                success:function(response){
                    window.location = response.url;
                },
                error:function(response){
                    //console.log(response);
                }
            });
        });

        $('#tipo_documento').change(function() {
            traerNumMasivo(APP_URL);
        });

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
            var idconfigmasivo = $('#idconfigmasivo').val();
            var cantidad =  null;
            var URL = {!! json_encode(url('agregar-linea-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idconfigmasivo:idconfigmasivo, cantidad:cantidad},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("click", ".eliminar_fila_factura" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{iditemsmasivo:id},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_cantidad_factura" , function(event) {
            var id = $(this).data('id');
            var cantidad = $(this).val();
            var idproducto = $(this).data('producto');
            var URL = {!! json_encode(url('actualizar-cant-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{iditemsmasivo:id, cantidad:cantidad, idproducto:idproducto},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_descuento_factura" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var porcentaje_descuento = $(this).val();
            var URL = {!! json_encode(url('actualizar-desc-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{iditemsmasivo:id, idproducto: idproducto, porcentaje_descuento:porcentaje_descuento},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_costo_factura" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_utilidad = $(this).val();
            var URL = {!! json_encode(url('actualizar-costo-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{iditemsmasivo:id, idproducto: idproducto, costo_utilidad:costo_utilidad},
                success:function(response){
                    location.reload();
                }
            });
        });

        $('#input-numero_exoneracion').on("blur", function( e ) {
            e.preventDefault();
            var autorizacion = $(this).val();
            var URL = 'https://api.hacienda.go.cr/fe/ex?autorizacion='+autorizacion;
            var tipo_exoneracion = $('#tipo_exoneracion').val();
            if (tipo_exoneracion === '04') {
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    success:function(response){
                        if (response.numeroDocumento === autorizacion) {
                            $('#input-fecha_exoneracion').val(response.fechaEmision);
                            $('#input-institucion').val(response.nombreInstitucion);
                            $('#input-institucion').attr("readonly", true);
                            $('#input-porcentaje_exoneracion').attr("readonly", false);
                            alert('Exoneracion Permitida');
                        }else{
                            $('#input-institucion').attr("readonly", true);
                            $('#input-porcentaje_exoneracion').attr("readonly", true);
                            alert('El numero de la exoneracion no existe en la base de datos de hacienda');
                        }
                    },
                    error:function(response){
                        alert('error en el servidor de hacienda, no se logro ubicar la informacion');
                    }
                });
            }else{
                $('#input-institucion').attr("readonly", false);
                $('#input-porcentaje_exoneracion').attr("readonly", false);
            }
        });

        $(document).on("click", "#AgregarExoneracion" , function(event) {
            event.preventDefault();
            if ($('#input-porcentaje_exoneracion').val() <= 13) {
                var prc_exo = $('#input-porcentaje_exoneracion').val();
                var id_sales_item = $('#idsaleitem_exo').val();
                var URL = {!! json_encode(url('validar-porcentaje-mas')) !!};
                $.ajax({
                    type:'GET',
                    url: URL,
                    dataType: 'json',
                    data:{prc_exo:prc_exo, id_sales_item:id_sales_item},
                    success:function(response){
                        if (response['respuesta'] === 1) {
                            $('#input-porcentaje_exoneracion').val(response['prc_a_usar']);
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
                        }else{
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
                        }
                    }
                });
            }else{
                alert('El Porcentaje de Exoneración es mayor al permitido');
            }

        });


        $('#AddExoneracion').on('show.bs.modal', function(e) {
            event.preventDefault();
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsaleitem_exo"]').val(id);
        });

        // ARTICULO FLOTANTE
        $('#ModArticulo').on('show.bs.modal', function(e) {
            event.preventDefault();
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsalesitem_flot"]').val(id);
            var APP_URL = {!! json_encode(url('/infoFlotanteMas')) !!};
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
            var URL = {!! json_encode(url('modificar-flotante-mas')) !!};
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


        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            $('#sales_item').val(idproducto);
            var sales_item = $('#sales_item').val();
            var idconfigmasivo = $('#idconfigmasivo').val();
            var cantidad = $('#cantidad_pos_envia').val();
            var URL = {!! json_encode(url('agregar-linea-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idconfigmasivo:idconfigmasivo, cantidad:cantidad},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", "#efectivo_dev" , function(event) {
            var efectivo = parseInt($(this).val());
            var tarjeta = parseInt($('#tarjeta_dev').val());
            var total_documento = parseInt($('#tot_pos_dev').val());
            var total = (efectivo + tarjeta);
            var cambio = (total_documento - total);
            if (cambio < 0) {
                var valor = (total - total_documento);
                $('#cambio_dev').val(valor);
            }else{
                $('#tarjeta_dev').prop('readonly', false);
                $('#efectivo_dev').prop('readonly', false);
                $('#tarjeta_dev').val(cambio);
                $('#cambio_dev').val(0);
            }
        });

        $(document).on("blur", "#tarjeta_dev" , function(event) {
            var efectivo = parseInt($('#efectivo_dev').val());
            var tarjeta = parseInt($(this).val());
            var total_documento = parseInt($('#tot_pos_dev').val());
            var total = (efectivo + tarjeta);
            var cambio = (total_documento - total);
            if (cambio < 0) {
                var valor = (total - total_documento);
                $('#cambio_dev').val(valor);
            }else{
                $('#tarjeta_dev').prop('readonly', false);
                $('#efectivo_dev').prop('readonly', false);
                $('#tarjeta_dev').val(cambio);
                $('#cambio_dev').val(0);
            }
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

        //ACTUALIZAR TODOS LOS CAMPOS ON BLUR

        $(document).on("blur", "#medio_pago" , function(event) {
            event.preventDefault();
            var medio_pago = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('editar-mediopago-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{medio_pago:medio_pago, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    //console.log(response);
                }
            });
        });
        $(document).on("blur", "#tipo_documento" , function(event) {
            event.preventDefault();
            var tipo_documento_mas = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('editar-tipodoc-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{tipo_documento_mas:tipo_documento_mas, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    //console.log(response);
                }
            });
        });

        $(document).on("blur", "#idcaja" , function(event) {
            event.preventDefault();
            var idcaja = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('editar-caja-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idcaja:idcaja, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    //console.log(response);
                }
            });
        });
        $(document).on("blur", "#actividad" , function(event) {
            event.preventDefault();
            var actividad = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('editar-actividad-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{actividad:actividad, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    //console.log(response);
                }
            });
        });

        $('#condición_venta').change(function() {
            var condicion = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            if (condicion === '02') {
                $('#input-p_credito').prop("required", true);
            }else{
                $('#input-p_credito').prop("required", false);
            }
            var URL = {!! json_encode(url('modificar-condicion-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{condicion:condicion, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    location.reload();
                },
                error:function(response){
                    //console.log(response);
                }
            });
        });
        
         $('#mo').change(function() {  ///orginal de moneda
            var moneda = $(this).val();
            var tipo_cambio = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
           
            var URL = {!! json_encode(url('modificar-moneda-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{moneda:moneda, tipo_cambio:tipo_cambio, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    location.reload();
                },
                error:function(response){
                    //console.log(response);
                }
            });
        });
        
        
  $('#moneda').change(function() {
            var moneda = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            switch(moneda){
                case 'CRC':
                    $('#tipo_cambio').css( "display", "none");
                    $('#input-tipo_cambio').val('0.00');
                    var tipocambio = 0.00;
                    var URL = {!! json_encode(url('modificar-moneda-masivo')) !!};
                        $.ajax({
                            type:'get',
                            url: URL,
                            dataType: 'json',
                            data:{tipocambio:tipocambio, moneda:moneda, idlogmasivo:idlogmasivo, idclientes:idclientes},
                            success:function(response){
                                //console.log(response);
                            },
                            error:function(response){
                                //console.log(response);
                            }
                        });
                break;
                case 'USD':
                    $('#tipo_cambio').css( "display", "block");
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
                                var  tipocambio = response.venta.valor;
                                var URL = {!! json_encode(url('modificar-moneda-masivo')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda, idlogmasivo:idlogmasivo, idclientes:idclientes},
                                    success:function(response){
                                        //console.log(response);
                                    },
                                    error:function(response){
                                        //console.log(response);
                                    }
                                });
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
                                $('#input-tipo_cambio').val(response.colones);
                                $("#input-tipo_cambio").prop('readonly', true);
                                var tipocambio = response.colones;
                                var URL = {!! json_encode(url('modificar-moneda-masivo')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda, idlogmasivo:idlogmasivo, idclientes:idclientes},
                                    success:function(response){
                                        //console.log(response);
                                    },
                                    error:function(response){
                                        //console.log(response);
                                    }
                                });
                            }
                        }
                    });
                break;
            }
        });
        
        $(document).on("blur", "#input-p_credito" , function(event) {
            event.preventDefault();
            var dias = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('modificar-dias-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{dias:dias, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    location.reload();
                    //console.log(response);

                },
                error:function(response){
                    //console.log(response);
                }
            });
        });

        $(document).on("blur", "#observaciones_masivo" , function(event) {
            event.preventDefault();
            var observacion = $(this).val();
            var idlogmasivo = $('#idlogmasivo').val();
            var idclientes = $('#cliente').val();
            var URL = {!! json_encode(url('editar-observacion-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{observacion:observacion, idlogmasivo:idlogmasivo, idclientes:idclientes},
                success:function(response){
                    location.reload();
                    //console.log(response);
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

         $('#agregar_cliente').click(function(e) {
            e.preventDefault();
            var datos_form = $('#form_add_cliente').serialize();
            var idlogmasivo = $('#idlogmasivo').val();
            var punto_venta = $('#punto_venta').val();
            var idcaja = $('#idcaja').val();
            var idcodigoactv = $('#actividad').val();
            var tipo_documento = $('#tipo_documento').val();
            var nombre_masivo = null;
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

        $(document).on("blur", "#delete_config" , function(event) {
            var idconfigmasivo = $('#idconfigmasivo').val();
            var URL = {!! json_encode(url('borrar-config-masivo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idconfigmasivo:idconfigmasivo},
                success:function(response){
                     if(response.success){
                        window.location = response.url;
                    }
                }
            });
        });

    });
</script>
@endsection
