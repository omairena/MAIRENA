@extends('layouts.app', ['page' => __('Crear Nota de Crédito'), 'pageSlug' => 'notaCredito'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nota de Débito Fiscal') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('notadebito.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Armar Nota de Débito Fiscal') }}</h6>
                            <div class="pl-lg-4">
                            	<div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required disabled="true">
                                        <option value="02" selected="true">Nota de Dédito Electrónica</option>
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
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Agregar Producto</a>
                                </div>
                                <div class="table-responsive">
    								<table class="table align-items-center" id="tabla_devolucion">
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
                                                <th scope="col">Total</th>
                                                <th></th>
        									</tr>
    									</thead>
    									<tbody class="tabla_productos">

    									</tbody>
									</table>
								</div>
								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ $sales->total_neto }}</b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ $sales->total_descuento }}</b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ $sales->total_impuesto }}</b></h4>
                                    <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ $sales->total_iva_devuelto }}</b></h4>
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ $sales->total_comprobante }}</b></h4>
                                	<input type="text" name="referencia_sale" id="referencia_sale" value="{{ $sales->idsale }}" hidden="true">
                                    <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                    <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
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
@include('modals.addProducts')
@include('modals.addExoneracion')
@endsection
@section('myjs')
<script type="text/javascript">
    $(document).ready(function() {
        $('#input-tipo_cambio').val('0.00');
        var idconfigfact = $('#idconfigfact').val();
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
                    $('#actividad').find('option').remove();
                    $(response['success']['codigo_actividad']).each(function(data) {
                    $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                        });
                }else{
                    $('#actividad').find('option').remove();
                    $('#combo_actividad').css( "display", "none");
                }
            }
        });
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
                        $('#actividad').find('option').remove();
                        $(response['success']['codigo_actividad']).each(function(data) {
                        $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                        });
                    }else{
                        $('#actividad').find('option').remove();
                        $('#combo_actividad').css( "display", "none");
                    }
                }
            });
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
            var num_doc = $('#numero_documento').val();
            var URL = {!! json_encode(url('agregar-linea-ndebito')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale, num_doc:num_doc},
                success:function(response){
                    //console.log(response);
                    //location.reload(URL_CALLBACK);
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

        $('#input-numero_exoneracion').on("blur", function( e ) {
            e.preventDefault();
            var valor = $(this).val();
            var identificacion = traerIdentificacion($('#cliente').val());
            var URL = 'https://api.hacienda.go.cr/fe/ex?identificacion='+identificacion;
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
                    }else{
                        //console.log(response);
                        alert('no es array');
                    }

                }
            });
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
    });
</script>
@endsection
