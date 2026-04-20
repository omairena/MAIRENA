@extends('layouts.app', ['page' => __('Crear Nuevo Movimiento'), 'pageSlug' => 'newMovimiento'])
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
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nuevo Movimiento') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('inventario.store') }}" autocomplete="off" enctype="multipart/form-data" id="form_inventario_new">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Movimiento') }}</h6>
                            <div class="pl-lg-4">
                            	<div class="form-group{{ $errors->has('tipo_movimiento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_movimiento">{{ __('Tipo de Movimiento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_movimiento" name="tipo_movimiento" value="{{ old('tipo_movimiento') }}" required>
                                        <option value="1">Entrada</option>
                                        <option value="2">Salida</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_movimiento'])
                                </div>
                                <div class="form-group{{ $errors->has('idcliente') ? ' has-danger' : '' }}" id="combo_idcliente">
                                    <label class="form-control-label" for="input-idcliente">{{ __('Proveedores') }}</label>
                                    <select class="form-control form-control-alternative" id="idcliente" name="idcliente" value="{{ old('idcliente') }}" required>
                                    	<option value="0">-- Seleccione un Proveedor --</option>
                                    @foreach($proveedores as $prov) 
                                        <option value="{{ $prov->idcliente }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idcliente'])
                                </div>
                                <div class="form-group{{ $errors->has('num_documento') ? ' has-danger' : '' }}" id="combo_numdoc">
                                    <label class="form-control-label" for="input-num_documento">{{ __('Número de Documento') }}</label>
                                    <input type="text" name="num_documento" id="input-num_documento" class="form-control form-control-alternative{{ $errors->has('num_documento') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Documento') }}" value="{{ old('num_documento') }}">
                                    @include('alerts.feedback', ['field' => 'num_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('condicion_movimiento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="condicion_movimiento">{{ __('Condición de Movimiento') }}</label>
                                    <select class="form-control form-control-alternative" id="condicion_movimiento" name="condicion_movimiento" value="{{ old('condicion_movimiento') }}" required>
                                        <option value="1">Contado</option>
                                        <option value="2">Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condicion_movimiento'])
                                </div>
                                <div class="form-group{{ $errors->has('plazo_credito') ? ' has-danger' : '' }}" id="plaz_credito" style="display: none;">
                                    <label class="form-control-label" for="input-plazo_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="plazo_credito" id="input-plazo_credito" class="form-control form-control-alternative{{ $errors->has('plazo_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('plazo_credito') }}">
                                    @include('alerts.feedback', ['field' => 'plazo_credito'])
                                </div>
                                <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-observaciones">{{ __('Observaciones') }}</label>
                                    <textarea class="form-control form-control-alternative{{ $errors->has('observaciones') ? ' is-invalid' : '' }}" id="observaciones" name="observaciones" required="true"></textarea>
                                    @include('alerts.feedback', ['field' => 'observaciones'])
                                </div>
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-success" data-target="#AddProductosInventario" data-toggle="modal" id="Agregar_producto" style="display: none;">Agregar Productos</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-items-center" id="tabla_productos">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Descripción</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Costo Unitario Sin IVA</th>
                                                <th scope="col">Taza IVA</th>
                                                <th scope="col">Útilidad</th>
                                                <th scope="col">Precio Final unitario venta</th>
                                                <th scope="col">Total Compra</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="tabla_productos">
                                        </tbody>
                                    </table>
                                </div>
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
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
@include('modals.addProductsInventario')
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
        $('#tipo_movimiento').change(function() {
            if ($(this).val() > 1) {
                $('#combo_numdoc').css( "display","none");
                $('#combo_idcliente').css( "display","none");
                $('#Agregar_producto').css( "display","");

            }else{
                $('#combo_numdoc').css( "display","");
                $('#combo_idcliente').css( "display", "");
                $('#Agregar_producto').css( "display","none");

            }
        });

        $(document).on("click", "#eliminar_fila" , function(event) {
            event.preventDefault();
            var valor = $('#exoneracion').val();
        });

        $('[name="seleccion[]"]').click(function() {
      
            var arr = $('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = arr.join(',');
            $('#sales_item').val(arr);
        });
        $('#agregar_inventario').click(function(e) {
            e.preventDefault();
            var datos = $('#form_inventario_new').serialize();
            $('#AddProductosInventario').modal('hide');
            $("#form_inventario_new").submit();
        });

        $('#idcliente').change(function() {
            if ($(this).val() > 1) {
                $('#Agregar_producto').css( "display","");
            }else{
                $('#Agregar_producto').css( "display","none");
            }
        });

        $('#condicion_movimiento').change(function() {
            if ($(this).val() === '1') {
                $('#plaz_credito').css( "display", "none");
            }else{
                $('#plaz_credito').css( "display", "block");  
            }
        });
    });
</script>
@endsection