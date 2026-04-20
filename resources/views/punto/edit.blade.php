@extends('layouts.pos', ['page' => "", 'pageSlug' => 'crearFactura'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>

</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif

<div class="container-fluid mt--7" >
    
    <div class="row">
        <div class="col-md-12">
            <form method="post"  autocomplete="off" enctype="multipart/form-data" id="form_factura" onsubmit="return submitResult();">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                    <div class="container-fluid mt--12">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="mb-12" id="encabezado_factura"></h3>
                                         <label class="form-control-label" for="input-tipo_documento">{{Auth::user()->config_u[0]->nombre_emisor }}</label>
                                          <left><a href="{{ route('facturar.index') }}" class="btn btn-sm btn-danger" >{{ __(' Salir de Punto de Venta.') }}</a></left>
      <a href="{{ url('punto_edit_data', $sales->idsale) }}" class="btn btn-sm btn-success">{{ __('Ver Encabezado') }}</a>
                                         
                                           <a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Buscar Productos</a>
                                           
                                    </div>
                                   
                                </div>
                                <div class="row">
                                    <div class="input-group">
                                          <a href="{{ url('punto_edit_data', $sales->idsale) }}" class="btn btn-sm btn-warning">{{ __('Continuar') }}</a>
                                         <!--  <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tim-icons icon-zoom-split"></i>
                                            </div>
                                        </div>
                                       
                                        <input type="text" class="form-control"  name="codigo_poss" id="codigo_poss" placeholder="Ingrese Codigo o Nombre del producto">
                                      
                                        
                                     <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" hidden="true">
                                        <button class="btn btn-sm btn-success" type="button" id="agregar_producto_pos" style="display: none;">+</button>-->
                                        <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                                        
                                        
                                        
                        
                                    </div>
                                </div>
                                
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
                                
                                <div class="col-12">
                                <div class="row">
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
                                    </div>
                                    <div class="tab-content text-center">
                                        <div class="tab-pane active" id="detalle">
                                            <div class="table">
                                                <table class="table align-items-center " id="tabla_productos">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">Codigo</th>
                                                            <th scope="col">Nombre Producto</th>
                                                              <th scope="col">Cantidad</th>
                                                              <th scope="col">P.Un S/IVA</th>
                                                              <th scope="col">P.Un C/IVA</th>
                                                            <th scope="col">Prec. Tot Linea<a  class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="Si actualizas el precio desde esta opticion, se tomara como el precio total de la linea, sin hacer calculos de descuento y cantidad, es decir, el valor indicado en este campo, sera el total, de la cantidad indicada para la linea a actualizar.">¿?
                                                            </a></th>
                                                          
                                                            
                                                            <th scope="col">Descuento %</th>
                                                            
                                                            <th scope="col">Impuesto Monto</th>
                                                            <th scope="col">¿Exoneracion?</th>
                                                            <th scope="col">Total</th>
                                                            <th class="text-right">+ Exo</th>
                                                            <th class="text-right"><i class="fas fa-pen"></i></th>
                                                            <th class="text-right">X</th>
                                                             <th class="text-right">ID</th>
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
                                                            $iva_dev_linea = 0;
                                                            if ($sale_i->existe_exoneracion == '00') {

                                                                $tiene_exoneracion = 'No';
                                                                if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {

                                                                    $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                                    $iva_dev_linea =  $iva_dev_linea +  $sale_i->valor_impuesto;
                                                                }
                                                                $total = $sale_i->valor_neto+($sale_i->valor_impuesto-$iva_dev_linea);
                                                                $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;

                                                            } else {

                                                                $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
                                                                $tiene_exoneracion = 'Si '.$exoneracion[0]->porcentaje_exoneracion. ' %';
                                                                $monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
                                                                if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {

                                                                    $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                                                                    $iva_dev_linea =  $iva_dev_linea +  $sale_i->valor_impuesto;

                                                                }
                                                                $total = ($sale_i->valor_neto+ $monto_imp_exonerado)-$iva_dev_linea;
                                                                $total_unitario=$total/$sale_i->cantidad;
                                                                $total_impuesto = $total_impuesto + $monto_imp_exonerado;
                                                            }
                                                            if(($sale_i->valor_impuesto == 0 or $sale_i->cantidad == 0) ){
                                                                 $costo_con_iva_u=0;
                                                            }else{
                                                                 $costo_con_iva_u = $sale_i->costo_utilidad +($sale_i->valor_impuesto/$sale_i->cantidad);
                                                            }
                                                            
                                                            
                                                            $total_neto = $total_neto + $sale_i->valor_neto;
                                                            $total_descuento = $total_descuento + $sale_i->valor_descuento;
                                                            $total_comprobante = $total_comprobante + $total;
                                                            ?>
                                                            <tr>
                                                                <td >{{ $sale_i->codigo_producto }}</td>
                                                               <td > <input  type="text" name="nombre_producto_pos" id="nombre_producto_pos{{ $sale_i->idsalesitem }}" value="{{ $sale_i->nombre_producto }}" class="form-control form-control-alternative{{ $errors->has('nombre_producto_pos') ? ' is-invalid' : '' }} update_nombre_producto_pos"  style="width:500px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                              </td>

                                                              

                                                                @if($sale_i->existe_exoneracion === '00')

                                                                    <td class="text-right">
                                                                        <input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:100px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                    </td>
                                                                    <td class="text-right">
                                                                    <input type="number" step="any" name="costo_sin_iva_u" id="costo_sin_iva_u{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->costo_utilidad ,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_sin_iva_u') ? ' is-invalid' : '' }} update_costo_sin_iva_u" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                     <td class="text-right">
                                                                    <input type="number" step="any" name="costo_con_iva_u" id="costo_con_iva_u{{ $sale_i->idsalesitem }}" value="{{ $costo_con_iva_u }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva_u') ? ' is-invalid' : '' }} update_costo_con_iva_u" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->valor_neto + $sale_i->valor_impuesto,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                    <td class="text-right">
                                                                        <input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:60px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                    </td>
                                                                @else

                                                                    <td class="text-right">
                                                                        <input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" style="width:100px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true">
                                                                    </td>
                                                                    <td class="text-right">
                                                                    <input type="number" step="any" name="costo_sin_iva_u" id="costo_sin_iva_u{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->costo_utilidad ,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva_u') ? ' is-invalid' : '' }} update_costo_con_iva_u" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                     <td class="text-right">
                                                                    <input type="number" step="any" name="costo_con_iva_u" id="costo_con_iva_u{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->costo_utilidad ,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva_u') ? ' is-invalid' : '' }} update_costo_con_iva_u" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->valor_neto ,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" style="width:110px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                                                                </td>
                                                                    <td class="text-right">
                                                                        <input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" style="width:80px;" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" readonly="true">
                                                                    </td>
                                                                @endif
                                                                 
                                                               
                                                                @if($sale_i->existe_exoneracion === '00')

                                                                    <td>{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                                                                @else

                                                                    <td>{{ number_format($monto_imp_exonerado,2,',','.') }}</td>
                                                                @endif
                                                                <td><?php echo $tiene_exoneracion; ?></td>
                                                                <td><?php echo number_format($total,2,',','.'); ?></td>
                                                                <td class="td-actions text-right">
                                                                    @if($sale_i->existe_exoneracion === '00')
                                                                        @if($sale_i->prod_sale[0]->porcentaje_imp > 0) 
                                                                        <button type="button" rel="tooltip" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-success btn-sm btn-icon agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </button>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td class="td-actions text-right">
                                                                    @if($sale_i->prod_sale[0]->flotante > 0)

                                                                        <button type="button" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                                                                            <i class="fas fa-pen"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                                <td class="td-actions text-right">
                                                                    @if(count($sales_item) > 1)

                                                                        <button type="button" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                                                                            <i class="tim-icons icon-simple-remove"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                                 <td >{{ $sale_i->idsalesitem }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="otro">
                                            <div class="row">
                                                <div class="col-8"></div>
                                                <div class="col-4 text-right">
                                                <button type="button" rel="tooltip" class="btn btn-sm btn-success text-right" data-target="#AddOtroCargo" data-toggle="modal">
                                                            Agregar
                                                        </button>
                                                </div>
                                            </div>
                                            <div class="table">
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
                            </div>
                            
                            
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-4">
                                <br><b>Sección de Cambio</b>
                                <div class="form-group">

                                    <label for="efectivo_dev" style="font-weight: bold;">{{ __('Efectivo:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="efectivo_dev" id="efectivo_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <label for="tarjeta_dev" style="font-weight: bold;">{{ __('Tarjeta:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="tarjeta_dev" id="tarjeta_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <label for="cambio_dev" style="font-weight: bold;">{{ __('Cambio:') }}</label>&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="any" name="cambio_dev" id="cambio_dev" class="form-control" style="width:180px; display: inline !important;" readonly="true" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    @if(Auth::user()->config_u[0]->usa_op > 0)

                                        <label for="abono_op" style="font-weight: bold;">{{ __('Abono:') }}</label>&nbsp;&nbsp;&nbsp;
                                        <input type="number" step="any" name="abono_op" id="abono_op" class="form-control" style="width:180px; display: inline !important;"  value="{{ old('total_abonos_op', $sales->total_abonos_op) }}">
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                                
                          <div class="table-responsive-sm">
                          
  <table class="table-sm " style="float:right;">
    <tr>
   
    </tr>
  </thead>
  <tbody>
    <tr>
     
     <th scope="col">Subtotal:</th>
     <td><b id="t_descuento">{{ number_format($total_neto,2,',','.') }}</b></td>
    </tr>
    <tr>
     
      <th scope="col">Total Descuento:</th>
     
      <td><b id="t_descuento">{{ number_format($total_descuento,2,',','.') }}</b></td>
    </tr>
    <tr>
     
    <th scope="col">Total Impuesto:</th>
     
      <td><b id="t_impuesto">{{ number_format($total_impuesto,2,',','.') }}</b></td>
    </tr>
     @if($total_iva_devuelto > 0)
      <tr>
     
  <th scope="col">IVA devuelto:</th>
     
      <td><b id="iva_devuelto">{{ number_format($total_iva_devuelto,2,',','.') }}</b></td>
    </tr>
     @endif
       @if($total_otros_cargos > 0)
      <tr>
    
    <th scope="col">Total Otros Cargos:</th>
     
      <td><b id="t_impuesto">{{ number_format($total_otros_cargos,2,',','.') }}</b></td>
    </tr>
      @endif
        @if(Auth::user()->config_u[0]->usa_op > 0)
      <tr>
     
     <th scope="col">Total Abono:</th>
     
      <td><b id="t_abono_op">{{ number_format($sales->total_abonos_op,2,',','.') }}</b></td>
    </tr>
      <tr>
     
    <th scope="col">Total Documento:</th>
     
      <td><b id="t_documento">{{ number_format(($total_comprobante + $total_otros_cargos) - $sales->total_abonos_op,2,',','.') }}</b></td>
    </tr>
      @else
      <tr>
     
    <th scope="col">Total Documento:</th>
     
      <td><b id="t_documento">{{ number_format($total_comprobante + $total_otros_cargos,2,',','.') }}</b></td>
    </tr>
     @endif
  </tbody>
</table>
 </div>

                            </div>
                            <div class="col-12">
                                <div class="text-center">
                                    <label class="form-control-label" for="desea_imprimir">{{ __('¿Desea Imprimir?') }}</label><br>
                                    <input type="checkbox" name="desea_imprimir" id="desea_imprimir" checked="true">
                                </div>
                                @if(Auth::user()->config_u[0]->usa_op > 0)
                                    <div class="text-center">
                                        <label class="form-control-label" for="desea_enviarcorreo">{{ __('¿Enviar Por Correo?') }}</label><br>
                                        <input type="checkbox" name="desea_enviarcorreo" id="desea_enviarcorreo">
                                    </div>
                                @endif
                                <div class="text-center">
                                    @if(count($lista_cli) > 0)
                                        @if($sales->uso_listaprecio === 0)
                                            <button type="button" class="btn btn-success mt-4" id="clickRecalcular" data-target="#recalcularModal" data-toggle="modal">{{ __('Recalcular') }}</button>
                                        @endif
                                    @endif
                                    
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="{{ old('existe_exoneracion', $sales->tiene_exoneracion) }}" hidden="true">
                <input type="text" name="tot_pos_dev" id="tot_pos_dev" value="{{ old('tot_pos_dev', $total_comprobante) }}" hidden="true">
                <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">
                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $sales->idcliente) }}" hidden="true">
                <input type="text" name="usa_lector" id="usa_lector" value="{{ old('usa_lector', $configuracion[0]->usa_lector ) }}" hidden="true">
                <input type="text" name="usa_balanza" id="usa_balanza" value="{{ old('usa_balanza', Auth::user()->config_u[0]->usa_balanza) }}" hidden="true">
                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                @if($sales->condicion_venta == '02')
                    <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="{{ old('idmovcxcobrar', $sales->idmovcxcobrar) }}" hidden="true">
                @endif
            </form>
        </div>
    </div>
</div>
@include('modals.addProducts')

@include('modals.addExoneracion')
@include('modals.modArticulo')
@include('modals.cargando')
@include('modals.addOtroCargo')
@include('modals.reCalcular')


@endsection

@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
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
        //document.body.style.zoom="90%";
        document.body.style.zoom = 0.85;

    $(document).ready(function() {
        $(window).scroll(function() {
            //scroll(0,0);
        });

        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var APP_URL2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        var APP_URL3 = {!! json_encode(url('/ajaxSerchFacelectron')) !!};
        var idsale = $('#idsale').val();
        traerNumFactura(APP_URL,APP_URL2);
        validaMedioPago();
        validaCondicionVenta();
        validaFacelectron(idsale, APP_URL3);
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
            "scrollY": 300,
           
            order: [[ 13, "desc" ]]
        });

        $('#tabla_otros_cargos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "autoWidth": true,
            "processing": true,
            "serverSide": false,
            "deferRender": true,
            "scrollX": true,
            order: [[ 0, "asc" ]]
        });
        var lista_clientes = @json($lista_cli);
        var sales_item_otro_cargo = @json($sales_item);
        if (sales_item_otro_cargo.length > 0) {

            $('#porcentaje_div_otro_cargo').css( "display", "block");

        } else {

            $('#porcentaje_div_otro_cargo').css( "display", "none");
        }

        $('#codigo_pos').focus();
        if ($('#medio_pago').val() === '01') {
            $('#referencia_p').css( "display", "none");
        }else{
            $('#referencia_p').css( "display", "block");
        }

        if ($('#moneda').val() === 'CRC') {
            $('#tipo_cambio').css( "display", "none");
            $('#input-tipo_cambio').val(0.00);
        }else{
            $('#tipo_cambio').css( "display", "block");
            $("#input-tipo_cambio").prop('readonly', true);
        }

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
        
        $( "#cliente_serchcla" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocompletecla/cliente')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.clave;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });

        $('#idconfigfact').change(function() {
            traerNumFactura(APP_URL,APP_URL2);
        });

       // $('#tipo_documento').change(function() {
         //   traerNumFactura(APP_URL,APP_URL2);
       // });

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
            if (condicion === '02') {
                $('#input-p_credito').prop("required", true);
            }else{
                $('#input-p_credito').prop("required", false);
            }
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('modificar-condicion')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{condicion:condicion, idsale:idsale},
                success:function(response){
                     //console.log(response);
                    location.reload();
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
                    //console.log(response);

                },
                error:function(response){
                    //console.log(response);
                }
            });
        });
       $('#agregar_producto').click(function(e) {
            e.preventDefault();
            if( $('.select-checkbox').is(':checked') ) {

              var sales_item = $('#sales_item').val();
              var idsale = $('#idsale').val();
              var cantidad =  null;
            var monto_total = null;
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

            } else {
                alert('Debe seleccionar al menos 1 producto.');
            }
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
//descripcion update_nombre_producto
        $(document).on("blur", ".update_nombre_producto_pos" , function(event) {
            var id = $(this).data('id');
            var nombre_producto_pos = $(this).val();
         
            var URL = {!! json_encode(url('actualizar-descripcion-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, nombre_producto_pos:nombre_producto_pos},
                success:function(response){
                    location.reload();
                }
            });
        });

//
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
 $(document).on("blur", ".update_costo_con_iva_u" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_con_iva_u = $(this).val();
            var URL = {!! json_encode(url('actualizar-costo-con-iva_u')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, idproducto: idproducto, costo_con_iva_u:costo_con_iva_u},
                success:function(response){
                    //console.log(response);
                    location.reload();
                }
            });
        });

 $(document).on("blur", ".update_costo_sin_iva_u" , function(event) {
            var id = $(this).data('id');
            var idproducto = $(this).data('producto');
            var costo_sin_iva_u = $(this).val();
            var URL = {!! json_encode(url('actualizar-costo-sin-iva_u')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, idproducto: idproducto, costo_sin_iva_u:costo_sin_iva_u},
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

                           
                           
                                   $('#input-fecha_exoneracionmh').val(response.fechaEmision);                
                            //$('#input-fecha_exoneracion').val(dt.toLocaleDateString());
                            $('#input-fecha_exoneracion').val(response.fechaEmision);
                           
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
                    //onsole.log(response);
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


        
        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            
            var idproducto = $('#idproducto_pos').val();
            if($('#idproducto_pos').val()===""){
                 alert('Primero Debes Seleccionar un Producto.');
            }else{
            $('#sales_item').val(idproducto);
            var sales_item = $('#sales_item').val();
            var idsale = $('#idsale').val();
            var cantidad = $('#cantidad_pos_envia').val();
            var monto_total = $('#monto_linea').val();
            var es_sin_impuesto = $('#es_sin_impuesto').is(':checked');
            var valor = es_sin_impuesto ? 1 : 0;
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{sales_item:sales_item, idsale:idsale, cantidad:cantidad, monto_total:monto_total, es_sin_impuesto:valor},
                beforeSend:function(){
                  $("#loadMe").modal({
                    backdrop: "static", //remove ability to close modal with click
                    keyboard: false, //remove option to close with keyboard
                    show: true //Display loader!
                  });
                },
                success:function(response){
                    demo.showNotification('top','right', 'Agregado Satisfactoriamente.', 2);
                    location.reload();
                },
                complete:function(){
                  //$('#loadMe').modal('hide');
                }
            });
        }
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
                    var tipo_documento = $('#tipo_documento').val();
                    if (arreglo > 0) {
                        if (response['success'][0]['num_id'] === 100000000 && tipo_documento === '01') {
                                alert('seleccionar otro tipo de documento');
                                $('#tipo_documento').focus();
                        }else{
                            if (response['success'][0]['num_id'] != 100000000 && tipo_documento === '04') {
                                var value = '01';
                                $.ajax({
                                    type:'get',
                                    url: '/editar-tipodoc-pos',
                                    dataType: 'json',
                                    data:{tipo_documento:value, idsale:idsale},
                                    success:function(datos){
                                    }
                                });
                            }
                            $('#cliente_serch').val(response['success'][0]['nombre']);
                            $('#cliente').val(response['success'][0]['idcliente']);
                            
                            location.reload();
                        }
                    }else{
                        $('#cliente_serch').val('No se encontraron resultados');
                        $('#cliente').val(0);
                    }
                }
            });
        });
        
       
        $(document).on("blur", "#cliente_serchcla" , function(event) {
            event.preventDefault();
            var ref_clave = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('claveref')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{ref_clave:ref_clave, idsale:idsale},
                success:function(response){

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
      //  $(document).on("blur", "#tipo_documento" , function(event) {
      //      event.preventDefault();
      //      var tipo_documento = $(this).val();
      //      var idsale = $('#idsale').val();
      //      var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};
      //      $.ajax({
       //         type:'get',
       //         url: URL,
       //         dataType: 'json',
       //         data:{tipo_documento:tipo_documento, idsale:idsale},
       //         success:function(response){
                    //location.reload();
      //          }
      //      });
     //   });
     
      $('#tipo_documento').change(function(event) { //15-11-2023 se inserta el traer numero de factura a este evento y se comenta el evento de la linea 1490, se envia a la url el id caja, para recalcular el consecutivo de
         //factura que seigue, para que se actualice tanto, la el tipo de documento como el consecutivo
           traerNumFactura(APP_URL,APP_URL2);
           event.preventDefault();
           var tipo_documento = $(this).val();
           var idsale = $('#idsale').val();
            var idcaja = $('#idcaja').val();
            // var numero_documento = $('#numero_documento').val();
           var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{tipo_documento:tipo_documento, idsale:idsale,idcaja:idcaja},
               success:function(response){
               location.reload();
                }
            });
        });
       
        $(document).on("blur", "#idconfigfact" , function(event) {
            event.preventDefault();
            var idconfigfact = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-config-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idconfigfact:idconfigfact, idsale:idsale},
                success:function(response){

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

// omairena 28-05-2021

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
                            $('#monto_linea').val(response['success'][0]['precio_final']);
                            
                            $('#cantidad_pos').prop('readonly', false);
                            $('#descuento_pos').prop('readonly', false);
                        }else{
                            $('#disponible_pos').prop('readonly', true);
                            $('#monto_linea').prop('readonly', true);
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
                                $('#cliente_serch').focus();
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

        $(document).on("blur", "#direccion" , function(event) {
            event.preventDefault();
            var direccion = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-direccion-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{direccion:direccion, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", "#observaciones" , function(event) {
            event.preventDefault();
            var observacion = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-observacion-pos')) !!};
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

        $('#agregar_lista').click(function(e) {
            e.preventDefault();
            var idlista = $('#seleccion_lista').val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-listaprecio-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idlista:idlista, idsale:idsale},
                success:function(response){
                    location.reload();
                }
            });
        });
    });
    function submitResult() {
        
        var doc_ref = $('#cliente_serchcla').val();
        var doc_ref_obs = $('#observaciones').val();
        
        if(doc_ref !=="" ){
            if(doc_ref_obs ===""){
            alert('Si se indica un documento de referencia, es obligatorio indicar una observacion.');
              return false ;
        }
        }
        
        var nombre_cli = $('#cliente_serch').val();
        if ( confirm("¿Desea procesar la factura para el cliente "+nombre_cli + " ?") == false ) {
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
                //console.log(response);
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
                         $('#monto_linea').val(response['success'][0]['precio_final']);
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
                         $('#monto_linea').val(response['success'][0]['precio_final']);
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                    }
                }else{
                    alert('No se encontro el Codigo de Producto, por favor agregelo.');
                    $('#disponible_pos').prop('readonly', true);
                    $('#monto_linea').prop('readonly', true);
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
