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
                        <form method="post" action="{{ route('fec.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_fec" onsubmit="return submitResult();">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Factura de Compra Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                        <option value="08">Fáctura Electrónica de Compra</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('TipoDocIR') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-TipoDocIR">{{ __('Tipo de Documento Referencia') }}</label>
                                    <select class="form-control form-control-alternative" id="TipoDocIR" name="TipoDocIR" value="{{ old('TipoDocIR') }}" required>
                                        <option value="14">Comprobante aportado por contribuyente de Régimen Especial.</option>
                                        <option value="15">Anulacion->Sustituye una Factura electrónica de Compra</option>
                                        <option value="16">Compras en Extranjero</option>
                                       
                                    </select>
                                    @include('alerts.feedback', ['field' => 'TipoDocIR'])
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
                                        <option value="{{ $cliente->idcliente }}" {{ ($sales->idcliente == $cliente->idcliente ? 'selected="selected"' : '') }}>{{ $cliente->nombre }} {{$tipo_ident}}{{$cliente->num_id }} </option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda') }}" required>
                                        <option value="CRC" {{ ($sales->moneda == 'CRC' ? 'selected="selected"' : '') }}>Colon Costaricense</option>
                                        <option value="USD" {{ ($sales->moneda == 'USD' ? 'selected="selected"' : '') }}>Dólar Americano</option>
                                        <option value="EUR" {{ ($sales->moneda == 'EUR' ? 'selected="selected"' : '') }}>Euro</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
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
                                            @foreach($sales_item as $sale_i)
                                                <?php 
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
                                                    <td class="text-right"><input type="number" name="costo" id="costo{{  $sale_i->costo_utilidad }}" value="{{  $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo') ? ' is-invalid' : '' }} update_costo_fec" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}"></td>
                                                    <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_fec" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}"></td>
                                                    <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_fec" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}"></td>
                                                    <td>{{ $sale_i->valor_neto }}</td>
                                                    <td>{{ $sale_i->valor_descuento }}</td>
                                                    @if($sale_i->existe_exoneracion === '00')
                                                    <td>{{ $sale_i->valor_impuesto }}</td>
                                                    @else
                                                    <td>{{ $monto_imp_exonerado }}</td>
                                                    @endif
                                                    <td><?php echo $tiene_exoneracion; ?></td>
                                                    <td><?php echo $total; ?></td>
                                                    <td class="td-actions text-right">

                                                        <button type="button" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                                                            <i class="fas fa-pen"></i>
                                                        </button>
                                                    </td>
                                                    <td class="td-actions text-right">
                                                        @if(count($sales_item) > 1)
                                                            <button type="button" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                                                                <i class="tim-icons icon-simple-remove"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
    									</tbody>
									</table>
								</div>
								<div class="form-group text-right">
                                    <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ number_format($total_neto, 2, '.', ',') }}</b></h4>  
                                    <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ number_format($total_descuento, 2, '.', ',') }}</b> </h4>  
                                    <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ number_format($total_impuesto, 2, '.', ',') }}</b></h4>  
                                    @if($total_iva_devuelto > 0)  
                                    <h4 class="mb-0" id="iva_d">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ number_format($total_iva_devuelto, 2, '.', ',') }}</b></h4>  
                                    @endif  
                                    <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante, 2, '.', ',') }}</b></h4>  
                                	<input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                	<input type="text" name="sales_item[]" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                	<input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                                    <input type="text" name="productos_fec" id="productos_fec" value="{{ old('productos_fec') }}" hidden="true">
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura de Compra') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control" required="true">{{ $sales->observaciones }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('referencia_compra') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="referencia_compra">{{ __('Número de Factura emisora de Factura de Compra') }}</label>
                                        <input type="text" id="referencia_compra" name="referencia_compra" class="form-control" max="50" value="{{ $sales->referencia_compra }}" required="true">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4 text-center" id="guardar_factura">{{ __('Enviar a Hacienda') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@include('modals.addProducts')
@include('modals.modArticulo')
@include('modals.cargando')
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
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var APP_URL2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,APP_URL2);
        validaMedioPago();
        validaCondicionVenta();
        $('#input-tipo_cambio').val('0.00');
        $('#moneda').change(function() {
            switch($(this).val()){
                case 'CRC':
                    $('#tipo_cambio').css( "display", "none");
                    $('#input-tipo_cambio').val('0.00');
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

        $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
            $('#sales_item').val(data);
        });

        $(document).on("click", ".eliminar_fila_fec" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-fec')) !!};
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

        $(document).on("blur", ".update_cantidad_fec" , function(event) {
            var id = $(this).data('id');
            var cantidad = $(this).val();
            var URL = {!! json_encode(url('actualizar-cant-fec')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, cantidad:cantidad},
                success:function(response){
                    location.reload();
                }
            });
        });
        //omairena 10-02-2023
        $(document).on("blur", ".update_costo_fec" , function(event) {
            var id = $(this).data('id');
            var costo = $(this).val();
            var cantidad = $(this).val();
            var URL = {!! json_encode(url('actualizar-cost-fec')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, costo:costo, cantidad:cantidad},
                success:function(response){
                    location.reload();
                }
            });
        });
        

        $(document).on("blur", ".update_descuento_fec" , function(event) {
            var id = $(this).data('id');
            var porcentaje_descuento = $(this).val();
            var URL = {!! json_encode(url('actualizar-desc-fec')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, porcentaje_descuento:porcentaje_descuento},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("click", ".eliminar_fila_factura" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-fec')) !!};
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
        
        $('#tipo_documento').change(function() {
            traerNumFactura(APP_URL,o2);
        });

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
            var URL = {!! json_encode(url('modificar-fec-flotante')) !!};
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

         $('#agregar_producto').click(function(e) {
            e.preventDefault();
            var sales_item = $('#sales_item').val();
            var idsale = $('#idsale').val();
            var cantidad =  null;
            var monto_total =  null;
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale, cantidad:cantidad, monto_total:monto_total},
                success:function(response){
                    location.reload();
                }
            });
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
    
    $(document).on("blur", "#referencia_compra" , function(event) {
            event.preventDefault();
            var ref_compra = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('ref_fact_fec')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{ref_compra:ref_compra, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });
    
    
    
    function submitResult() {
        if ( confirm("¿Desea procesar la Factura?") == false ) {
            return false ;
        } else {
            $("#loadMe").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            return true ;
        }
    }
</script>
@endsection