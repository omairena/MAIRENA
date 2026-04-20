@extends('layouts.app', ['page' => __('Ver Movimiento'), 'pageSlug' => 'showMovimiento'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Ver Movimiento') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('inventario.update', $inventario->idinventario) }}" autocomplete="off" enctype="multipart/form-data" id="form_inventario_edit">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Ver Movimiento') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_movimiento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_movimiento">{{ __('Tipo de Movimiento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_movimiento" name="tipo_movimiento" value="{{ old('tipo_movimiento') }}" required  disabled="true">
                                        <option value="1" {{ ($inventario->tipo_movimiento == 1 ? 'selected="selected"' : '') }}>Entrada</option>
                                        <option value="2" {{ ($inventario->tipo_movimiento == 2 ? 'selected="selected"' : '') }}>Salida</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_movimiento'])
                                </div>
                                <div class="form-group{{ $errors->has('idcliente') ? ' has-danger' : '' }}" id="combo_idcliente">
                                    <label class="form-control-label" for="input-idcliente">{{ __('Proveedores') }}</label>
                                    <select class="form-control form-control-alternative" id="idcliente" name="idcliente" value="{{ old('idcliente') }}" required  disabled="true">
                                        <option value="0">-- Seleccione un Proveedor --</option>
                                    @foreach($proveedores as $prov) 
                                        <option value="{{ $prov->idcliente }}" {{ ($inventario->idcliente == $prov->idcliente ? 'selected="selected"' : '') }}>{{ $prov->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idcliente'])
                                </div>
                                <div class="form-group{{ $errors->has('num_documento') ? ' has-danger' : '' }}" id="combo_numdoc">
                                    <label class="form-control-label" for="input-num_documento">{{ __('Número de Documento') }}</label>
                                    <input type="text" name="num_documento" id="input-num_documento" class="form-control form-control-alternative{{ $errors->has('num_documento') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Documento') }}" value="{{ old('num_documento', $inventario->num_documento) }}"  readonly="true">
                                    @include('alerts.feedback', ['field' => 'num_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-observaciones">{{ __('Observaciones') }}</label>
                                    <textarea class="form-control form-control-alternative{{ $errors->has('observaciones') ? ' is-invalid' : '' }}" id="observaciones" name="observaciones" readonly="true">{{ $inventario->observaciones }}</textarea>
                                    @include('alerts.feedback', ['field' => 'observaciones'])
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-items-center" id="tabla_productos">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Descripción</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Costo del Producto</th>
                                                <th scope="col">Costo Unitario Sin IVA</th>
                                                <th scope="col">Taza IVA</th>
                                                <th scope="col">Útilidad</th>
                                                <th scope="col">Precio Final unitario venta</th>
                                                <th scope="col">Total Compra</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="tabla_productos">
                                            @foreach($inventario_item as $inv_item)
                                                <?php 
                                                    $prod = App\Productos::find($inv_item->idproducto);
                                                    $total = $prod->precio_final * $inv_item->cantidad_inventario;
                                                ?>
                                                <tr>
                                                    <td>{{ $inv_item->idinventario_item }}</td>
                                                    <td>{{ $prod->codigo_producto }}</td>
                                                    <td>{{ $prod->nombre_producto }}</td>
                                                    <td class="text-right"><input type="number" name="cantidad_inventario" id="cantidad_inventario{{ $inv_item->idinventario_item }}" value="{{ $inv_item->cantidad_inventario }}" class="form-control form-control-alternative{{ $errors->has('cantidad_inventario') ? ' is-invalid' : '' }} update_fila_inventario" style="width:80px;" data-id="{{ $inv_item->idinventario_item }}" readonly="true"></td>
                                                    <td class="text-right"><input type="number" name="costo_producto" id="costo_producto{{ $inv_item->idinventario_item }}" value="{{ $prod->costo }}" class="form-control form-control-alternative{{ $errors->has('costo_producto') ? ' is-invalid' : '' }} update_costo_inventario" style="width:80px;" data-id="{{ $prod->idproducto }}" data-inventario="{{ $inv_item->idinventario_item }}" readonly="true"></td>
                                                    <td class="text-right">{{ $prod->precio_sin_imp }}</td>
                                                    <td class="text-right">{{ $prod->porcentaje_imp }} %</td>
                                                    <td class="text-right">{{ $prod->utilidad_producto }} %</td>
                                                    <td class="text-right">{{ $prod->precio_final }}</td>
                                                    <td id="total_final{{ $inv_item->idinventario_item }}" class="text-right">{{ $total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="6">{{$inventario_item->links() }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

