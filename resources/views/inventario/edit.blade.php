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
            <div class="col-xl-12 order-xl-1">
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
                        <form method="post" action="{{ route('inventario.update', $inventario->idinventario) }}" autocomplete="off" enctype="multipart/form-data" id="form_inventario_edit">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Movimiento') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_movimiento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_movimiento">{{ __('Tipo de Movimiento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_movimiento" name="tipo_movimiento" value="{{ old('tipo_movimiento') }}" required>
                                        <option value="1" {{ ($inventario->tipo_movimiento == 1 ? 'selected="selected"' : '') }}>Entrada</option>
                                        <option value="2" {{ ($inventario->tipo_movimiento == 2 ? 'selected="selected"' : '') }}>Salida</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_movimiento'])
                                </div>
                                <div class="form-group{{ $errors->has('idcliente') ? ' has-danger' : '' }}" id="combo_idcliente">
                                    <label class="form-control-label" for="input-idcliente">{{ __('Proveedores') }}</label>
                                    <select class="form-control form-control-alternative" id="idcliente" name="idcliente" value="{{ old('idcliente') }}" required>
                                        <option value="0">-- Seleccione un Proveedor --</option>
                                    @foreach($proveedores as $prov) 
                                        <option value="{{ $prov->idcliente }}" {{ ($inventario->idcliente == $prov->idcliente ? 'selected="selected"' : '') }}>{{ $prov->nombre }}</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idcliente'])
                                </div>
                                <div class="form-group{{ $errors->has('num_documento') ? ' has-danger' : '' }}" id="combo_numdoc">
                                    <label class="form-control-label" for="input-num_documento">{{ __('Número de Documento') }}</label>
                                    <input type="text" name="num_documento" id="input-num_documento" class="form-control form-control-alternative{{ $errors->has('num_documento') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Documento') }}" value="{{ old('num_documento', $inventario->num_documento) }}">
                                    @include('alerts.feedback', ['field' => 'num_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('condicion_movimiento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="condicion_movimiento">{{ __('Condición de Movimiento') }}</label>
                                    <select class="form-control form-control-alternative" id="condicion_movimiento" name="condicion_movimiento" value="{{ old('condicion_movimiento' , $inventario->condicion_movimiento) }}" required>
                                        <option value="1" {{ ($inventario->condicion_movimiento == 1 ? 'selected="selected"' : '') }}>Contado</option>
                                        <option value="2" {{ ($inventario->condicion_movimiento == 2 ? 'selected="selected"' : '') }}>Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condicion_movimiento'])
                                </div>
                                <div class="form-group{{ $errors->has('plazo_credito') ? ' has-danger' : '' }}" id="plaz_credito" style="display: none;">
                                    <label class="form-control-label" for="input-plazo_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="plazo_credito" id="input-plazo_credito" class="form-control form-control-alternative{{ $errors->has('plazo_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('plazo_credito', $inventario->plazo_credito) }}">
                                    @include('alerts.feedback', ['field' => 'plazo_credito'])
                                </div>
                                <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-observaciones">{{ __('Observaciones') }}</label>
                                    <textarea class="form-control form-control-alternative{{ $errors->has('observaciones') ? ' is-invalid' : '' }}" id="observaciones" name="observaciones">{{ $inventario->observaciones }}</textarea>
                                    @include('alerts.feedback', ['field' => 'observaciones'])
                                </div>
                                <div class="form-group text-right">
                                    <a href="#" class="btn btn-sm btn-success" data-target="#AddProductosInventario" data-toggle="modal" id="Agregar_producto">Agregar Productos</a>
                                </div>
                                <div class="table-responsive">
                                   <table class="table align-items-center" id="tabla_productos">
    									                <thead class="thead-light">
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Descripción</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Costo del Producto</th>
                                                <th scope="col">Costo Unitario Sin IVA</th>
                                                <th scope="col">Tasa IVA</th>
                                                <th scope="col">Útilidad</th>
                                                <th scope="col">Precio Final unitario venta</th>
                                                <th scope="col">Total Compra</th>
                                                <th scope="col">Acciones</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody class="tabla_productos">
                                            
                                             <?php
                                             $total_compra=0;
                                              $costo_compra=0;
                                             ?>
                                             
                                          
                                                                    
                                                                    
                                            @foreach($inventario_item as $inv_item)
                                                <?php 
                                                    //$prod = App\Productos::find($inv_item->idproducto)->get();
                                                    $prod = App\Productos::where('idproducto', $inv_item->idproducto)->get();
                                                   // dd($prod[0]->nombre_producto);
                                                    $total = $prod[0]->precio_final * $inv_item->cantidad_inventario;
                                                    $total_compra=$total_compra+($prod[0]->precio_final * $inv_item->cantidad_inventario);
                                                    $costo_compra=$costo_compra+($prod[0]->costo * $inv_item->cantidad_inventario);
                                                ?>
                                                
                                                <tr>
                                                    <td>{{ $inv_item->idinventario_item }}</td>
                                                    <td>{{ $prod[0]->codigo_producto }}</td>
                                                    <td>{{ $prod[0]->nombre_producto }}</td>
                                                    <td class="text-right"><input type="number" name="cantidad_inventario" id="cantidad_inventario{{ $inv_item->idinventario_item }}" value="{{ $inv_item->cantidad_inventario }}" class="form-control form-control-alternative{{ $errors->has('cantidad_inventario') ? ' is-invalid' : '' }} update_fila_inventario" style="width:80px;" data-id="{{ $inv_item->idinventario_item }}"></td>
                                                    <td class="text-right"><input type="number" name="costo_producto" id="costo_producto{{ $inv_item->idinventario_item }}" value="{{ $prod[0]->costo }}" class="form-control form-control-alternative{{ $errors->has('costo_producto') ? ' is-invalid' : '' }} update_costo_inventario" style="width:80px;" data-id="{{ $prod[0]->idproducto }}" data-inventario="{{ $inv_item->idinventario_item }}"></td>
                                                    <td class="text-right">{{number_format( $prod[0]->precio_sin_imp,2,',','.')  }}</td>
                                                    <td class="text-right">{{ $prod[0]->porcentaje_imp }} %</td>
                                                    <td class="text-right">{{ $prod[0]->utilidad_producto }} %</td>
                                                    <td class="text-right">{{ number_format($prod[0]->precio_final,2,',','.')  }}</td>
                                                    <td id="total_final{{ $inv_item->idinventario_item }}" class="text-right">{{ number_format($total,2,',','.')  }}</td>
                                                    <td>
                                                                            <div class="dropdown">
                                                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                                </a>
                                                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                                   
                                                                                   
                                                                                    <a href="#" id="eliminar_fila{{ $inv_item->idinventario_item }}" data-id="{{$inv_item->idinventario_item }}" class="dropdown-item eliminar_fila_inv">{{ __('Eliminar Fila') }}</a>
                                                                                  
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                </div>
                                <div class="col-8">
                                <h3 class="mb-0">{{ __('Total Compra (Fact-Proveedor)->') }}
                                <input type="text" name="sales_item" id="sales_item" value="{{ number_format($costo_compra,2,',','.') }}" ></h3><br>
                                 <h3 class="mb-0">{{ __('Total Ingreso Inv->') }}
                                <input type="text" name="sales_item" id="sales_item" value="{{ number_format($total_compra,2,',','.') }}" ></h3><br>
                            </div>
                                 
                                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                <input type="text" name="idinventario" id="idinventario" value="{{ old('idinventario', $inventario->idinventario) }}" hidden="true" value="{{ $inventario->idinventario }}">
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

 $('#tabla_productos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "autoWidth": true,
            "processing": true,
            "serverSide": false,
            "deferRender": true,
            order: [[ 0, "desc" ]]
        });
        if ($('#condicion_movimiento').val() === '1') {
            $('#plaz_credito').css( "display", "none");
        }else{
            $('#plaz_credito').css( "display", "block");  
        }
        if ($('#tipo_movimiento').val() > 1) {
                $('#combo_numdoc').css( "display","");
                $('#combo_idcliente').css( "display","none");

            }else{
                $('#combo_numdoc').css( "display","");
                $('#combo_idcliente').css( "display", "");
            }
       // $('#tipo_movimiento').change(function() {
         //   if ($(this).val() > 1) {
          //      $('#combo_numdoc').css( "display","none");
           //     $('#combo_idcliente').css( "display","none");

           // }else{
             //   $('#combo_numdoc').css( "display","");
              //  $('#combo_idcliente').css( "display", "");
           // }
        //});
$(document).on("click", ".eliminar_fila_inv" , function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var URL = {!! json_encode(url('eliminar-fila-inv')) !!};
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
        
        $(document).on("click", "#eliminar_fila" , function(event) {
            event.preventDefault();
            var valor = $('#exoneracion').val();
        });


 $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
           $('#sales_item').val(data);
        });
        
        
     //   $('[name="seleccion[]"]').click(function() {
      
       //    var arr = $('[name="seleccion[]"]:checked').map(function(){
              //  return this.value;
          //  }).get();
         //   var str = arr.join(',');
  //  $('#sales_item').val(arr);
      //  });
        $('#agregar_inventario').click(function(e) {
            e.preventDefault();
            var datos = $('#sales_item').val();
            var idinventario = $('#idinventario').val();
            var URL = {!! json_encode(url('agregar-linea')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{datos:datos, idinventario:idinventario},
                success:function(response){
                    location.reload();
                }
            });
        });

        $(document).on("blur", ".update_fila_inventario" , function(event) {
            var id = $(this).data('id');
            var cantidad_inv = $(this).val();
            var URL = {!! json_encode(url('buscar-inventario')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idinventario_item:id, cantidad_inv:cantidad_inv},
                success:function(response){
                    $('#total_final' + id).empty();
                    $('#total_final' + id).text(response.success);
                    location.reload();
                }
            });
             header("Location: URL");
        });
        $(document).on("blur", ".update_costo_inventario" , function(event) {
            var id = $(this).data('id');
            var costo = $(this).val();
            var idinventario = $(this).data('inventario');
            var cantidad_inv = $('#cantidad_inventario' +idinventario).val();
            var URL = {!! json_encode(url('actualizar-producto')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idinventario_item:idinventario, cantidad_inv:cantidad_inv, costo:costo, idproducto:id},
                success:function(response){
                    location.reload();
                }
            });
            
            header("Location: URL");
        });
    });
</script>
@endsection
