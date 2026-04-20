@extends('layouts.app', ['page' => __('Crear Factura Electronica'), 'pageSlug' => 'crearFactura'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>
</head>
@section('content')
@if(Auth::user()->config_u[0]->es_transporte > 0)
    <div class="container-fluid mt--7">
        <div class="row">
              <!--  <div class="col-xl-4 order-xl-1">
                <div class="card card-user">
                    <div class="image">
                        <img src="{{ asset('black') }}/img/img_3115.jpg" alt="...">
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <a href="#">
                                <img class="logo" src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="125">
                                <h5 class="title">{{ Auth::user()->config_u[0]->nombre_emisor }}</h5>
                            </a>
                            <p class="description">
                              @ {{ Auth::user()->config_u[0]->email_emisor }}
                            </p>
                        </div>
                        <p class="description text-center">
                            {{ Auth::user()->config_u[0]->direccion_emisor }}
                        </p>
                    </div>
                </div>
            </div>-->
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <h5 class="mb-0">{{ __('Crear Factura Fiscal ') }}   <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a></h5><br>
                                <h5 class="mb-0" id="encabezado_factura"></h5>
                            </div>
                            <div class="col-12 text-right">
                              
                            </div>
                        </div>
                    </div>
                      <div class="card-body">
                        <form method="post" action="{{ route('facturar.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_factura" onsubmit="return submitResult();">
                            @csrf
                            @method('PUT')

                          
                            <div class="pl-lg-4">
                                
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                     <input type="hidden" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', $sales->tipo_documento ?? '') }}" required>  
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required disabled='true'>
                                        <option value="0">-- Seleccione un tipo de documento --</option>
                                        <option value="01" {{ ($sales->tipo_documento == 01 ? 'selected="selected"' : '') }}>Fáctura Electrónica</option>
                                        <option value="04" {{ ($sales->tipo_documento == 04 ? 'selected="selected"' : '') }}>Tiquete</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad',$sales->idcodigoactv) }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                    <input type="text" name="valor_actividad" id="valor_actividad" value="{{ old('valor_actividad', $sales->idcodigoactv) }}" hidden="true">
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}" {{ ($sales->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor.') }}</label>
                                    <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required>
                                        <option value="0">-- Seleccione un Cliente --</option>
                                    @foreach($clientes as $cliente)
                                    <?php
                                        switch ($usuario->tipo_id) {
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
                                        <option value="{{ $cliente->idcliente }}" {{ ($sales->idcliente == $usuario->idcliente ? 'selected="selected"' : '') }}>{{$tipo_ident}}{{$usuario->num_id }} {{ $usuario->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                
                                                            <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
  <label class="form-control-label" for="input-actividad">Código Actividad</label>
   <input type="hidden" name="ced_receptor_act" id="ced_receptor_act" class="form-control form-control-alternative{{ $errors->has('ced_receptor_act') ? ' is-invalid' : '' }}" value="{{ $usuario->num_id }}">
  <input type="text" list="actividad-list" id="input-actividad" id = "codigo_actividad" name="codigo_actividad" class="form-control form-control-alternative" value="{{ $usuario->codigo_actividad }}" required>
  <datalist id="actividad-list">
    <!-- Opcional: un valor por defecto para guiar al usuario -->
    <option value="930903">112233-Actividad por defecto</option>
  </datalist>
  @include('alerts.feedback', ['field' => 'codigo_actividad'])
</div>
                                
                                
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01" {{ ($sales->medio_pago == '01' ? 'selected="selected"' : '') }}>Efectivo</option>
                                        <option value="02" {{ ($sales->medio_pago == '02' ? 'selected="selected"' : '') }}>Tarjeta</option>
                                        <option value="03" {{ ($sales->medio_pago == '03' ? 'selected="selected"' : '') }}>Cheque</option>
                                        <option value="04" {{ ($sales->medio_pago == '04' ? 'selected="selected"' : '') }}>Transferencia – depósito bancario</option>
                                        <option value="05" {{ ($sales->medio_pago == '05' ? 'selected="selected"' : '') }}>Recaudado por terceros</option>
                                        <option value="06" {{ ($sales->medio_pago == '06' ? 'selected="selected"' : '') }}>Sinpe Movil</option>
                                        <option value="07" {{ ($sales->medio_pago == '07' ? 'selected="selected"' : '') }}>Plataformas Digitales</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago', $sales->referencia_pago) }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-info" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Agregar Producto/Servicio</a>
                                </div>
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
                                                    <table class="table align-items-center" id="tabla_productos">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Nombre</th>
                                                                <th scope="col">Precio Unitario</th>
                                                                <th scope="col">Cant</th>
                                                                <th scope="col">Neto</th>
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

                                                            @if(count($sales_item) > 0)

                                                                @foreach($sales_item as $sale_i)

                                                                    <?php
                                                                        if ($sale_i->existe_exoneracion == '00') {

                                                                            $tiene_exoneracion = 'No';
                                                                            if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {

                                                                                $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                                            }
                                                                            $total = $sale_i->valor_neto + ($sale_i->valor_impuesto -  $total_iva_devuelto);
                                                                            $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
                                                                        } else {

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
                                                                        @if($sale_i->prod_sale[0]->tipo_producto === 2)
                                                                            <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true">
                                                                        @else
                                                                            <td>{{ number_format($sale_i->costo_utilidad,2,',','.') }}</td>
                                                                        @endif
                                                                        @if($sale_i->existe_exoneracion === '00')
                                                                            <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                                        @else
                                                                            <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                                           
                                                                            <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                                        @endif
                                                               
                                                                        <td>{{ number_format($sale_i->valor_neto,2,',','.') }}</td>
                                                                        @if($sale_i->existe_exoneracion === '00')
                                                                            <td>{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                                                                        @else
                                                                            <td>{{ number_format($monto_imp_exonerado,2,',','.') }}</td>
                                                                        @endif
                                                                        <td><?php echo $tiene_exoneracion; ?></td>
                                                                        <td><?php echo $total; ?></td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                                </a>
                                                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                                    @if($sale_i->existe_exoneracion === '00')
                                                                                    <a href="#" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">{{ __('Agregar exoneración') }}</a>
                                                                                    @else
                                                                                    @endif
                                                                                    @if(count($sales_item) > 1)
                                                                                    <a href="#" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item eliminar_fila_factura">{{ __('Eliminar Fila') }}</a>
                                                                                    @endif
                                                                                    @if($sale_i->prod_sale[0]->flotante > 0)
                                                                                    <a href="#" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item modificar_flotante" data-target="#ModArticulo" data-toggle="modal">{{ __('Modificar Artículo') }}</a>
                                                                                    @endif

                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="otro">
                                                <div class="row">
                                                    <div class="col-8"></div>
                                                    <div class="col-4 text-right">
                                                        <button type="button" rel="tooltip" class="btn btn-sm btn-success" data-target="#AddOtroCargo" data-toggle="modal">
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table align-items-center" id="tabla_otros_cargos">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Identificacion</th>
                                                                <th scope="col">Detalle</th>
                                                                <th scope="col">Porcentaje</th>
                                                                <th scope="col">Monto</th>
                                                                <th></th>
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
                                                                    <td>
                                                                        <button type="button" id="eliminar_fila_otrocargo{{ $otro_cargo->idotrocargo }}" data-id="{{ $otro_cargo->idotrocargo }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_otrocargo">
                                                                            <i class="tim-icons icon-simple-remove"></i>
                                                                        </button>
                                                                    </td>
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
                                    <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ $total_neto }}</b></h4>
                                    <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ $total_descuento }}</b> </h4>
                                    <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ $total_impuesto }}</b></h4>
                                    @if($total_iva_devuelto > 0)
                                    <h4 class="mb-0" id="iva_d" >IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ $total_iva_devuelto }}</b></h4>
                                    @endif
                                    <h4 class="mb-0"> Total Otros Cargos: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_otro_cargo">{{ number_format($total_otros_cargos,2,',','.') }}</b></h4>

                                    <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante + $total_otros_cargos,2,',','.') }}</b></h4>
                                    <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                    <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="{{ old('existe_exoneracion', $sales->tiene_exoneracion) }}" hidden="true">
                                    <input type="text" name="hidden_cliente" id="hidden_cliente" value="{{ old('hidden_cliente', $sales->idcliente) }}" hidden="true">
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                    <input type="text" name="hidden_observaciones" id="hidden_observaciones" value="{{ old('hidden_observaciones') }}" hidden="true">
                                    <input type="text" name="moneda" id="moneda" value="{{ old('moneda', 'CRC') }}" hidden="true">
                                    <input type="text" name="tipo_cambio" id="tipo_cambio" value="{{ old('tipo_cambio', '0') }}" hidden="true">
                                    <input type="text" name="condición_venta" id="condición_venta" value="{{ old('condición_venta', '01') }}" hidden="true">
                                    <input type="text" name="condicion_comprobante" id="condicion_comprobante" value="{{ old('condicion_comprobante', '1') }}" hidden="true">
                                     <input type="text" name="datos_internos" id="datos_internos" hidden="true">
                                    <input type="hidden" name="cliente_hacienda" id="cliente_hacienda" >
                                    <input type="text" name="p_credito" id="p_credito" value="{{ old('p_credito', '0') }}" hidden="true">
                                    <input type="hidden" name="cliente" id="cliente" value="{{ old('cliente', $sales->idcliente) }}" readonly>
                                <input type="hidden" name="cliente_cod" id="cliente_cod" value="{{ old('cliente', $sales->idcliente) }}" readonly>
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control">{{ $sales->observaciones }}</textarea>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Enviar a Hacienda') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Factura Fiscal ') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3>
                                	<a href="#" class="btn btn-sm btn-info" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Buscar Producto</a>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('facturar.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_factura" onsubmit="return submitResult();">
                            @csrf
                            @method('PUT')
                            
                            <div class="pl-lg-4">
                                <div>
                                     <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">  
    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>  
    
    <!-- Campo oculto para enviar el valor -->  
    <input type="hidden" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', $sales->tipo_documento ?? '') }}" required>  

    <!-- Campo de selección deshabilitado -->  
    <select class="form-control form-control-alternative" id="tipo_documento_display" disabled>  
        <option value="0">-- Seleccione un tipo de documento --</option>  
        <option value="01" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '01' ? 'selected="selected"' : '') }}>Fáctura Electrónica</option>  
        @if(Auth::user()->config_u[0]->es_simplificado == 1)  
            @if(Auth::user()->config_u[0]->usa_op > 0)  
                <option value="04" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '04' ? 'selected="selected"' : '') }}>Tiquete</option>  
                <option value="96" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '96' ? 'selected="selected"' : '') }}>Orden de Pedido</option>  
            @else  
                <option value="04" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '04' ? 'selected="selected"' : '') }}>Tiquete</option>  
                <option value="96" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '96' ? 'selected="selected"' : '') }}>Fáctura Regimen Simplificado</option>  
            @endif  
        @else  
            <option value="04" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '04' ? 'selected="selected"' : '') }}>Tiquete</option>  
        @endif  
        <option value="09" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '09' ? 'selected="selected"' : '') }}>Fáctura Electrónica de Exportación</option>  
         <option value="03" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '03' ? 'selected="selected"' : '') }}>Nota de Credito Electronica</option>  
    </select>  

    @include('alerts.feedback', ['field' => 'tipo_documento'])  
</div>  

                            	<!--<div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required disabled='true'>
                                    	<option value="0">-- Seleccione un tipo de documento --</option>
                                        <option value="01" {{ ($sales->tipo_documento == 01 ? 'selected="selected"' : '') }}>Fáctura Electrónica</option>
                                        <option value="04" {{ ($sales->tipo_documento == 04 ? 'selected="selected"' : '') }}>Tiquete</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>-->

                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad',$sales->idcodigoactv) }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                    <input type="text" name="valor_actividad" id="valor_actividad" value="{{ old('valor_actividad', $sales->idcodigoactv) }}" hidden="true">
                                </div>
                               
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <select class="form-control form-control-alternative" id="cliente" name="cliente" value="{{ old('cliente') }}" required disabled='true'>
                                    	<option value="0">-- Seleccione un Cliente --</option>
                                    @foreach($clientes as $cliente)
                                    <?php
                                    	switch ($usuario->tipo_id) {
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
                                        <option value="{{ $cliente->idcliente }}" {{ ($sales->idcliente == $usuario->idcliente ? 'selected="selected"' : '') }}>{{$tipo_ident}}{{$usuario->num_id }} {{ $usuario->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                
                                <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad Cliente') }} &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a></label>
                                        
                                         <input type="hidden" name="ced_receptor_act" id="ced_receptor_act" class="form-control form-control-alternative{{ $errors->has('ced_receptor_act') ? ' is-invalid' : '' }}" value="{{ $usuario->num_id }}">
                                    <select class="form-control form-control-alternative" id="codigo_actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" >
                                        <option value="{{ $usuario->codigo_actividad }}"> {{ $usuario->codigo_actividad }} </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>
                               <!-- <div class="form-group{{ $errors->has('productos') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="productos_t">{{ __('Seleccione el Producto/Servicio a Facturar') }}</label>
                                    <select class="form-control form-control-alternative" id="productos_t" name="productos_t" value="{{ old('productos_t') }}" required>
                                       @foreach($productos as $prod)
                                            <option value="{{ $prod->idproducto }}">{{ $prod->nombre_producto }}</option>
                                       @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'productos_t'])
                                </div>
                                <div class="text-center">
                                    <label class="form-control-label" for="condicion_iva">{{ __('Desmarque el check(✓) de abajo si el precio a indicar incluye el IVA:') }}</label><br>
                                    <input type="checkbox" name="condicion_iva" id="condicion_iva" checked="true">
                                </div>
                                <div class="form-group{{ $errors->has('precio_t') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="precio_t">{{ __('Precio del Producto/Servicio') }}</label>
                                    <input type="number" name="precio_t" id="precio_t" class="form-control form-control-alternative{{ $errors->has('precio_t') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio del Servicio') }}" value="0" required="true">
                                    @include('alerts.feedback', ['field' => 'precio_t'])
                                </div>
                               
                                <div class="form-group text-right">
                                    
                                    <a href="#" class="btn btn-sm btn-success" id="Agregar_producto_transporte">Agregar Producto/Servicio</a>
                                    
                                </div>-->
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">  
    Agregar Otro Producto/Servicio  
</button>
                                 </div>
                                 <button type="button" id="toggleButton" class="btn btn-primary btn-sm">Más Opciones</button>  

                                 <!--segundo bloque><-->
                                <div id="additionalOptions" style="display: none;"> 
                                 <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}" {{ ($sales->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
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
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" step="any" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio', $sales->tipo_cambio) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required>
                                        <option value="01" {{ ($sales->condicion_venta == '01' ? 'selected="selected"' : '') }}>Contado</option>
                                        <option value="02" {{ ($sales->condicion_venta == '02' ? 'selected="selected"' : '') }}>Crédito</option>
                                        <option value="10" {{ ($sales->condicion_venta == '10' ? 'selected="selected"' : '') }}>Venta a crédito en IVA hasta 90 días (Artículo 27, LIVA)</option>
                                        <option value="11" {{ ($sales->condicion_venta == '11' ? 'selected="selected"' : '') }}>Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA)</option>
                                        
                                        
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito', $sales->p_credito) }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>
<div class="form-group{{ $errors->has('condicion_comprobante') ? ' has-danger' : '' }}">  
    <label class="form-control-label" for="input-condicion_comprobante">{{ __('Condición Comprobante') }}</label>  
    <select class="form-control form-control-alternative" id="condicion_comprobante" name="condicion_comprobante" required>  
        <option value="1" {{ ($sales->situacion == '1' ? 'selected="selected"' : '') }}>Normal</option>  
       <option value="2" {{ ($sales->situacion == '2' ? 'selected="selected"' : '') }}>Contingencia</option>  
        <option value="3" {{ ($sales->situacion == '3' ? 'selected="selected"' : '') }}>Sin Internet</option> 
    </select>  
    @include('alerts.feedback', ['field' => 'condición_venta'])  
</div>  

<div class="form-group{{ $errors->has('fecha_ref') ? ' has-danger' : '' }}" id="fecha_ref" style="display: none;">  
    <label class="form-control-label" for="input-fecha_reenvio">{{ __('Fecha Emision') }}</label>  
    <input type="date" name="fecha_reenvio" id="input-fecha_reenvio" class="form-control form-control-alternative{{ $errors->has('fecha_reenvio') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Emision') }}"  hidden="true" value="{{ old('fecha_reenvio', $sales->fecha_creada) }}">  
     <input type="date" name="fecha_ref" id="input-fecha_ref" class="form-control form-control-alternative{{ $errors->has('fecha_ref') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Documento Referencia') }}"  value="">  
    @include('alerts.feedback', ['field' => 'fecha_reenvio'])  
</div>  
<div class="form-group{{ $errors->has('doc_Referencia') ? ' has-danger' : '' }}" id="doc_Referencia" style="display: none;">  
    <label class="form-control-label" for="input-doc_Referencia">{{ __('Doc Referencia') }}</label>  
    <input type="text" name="doc_Referencia" id="input-doc_Referencia" class="form-control form-control-alternative{{ $errors->has('doc_Referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Doc Referencia') }}" value="">  
    @include('alerts.feedback', ['field' => 'fecha_reenvio'])  
</div>
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01" {{ ($sales->medio_pago == '01' ? 'selected="selected"' : '') }}>Efectivo</option>
                                        <option value="02" {{ ($sales->medio_pago == '02' ? 'selected="selected"' : '') }}>Tarjeta</option>
                                        <option value="03" {{ ($sales->medio_pago == '03' ? 'selected="selected"' : '') }}>Cheque</option>
                                        <option value="04" {{ ($sales->medio_pago == '04' ? 'selected="selected"' : '') }}>Transferencia – depósito bancario</option>
                                        <option value="05" {{ ($sales->medio_pago == '05' ? 'selected="selected"' : '') }}>Recaudado por terceros</option>
                                        <option value="06" {{ ($sales->medio_pago == '06' ? 'selected="selected"' : '') }}>Sinpe Movil</option>
                                        <option value="07" {{ ($sales->medio_pago == '07' ? 'selected="selected"' : '') }}>Plataformas Digitales</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago', $sales->referencia_pago) }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                </div>
                                <div class="form-group text-right">
                                
                                </div>
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
    								                <table class="table align-items-center" id="tabla_productos">
    									                <thead class="thead-light">
        									                <tr>
            									                <th scope="col">Codigo</th>
            									                <th scope="col">Nombre</th>
            									                <th scope="col">Cantidad</th>
                                                                <th scope="col">Precio Unit S/IVA</th>
                                                                <th scope="col">Impuesto Monto</th>
                                                                <th scope="col">¿Exoneración?</th>
            									                <th scope="col">Precio Total<a  class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="Si actualizas el precio desde esta opticion, se tomara como el precio total de la linea, sin hacer calculos de descuento y cantidad, es decir, el valor indicado en este campo, sera el total, de la cantidad indicada para la linea a actualizar.">¿?
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
                                                                $total_iva_devuelto_linea = 0;
                                                                $monto_imp_exonerado_t=0;
                                                            ?>
                                                            @if(count($sales_item) > 0)
                                                                @foreach($sales_item as $sale_i)
                                                                    <?php
                                                                    
                                                                     if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                                                                                $total_iva_devuelto_linea =   $sale_i->valor_impuesto;
                                                                            }
                                                                            
                                                                    $total_linea = $sale_i->valor_neto + $sale_i->valor_impuesto  - $sale_i->exo_monto - $total_iva_devuelto_linea;
                                                                    $monto_imp_exonerado_t=$monto_imp_exonerado_t+$sale_i->exo_monto;
                                                                        if ($sale_i->existe_exoneracion == '00') {

                                                                            $tiene_exoneracion = 'No';
                                                                            if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                                                                                $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                                            }
                                                                            $total = $sale_i->valor_neto + ($sale_i->valor_impuesto -  $total_iva_devuelto);
                                                                            $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
                                                                        } else {

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
                                                                        <td>
                                                                          
                                                                        
                                                                            {{ $sale_i->codigo_producto }}</td>
                                                                        <td>{{ $sale_i->nombre_producto }}</td>
                                                                     
                                                                        @if($sale_i->existe_exoneracion === '00')
                                                                           
                                                                                 <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}"></td>
                                                                                  <td class="text-right"><input type="number" step="any" name="costo_utilidad" id="costo_utilidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->costo_utilidad }}" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }} update_costo_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                                  <td>{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                                                                               
                                                                           
                                                                                 
                                                                        @else

                                                                            <td class="text-right"><input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                                             <td>{{ number_format($monto_imp_exonerado,2,',','.') }}</td>
                                                                            <td class="text-right"><input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true"></td>
                                                                        @endif
                                                                       
                                                                    
                                                                        <td><?php echo $tiene_exoneracion; ?></td>
                                                                       <td><input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $sale_i->idsalesitem }}" value="{{ $total_linea }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}"> </td>
                                                                        <td>
                                                                                <button type="button" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">  
                                                                             <i class="far fa-trash-alt"></i> 
                                                                        </td>
                                                                        <td>
                                                                            <div>
                                                                            <!--<a href="#" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item eliminar_fila_factura">{{ __('Eliminar Fila') }}</a>-->
                                                                             
                                                                             </div>
                                                                            <div class="dropdown">
                                                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                                </a>
                                                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">

                                                                                    @if($sale_i->existe_exoneracion === '00')

                                                                                        <a href="#" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">{{ __('Agregar exoneración') }}</a>
                                                                                    @else
                                                                                    @endif
                                                                                    @if(count($sales_item) > 1)

                                                                                        <a href="#" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item eliminar_fila_factura">{{ __('Eliminar Fila') }}</a>
                                                                                    @endif
                                                                                    @if($sale_i->prod_sale[0]->flotante > 0)

                                                                                        <a href="#" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="dropdown-item modificar_flotante" data-target="#ModArticulo" data-toggle="modal">{{ __('Modificar Artículo') }}</a>
                                                                                    @endif

                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="otro">
                                                <div class="row">
                                                    <div class="col-8"></div>
                                                        <div class="col-4 text-right">
                                                            <button type="button" rel="tooltip" class="btn btn-sm btn-success" data-target="#AddOtroCargo" data-toggle="modal">
                                                                Agregar
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table align-items-center" id="tabla_otros_cargos">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Identificacion</th>
                                                                    <th scope="col">Detalle</th>
                                                                    <th scope="col">Porcentaje</th>
                                                                    <th scope="col">Monto</th>
                                                                    <th></th>
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
                                                                        <td>
                                                                            <button type="button" id="eliminar_fila_otrocargo{{ $otro_cargo->idotrocargo }}" data-id="{{ $otro_cargo->idotrocargo }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_otrocargo">
                                                                                <i class="tim-icons icon-simple-remove"></i>
                                                                            </button>
                                                                        </td>
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
                                    <!-- Botón para abrir el modal -->  
<!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">  
    + Producto/Servicio  
</button>  -->

<!-- Modal -->  
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">  
    <div class="modal-dialog" role="document">  
        <div class="modal-content">  
            <div class="modal-header">  
                <h5 class="modal-title" id="myModalLabel">Formulario de Facturación</h5>  
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">  
                    <span aria-hidden="true">&times;</span>  
                </button>  
            </div>  
            <div class="modal-body">  
                <form id="formulario_producto">  
                    <div class="form-group{{ $errors->has('productos') ? ' has-danger' : '' }}">  
                        <label class="form-control-label" for="productos_t">{{ __('Seleccione el Producto/Servicio a Facturar') }}</label>  
                        <select class="form-control form-control-alternative" id="productos_tm" name="productos_tm" value="{{ old('productos_t') }}" required>  
                            @foreach($productos as $prod)  
                                <option value="{{ $prod->idproducto }}">{{ $prod->nombre_producto }}</option>  
                            @endforeach  
                        </select>  
                        @include('alerts.feedback', ['field' => 'productos_t'])  
                    </div>  
                    
                    <div class="form-group{{ $errors->has('precio_t') ? ' has-danger' : '' }}">  
                        <label class="form-control-label" for="precio_t">{{ __('Precio del Producto/Servicio') }}</label>  
                        <input type="number" name="precio_tm" id="precio_tm" class="form-control form-control-alternative{{ $errors->has('precio_t') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio del Servicio') }}" value="0" required="true">  
                        @include('alerts.feedback', ['field' => 'precio_t'])  
                    </div>  
                    <div class="text-center">  
                        <label class="form-control-label" for="condicion_iva">{{ __('Con IVA?') }}</label><br>  
                        <input type="checkbox" name="condicion_ivam" id="condicion_ivam" checked="true">  
                    </div>  
                </form>  
            </div>  
            <div class="modal-footer">  
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>  
                <a href="#" class="btn btn-sm btn-success" id="Agregar_producto_transportem">Agregar Producto/Servicio</a>  
            </div>  
        </div>  
    </div>  
</div>  

<!-- Incluye jQuery y Bootstrap JS -->  
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>  


                                    <div class="form-group text-right">
                                        <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto">{{ number_format($total_neto,2,',','.') }}</b></h4>
                                        <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento">{{ number_format($total_descuento,2,',','.') }}</b> </h4>
                                        <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto">{{ number_format($total_impuesto,2,',','.') }}</b></h4>
                                         @if($monto_imp_exonerado_t > 0)
                                    <h4 class="mb-0" id="iva_exo" >IVA Exonerado: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_exo">{{ number_format($monto_imp_exonerado_t,2,',','.') }}</b></h4>
                                    @endif
                                    @if($total_iva_devuelto > 0)
                                    <h4 class="mb-0" id="iva_d" >IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto">{{ number_format($total_iva_devuelto,2,',','.') }}</b></h4>
                                    @endif
                                      @if($total_otros_cargos > 0)
                                    <h4 class="mb-0"> Total Otros Cargos: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_otro_cargo">{{ number_format($total_otros_cargos,2,',','.') }}</b></h4>
                                             @endif
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento">{{ number_format($total_comprobante + $total_otros_cargos,2,',','.') }}</b></h4>
                                	<input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                    <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                                	<input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="{{ old('existe_exoneracion', $sales->tiene_exoneracion) }}" hidden="true">
                                    
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                    @if($sales->condicion_venta == '02')
                                        <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="{{ old('idmovcxcobrar', $sales->idmovcxcobrar) }}" hidden="true">
                                    @endif
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control">{{ $sales->observaciones }}</textarea>
                                    </div>
                                    @if($sales->tipo_documento == '03')  
    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">  
        <label class="form-control-label" for="clave_sale">{{ __('Documento Referencia') }}</label>  
        <input type="text" name="clave_sale" id="clave_sale" value="" size="60" required>  
    </div>  

    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">  
        <label class="form-control-label" for="fecha_emision">{{ __('Fecha Emision') }}</label>  
        <input type="date" name="fecha_emision" id="fecha_emision" value="" required>  
    </div>  

      <div class="form-group{{ $errors->has('tipo_doc_ref') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_doc_ref">{{ __('Tipo de Documento Referencia') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_doc_ref" name="tipo_doc_ref" value="{{ old('tipo_doc_ref') }}" required>
                                        <option value="01">Factura Electronica.</option>
                                        <option value="02">Nota de Debito</option>
                                        <option value="04">Tique electronico</option>
                                         <option value="17">Factura Electronica Compra</option>
                                       
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_doc_ref'])
                                </div>  

    <div class="form-group{{ $errors->has('tipo_devolucion') ? ' has-danger' : '' }}">  
        <label class="form-control-label" for="tipo_devolucion">{{ __('Tipo de Devolución') }}</label>  
        <select class="form-control form-control-alternative" id="tipo_devolucion" name="tipo_devolucion" required>  
            <option value="" disabled selected>-- Seleccione un tipo de Devolución --</option>  
            <option value="1">Devolución Parcial</option>  
            <option value="2">Devolución Total</option>  
        </select>  
    </div>  

    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">  
        <label class="form-control-label" for="razon">{{ __('Razon de Nota de Credito') }}</label>  
        <input type="text" name="razon" id="razon" value="" size="60" required>  
    </div>  
@endif  
                                </div>
                               
                                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $sales->idcliente) }}" readonly>
                                <input type="text" name="cliente_cod" id="cliente_cod" value="{{ old('cliente', $sales->idcliente) }}" readonly>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Enviar Hacienda') }}</button>
                                </div>
                            </div>
                           
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@include('modals.addProducts')
@include('modals.addExoneracion')
@include('modals.modArticulo')
@include('modals.cargando')
@include('modals.addOtroCargo')
@endsection
@section('myjs')



<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">

//para el cambio de condicion del comprobante
document.addEventListener('DOMContentLoaded', function() {  
        const condicionComprobante = document.getElementById('condicion_comprobante');  
        const fechaReenvio = document.getElementById('fecha_ref');  
        const doc_referencia = document.getElementById('doc_Referencia'); 

        // Función para mostrar u ocultar el campo de fecha  
        function toggleFechaReenvio() {  
            if (condicionComprobante.value > 1) {  
                fechaReenvio.style.display = 'block'; // Muestra el campo 
                doc_referencia.style.display = 'block'; // Muestra el campo 
                alert('Al cambiar la Condicion del Comprobante, debes indicar la fecha del Doc de Referencia, es importante tomar en cuentas las medidas del caso y escoger la opcion correcta: Normal= Envio de un Comprobante día a día, Contingencia= La plataforma de Facturación no estaba Disponible, Sin Internet= Sin acceso a la Red de Internet.'); // Alerta  
            } else {  
                fechaReenvio.style.display = 'none'; // Oculta el campo
                doc_referencia.style.display = 'none'; // Muestra el campo 
            }  
        }  

        // Escucha el evento change en el select  
        condicionComprobante.addEventListener('change', toggleFechaReenvio);  

        // Llama a la función al cargar la página para establecer el estado inicial  
        toggleFechaReenvio();  
    });  
//bloques
document.addEventListener('DOMContentLoaded', function() {  
        document.getElementById('toggleButton').addEventListener('click', function() {  
            var additionalOptions = document.getElementById('additionalOptions');  
            if (additionalOptions) { // Verifica que el elemento exista  
                if (additionalOptions.style.display === 'none') {  
                    additionalOptions.style.display = 'block';  
                } else {  
                    additionalOptions.style.display = 'none';  
                }  
            } else {  
                console.error('Elemento adicional no encontrado');  
            }  
        });  
    });  

//bloques

$(document).ready(function() {
  var $input = $('#input-actividad');
  var $datalist = $('#actividad-list');
  var id = $('#ced_receptor_act').val() || '';

  // Asegurar editable
  $input.prop('readonly', false).prop('disabled', false);

  if (!id) {
    $datalist.empty().append('<option value="0">112233-Actividad por defecto</option>');
    return;
  }

  var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + encodeURIComponent(id);

  $datalist.empty().append('<option value="">Cargando...</option>');
  // opcional mostrar placeholder en el input
  // $input.val('');

  $.ajax({
    type: 'GET',
    url: URL,
    dataType: 'json',
    success: function(response) {
      $datalist.empty();

      if (response && typeof response === 'object') {
        if (response.nombre) {
          $('#input-nombre').val(response.nombre);
          $('#input-razon_social').val(response.nombre);
        }
        if (response.tipoIdentificacion) {
          $('#tipo_id').val(response.tipoIdentificacion);
        }

        var actividades = Array.isArray(response.actividades) ? response.actividades : [];

        if (actividades.length > 0) {
          actividades.forEach(function(act) {
            var cod = act.codigo || '';
            var des = act.descripcion || '';
            // datalist acepta option con value; el texto extra es guía
            $datalist.append('<option value="' + cod + '">' + cod + (des ? ' - ' + des : '') + '</option>');
          });
          // no sobreescribimos el input si el usuario ya tiene valor
        } else {
          $datalist.append('<option value="0">112233-Actividad por defecto</option>');
          // opcional establecer por defecto en input:
          // $input.val('0');
        }
      } else {
        $datalist.append('<option value="0">112233-Actividad por defecto</option>');
      }
    },
    error: function() {
      console.error('AJAX Error');
      alert('Identificación No Encontrada');
      $('#input-nombre').val('');
      $('#tipo_id').val('');
      $('#input-razon_social').val('');
      $datalist.empty().append('<option value="0">112233-Actividad por defecto</option>');
      // $input.val('0');
    }
  });
});

// Ejecutar después de cargar jQuery
$(document).ready(function() {
  $(document).on('change blur', '#input-actividad', function() {
    var codigo_actividad = $(this).val();
    var cliente = $('#cliente_cod').val();
    var URL = {!! json_encode(url('modificar-actividad-cliente')) !!};

    // Validar valor mínimo (opcional)
    if (!codigo_actividad) return;

    $.ajax({
      type: 'GET',
      url: URL,
      dataType: 'json',
      data: { codigo_actividad: codigo_actividad, cliente: cliente },
      success: function(response) {
        console.log(response);
        location.reload();
      },
      error: function(xhr, status, err) {
        console.error('AJAX Error:', status, err);
      }
    });
  });
});

    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var APP_URL2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,APP_URL2);
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

        $('#tabla_otros_cargos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "autoWidth": true,
            "processing": true,
            "serverSide": false,
            "deferRender": true,
            order: [[ 0, "asc" ]]
        });

        var sales_item_otro_cargo = @json($sales_item);
        if (sales_item_otro_cargo.length > 0) {

            $('#porcentaje_div_otro_cargo').css( "display", "block");

        } else {

            $('#porcentaje_div_otro_cargo').css( "display", "none");
        }
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

        if ($('#moneda').val() != 'CRC') {
            $('#tipo_cambio').css( "display", "block");
        }else{
            $('#tipo_cambio').css( "display", "none");
        }

        $('#actividad').change(function() {
          var actividad = $(this).val();
          var idsale = $('#idsale').val();
          var URL = {!! json_encode(url('modificar-actividad')) !!};
          $.ajax({
            type:'get',
            url: URL,
            dataType: 'json',
            data:{actividad:actividad,idsale:idsale},
            success:function(response){
              //console.log(response);
              location.reload();
            }
          });
        });



 $('#codigo_actividad').change(function() {
          var codigo_actividad = $(this).val();
          var cliente = $('#cliente_cod').val();
          var URL = {!! json_encode(url('modificar-actividad-cliente')) !!};
          $.ajax({
            type:'get',
            url: URL,
            dataType: 'json',
            data:{codigo_actividad:codigo_actividad,cliente:cliente},
            success:function(response){
              console.log(response);
              location.reload();
            }
          });
        });
        
        
        // Lcarreno mes de julio Otros Cargos
        $('#tipo_doc_otro_cargo').change(function() {
            var tipodoc = $(this).val();
            if (tipodoc != 0) {

                if (tipodoc == '04') {

                    $('#identificacion_otro_cargo').prop("required", true);
                    $('#nombre_otro_cargo').prop("required", true);
                    $('#identificacion_otro_cargo').prop('readonly', false);
                    $('#nombre_otro_cargo').prop('readonly', false);

                } else {

                    $('#identificacion_otro_cargo').prop("required", false);
                    $('#nombre_otro_cargo').prop("required", false);
                    $('#identificacion_otro_cargo').prop('readonly', true);
                    $('#nombre_otro_cargo').prop('readonly', true);
                }
            } else {

                $('#identificacion_otro_cargo').prop("required", false);
                $('#nombre_otro_cargo').prop("required", false);
                $('#identificacion_otro_cargo').prop('readonly', true);
                $('#nombre_otro_cargo').prop('readonly', true);
                alert('debe escojer un valor valido');
            }
        });

        //tiene_porcentaje_otro_cargo
        $('#tiene_porcentaje_otro_cargo').on('click', function () {

            if( $('#tiene_porcentaje_otro_cargo').is(':checked') ) {

                $('#porcentaje_otro_cargo').prop("required", true);
                $('#porcentaje_otro_cargo').prop('readonly', false);
                $('#monto_otro_cargo').prop('readonly', true);
                $('#monto_otro_cargo').val(0);
            } else {

                $('#porcentaje_otro_cargo').prop("required", false);
                $('#porcentaje_otro_cargo').prop('readonly', true);
                $('#monto_otro_cargo').prop('readonly', false);
                $('#monto_otro_cargo').val(0);
                $('#porcentaje_otro_cargo').val(0);

            }
        });
        $(document).on("blur", "#porcentaje_otro_cargo" , function(event) {
            var porcentaje = $(this).val();
            var subtotal = 0;
            for(var i = 0; i < sales_item_otro_cargo.length; i++) {

                subtotal = subtotal + sales_item_otro_cargo[i].valor_neto;
            }
            var calculo = (subtotal * porcentaje)/100;
            $('#monto_otro_cargo').val(calculo);
        });
        $(document).on("submit", "#form_add_cargo" , function(event) {
            event.preventDefault();
            var idsale = $('#idsale').val();
            var unindexed_array = $('#form_add_cargo').serializeArray();
            var datos = {};

            $.map(unindexed_array, function(n, i){
                datos[n['name']] = n['value'];
            });

            var URL = {!! json_encode(url('agregar-otrocargo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{datos:datos, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });


        $('#moneda').change(function() {
            var moneda = $(this).val();
            switch(moneda){
                case 'CRC':
                    $('#tipo_cambio').css( "display", "none");
                    $('#input-tipo_cambio').val('0.00');
                    var tipocambio = 0.00;
                    var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                        $.ajax({
                            type:'get',
                            url: URL,
                            dataType: 'json',
                            data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
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
                                var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
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
                                var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda, idsale:idsale},
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

       $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
            $('#sales_item').val(data);
        });

        $('#condición_venta').change(function() {
            var condicion = $(this).val();
            var idsale = $('#idsale').val();
            if (condicion === '02') {
                $('#input-p_credito').prop("required", true);
            }else{
                $('#input-p_credito').prop("required", false);
            }
            var URL = {!! json_encode(url('modificar-condicion')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{condicion:condicion, idsale:idsale},
                success:function(response){
                    //console.log();
                },
                error:function(response){
                    //console.log(response);
                }
            });
        });

        $(document).on("blur", "#input-p_credito" , function(event) {
            event.preventDefault();
            var dias = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('modificar-dias-cxc')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{dias:dias, idsale:idsale},
                success:function(response){
                    location.reload();
                },
                error:function(response){
                    //console.log(response);
                }
            });
        });


 
        
        
       $(document).on("click", "#Agregar_producto_transporte" , function(event) {
            event.preventDefault();
            var sales_item = $('#productos_t').val();
            var idsale = $('#idsale').val();
            var cantidad = 1;
            var monto_total =  $('#precio_t').val();
           
            var es_sin_impuesto = $('#condicion_iva').is(':checked');
            var valor = es_sin_impuesto ? 1 : 0;
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale, cantidad:cantidad, monto_total:monto_total, es_sin_impuesto:valor},
                success:function(response){
                    location.reload();
                }
            });
        });
        
               $(document).on("click", "#Agregar_producto_transportem" , function(event) {
            event.preventDefault();
            var sales_item = $('#productos_tm').val();
            var idsale = $('#idsale').val();
            var cantidad = 1;
            var monto_total =  $('#precio_tm').val();
           
            var es_sin_impuesto = $('#condicion_ivam').is(':checked');
            var valor = es_sin_impuesto ? 1 : 0;
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale, cantidad:cantidad, monto_total:monto_total, es_sin_impuesto:valor},
                success:function(response){
                    location.reload();
                }
            });
        });
        
        $('#agregar_producto').click(function(e) {
            e.preventDefault();
            var sales_item = $('#sales_item').val();
            var idsale = $('#idsale').val();
            var cantidad = null;
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
//omairena 25-06-2021
         $('#agregar_producto').click(function(j) {
            e.preventDefault();
            if( $('.select-checkbox').is(':checked') ) {
                enviarDatosProducto();
            } else {
                alert('Debe seleccionar al menos 1 producto.');
            }
        });



        ////
        $(document).on("click", ".eliminar_fila_otrocargo" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-otrocargo')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idotrocargo:id},
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
//omariena - 26-05-2021
        $(document).on("blur", ".update_costo_con_iva" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_con_iva = $(this).val();
            var URL = {!! json_encode(url('actualizar-costo-con-iva')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, idproducto: idproducto, costo_con_iva:costo_con_iva},
                success:function(response){
                    //console.log(response);
                    location.reload();
                }
            });
        });
        //
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
                             $('#input-fecha_exoneracionmh').val(response.fechaEmision);
                            $('#input-institucion').val(response.nombreInstitucion);
                            $('#input-institucion').attr("readonly", true);
                             $('#input-porcentaje_exoneracion').val(response.porcentajeExoneracion);
                            $('#input-porcentaje_exoneracion').attr("readonly", true);
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
                var URL = {!! json_encode(url('validar-porcentaje')) !!};
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
        $(document).on("blur", "#cliente_serch" , function(event) {
            event.preventDefault();
            var nombre_cli = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-cliente-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{nombre_cli:nombre_cli, idsale:idsale},
                success:function(response){
                    var arreglo = response['success'].length;
                    if (arreglo > 0) {
                        $('#cliente_serch').val(response['success'][0]['nombre']);
                        $('#cliente_hidden').val(response['success'][0]['idcliente']);
                    }else{
                        $('#cliente_serch').val('No se encontraron resultados');
                        $('#cliente_hidden').val(0);
                    }
                }
            });
        });
        $(document).on("blur", "#medio_pago" , function(event) {
            event.preventDefault();
            var medio_pago = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-mediopago-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{medio_pago:medio_pago, idsale:idsale},
                success:function(response){

                }
            });
        });
        $(document).on("blur", "#tipo_documento" , function(event) {
            event.preventDefault();
            var tipo_documento = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{tipo_documento:tipo_documento, idsale:idsale},
                success:function(response){
                    //console.log(response);
                }
            });
        });

        $(document).on("blur", "#idcaja" , function(event) {
            event.preventDefault();
            var idcaja = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-caja-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idcaja:idcaja, idsale:idsale},
                success:function(response){

                }
            });
        });
        $(document).on("blur", "#actividad" , function(event) {
            event.preventDefault();
            var actividad = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-actividad-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{actividad:actividad, idsale:idsale},
                success:function(response){
                    //console.log(response);
                }
            });
        });
        $(document).on("blur", "#referencia_pago" , function(event) {
            event.preventDefault();
            var referencia = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-referencia-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{referencia:referencia, idsale:idsale},
                success:function(response){
                    //console.log(response);
                }
            });
        });
    });


$(document).ready(function() {
    var $selectActividad = $('#codigo_actividad');
    var valorActual = $selectActividad.val();

    // Si el valor es 0 o está vacío, consultar la API para obtener actividades
    if (valorActual === '0' || !valorActual || valorActual === '') {
        var idReceptor = $('#ced_receptor_act').val() || '';
        if (!idReceptor) {
            // Si no tienes id, dejar la primera opción y salir
            $selectActividad.empty().append('<option value="">Sin Actividad Definida</option>');
            return;
        }

        // Mantener siempre la primera opción
        $selectActividad.empty().append('<option value="">Sin Actividad Definida</option>');

        var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + idReceptor;

        // Indicador de carga
        $selectActividad.append('<option value="">Cargando...</option>');

        $.ajax({
            type: 'GET',
            url: URL,
            dataType: 'json',
            success: function(response) {
                // Conservamos la primera opción
                // Removemos solo las opciones que no sean la primera
                // (Seleccionamos todas las opciones excepto la primera y las limpiamos)
                var firstOpt = $selectActividad.find('option:first');
                $selectActividad.find('option:not(:first)').remove();

                if (response && typeof response === 'object' && Array.isArray(response.actividades)) {
                    if (response.actividades.length > 0) {
                        response.actividades.forEach(function(act) {
                            var valor = act.codigo;
                            var label = act.codigo + ' - ' + (act.descripcion ?? '');
                            $selectActividad.append('<option value="' + valor + '">' + label + '</option>');
                        });
                    } else {
                        // Sin actividades adicionales; ya está la primera opción
                    }
                }

                // Si había un valor previo válido, restáralo
                if (valorActual && valorActual !== '0') {
                    $selectActividad.val(valorActual);
                } else {
                    // Dejar la primera opción como selección por defecto
                    $selectActividad.val('');
                }
            },
            error: function() {
                // En caso de error, mantener la primera opción
                $selectActividad.empty().append('<option value="">Sin Actividad Definida</option>');
            }
        });
    }
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
