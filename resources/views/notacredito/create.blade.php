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
                            <div class="col-4 text-right">
                                <a href="{{ route('notacredito.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('notacredito.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Nota de Crédito Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_devolucion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_devolucion">{{ __('Tipo de Devolución') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_devolucion" name="tipo_devolucion" value="{{ old('tipo_devolucion') }}" required>
                                        @if(count($ales_item_otrocargo) > 0)
                                        @else
                                            <option value="0">-- Seleccione un tipo de Devolución --</option>
                                            <option value="1">Devolución Parcial</option>
                                            <option value="2">Devolución Total</option>
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
                                <div class="table-responsive">
    								<table class="table align-items-center" id="tabla_devolucion">
    									<thead class="thead-light">
        									<tr>
            									<th scope="col">#</th>
            									<th scope="col">Nombre</th>
            									<th scope="col">Cant</th>
            									<th scope="col">Precio Unitario</th>
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

                                             ?>
                                            @foreach($sales_item as $item)
                                            <?php
                                                 if ($item->existe_exoneracion == '00') {
                                                        $tiene_exoneracion = 'No';
                                                        $total = ($item->valor_neto - $item->valor_descuento)+ $item->valor_impuesto;
                                                        $total_impuesto = $total_impuesto + $item->valor_impuesto;
                                                    }else{
                                                        $exoneracion = App\Items_exonerados::where('idsalesitem', $item->idsalesitem)->get();
                                                        $tiene_exoneracion = 'Si '.$exoneracion[0]->porcentaje_exoneracion. ' %';
                                                        $monto_imp_exonerado = $item->valor_impuesto - $exoneracion[0]->monto_exoneracion;
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
                                                <td><input type="number" name="cantidad_dev" id="cantidad_dev{{ $item->idsalesitem }}" value="{{ $item->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad_dev') ? ' is-invalid' : '' }} update_fila" style="width:80px; display: none;" max="{{ $item->cantidad }}" data-id="{{ $item->idsalesitem }}"></td>
                                                <td>{{ $item->valor_neto }}</td>
                                                <td id="valor_descuento{{ $item->idsalesitem }}">{{ $item->valor_descuento }}</td>
                                                <td id="valor_impuesto{{ $item->idsalesitem }}">{{ $item->valor_impuesto }}</td>
                                                <td>{{ $tiene_exoneracion }}</td>
                                                <td id="total_total{{ $item->idsalesitem }}">{{ $total }}</td>
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
								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ $total_neto }}</b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ $total_descuento }}</b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ $total_impuesto }}</b></h4>
                                    <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ $sales->total_iva_devuelto }}</b></h4>
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ $total_comprobante }}</b></h4>
                                	<input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                	<input type="text" name="referencia_sale" id="referencia_sale" value="{{ $sales->idsale }}" hidden="true">
                                    <input type="text" name="cantidades_devueltas[]" id="cantidades_devueltas" value="{{ old('cantidades_devueltas') }}" hidden="true">
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
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        $('#tipo_devolucion').change(function() {
            switch($(this).val()){
                case '0':
                    $('.update_fila').css("display", "none");
                    $('#guardar_factura').css("display", "none");
                    $('#cantidad_dev').prop('disabled', true);
                break;
                case '1':
                    $('.update_fila').prop('disabled', false);
                    $('.update_fila').css("display", "block");
                    $('#guardar_factura').css("display", "block");
                break;
                case '2':
                    $('.update_fila').prop('disabled', true);
                    $('.update_fila').css("display", "block");
                    $('#guardar_factura').css("display", "block");
                break;
            }
        });

        $(document).on("blur", ".update_fila" , function(event) {
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

    });
</script>
@endsection
