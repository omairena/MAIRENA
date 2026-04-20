@extends('layouts.pos', ['page' => Auth::user()->config_u[0]->nombre_emisor, 'pageSlug' => 'crearFactura'])

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
            <form method="post" action="{{ route('pos.guardar') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
            @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="container-fluid mt--7">
                        <div class="row">
                             <div class="col-10" id="tableBlock">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="mb-0" id="encabezado_factura"></h3>
                                        <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-warning" >{{ __('Salir') }}</a>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group text-right">
                                            <a href="#" class="btn btn-sm btn-info" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Buscar Producto</a>
                                             <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                                        </div>
                                    </div>
                                </div>
                                  @if(Auth::user()->config_u[0]->usa_lector > 0)
                                             <div class="row">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tim-icons icon-zoom-split"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control"  name="codigo_pos" id="codigo_pos" placeholder="Ingrese Codigo o Nombre del producto">
                                        <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" hidden="true">
                                        <button class="btn btn-sm btn-success" type="button" id="agregar_producto_pos" style="display: none;">+</button>
                                        <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">

                                    </div>
                                </div>
                                            @else
                                <!---->

                                 <div class="col-md-12">
                                <table class="table align-items-center">
                             <thead class="thead-light">
                                <th scope="col" style="text-right: center;">{{ __('Codigo') }}</th>
                                <th scope="col" style="text-right: center;">{{ __('Nombre Producto') }}</th>
                                <th scope="col" style="text-right: center;">{{ __('Cantidad') }}</th>
                                <th scope="col" style="text-right: center;">{{ __('P. Unitario ') }}</th>
                                 <th scope="col" style="text-right: center;">{{ __('Con IVA') }}</th>

                                <th scope="col" style="text-right: center;">{{ __('Disponible') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody id="buscar_articulo_pos">
                                <tr>
                                    <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                                    <td style="width:300px;">
                                        <input type="text" name="codigo_pos" id="codigo_pos" class="form-control form-control-alternative{{ $errors->has('codigo_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td style="width:800px;">
                                       <input type="text" name="nombre_pos" id="nombre_pos" class="form-control form-control-alternative{{ $errors->has('nombre_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" style="width:100px;">
                                    </td>
                                     <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" name="monto_linea" id="monto_linea" class="form-control form-control-alternative{{ $errors->has('monto_linea') ? ' is-invalid' : '' }}" style="width:100px;">
                                    </td>
                                     <td class="text-left" style="width:100px;">

                                    <input type="checkbox" name="es_sin_impuesto" id="es_sin_impuesto" {{$configuracion[0]->sin_impuesto_pos == false ? 'checked' : ''}}>
                                    </td>
                                    <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" name="disponible_pos" id="disponible_pos" class="form-control form-control-alternative{{ $errors->has('disponible_pos') ? ' is-invalid' : '' }}" style="width:80px;" readonly="true">
                                    </td>
                                    <td>
                                        <button class="btn btn-success" type="submit" id="agregar_producto_pos" style="display: none;">Agregar Producto</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                         </div>
                         @endif
                                <div class="row">
                                    <div class="table">
                                        <table class="table align-items-center" id="tabla_productos">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Nombre</th>
                                                    <th scope="col">Cant</th>
                                                    <th scope="col">Neto</th>
                                                    <th scope="col">Descuento</th>
                                                    <th scope="col">Impuesto</th>
                                                    <th scope="col">¿Tiene exoneración?</th>
                                                    <th scope="col">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tabla_productos">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2" id="firstBlock">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>

                                        <option value="0">-- Seleccione un tipo de documento --</option>
                                        @if(Auth::user()->config_u[0]->es_simplificado == 1)

                                            <option value="01">Fáctura Electrónica</option>
                                            <option value="09">Fáctura Electrónica de Exportación</option>


                                            @if(Auth::user()->config_u[0]->usa_op > 0)
                                             <option value="04" selected="true"> Tiquete</option>
                                                <option value="96" >Orden de Pedido</option>
                                            @else
                                                <option value="96" selected="true">Fáctura Regimen Simplificado</option>
                                                <option value="04" > Tiquete</option>
                                            @endif
                                        @else

                                            <option value="01" >Fáctura Electrónica</option>
                                            <option value="09">Fáctura Electrónica de Exportación</option>
                                            <option value="04" selected ="true" >Tiquete</option>

                                        @endif
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <div class="d-flex">
                                        <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $contado[0]->nombre }}">

                                        <input type="button" class="btn btn-sm btn-success" value="+" data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>

                                    </div>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                 <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
    <select class="form-control form-control-alternative" id="moneda" name="moneda" required>
        @if(Auth::user()->config_u[0]->tipo_moneda == 'CRC')
            <option value="CRC" selected>Colón Costarricense</option>
            <option value="USD">Dólar Americano</option>
            <option value="EUR">Euro</option>
        @elseif(Auth::user()->config_u[0]->tipo_moneda == 'USD')
            <option value="CRC">Colón Costarricense</option>
            <option value="USD" selected>Dólar Americano</option>
            <option value="EUR">Euro</option>
        @elseif(Auth::user()->config_u[0]->tipo_moneda == 'EUR')
            <option value="CRC">Colón Costarricense</option>
            <option value="USD">Dólar Americano</option>
            <option value="EUR" selected>Euro</option>
        @else
            <option value="CRC">Colón Costarricense</option>
            <option value="USD">Dólar Americano</option>
            <option value="EUR">Euro</option>
        @endif
    </select>
    @include('alerts.feedback', ['field' => 'moneda'])
</div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio') }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
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
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_creditito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_creditito') }}">
                                    @include('alerts.feedback', ['field' => 'p_creditito'])
                                </div>

                               
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago') }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                 <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                    <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                                </div>
                                 <button id="toggleSecondBlock" type="button" class="btn btn-sm btn-primary">Más Opciones</button>
                            </div>
                          <div class="col-2" id="secondBlock" style="display: none;">
                               <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}" id="combo_actividad" style="display: none;">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
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
                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}" readonly>
                <input type="text" name="usa_lector" id="usa_lector" value="{{ old('usa_lector', $configuracion[0]->usa_lector ) }}" hidden="true">
                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="usa_balanza" id="usa_balanza" value="{{ old('usa_balanza', Auth::user()->config_u[0]->usa_balanza) }}" hidden="true">
                                <!-- nuevos cambios para pasar el POS con el modal-->
                                <input type="text" name="datos_internos" id="datos_internos" hidden="true">

                                <!-- Botón para mostrar el modal de factura -->
                                <div class="form-group text-right mt-3">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFactura" id="btnAbrirModalFactura">
                                            Crear factura
                                        </button>
                                </div>
                        </form>

                        <!-- Modal para crear factura -->
                        <div class="modal fade" id="modalFactura" tabindex="-1" role="dialog" aria-labelledby="modalFacturaLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalFacturaLabel">Información de la factura</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                            <!-- Input para filtrar facturas en el modal -->
                                            <div class="mb-2">
                                                <input type="text" id="modalFiltroFacturas" class="form-control" placeholder="Filtrar facturas...">
                                            </div>
                                            <div id="modal_facturas_contenido">
                                                <div style="max-height:55vh;overflow-y:auto;">
                                                    <p>Cargando información de facturas...</p>
                                                </div>
                                            </div>
                                            <!-- Puedes agregar más datos si lo requieres -->
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="{{ route('masivo.store') }}">
                                            @csrf
                                            <input type="hidden" name="idcliente" id="modal_idcliente" value="">
                                            <input type="hidden" name="total_factura" id="modal_total_factura" value="">
                                            <button type="submit" class="btn btn-success">Crear documento</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>
        </div>
</div>



@endsection
@include('modals.addProducts')
@include('modals.newCliente')

@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#toggleSidebarManual').on('click', function() {
            $('.sidebar').toggleClass('show');
        });

        // Adaptar modal para mostrar datos dinámicos
        $('#btnAbrirModalFactura').on('click', function() {
            // Petición AJAX a mostrarFacturas
            $.ajax({
                url: '{{ route('masivo.mostrarFacturasAjax') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                        if (response && response.length > 0) {
                            var html = '<div style="max-height:55vh;overflow-y:auto;">';
                            html += '<table class="table" id="modalTablaFacturas"><thead><tr><th>ID</th><th>Cliente</th><th>Total</th><th>Acción</th></tr></thead><tbody>';
                            response.forEach(function(factura) {
                                html += '<tr>' +
                                    '<td>' + factura.idconfigmasivo + '</td>' +
                                    '<td>' + factura.cliente + '</td>' +
                                    '<td>' + factura.total_comprobante + '</td>' +
                                    '<td>' +
                                        '<button type="button" class="btn btn-success btn-sm crear-documento-modal" data-id="' + factura.idconfigmasivo + '">Crear documento</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                            html += '</tbody></table></div>';
                            $('#modal_facturas_contenido').html(html);
                            // Activar filtro al input del modal
                            $('#modalFiltroFacturas').off('keyup').on('keyup', function() {
                                var value = $(this).val().toLowerCase();
                                $('#modalTablaFacturas tbody tr').filter(function() {
                                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                                });
                            });
                        } else {
                            $('#modal_facturas_contenido').html('<p>No hay facturas disponibles.</p>');
                        }
                    // Script para el botón Crear documento en el modal
                    $('.crear-documento-modal').off('click').on('click', function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var APP_URL = '{{ url('/update_lista') }}';
                        $.ajax({
                            type: 'POST',
                            url: APP_URL,
                            dataType: 'json',
                            data: {
                                idlogmasivo: id,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function(response) {
                                $('.loader').css('display', 'block');
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.location.href = response.url;
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            complete: function(response) {
                                $('.loader').css('display', 'none');
                            },
                            error: function(response) {
                                console.error('Error AJAX:', response);
                                alert('Ocurrió un error al intentar enviar.');
                            }
                        });
                    });
                },
                error: function() {
                    $('#modal_facturas_contenido').html('<p>Error al cargar las facturas.</p>');
                }
            });
        });
    });
</script>
<script type="text/javascript">
//bloqueo de enter
$(document).ready(function() {
            $('#form_factura').on('keydown', function(event) {
                if (event.key === "Enter") { // Verifica si la tecla presionada es "Enter"
                    event.preventDefault();  // Previene el envío del formulario
                }
            });
        });

//bloqueo de enter
//bloques
 document.addEventListener('DOMContentLoaded', function() {
        var tableBlock = document.getElementById('tableBlock');
        var firstBlock = document.getElementById('firstBlock');
        var secondBlock = document.getElementById('secondBlock');
        var toggleButton = document.getElementById('toggleSecondBlock');

        // Al cargar la página, el bloque de la tabla ocupa col-10
        tableBlock.classList.add('col-10');
        firstBlock.classList.add('col-2');
        secondBlock.classList.add('col-2');

        toggleButton.addEventListener('click', function() {
            // Mostrar el segundo bloque y ajustar las columnas
            if (secondBlock.style.display === 'none') {
                secondBlock.style.display = 'block';
                tableBlock.classList.remove('col-10');
                tableBlock.classList.add('col-8'); // Cambia la tabla a col-8
                firstBlock.classList.remove('col-2');
                firstBlock.classList.add('col-2'); // Se mantiene en col-2
            } else {
                secondBlock.style.display = 'none';
                tableBlock.classList.remove('col-8');
                tableBlock.classList.add('col-10'); // Regresa la tabla a col-10
            }
        });
    });

//bloques

    $(document).ready(function() {
        
        valorMoneda($('#moneda').val());

    $('#moneda').change(function() {
        valorMoneda($(this).val());
    });

    function valorMoneda(moneda) {
        if (moneda === 'CRC') {
            $('#tipo_cambio').css("display", "none");
            $('#input-tipo_cambio').val('0.00');  // Establece el valor en 0.00
            var tipocambio = 0.00;
            var URL = {!! json_encode(url('modificar-tipocambio')) !!};
            $.ajax({
                type: 'get',
                url: URL,
                dataType: 'json',
                data: { tipocambio: tipocambio, moneda: moneda },
                success: function(response) {
                    // Procesar la respuesta si es necesario
                },
                error: function(response) {
                    // Manejar el error si es necesario
                }
            });
        } else { 
            // Para USD y EUR, mostrar el campo de tipo cambio
            $('#tipo_cambio').css("display", "block");

            if (moneda === 'USD') {
                var URL_USD = 'https://api.hacienda.go.cr/indicadores/tc/dolar';
                $.ajax({
                    type: 'get',
                    url: URL_USD,
                    dataType: 'json',
                    success: function(response) {
                        if (response == null) {
                            alert('Conexión fallida con Hacienda');
                        } else {
                            $('#input-tipo_cambio').val(response.venta.valor);
                            $("#input-tipo_cambio").prop('readonly', true);
                            var tipocambio = response.venta.valor;
                            var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                            $.ajax({
                                type: 'get',
                                url: URL,
                                dataType: 'json',
                                data: { tipocambio: tipocambio, moneda: moneda },
                                success: function(response) {
                                    // Procesar la respuesta si es necesario
                                },
                                error: function(response) {
                                    // Manejar el error si es necesario
                                }
                            });
                        }
                    }
                });
            } else if (moneda === 'EUR') {
                var URL_EUR = 'https://api.hacienda.go.cr/indicadores/tc/euro';
                $.ajax({
                    type: 'get',
                    url: URL_EUR,
                    dataType: 'json',
                    success: function(response) {
                        if (response == null) {
                            alert('Conexión fallida con Hacienda');
                        } else {
                            $('#input-tipo_cambio').val(response.colones);
                            $("#input-tipo_cambio").prop('readonly', true);
                            var tipocambio = response.colones;
                            var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                            $.ajax({
                                type: 'get',
                                url: URL,
                                dataType: 'json',
                                data: { tipocambio: tipocambio, moneda: moneda },
                                success: function(response) {
                                    // Procesar la respuesta si es necesario
                                },
                                error: function(response) {
                                    // Manejar el error si es necesario
                                }
                            });
                        }
                    }
                });
            }
        }
    }
    
        $(window).scroll(function() {
            scroll(0,0);
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
            traerNumFactura(APP_URL,o2);
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
 var consultandoCliente = false;

$(document).on("blur", "#cliente_serch", function(event) {
    event.preventDefault();

    if (consultandoCliente) return;
    consultandoCliente = true;

    var nombre_cli = $(this).val().trim();

    var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
    var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
    var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};

    $('#agregar_producto_pos').prop('disabled', true);

    if (nombre_cli.length <= 0) {
        $('#agregar_producto_pos').prop('disabled', false);
        consultandoCliente = false;
        return;
    }

    $.ajax({
        type: 'get',
        url: URL,
        dataType: 'json',
        data: { nombre_cli: nombre_cli },

        success: function(response) {

            console.log("Cliente encontrado:", response);

            if (response.success && response.success.length > 0) {

                var cliente = response.success[0];

                $('#cliente_serch').val(cliente.nombre);
                $('#cliente').val(cliente.idcliente);
                $('#ced_receptor').val(cliente.num_id);
                $('#exocli').val(cliente.exocli);
                $('#continuar').css("display", "");
                $('#datos_internos').val(1);

                var numId = cliente.num_id;

                if (!numId) {
                    console.warn("Cliente sin identificación");
                    $('#tipo_documento').val('04');
                    traerNumFactura(APP_URL, o2);
                    return;
                }

                var apiUrl = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + numId;

                $.ajax({
                    type: 'get',
                    url: apiUrl,
                    dataType: 'json',

                    success: function(apiResponse) {

                        console.log("Respuesta Hacienda:", apiResponse);

                        if (!apiResponse || !apiResponse.situacion) {
                            throw "Respuesta inválida de Hacienda";
                        }

                        // 🔹 Normalizar estado
                        var estado = (apiResponse.situacion.estado || '')
                            .toLowerCase()
                            .replace(/\s+/g, ' ')
                            .trim();

                        var estadosValidos = [
                            'inscrito',
                            'inscrito de oficio'
                        ];

                        var actividades = apiResponse.actividades || [];
                        var situacion = apiResponse.situacion || {};

                        console.log("Estado:", estado);
                        console.log("Actividades:", actividades);

                        // 🔥 VALIDACIÓN COMPLETA
                        var esValido = true;
                        var motivos = [];

                        if (!estadosValidos.includes(estado)) {
                            esValido = false;
                            motivos.push("Estado: " + estado);
                        }

                        if (actividades.length === 0) {
                            esValido = false;
                            motivos.push("Sin actividades económicas");
                        }

                    

                        if (!esValido) {

                            alert(
                                'El cliente no es válido para Factura Electrónica.\n\n' +
                                motivos.join('\n') +
                                '\n\nSe generará como Tiquete.'
                            );

                            $('#tipo_documento').val('04'); // TIQUETE

                        } else {

                            $('#tipo_documento').val('01'); // FACTURA
                        }

                        traerNumFactura(APP_URL, o2);
                    },

                    error: function(err) {

                        console.error("Error Hacienda:", err);

                        alert(
                            'No se pudo validar en Hacienda.\n' +
                            'Se generará como Tiquete por seguridad.'
                        );

                        $('#tipo_documento').val('04');
                        traerNumFactura(APP_URL, o2);
                    }
                });

            } else {

                console.warn("Cliente no encontrado");

                $('#cliente_serch').val('');
                $('#cliente').val(1);
            }
        },

        error: function(err) {
            console.error("Error buscando cliente:", err);
        },

        complete: function() {
            $('#agregar_producto_pos').prop('disabled', false);
            consultandoCliente = false;
        }
    });
});
        //// fin cambio 30-03-2022


        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            var cantidad_pos = $('#cantidad_pos').val();
            var descuento_pos = $('#descuento_pos').val();
            var monto_total = $('#monto_linea').val();
            var es_sin_impuesto = $('#es_sin_impuesto').is(':checked');
            var valor = es_sin_impuesto ? 1 : 0;
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




        $( "#telefono" ).autocomplete({

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

                              //  $('#tipo_documento').val('01');
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
                           // $('#tipo_documento').val('01');
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
                                        $('#codigo_actividad_modal').find('option').remove();
                                        $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                    }
                                },
                                error:function(response){
                                    alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación');
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


</script>
@endsection
