@extends('layouts.pos', ['page' => "", 'pageSlug' => 'crearFactura'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <style type="text/css">

/* hide scrollbar but allow scrolling */
body {
  -ms-overflow-style: none; /* for Internet Explorer, Edge */
  scrollbar-width: none; /* for Firefox */
  overflow-y: scroll;
}

body::-webkit-scrollbar {
  display: none; /* for Chrome, Safari, and Opera */
}


        th, td { white-space: nowrap; }
        div.dataTables_wrapper {
            width: 1080px;
            margin: 0 auto;
        }
        div.inline { float:left; }
        .clearBoth { clear:both; }
        /** SPINNER CREATION **/
        .modal-content {
            border-radius: 0px;
            box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
        }

        .modal-backdrop.show {
            opacity: 0.75;
        }
        .loader {
  color: #28a0db;
  font-size: 90px;
  text-indent: -9999em;
  overflow: hidden;
  width: 1em;
  height: 1em;
  border-radius: 50%;
  margin: 72px auto;
  position: relative;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load6 1.7s infinite ease, round 1.7s infinite ease;
  animation: load6 1.7s infinite ease, round 1.7s infinite ease;
}
@-webkit-keyframes load6 {
  0% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  5%,
  95% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  10%,
  59% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
  }
  20% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
  }
  38% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
  }
  100% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
}
@keyframes load6 {
  0% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  5%,
  95% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  10%,
  59% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
  }
  20% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
  }
  38% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
  }
  100% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
}
@-webkit-keyframes round {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes round {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
#tabla_productos {
    table-layout: auto; /* Permite que la tabla ajuste su ancho automáticamente */
    width: 100%; /* Asegúrate de que la tabla ocupe el 100% del contenedor */
}

#tabla_productos th, #tabla_productos td {
    overflow: hidden; /* Oculta el desbordamiento */
    text-overflow: ellipsis; /* Agrega puntos suspensivos si el texto es demasiado largo */
    white-space: normal; /* Permite que el texto se divida en varias líneas */

}
.table {
    font-size: 1.2rem; /* Ajusta el tamaño de la fuente según sea necesario */
}
.table input {
    font-size: 1.2rem; /* Ajusta el tamaño de la fuente según sea necesario */
}
#tabla_productos th:nth-child(2), #tabla_productos td:nth-child(2) {
    min-width: 200px; /* Aumenta el ancho mínimo de la columna "Nombre Producto" */
}
#firstBlock, #detailsSection {
    margin: 0; /* Elimina márgenes */
    padding: 0; /* Elimina padding */
}
#firstBlock, #detailsSection {
    margin: 0; /* Elimina márgenes */
    padding: 0; /* Elimina padding */
}
 table {
        width: 100%;
        border-collapse: collapse; /* Para que los bordes se fusionen */
    }
    th, td {
        border: 1px solid #ddd; /* Bordes de las celdas */
        padding: 8px; /* Espaciado interno */
        text-align: right; /* Alinear texto a la derecha */
    }
    td:first-child {
        text-align: left; /* Alinear el primer campo a la izquierda */
    }
    tr:nth-child(even) {
        background-color: #f2f2f2; /* Color de fondo alternativo para filas */
    }
    /* Estilo para eliminar el borde entre la fila del nombre y la siguiente fila */
    .no-border {
        border: none; /* Sin borde */
    }
div.dataTables_wrapper {
    width: 100%; /* Cambia a 100% para que se ajuste al contenedor */
    margin: 0 auto;
}
.container {
        display: flex;
        transition: transform 0.3s ease; /* Suaviza la transición */
    }

    .move-right {
        transform: translateX(20px); /* Mueve el bloque a la derecha */
    }

    .move-left {
        transform: translateX(-20px); /* Mueve el bloque a la izquierda */
    }
    /* Asegúrate de que el contenedor de la tabla tenga un ancho máximo */
    #productTableContainer {
        max-width: 100%; /* Asegura que no se desborde */
        overflow-x: auto; /* Permite desplazamiento horizontal si es necesario */
    }
    </style>

     <script src="{{ asset('black') }}/js/nucleo_app.js"></script>

</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif

<div class="container-fluid mt--7" >

    <div class="row">
        <div class="col-md-12">

            <form method="post" action="{{ route('pos.update', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_factura" onsubmit="return submitResult();">
                @csrf
                @method('PUT')

    <div class="card">
    <div class="card-body">
        <div class="container-fluid mt--8">
            <div class="row">
                <!-- Tabla (col-10 inicialmente) -->
                <div class="col-8" id="tableBlock">
                    <div class="row">
                        <div class="col-md-6">
                      <label>{{ Auth::user()->config_u[0]->nombre_emisor }}</label>
                       <h3 >Numero Documento: {{$sales->numero_documento}} </h3>
                            <h3 class="mb-0" id="encabezado_factura"></h3>

                            <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-warning">{{ __('Salir') }}</a>
                        </div>
                       
                    </div>

                  @if(Auth::user()->config_u[0]->usa_lector > 0)
                  <div class="row">
                        <div class="input-group">

                           <div class="col-12">


        <input type="text" class="form-control" name="codigo_pos" id="codigo_pos" placeholder="Ingrese Codigo o Nombre del producto">

</div>
                            <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" hidden="true">
                            <button class="btn btn-sm btn-success" type="button" id="agregar_producto_pos" style="display: none;">+</button>
                            <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                        </div>
                    </div>
                    @else
                     <div class="col-md-12">
                       

                         </div>
                    @endif
                    <div class="row">
                        <div class="card card-nav-tabs card-plain">
                            <div class="card-header card-header-danger">
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
                            <div class="tab-content text-center">
                                <div class="tab-pane active" id="detalle">
                                   <div class="col-12" id="tableBlock1">
                                   <style>
.hidden {
    display: none;
}
</style>


<table class="table align-items-left" id="tabla_productos">
    <thead class="thead-light">
        <tr>
            <th colspan="3" scope="col" style="word-wrap: break-word;">Nombre Producto</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col">Cantidad</th>
            @if(Auth::user()->config_u[0]->p_siva == 1)
                <th scope="col">Precio Unit S/IVA</th>
                <th scope="col">Descuento %</th>
            @endif
            <th scope="col">Prec. Tot Linea<a class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="Si actualizas el precio desde esta opción, se tomará como el precio total de la línea, sin hacer cálculos de descuento y cantidad, es decir, el valor indicado en este campo, será el total, de la cantidad indicada para la línea a actualizar.">¿?</a></th>
        </tr>
    </thead>
    <tbody class="tabla_productos">
        <?php
            $total_neto = 0;
            $total_descuento = 0;
            $total_comprobante = 0;
            $total_impuesto = 0;
            $total_iva_devuelto = 0;
            $monto_imp_exonerado_t = 0;
            
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
                $total = $sale_i->valor_neto + ($sale_i->valor_impuesto - $iva_dev_linea);
                $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
                $monto_imp_exonerado_t = $monto_imp_exonerado_t +  $sale_i->exo_monto;
            } else {
                $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
                $tiene_exoneracion = 'Si ' . $exoneracion[0]->porcentaje_exoneracion . ' %';
                $monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
                $monto_imp_exonerado_t = $monto_imp_exonerado_t +  $sale_i->exo_monto;
                if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                    $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
                    $iva_dev_linea =  $iva_dev_linea +  $sale_i->valor_impuesto;
                }
                $total = ($sale_i->valor_neto + $monto_imp_exonerado) - $iva_dev_linea;
                $total_unitario = $total / $sale_i->cantidad;
                $total_impuesto = $total_impuesto + $monto_imp_exonerado;
            }
            if (($sale_i->valor_impuesto == 0 or $sale_i->cantidad == 0) ) {
                $costo_con_iva_u = 0;
            } else {
                $costo_con_iva_u = $sale_i->costo_utilidad + ($sale_i->valor_impuesto / $sale_i->cantidad);
            }
            $total_neto = $total_neto + $sale_i->valor_neto + $sale_i->valor_descuento;
            $total_descuento = $total_descuento + $sale_i->valor_descuento;
            $total_comprobante = $total_comprobante + $total;
            ?>
            <tr>
                <td colspan="5">
                    <input type="text" name="nombre_producto_pos" id="nombre_producto_pos{{ $sale_i->idsalesitem }}" value="{{ $sale_i->nombre_producto }}" class="form-control form-control-alternative{{ $errors->has('nombre_producto_pos') ? ' is-invalid' : '' }} update_nombre_producto_pos" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
               
                   
                </td>
                <td class="text-right">
                    <input type="number" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                </td>
                @if(Auth::user()->config_u[0]->p_siva == 1)
                <td class="text-right">
                    <input type="number" step="any" name="costo_sin_iva_u" id="costo_sin_iva_u{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->costo_utilidad ,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_sin_iva_u') ? ' is-invalid' : '' }} update_costo_sin_iva_u" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                </td>
                <td class="text-right">
                    <input type="number" name="descuento" id="descuento{{ $sale_i->idsalesitem }}" value="{{ $sale_i->descuento_prc }}" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }} update_descuento_factura" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                </td>
                @endif
                <td class="text-right">
                    <input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->valor_neto + $sale_i->valor_impuesto - $sale_i->exo_monto,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
                </td>
            </tr>
            <tr id="details-row-{{ $sale_i->idsalesitem }}" class="hidden">
                <td colspan="4">Cod: {{ $sale_i->codigo_producto }}</td>
                <td class="text-right">IVA = {{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                <td class="text-right">Exonerado?: <?php echo $tiene_exoneracion; ?>
                    @if($sale_i->existe_exoneracion === '00')
                        @if($sale_i->prod_sale[0]->porcentaje_imp > 0)
                        <button type="button" rel="tooltip" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-success btn-sm btn-icon agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">
                            <i class="fas fa-file-alt"></i>
                        </button>
                        @else
                        <button type="button" rel="tooltip" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-success btn-sm btn-icon agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">
                            <i class="fas fa-file-alt"></i>
                        </button>
                        @endif
                    @endif
                </td>
                <td class="td-actions text-right">
                    <button type="button" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
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
                                            <button type="button" rel="tooltip" class="btn btn-sm btn-success text-right" data-target="#AddOtroCargo" data-toggle="modal">Agregar</button>
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


                        

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
            <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" required>
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
            </select>
            @include('alerts.feedback', ['field' => 'tipo_documento'])
        </div>

        <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
            <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $usuario->nombre }}">
            @include('alerts.feedback', ['field' => 'cliente'])
        </div>

        <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
            <select class="form-control form-control-alternative" id="moneda" name="moneda" required>
                <option value="CRC" {{ ($sales->tipo_moneda == 'CRC' ? 'selected="selected"' : '') }}>Colon Costaricense</option>
                <option value="USD" {{ ($sales->tipo_moneda == 'USD' ? 'selected="selected"' : '') }}>Dólar Americano</option>
                <option value="EUR" {{ ($sales->tipo_moneda == 'EUR' ? 'selected="selected"' : '') }}>Euro</option>
            </select>
            @include('alerts.feedback', ['field' => 'moneda'])
        </div>

        <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
            <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
            <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio' , $sales->tipo_cambio) }}">
            @include('alerts.feedback', ['field' => 'tipo_cambio'])
        </div>
 
        <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
            <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" required>
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

       

        <div class="form-group{{ $errors->has('exoneracion') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="exocli">{{ __('Número Exoneración') }}</label>
            <input type="text" name="exocli" id="exocli" class="form-control form-control-alternative{{ $errors->has('exocli') ? ' is-invalid' : '' }}" placeholder="{{ __('Número exocli') }}" value="{{ $usuario->exocli }}">
            @include('alerts.feedback', ['field' => 'exocli'])
        </div>

        <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
            <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                @foreach($cajas as $caja)
                    <option value="{{ $caja->idcaja }}" {{ ($sales->idcaja == $caja->idcaja ? 'selected="selected"' : '') }}>{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                @endforeach
            </select>
        </div>
         <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
            <textarea id="observaciones" name="observaciones" class="form-control">{{ $sales->observaciones }}</textarea>
        </div>
    </div>







                           
                        </div>
                    </div>


                            <div class="col-4">
                                 <br><b>Totales</b>   <br>
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
     @if($monto_imp_exonerado_t > 0)
      <tr>

  <th scope="col">IVA Exonerado:</th>

      <td><b id="iva_devuelto">{{ number_format($monto_imp_exonerado_t,2,',','.') }}</b></td>
    </tr>
     @endif
     @if($total_iva_devuelto > 0)
      <tr>

  <th scope="col">IVA Devuelto:</th>

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
            <td>
                <b id="t_abono_op">{{ number_format($sales->total_abonos_op,2,',','.') }}</b>
            </td>
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
                            <div class="col-4">

                               

                                    <!--<button type="submit" class="btn btn-success mt-4">{{ __('Facturar') }}</button>-->
                                  
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
                <input type="text" name="tot_otros_cargos" id="tot_otros_cargos" value="{{ old('tot_otros_cargos', $total_otros_cargos) }}" hidden="true">
                <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">
                <input type="text" name="cliente" id="cliente" value="{{ old('cliente', $sales->idcliente) }}" readonly>
                <input type="text" name="usa_lector" id="usa_lector" value="{{ old('usa_lector', $configuracion[0]->usa_lector ) }}" hidden="true">
                <input type="text" name="usa_balanza" id="usa_balanza" value="{{ old('usa_balanza', Auth::user()->config_u[0]->usa_balanza) }}" hidden="true">
                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                @if($sales->condicion_venta == '02')
                    <input type="text" name="idmovcxcobrar" id="idmovcxcobrar" value="{{ old('idmovcxcobrar', $sales->idmovcxcobrar) }}" hidden="true">
                @endif
                <input type="hidden" name="medios_pago">

            </form>
        </div>
    </div>
   <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center; text-align: center;">
    <div style="color: white;">
        <h1>Enviando documento, por favor espere...</h1>
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
</div>
</div>


<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">


</script>
@endsection
