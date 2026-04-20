@extends('layouts.app', ['page' => __('User Management'), 'pageSlug' => 'users'])

@section('content')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Nuevo Producto/Servicio') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('productos.update', $producto->idproducto) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Producto/Servicio') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_producto">{{ __('Tipo de Producto') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_producto" name="tipo_producto" value="{{ old('tipo_producto', $producto->tipo_producto) }}" required disabled="true">
                                        <option value="1" {{ ($producto->tipo_producto == 1 ? 'selected="selected"' : '') }}>Producto</option>
                                        <option value="2" {{ ($producto->tipo_producto == 2 ? 'selected="selected"' : '') }}>Servicio</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('idconfigfact') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idconfigfact">{{ __('Configuracion Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="idconfigfact" name="idconfigfact" value="{{ old('idconfigfact', $producto->idconfigfact ) }}" required disabled="true">
                                        <option value="0">-- Seleccione una Configuración --</option>
                                    @foreach($configuracion as $config) 
                                        <option value="{{ $config->idconfigfact }}" {{ ($producto->idconfigfact == $config->idconfigfact ? 'selected="selected"' : '') }}>{{ $config->nombre_empresa }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idconfigfact'])
                                </div>
                                <div class="form-group{{ $errors->has('idcodigoactv') ? ' has-danger' : '' }}" id="combo_actividad" style="display: none;">
                                    <label class="form-control-label" for="input-idcodigoactv">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="idcodigoactv" name="idcodigoactv" value="{{ old('idcodigoactv', $producto->idcodigoactv) }}" required disabled="true">
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idcodigoactv'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_producto">{{ __('Código Producto') }}</label>
                                    <input type="text" name="codigo_producto" id="input-codigo_producto" class="form-control form-control-alternative{{ $errors->has('codigo_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Producto') }}" value="{{ old('codigo_producto', $producto->codigo_producto) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'codigo_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_producto">{{ __('Nombre Producto') }}</label>
                                    <input type="text" name="nombre_producto" id="input-nombre_producto" class="form-control form-control-alternative{{ $errors->has('nombre_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Producto') }}" value="{{ old('nombre_producto', $producto->nombre_producto) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'nombre_producto'])
                                </div>                                
                                <div class="form-group{{ $errors->has('unidad_medida') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idunidadmedida">{{ __('Unidad Medida') }}</label>
                                    <select class="form-control form-control-alternative" id="idunidadmedida" name="idunidadmedida" value="{{ old('idunidadmedida', $producto->idunidadmedida) }}" required disabled="true">
                                        <option value="0">-- Seleccione una unidad --</option>
                                    @foreach($unidad_medida as $u_m) 
                                        <option value="{{ $u_m->idunidadmedida }}"  {{ ($producto->idunidadmedida == $u_m->idunidadmedida ? 'selected="selected"' : '') }}>{{$u_m->simbolo }} - ({{ $u_m->descripcion }})</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idunidadmedida'])
                                </div>
                                <div class="form-group{{ $errors->has('impuesto_iva') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-impuesto_iva">{{ __('Tipo de Impuestos') }}</label>
                                    <select class="form-control form-control-alternative" id="impuesto_iva" name="impuesto_iva" value="{{ old('impuesto_iva') }}" required disabled="true">
                                        <option value="01" {{ ($producto->impuesto_iva == '01' ? 'selected="selected"' : '') }}>Tarifa 0% (Exento)</option>
                                        <option value="02" {{ ($producto->impuesto_iva == '02' ? 'selected="selected"' : '') }}>Tarifa reducida 1%</option>
                                        <option value="03" {{ ($producto->impuesto_iva == '03' ? 'selected="selected"' : '') }}>Tarifa reducida 2%</option>
                                        <option value="04" {{ ($producto->impuesto_iva == '04' ? 'selected="selected"' : '') }}>Tarifa reducida 4%</option>
                                        <option value="05" {{ ($producto->impuesto_iva == '05' ? 'selected="selected"' : '') }}>Transitorio 0%</option>
                                        <option value="06" {{ ($producto->impuesto_iva == '06' ? 'selected="selected"' : '') }}>Transitorio 4%</option>
                                        <option value="07" {{ ($producto->impuesto_iva == '07' ? 'selected="selected"' : '') }}>Transitorio 8%</option>
                                        <option value="08" {{ ($producto->impuesto_iva == '08' ? 'selected="selected"' : '') }}>Tarifa general 13%</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'impuesto_iva'])
                                </div>
                                <div class="form-group{{ $errors->has('porcentaje_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="porcentaje_imp">{{ __('Porcentaje Impuesto') }}</label>
                                    <input type="number" name="porcentaje_imp" id="porcentaje_imp" class="form-control form-control-alternative{{ $errors->has('porcentaje_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Porcentaje Impuesto') }}" value="{{ old('porcentaje_imp', $producto->porcentaje_imp) }}" readonly readonly="true">
                                    @include('alerts.feedback', ['field' => 'porcentaje_imp'])
                                </div>
                                <div class="form-group{{ $errors->has('costo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-costo">{{ __('Costo del Producto') }}</label>
                                    <input type="number" name="costo" id="input-costo" class="form-control form-control-alternative{{ $errors->has('costo') ? ' is-invalid' : '' }}" placeholder="{{ __('Costo del Producto') }}" value="{{ old('costo', $producto->costo) }}"  readonly="true">
                                    @include('alerts.feedback', ['field' => 'costo'])
                                </div>
                                <div class="form-group{{ $errors->has('utilidad_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-utilidad_producto">{{ __('Utilidad del Producto') }}</label>
                                    <input type="number" name="utilidad_producto" id="input-utilidad_producto" class="form-control form-control-alternative{{ $errors->has('utilidad_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Utilidad del Producto') }}" value="{{ old('utilidad_producto', $producto->utilidad_producto) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'utilidad_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('precio_sin_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-precio_sin_imp">{{ __('Precio Sin IVA') }}</label>
                                    <input type="number" name="precio_sin_imp" id="input-precio_sin_imp" class="form-control form-control-alternative{{ $errors->has('precio_sin_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Sin IVA') }}" value="{{ old('precio_sin_imp', $producto->precio_sin_imp) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'precio_sin_imp'])
                                </div>
                                <div class="form-group{{ $errors->has('precio_final') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-precio_final">{{ __('Precio Final') }}</label>
                                    <input type="number" name="precio_final" id="input-precio_final" class="form-control form-control-alternative{{ $errors->has('precio_final') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Final') }}" value="{{ old('precio_final', $producto->precio_final) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'precio_final'])
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table align-items-center" id="tabla_productos">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Tipo de Movimiento</th>
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
                                            $inventario = App\Inventario::find($inv_item->idinventario);
                                            if ($inventario->tipo_movimiento === 1) {
                                                $flecha = "<i class='fas fa-angle-double-up' style='color: #04f704;'></i> Entrada";
                                            }else{
                                                $flecha = "<i class='fas fa-angle-double-down' style='color: #f70404;'></i> Salida";
                                            }
                                        ?>
                                        <tr>
                                            <td>{{ $inv_item->idinventario_item }}</td>
                                            <td><?php echo $flecha; ?></td>
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
                                <tfoot>
                                    <tr>
                                        <td colspan="6">{{$inventario_item->links() }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

