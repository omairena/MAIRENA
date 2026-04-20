@extends('layouts.app', ['page' => __('Crear Nota de Crédito'), 'pageSlug' => 'notaCredito'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nota de Crédito Fiscal') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3>
                            </div>
                            @if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
   
@endif
                            <div class="col-4 text-right">
                                <a href="{{ route('notacredito.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('notacredito.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" onsubmit="return submitResult();">


                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Nota de Crédito Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_devolucion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_devolucion">{{ __('Tipo de Devolución') }}</label>
                                    @if(count($sales_item_otrocargo) > 0)
                                        <select class="form-control form-control-alternative" id="tipo_devolucion" name="tipo_devolucion" value="{{ old('tipo_devolucion') }}" required readonly>

                                            <option value="2" {{ ($sales->tipo_devolucion == 2 ? 'selected="selected"' : '') }}>Devolución Total</option>
                                    @else
                                        <select class="form-control form-control-alternative" id="tipo_devolucion" name="tipo_devolucion" value="{{ old('tipo_devolucion') }}" required>

                                            <option value="0">-- Seleccione un tipo de Devolución --</option>
                                            <option value="1" {{ ($sales->tipo_devolucion == 1 ? 'selected="selected"' : '') }}>Devolución Parcial</option>
                                            <option value="2" {{ ($sales->tipo_devolucion == 2 ? 'selected="selected"' : '') }}>Devolución Total</option>
                                    @endif
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_devolucion'])
                                </div>
                            	<div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required disabled="true">
                                        <option value="03" selected="true">Nota de Crédito Electrónica</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                            	<div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact') }}" required disabled="true">
                                        <option value="{{ $sales->idconfigfact }}">{{ $configuracion->nombre_empresa }}</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required disabled="true">
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
                                    	}

                                    ?>
                                        <option value="{{ $cliente->idcliente }}">{{$tipo_ident}}{{$cliente->num_id }} {{ $cliente->nombre }}</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda') }}" required disabled="true">
                                        <option value="{{ $sales->tipo_moneda }}">
                                            <?php
                                                switch ($sales->tipo_moneda) {
                                                    case 'CRC':
                                                        echo 'Colon Costaricense';
                                                    break;
                                                    case 'USD':
                                                        echo "Dolar Americano";
                                                    break;
                                                    case 'EUR':
                                                       echo "Euro";
                                                    break;
                                                }
                                             ?>
                                        </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ $sales->tipo_cambio }}" disabled="true">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required disabled="true">
                                        @if($sales->condicion_venta == '01')
                                            <option value="01" selected="true">Contado</option>
                                            <option value="02">Crédito</option>
                                        @else
                                            <option value="01">Contado</option>
                                            <option value="02" selected="true">Crédito</option>
                                        @endif
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                @if($sales->condicion_venta == '01')
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ $sales->p_credito }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                                @else
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ $sales->p_credito }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
                                @endif
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required disabled="true">
                                        <option value="{{ $sales->medio_pago }}">
                                            <?php
                                                switch ($sales->medio_pago) {
                                                    case '01':
                                                        echo "Efectivo";
                                                    break;
                                                    case '02':
                                                        echo "Tarjeta";
                                                    break;
                                                    case '03':
                                                        echo "Cheque";
                                                    break;
                                                    case '04':
                                                        echo "Transferencia – depósito bancario";
                                                    break;
                                                    case '05':
                                                        echo "Recaudado por terceros";
                                                    break;
                                                }
                                            ?>
                                        </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                @if($sales->medio_pago == '01')
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ $sales->referencia_pago }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                @else
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ $sales->referencia_pago }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                @endif
                                <div class="card card-nav-tabs card-plain">
                                    <div class="card-header card-header-danger">
                                        <!-- colors: "header-primary", "header-info", "header-success", "header-warning", "header-danger" -->
                                        <div class="nav-tabs-navigation">
                                            <div class="nav-tabs-wrapper">
                                                <ul class="nav nav-pills nav-pills-primary" data-tabs="tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="#detalle" data-toggle="tab">Linea detalle</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="#otro" data-toggle="tab">Otro Cargo</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content text-center">
                                            <div class="tab-pane active" id="detalle">
                                                <div class="table-responsive">
    								                <table class="table align-items-center" id="tabla_devolucion">
    									                <thead class="thead-light">
        									                <tr>
            									                <th scope="col">#</th>
            									                <th scope="col">Nombre</th>
                                                                <th scope="col">Costo Unitario</th>
            									                <th scope="col">Cant</th>
            									                <th scope="col">Monto Neto</th>
            									                <th scope="col">Descuento</th>
            									                <th scope="col">Impuesto</th>
                                                                <th scope="col">¿Posee Exoneración?</th>
            									                <th scope="col">Total</th>
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
                                                            @foreach($sales_item as $item)
                                                                <?php
                                                                if ($item->existe_exoneracion == '00') {

                                                                    $tiene_exoneracion = 'No';
                                                                    if ($sales->medio_pago == '02' and $item->impuesto_prc == '4.00') {
                                                                        $total_iva_devuelto =  $total_iva_devuelto +  $item->valor_impuesto;
                                                                    }
                                                                    $total = ($item->valor_neto - $item->valor_descuento)+ $item->valor_impuesto;
                                                                    $total_impuesto = $total_impuesto + $item->valor_impuesto;
                                                                } else {

                                                                    $exoneracion = App\Items_exonerados::where('idsalesitem', $item->idsalesitem)->get();
                                                                    $tiene_exoneracion = 'Si '.$exoneracion[0]->porcentaje_exoneracion. ' %';
                                                                    $monto_imp_exonerado = $item->valor_impuesto - $exoneracion[0]->monto_exoneracion;
                                                                    if ($sales->medio_pago == '02' and $item->impuesto_prc == '4.00') {
                                                                        $total_iva_devuelto =  $total_iva_devuelto +  $item->valor_impuesto;
                                                                    }
                                                                    $total = ($item->valor_neto - $item->valor_descuento)+ $monto_imp_exonerado;
                                                                    $total_impuesto = $total_impuesto + $monto_imp_exonerado;
                                                                }
                                                                $total_neto = $total_neto + $item->valor_neto;
                                                                $total_descuento = $total_descuento + $item->valor_descuento;
                                                                $total_comprobante = $total_comprobante + $total;
                                                                ?>
                                                                <tr>
                                                                    <td>{{ $item->codigo_producto }}</td>
                                                                    <td>{{ $item->nombre_producto }}</td>
                                                                    <!--<td>{{ $item->costo_utilidad }}</td>-->
                                                                     <td><input type="number" name="costo_dev" id="cantidad_dev{{ $item->idsalesitem }}" value="{{  $item->costo_utilidad}}" class="form-control form-control-alternative{{ $errors->has('costo_dev') ? ' is-invalid' : '' }} update_costo" style="width:80px; display: none;" max="{{  $item->costo_utilidad }}" data-id="{{ $item->idsalesitem }}" data-producto="{{ $item->idproducto }}"></td>
                                                                    <td><input type="number" name="cantidad_dev" id="cantidad_dev{{ $item->idsalesitem }}" value="{{ $item->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad_dev') ? ' is-invalid' : '' }} update_fila" style="width:80px; display: none;" max="{{ $item->cantidad }}" data-id="{{ $item->idsalesitem }}" data-producto="{{ $item->idproducto }}"></td>
                                                                    <td>{{ $item->valor_neto }}</td>
                                                                    <td id="valor_descuento{{ $item->idsalesitem }}">{{ $item->valor_descuento }}</td>
                                                                    <td id="valor_impuesto{{ $item->idsalesitem }}">{{ $item->valor_impuesto }}</td>
                                                                    <td>{{ $tiene_exoneracion }}</td>
                                                                    <td id="total_total{{ $item->idsalesitem }}">{{ $total }}</td>
                                                                    <td class="td-actions text-right">

                                                        <button type="button" id="modificar_articulo_flotante{{ $item->idsalesitem }}" data-id="{{ $item->idsalesitem }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                                                            <i class="fas fa-pen"></i>
                                                        </button>
                                                    </td>
                                                    <td class="td-actions text-right">
                                                        @if(count($sales_item) > 1)
                                                            <button type="button" id="eliminar_fila{{ $item->idsalesitem }}" data-id="{{ $item->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                                                                <i class="tim-icons icon-simple-remove"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                                    <input type="text" name="valor_neto" id="valor_neto{{ $item->idsalesitem }}" value="{{ $item->valor_neto }}" hidden="true">
                                                                    <input type="text" name="descuento_prc" id="descuento_prc{{ $item->idsalesitem }}" value="{{ $item->descuento_prc }}" hidden="true">
                                                                    <input type="text" name="impuesto_prc" id="impuesto_prc{{ $item->idsalesitem }}" value="{{ $item->impuesto_prc }}" hidden="true">
                                                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion{{ $item->idsalesitem }}" value="{{ $item->existe_exoneracion }}" hidden="true">
                                                                    <input type="text" name="idproducto" id="idproducto{{ $item->idsalesitem }}" value="{{ $item->idproducto }}" hidden="true">
                                                                </tr>
                                                            @endforeach
    									                </tbody>
									                </table>
								                </div>
                                            </div>
                                            <div class="tab-pane" id="otro">
                                                <div class="table-responsive">
                                                    <table class="table align-items-center" id="tabla_otros_cargos">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Identificacion</th>
                                                                <th scope="col">Detalle</th>
                                                                <th scope="col">Porcentaje</th>
                                                                <th scope="col">Monto</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $total_otros_cargos = 0;
                                                            ?>
                                                            @foreach($sales_item_otrocargo as $otro_cargo)

                                                                <tr>
                                                                    <td>{{ $otro_cargo->idotrocargo }}</td>
                                                                    <td>{{ $otro_cargo->numero_identificacion }}</td>
                                                                    <td>{{ $otro_cargo->detalle }}</td>
                                                                    <td>{{ $otro_cargo->porcentaje_cargo }}</td>
                                                                    <td>{{ $otro_cargo->monto_cargo }}</td>
                                                                </tr>
                                                                <?php
                                                                    $total_otros_cargos += $otro_cargo->monto_cargo;
                                                                ?>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{  number_format($total_neto,2,',','.')  }}</b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{  number_format($total_descuento,2,',','.')  }}</b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{  number_format($total_impuesto,2,',','.')  }}</b></h4>
                                    @if($total_iva_devuelto > 0)
                                        <h4 class="mb-0" id="iva_d">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{  number_format($total_iva_devuelto,2,',','.')  }}</b></h4>
                                    @endif
                                    <h4 class="mb-0"> Total Otros Cargos: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ number_format($total_otros_cargos,2,',','.') }}</b></h4>
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante + $total_otros_cargos,2,',','.')   }}</b></h4>
                                	<input type="text" name="referencia_sale" id="referencia_sale" value="{{ $sales->referencia_sale }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">

                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Nota de Credito') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control" required="true">{{ $sales->observaciones }}</textarea>
                                    </div>
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Documento Referencia') }}</label>
                                        	<input type="text" name="clave_sale" id="clave_sale" value="{{ $info_ref->clave }}" readonly size="60" >
                                    </div>
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Fecha Emision') }}</label>
                                        	<input type="date" name="fecha_emision" id="fecha_emision" value="{{ $info_ref->fechahora }}" readonly >
                                    </div>
                                  
                                     <div class="form-group{{ $errors->has('tipo_doc_ref') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_doc_ref">{{ __('Tipo de Documento Referencia') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_doc_ref" name="tipo_doc_ref" value="{{ old('tipo_doc_ref') }}" required>
                                        <option value="01">Factura Electronica.</option>
                                        <option value="02">Nota de Debito</option>
                                        <option value="04">Tique electronico</option>
                                         <option value="17">Factura Electronica Compra</option>
                                       
                                    </select>
                                    @include('alerts.feedback', ['field' => 'TipoDocIR'])
                                </div>
                                
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4" id="guardar_factura" style="display: none;">{{ __('Guardar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
@include('modals.cargando')
@include('modals.modArticulo')
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        switch($('#tipo_devolucion').val()){
                case '0':
                    $('.update_fila').css("display", "none");
                    $('.update_costo').css("display", "none");
                    $('#guardar_factura').css("display", "none");
                    $('#cantidad_dev').prop('disabled', true);
                break;
                case '1':
                    $('.update_fila').prop('disabled', false);
                    $('.update_fila').css("display", "block");
                    $('.update_costo').prop('disabled', false);
                    $('.update_costo').css("display", "block");
                    
                    $('#guardar_factura').css("display", "block");
                break;
                case '2':
                    $('.update_fila').prop('disabled', true);
                    $('.update_fila').css("display", "block");
                     $('.update_costo').prop('disabled', true);
                    $('.update_costo').css("display", "block");
                    
                    $('#guardar_factura').css("display", "block");
                break;
            }
        $('#tipo_devolucion').change(function() {
            switch($(this).val()){
                case '0':
                    $('.update_fila').css("display", "none");
                     $('.update_costo').css("display", "none");
                    
                    $('#guardar_factura').css("display", "none");
                    $('#cantidad_dev').prop('disabled', true);
                break;
                case '1':
                    $('.update_fila').prop('disabled', false);
                    $('.update_costo').prop('disabled', false);
                    
                    $('.update_fila').css("display", "block");
                    $('.update_costo').css("display", "block");
                    
                    $('#guardar_factura').css("display", "block");
                break;
                case '2':
                    $('.update_fila').prop('disabled', true);
                    $('.update_fila').css("display", "block");
                    $('.update_costo').prop('disabled', true);
                    $('.update_costo').css("display", "block");
                    
                    $('#guardar_factura').css("display", "block");
                break;
            }
        });

        $(document).on("blur", ".update_fila" , function(event) {
            var id = $(this).data('id');
            var cantidad = $(this).val();
            var idproducto = $(this).data('producto');
            var tipo_devolucion = $('#tipo_devolucion').val();
            var URL = {!! json_encode(url('actualizar-cant-notac')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, cantidad:cantidad, idproducto:idproducto, tipo_devolucion:tipo_devolucion},
                success:function(response){
                    location.reload();
                }
            });
        });
        
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

        $(document).on("blur", ".update_costo" , function(event) {
            var id = $(this).data('id');
           // var cantidad = $('#cantidad_dev').val();
			var costo_dev = $(this).val();
            var idproducto = $(this).data('producto');
            var tipo_devolucion = $('#tipo_devolucion').val();
            var URL = {!! json_encode(url('actualizar-costo-notac')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, costo_dev:costo_dev, idproducto:idproducto, tipo_devolucion:tipo_devolucion},
                success:function(response){
                    location.reload();
                }
            });
        });

    });

     function submitResult() {
        if ( confirm("¿Desea procesar la Nota de Cr?") == false ) {
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
