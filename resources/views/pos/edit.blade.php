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

/* Evita que el modal de busqueda de productos se desborde en pantallas angostas */
#AddProductos .modal-dialog {
    max-width: min(92vw, 1180px);
    margin: 0.75rem auto;
}

#AddProductos .modal-content {
    max-height: calc(100vh - 1.5rem);
}

#AddProductos .modal-body {
    max-height: 62vh;
    overflow-x: auto;
    overflow-y: auto;
    padding: 0.75rem;
}

#AddProductos .dataTables_wrapper,
#AddProductos table.dataTable {
    width: 100% !important;
}

#AddProductos table.dataTable th,
#AddProductos table.dataTable td {
    white-space: nowrap;
}

#AddProductos table.dataTable thead th {
    background: #eef1f6;
    position: sticky;
    top: 0;
    z-index: 2;
}

#AddProductos table.dataTable th:nth-child(3),
#AddProductos table.dataTable td:nth-child(3) {
    white-space: normal;
    word-break: break-word;
    min-width: 300px;
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
                <div class="col-10" id="tableBlock">
                    <div class="row">
                        <div class="col-md-6">
                      <label>{{ Auth::user()->config_u[0]->nombre_emisor }}</label>
                            <h3 class="mb-0" id="encabezado_factura"></h3>

                            <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-warning">{{ __('Salir') }}</a>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group text-right">
                                <a href="#" class="btn btn-sm btn-info" data-target="#AddProductos" data-toggle="modal" id="Agregar_producto">Buscar Producto</a>
                            </div>
                        </div>
                    </div>

                  @if(Auth::user()->config_u[0]->usa_lector > 0)
                  <div class="row">
                        <div class="input-group">

                           <div class="col-12">


        <input type="text" class="form-control" name="codigo_pos" id="codigo_pos" placeholder="Ingrese Codigo o Nombre del producto">

</div>
                            <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" hidden="true">
                            <button class="btn btn-sm btn-success agregar_producto_pos_btn" type="button" style="display: none;">+</button>
                            <input type="number" name="idproducto_pos" id="idproducto_pos" value="{{ old('idproducto_pos') }}" hidden="true">
                        </div>
                    </div>
                    @else
                     <div class="col-md-12">
                                <table class="table align-items-center">
                             <thead class="thead-light">
                                <th scope="col" style="text-right: center;">{{ __('Codigo') }}</th>
                                <th scope="col" style="text-right: center;">{{ __('Nombre Producto') }}</th>
                                <th scope="col" style="text-right: center;">{{ __('Cantidad') }}</th>
                                <th scope="col" style=" text-right: center;">{{ __('P. Unitario ') }}</th>
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
                                    <td style="width:600px;">
                                       <input type="text" name="nombre_pos" id="nombre_pos" class="form-control form-control-alternative{{ $errors->has('nombre_pos') ? ' is-invalid' : '' }}">
                                    </td>
                                    <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" name="cantidad_pos_envia" id="cantidad_pos_envia" class="form-control form-control-alternative{{ $errors->has('cantidad_pos_envia') ? ' is-invalid' : '' }}" style="width:100px;">
                                    </td>
                                     <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" style="width:200px;" name="monto_linea" id="monto_linea" class="form-control form-control-alternative{{ $errors->has('monto_linea') ? ' is-invalid' : '' }}" style="width:100px;">
                                    </td>
                                     <td class="text-left" style="width:100px;">

                                    <input type="checkbox" name="es_sin_impuesto" id="es_sin_impuesto" {{$configuracion[0]->sin_impuesto_pos == false ? 'checked' : ''}}>
                                    </td>
                                    <td class="text-right" style="width:100px;">
                                        <input type="number" step="any" name="disponible_pos" id="disponible_pos" class="form-control form-control-alternative{{ $errors->has('disponible_pos') ? ' is-invalid' : '' }}" style="width:80px;" readonly="true">
                                    </td>
                                    <td>
                                        <button class="btn btn-success agregar_producto_pos_btn" type="submit" style="display: none;">Agregar Producto</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

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
                                   <div class="col-10" id="tableBlock1">
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
            $exo_monto_linea = (float) ($sale_i->exo_monto ?? 0);
            $impuesto_linea = (float) $sale_i->valor_impuesto;
            $impuesto_aplicado_linea = max(0, $impuesto_linea - $exo_monto_linea);
            $cantidad_linea = (float) $sale_i->cantidad;
            $precio_sin_iva_linea = (float) $sale_i->costo_utilidad;
            $descuento_prc_linea = (float) $sale_i->descuento_prc;
            if ($descuento_prc_linea < 0) {
                $descuento_prc_linea = 0;
            }
            if ($descuento_prc_linea > 100) {
                $descuento_prc_linea = 100;
            }
            $base_linea = $precio_sin_iva_linea * $cantidad_linea;
            $descuento_linea = ($descuento_prc_linea > 0) ? (($base_linea * $descuento_prc_linea) / 100) : 0;
            $neto_linea = $base_linea - $descuento_linea;

            if ($sale_i->existe_exoneracion == '00' || $exo_monto_linea <= 0) {
                $tiene_exoneracion = 'No';
            } else {
                $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->first();
                if ($exoneracion) {
                    $tiene_exoneracion = 'Si ' . $exoneracion->porcentaje_exoneracion . ' %';
                } else {
                    $tiene_exoneracion = 'Si';
                }
            }

            if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                $total_iva_devuelto =  $total_iva_devuelto +  $impuesto_linea;
                $iva_dev_linea =  $iva_dev_linea +  $impuesto_linea;
            }

            $total = ($neto_linea + $impuesto_aplicado_linea) - $iva_dev_linea;
            // Total Impuesto se mantiene bruto; la exoneracion se resta aparte en Total Documento.
            $total_impuesto = $total_impuesto + $impuesto_linea;
            $monto_imp_exonerado_t = $monto_imp_exonerado_t + $exo_monto_linea;
            if (($sale_i->valor_impuesto == 0 or $sale_i->cantidad == 0) ) {
                $costo_con_iva_u = 0;
            } else {
                $costo_con_iva_u = $sale_i->costo_utilidad + ($sale_i->valor_impuesto / $sale_i->cantidad);
            }
            // Subtotal = base sin IVA (cantidad * precio S/IVA), independiente de exoneracion.
            $total_neto = $total_neto + $base_linea;
            $total_descuento = $total_descuento + $descuento_linea;
            $total_comprobante = $total_comprobante + $total;
            ?>
            <tr>
                <td colspan="5">
                    <input type="text" name="nombre_producto_pos" id="nombre_producto_pos{{ $sale_i->idsalesitem }}" value="{{ $sale_i->nombre_producto }}" class="form-control form-control-alternative{{ $errors->has('nombre_producto_pos') ? ' is-invalid' : '' }} update_nombre_producto_pos" data-id="{{ $sale_i->idsalesitem }}" readonly data-producto="{{ $sale_i->idproducto }}">
                    
                    <button type="button" class="btn btn-warning btn-sm editar-descripcion" 
                            data-id="{{ $sale_i->idsalesitem }}" 
                            data-nombre="{{ $sale_i->nombre_producto }}" 
                            data-toggle="modal" 
                            data-target="#modalEditarDescripcion">
                        <i class="fas fa-pen"></i> Edit Desc.
                    </button>
                    <button type="button" id="modificar_articulo_flotante{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button type="button" id="eliminar_fila{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                        <i class="far fa-trash-alt"></i>
                    </button>
                    <button type="button" rel="tooltip" id="agregar_exoneracion{{ $sale_i->idsalesitem }}" data-id="{{ $sale_i->idsalesitem }}" class="btn btn-success btn-sm btn-icon agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">
                        <i class="fas fa-file-alt"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm btn-detalles-linea" onclick="toggleRow('{{ $sale_i->idsalesitem }}')">+ Detalles</button>
                   
                </td>
                <td class="text-right">
                    <input type="number" step="any" name="cantidad" id="cantidad{{ $sale_i->idsalesitem }}" value="{{ $sale_i->cantidad }}" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }} update_cantidad_factura" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}">
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
                    <input type="number" step="any" name="costo_con_iva" id="costo_con_iva{{ $sale_i->idsalesitem }}" value="{{ round($sale_i->valor_neto + $sale_i->valor_impuesto - $sale_i->exo_monto,2) }}" class="form-control form-control-alternative{{ $errors->has('costo_con_iva') ? ' is-invalid' : '' }} update_costo_con_iva" data-id="{{ $sale_i->idsalesitem }}" data-producto="{{ $sale_i->idproducto }}" data-exo-monto="{{ round($sale_i->exo_monto, 5) }}" data-impuesto-prc="{{ isset($sale_i->impuesto_prc) ? $sale_i->impuesto_prc : (isset($sale_i->prod_sale[0]->porcentaje_imp) ? $sale_i->prod_sale[0]->porcentaje_imp : 0) }}">
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

<div class="mb-1" style="display:none;">
    <button type="button" id="agregar_fila" class="btn btn-success btn-sm" disabled>
        <i class="fas fa-plus"></i> Fila
    </button>
</div>

<!-- Botón para editar la descripción -->


<!-- Modal -->
<div class="modal fade" id="modalEditarDescripcion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Actualizar Descripción del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="nueva_descripcion" placeholder="Ingrese nueva descripción">
                <input type="hidden" id="id_producto" value=""> <!-- Para almacenar el ID del producto -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="actualizarDescripcion">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Desactiva notificaciones emergentes de Black Dashboard en esta vista.
window.__disableDashboardNotifications = true;
(function muteDashboardNotifications() {
    function applyMute() {
        if (window.demo && typeof window.demo.showNotification === 'function') {
            window.demo.showNotification = function() {};
            return true;
        }
        return false;
    }

    if (!applyMute()) {
        document.addEventListener('DOMContentLoaded', applyMute);
        window.addEventListener('load', applyMute);
        setTimeout(applyMute, 400);
    }
})();

let __refreshFacturaPending = false;
let __refreshFacturaQueued = false;
let __refreshFacturaTimer = null;
let __submitButtonLocks = 0;
let __lineTaxRateCache = {};

function syncSubmitButtonState() {
    var shouldDisable = (__submitButtonLocks > 0) || __refreshFacturaPending || __refreshFacturaQueued;
    $('#submit-button').prop('disabled', shouldDisable);
}

function lockSubmitButton() {
    __submitButtonLocks += 1;
    syncSubmitButtonState();
}

function unlockSubmitButton() {
    __submitButtonLocks = Math.max(0, __submitButtonLocks - 1);
    syncSubmitButtonState();
}

function isLineEditingInProgress() {
    var active = document.activeElement;
    if (!active || typeof active.closest !== 'function') {
        return false;
    }

    var inProductos = active.closest('#tabla_productos') || active.closest('#buscar_articulo_pos');
    if (!inProductos) {
        return false;
    }

    return active.tagName === 'INPUT' && !active.readOnly;
}

function runRefreshFacturaUI() {
    __refreshFacturaPending = true;
    syncSubmitButtonState();

    fetch(window.location.href, {
        method: 'GET',
        credentials: 'same-origin',
        cache: 'no-store'
    })
    .then(function(resp) {
        return resp.text();
    })
    .then(function(html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var editingNow = isLineEditingInProgress();

        var currentTbody = document.querySelector('#tabla_productos tbody');
        var nextTbody = doc.querySelector('#tabla_productos tbody');
        if (!editingNow && currentTbody && nextTbody) {
            currentTbody.innerHTML = nextTbody.innerHTML;
            __lineTaxRateCache = {};
            primeLineTaxRateCache();
            primeLineCostoBaseCache();
        }

        var currentTotales = document.getElementById('totales_resumen');
        var nextTotales = doc.getElementById('totales_resumen');
        if (currentTotales && nextTotales) {
            currentTotales.innerHTML = nextTotales.innerHTML;
        }

        ['tot_pos_dev', 'tot_otros_cargos', 'numero_documento'].forEach(function(fieldId) {
            var currentInput = document.getElementById(fieldId);
            var nextInput = doc.getElementById(fieldId);
            if (currentInput && nextInput) {
                currentInput.value = nextInput.value;
            }
        });

        recalcFrontTotalsFromLines();
    })
    .catch(function(err) {
        console.error('No se pudo refrescar la UI en caliente:', err);
    })
    .finally(function() {
        __refreshFacturaPending = false;

        if (__refreshFacturaQueued) {
            __refreshFacturaQueued = false;
            if (__refreshFacturaTimer) {
                clearTimeout(__refreshFacturaTimer);
            }
            __refreshFacturaTimer = setTimeout(function() {
                runRefreshFacturaUI();
            }, 80);
        }

        syncSubmitButtonState();
    });
}

function refreshFacturaUI(forceImmediate) {
    if (__refreshFacturaPending) {
        __refreshFacturaQueued = true;
        syncSubmitButtonState();
        return;
    }

    if (__refreshFacturaTimer) {
        clearTimeout(__refreshFacturaTimer);
    }

    // Debounce corto: junta varios cambios seguidos en un solo refresh.
    __refreshFacturaQueued = true;
    syncSubmitButtonState();
    var debounceMs = forceImmediate ? 0 : 120;
    __refreshFacturaTimer = setTimeout(function() {
        __refreshFacturaQueued = false;
        runRefreshFacturaUI();
    }, debounceMs);
}

function handleSoftRefresh(forceImmediate) {
    if (typeof demo !== 'undefined' && typeof demo.showNotification === 'function') {
        demo.showNotification('top', 'right', 'Cambios guardados sin recargar la pagina.', 2);
    }

    refreshFacturaUI(!!forceImmediate);

    var recalcBtn = document.getElementById('clickRecalcular');
    if (recalcBtn) {
        recalcBtn.classList.remove('btn-success');
        recalcBtn.classList.add('btn-warning');
    }
}

function clearQuickProductRow() {
    var scope = document.getElementById('buscar_articulo_pos');
    if (!scope) {
        return;
    }

    var fieldsToClear = [
        'idproducto_pos',
        'codigo_pos',
        'nombre_pos',
        'cantidad_pos_envia',
        'monto_linea',
        'disponible_pos'
    ];

    fieldsToClear.forEach(function(name) {
        scope.querySelectorAll('[name="' + name + '"]').forEach(function(el) {
            el.value = '';
        });
    });

    scope.querySelectorAll('.agregar_producto_pos_btn').forEach(function(btn) {
        btn.style.display = 'none';
    });
}

function clearProductModalSelection() {
    document.querySelectorAll('.select-checkbox').forEach(function(chk) {
        chk.checked = false;
    });
    var salesItem = document.getElementById('sales_item');
    if (salesItem) {
        salesItem.value = '';
    }
}

function parseInputNumber(value) {
    if (value === null || value === undefined) {
        return 0;
    }
    var text = String(value).trim();
    if (!text.length) {
        return 0;
    }

    text = text.replace(/\s/g, '');

    var hasComma = text.indexOf(',') !== -1;
    var hasDot = text.indexOf('.') !== -1;

    if (hasComma && hasDot) {
        if (text.lastIndexOf(',') > text.lastIndexOf('.')) {
            text = text.replace(/\./g, '').replace(',', '.');
        } else {
            text = text.replace(/,/g, '');
        }
    } else if (hasComma) {
        var commaParts = text.split(',');
        if (commaParts.length === 2 && commaParts[1].length <= 2) {
            text = text.replace(',', '.');
        } else {
            text = text.replace(/,/g, '');
        }
    } else if (hasDot) {
        var dotParts = text.split('.');
        if (dotParts.length === 2 && dotParts[1].length === 3 && dotParts[0].length >= 1) {
            text = dotParts[0] + dotParts[1];
        }
    }

    var parsed = parseFloat(text);
    return isNaN(parsed) ? 0 : parsed;
}

function parseLocaleNumber(value) {
    return parseInputNumber(value);
}

function formatNumberCR(value) {
    return Number(value || 0).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function round5(value) {
    return Math.round((Number(value || 0) + Number.EPSILON) * 100000) / 100000;
}

function setTotalValueByLabel(label, value) {
    var table = document.getElementById('totales_resumen');
    if (!table) {
        return;
    }

    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
        var th = row.querySelector('th');
        var td = row.querySelector('td b');
        if (!th || !td) {
            return;
        }

        var header = th.textContent.replace(/\s+/g, ' ').trim();
        if (header.indexOf(label) === 0) {
            td.textContent = formatNumberCR(value);
        }
    });
}

function getLineTaxCacheKey(row) {
    if (!row) {
        return '';
    }
    var totalInput = row.querySelector('.update_costo_con_iva');
    if (!totalInput) {
        return '';
    }
    return totalInput.getAttribute('data-id') || totalInput.id || '';
}

function getLineIvaFromDetail(row) {
    if (!row) {
        return 0;
    }
    var detailRow = row.nextElementSibling;
    if (!detailRow) {
        return 0;
    }
    var detailText = detailRow.textContent || '';
    var match = detailText.match(/IVA\s*=\s*([\d\.,]+)/i);
    if (!match || !match[1]) {
        return 0;
    }
    return parseInputNumber(match[1]);
}

function primeLineTaxRateForRow(row) {
    if (!row) {
        return 0;
    }

    var key = getLineTaxCacheKey(row);
    if (!key) {
        return 0;
    }

    var totalInput = row.querySelector('.update_costo_con_iva');
    if (!totalInput) {
        return 0;
    }

    var total = parseInputNumber(totalInput.value);
    var iva = getLineIvaFromDetail(row);
    if (total <= 0 || iva <= 0 || iva >= total) {
        return 0;
    }

    var neto = total - iva;
    if (neto <= 0) {
        return 0;
    }

    var rate = iva / neto;
    __lineTaxRateCache[key] = rate;
    return rate;
}

function primeLineTaxRateCache() {
    document.querySelectorAll('#tabla_productos tbody tr').forEach(function(row) {
        if (row.querySelector('.update_costo_con_iva')) {
            primeLineTaxRateForRow(row);
        }
    });
}

function primeLineCostoBaseForRow(row) {
    if (!row) {
        return;
    }

    var totalInput = row.querySelector('.update_costo_con_iva');
    if (!totalInput) {
        return;
    }

    // No sobreescribir una base que ya fue definida durante la edicion.
    var existingBase = parseInputNumber(totalInput.getAttribute('data-costo-base') || '0');
    if (existingBase > 0) {
        return;
    }

    var cantidadInput = row.querySelector('.update_cantidad_factura');
    var descuentoInput = row.querySelector('.update_descuento_factura');
    var cantidad = cantidadInput ? parseInputNumber(cantidadInput.value) : 0;
    var descuentoPrc = descuentoInput ? parseInputNumber(descuentoInput.value) : 0;
    var totalLinea = parseInputNumber(totalInput.value);
    var tasaIva = inferLineTaxRate(row);

    if (cantidad <= 0 || totalLinea <= 0) {
        return;
    }

    if (descuentoPrc < 0) descuentoPrc = 0;
    if (descuentoPrc > 100) descuentoPrc = 100;

    var factorDescuento = 1 - (descuentoPrc / 100);
    if (factorDescuento <= 0) {
        return;
    }

    var netoFinal = tasaIva > 0 ? (totalLinea / (1 + tasaIva)) : totalLinea;
    var costoBase = round5(netoFinal / (cantidad * factorDescuento));
    if (costoBase > 0) {
        totalInput.setAttribute('data-costo-base', costoBase.toFixed(5));
        totalInput.setAttribute('data-last-total', round5(totalLinea).toFixed(5));
        totalInput.setAttribute('data-last-cantidad', String(cantidad));
    }
}

function primeLineCostoBaseCache() {
    document.querySelectorAll('#tabla_productos tbody tr').forEach(function(row) {
        if (row.querySelector('.update_costo_con_iva')) {
            primeLineCostoBaseForRow(row);
        }
    });
}

function inferLineTaxRate(row) {
    if (!row) {
        return 0;
    }

    var lineId = getLineTaxCacheKey(row);
    if (lineId && __lineTaxRateCache.hasOwnProperty(lineId)) {
        return __lineTaxRateCache[lineId];
    }

    var primedRate = primeLineTaxRateForRow(row);
    if (primedRate > 0) {
        return primedRate;
    }

    var totalInput = row.querySelector('.update_costo_con_iva');
    if (!totalInput) {
        return 0;
    }

    var prcAttr = parseInputNumber(totalInput.getAttribute('data-impuesto-prc') || '0');
    if (prcAttr > 0) {
        var fallbackRate = prcAttr / 100;
        if (lineId) {
            __lineTaxRateCache[lineId] = fallbackRate;
        }
        return fallbackRate;
    }

    return 0;
}

function getLineTaxPercent(row) {
    if (!row) {
        return 0;
    }

    var totalInput = row.querySelector('.update_costo_con_iva');
    if (totalInput) {
        var prcAttr = parseInputNumber(totalInput.getAttribute('data-impuesto-prc') || '0');
        if (prcAttr > 0) {
            return prcAttr;
        }
    }

    return inferLineTaxRate(row) * 100;
}

function getLineCostoUtilidadBase(row, cantidad, totalLinea, descuentoPrc, tasaIva) {
    if (!row) {
        return 0;
    }

    var totalInput = row.querySelector('.update_costo_con_iva');
    var sinIvaInput = row.querySelector('.update_costo_sin_iva_u');
    if (!totalInput) {
        return 0;
    }

    var fromCache = parseInputNumber(totalInput.getAttribute('data-costo-base') || '0');
    if (fromCache > 0) {
        return fromCache;
    }

    var fromInput = sinIvaInput ? parseInputNumber(sinIvaInput.value) : 0;
    if (fromInput > 0) {
        totalInput.setAttribute('data-costo-base', round5(fromInput).toFixed(5));
        return fromInput;
    }

    if (cantidad <= 0) {
        return 0;
    }

    var factorDescuento = 1 - (descuentoPrc / 100);
    if (factorDescuento <= 0) {
        return 0;
    }

    var netoFinal = tasaIva > 0 ? (totalLinea / (1 + tasaIva)) : totalLinea;
    var costoBase = round5(netoFinal / (cantidad * factorDescuento));
    if (costoBase > 0) {
        totalInput.setAttribute('data-costo-base', costoBase.toFixed(5));
    }
    return costoBase;
}

function updateLineIvaLabel(row, ivaAmount) {
    if (!row) {
        return;
    }

    var detailRow = row.nextElementSibling;
    if (!detailRow) {
        return;
    }

    var targetTd = null;
    detailRow.querySelectorAll('td').forEach(function(td) {
        if (targetTd) {
            return;
        }
        if ((td.textContent || '').indexOf('IVA =') !== -1) {
            targetTd = td;
        }
    });

    if (!targetTd) {
        return;
    }

    targetTd.textContent = 'IVA = ' + formatNumberCR(ivaAmount);
}

function updateLineFromEditableFields(changedInputEl) {
    var row = changedInputEl ? changedInputEl.closest('tr') : null;
    if (!row) {
        return;
    }

    var cantidadInput = row.querySelector('.update_cantidad_factura');
    var sinIvaInput = row.querySelector('.update_costo_sin_iva_u');
    var descuentoInput = row.querySelector('.update_descuento_factura');
    var totalInput = row.querySelector('.update_costo_con_iva');

    if (!totalInput) {
        return;
    }

    var changedIsTotal = changedInputEl && changedInputEl.classList.contains('update_costo_con_iva');
    var tasaIva = inferLineTaxRate(row);

    var cantidad = cantidadInput ? parseInputNumber(cantidadInput.value) : 0;
    var sinIvaUnit = sinIvaInput ? parseInputNumber(sinIvaInput.value) : 0;
    var descuentoPrc = descuentoInput ? parseInputNumber(descuentoInput.value) : 0;
    var totalActual = parseInputNumber(totalInput.value);

    if (cantidad < 0) cantidad = 0;
    if (sinIvaUnit < 0) sinIvaUnit = 0;
    if (descuentoPrc < 0) descuentoPrc = 0;
    if (descuentoPrc > 100) descuentoPrc = 100;

    if (changedInputEl && changedInputEl.classList.contains('update_costo_sin_iva_u') && sinIvaUnit > 0) {
        totalInput.setAttribute('data-costo-base', round5(sinIvaUnit).toFixed(5));
    }

    var impuestoPrc = getLineTaxPercent(row);
    var costoBase = getLineCostoUtilidadBase(row, cantidad, totalActual, descuentoPrc, tasaIva);

    if (!changedIsTotal && cantidad > 0 && costoBase > 0) {
        // Replica la formula de actualiarCantFactura con precision de 5 decimales.
        var totalNeto = round5(costoBase * cantidad);
        var totalDescuento = descuentoPrc > 0 ? round5((totalNeto * descuentoPrc) / 100) : 0;
        var netoFinal = round5(totalNeto - totalDescuento);
        var totalImpuesto = round5((netoFinal * impuestoPrc) / 100);
        var totalRecalculado = round5(netoFinal + totalImpuesto);

        totalInput.value = totalRecalculado.toFixed(5);
        updateLineIvaLabel(row, totalImpuesto);

        // El total fue recalculado por JS; se debe encolar para persistir en BD al guardar.
        if (changedInputEl !== totalInput && typeof sendLineUpdate === 'function') {
            sendLineUpdate(totalInput, false);
        }
    }

    var totalLinea = parseInputNumber(totalInput.value);
    if (changedIsTotal) {
        var factorDescuento = 1 - (descuentoPrc / 100);
        if (cantidad > 0 && factorDescuento > 0) {
            var netoDesdeTotal = impuestoPrc > 0 ? (totalLinea / (1 + (impuestoPrc / 100))) : totalLinea;
            var costoBaseDesdeTotal = round5(netoDesdeTotal / (cantidad * factorDescuento));

            if (costoBaseDesdeTotal > 0) {
                totalInput.setAttribute('data-costo-base', costoBaseDesdeTotal.toFixed(5));

                if (sinIvaInput && document.activeElement !== sinIvaInput) {
                    sinIvaInput.value = costoBaseDesdeTotal.toFixed(5);
                }
            }
        }
    }
    var netoActual = tasaIva > 0 ? (totalLinea / (1 + tasaIva)) : totalLinea;
    var ivaLinea = Math.max(0, totalLinea - netoActual);
    updateLineIvaLabel(row, ivaLinea);

    totalInput.setAttribute('data-last-total', round5(totalLinea).toFixed(5));
    totalInput.setAttribute('data-last-cantidad', String(cantidad));
}

function recalcFrontTotalsFromLines() {
    var subtotal = 0;
    var totalDescuento = 0;
    var totalImpuesto = 0;
    var lineTotal = 0;
    var totalExonerado = 0;

    document.querySelectorAll('.update_costo_con_iva').forEach(function(totalInput) {
        var row = totalInput.closest('tr');
        if (!row) {
            return;
        }

        var cantidadInput = row.querySelector('.update_cantidad_factura');
        var sinIvaInput = row.querySelector('.update_costo_sin_iva_u');
        var descuentoInput = row.querySelector('.update_descuento_factura');

        var cantidad = cantidadInput ? parseInputNumber(cantidadInput.value) : 0;
        var sinIvaUnit = sinIvaInput ? parseInputNumber(sinIvaInput.value) : 0;
        var descuentoPrc = descuentoInput ? parseInputNumber(descuentoInput.value) : 0;
        var totalLinea = parseInputNumber(totalInput.value);
        var exoMontoLinea = parseInputNumber(totalInput.getAttribute('data-exo-monto') || '0');
        var tasaIva = inferLineTaxRate(row);

        if (cantidad < 0) cantidad = 0;
        if (descuentoPrc < 0) descuentoPrc = 0;
        if (descuentoPrc > 100) descuentoPrc = 100;

        var costoBase = 0;
        if (sinIvaInput) {
            costoBase = parseInputNumber(sinIvaInput.value);
        }
        if (costoBase <= 0) {
            costoBase = getLineCostoUtilidadBase(row, cantidad, totalLinea, descuentoPrc, tasaIva);
        }
        var baseLinea = round5(costoBase * cantidad);
        var descuentoLinea = descuentoPrc > 0 ? round5((baseLinea * descuentoPrc) / 100) : 0;
        var netoConDescuento = round5(baseLinea - descuentoLinea);
        var impuestoLinea = Math.max(0, round5(totalLinea - netoConDescuento));
        var impuestoPrcLinea = getLineTaxPercent(row);
        var impuestoBrutoLinea = Math.max(0, round5((netoConDescuento * impuestoPrcLinea) / 100));

        if (!sinIvaInput) {
            netoConDescuento = tasaIva > 0 ? (totalLinea / (1 + tasaIva)) : totalLinea;
            baseLinea = descuentoPrc < 100 ? (netoConDescuento / (1 - (descuentoPrc / 100))) : netoConDescuento;
            descuentoLinea = Math.max(0, baseLinea - netoConDescuento);
            impuestoLinea = Math.max(0, totalLinea - netoConDescuento);
            impuestoBrutoLinea = Math.max(0, round5((netoConDescuento * impuestoPrcLinea) / 100));
        }

        if (!isFinite(baseLinea)) {
            baseLinea = netoConDescuento;
            descuentoLinea = 0;
        }

        // Fallback para filas sin detalle/IVA disponible.
        if (tasaIva === 0 && cantidad > 0 && descuentoPrc === 0) {
            baseLinea = totalLinea;
            descuentoLinea = 0;
            impuestoLinea = 0;
            impuestoBrutoLinea = 0;
        }

        // Subtotal es base (sin IVA), no incluye exoneraciones.
        subtotal += baseLinea;
        totalDescuento += descuentoLinea;
        totalImpuesto += impuestoBrutoLinea;
        lineTotal += totalLinea;
        totalExonerado += exoMontoLinea;
    });

    var otrosCargosInput = document.getElementById('tot_otros_cargos');
    var otrosCargos = otrosCargosInput ? parseInputNumber(otrosCargosInput.value) : 0;

    var abonoElement = document.getElementById('t_abono_op');
    var abono = abonoElement ? parseLocaleNumber(abonoElement.textContent) : 0;

    var totalDocumento = ((subtotal - totalDescuento + totalImpuesto - totalExonerado) + otrosCargos) - abono;

    setTotalValueByLabel('Subtotal:', subtotal);
    setTotalValueByLabel('Total Descuento:', totalDescuento);
    setTotalValueByLabel('Total Impuesto:', totalImpuesto);
    setTotalValueByLabel('Total Documento:', totalDocumento);

    var totalDocElement = document.getElementById('t_documento');
    if (totalDocElement) {
        totalDocElement.textContent = formatNumberCR(totalDocumento);
    }

    var totalPosInput = document.getElementById('tot_pos_dev');
    if (totalPosInput) {
        totalPosInput.value = lineTotal.toFixed(2);
    }
}

if (!window.__softReloadPatched) {
    window.__softReloadPatched = true;
    window.__originalReload = window.location.reload ? window.location.reload.bind(window.location) : null;
    window.location.reload = function() {
        handleSoftRefresh();
    };
}

document.addEventListener('DOMContentLoaded', function() {
    primeLineTaxRateCache();
    primeLineCostoBaseCache();
});

// Mostrar el modal y cargar la información
$(document).on('click', '.editar-descripcion', function() {
    var id = $(this).data('id'); // Obtener el ID del producto
    var nombre_actual = $(this).data('nombre'); // Obtener el nombre actual

    // Llenar el campo del modal
    $('#nueva_descripcion').val(nombre_actual);
    $('#id_producto').val(id); // Guardar ID en un campo oculto

    // Abrir el modal
    $('#modalEditarDescripcion').modal('show');
});

// Manejar la actualización en el modal
$('#actualizarDescripcion').on('click', function() {
    var id = $('#id_producto').val(); // Obtener el ID del producto
    var nuevaDescripcion = $('#nueva_descripcion').val(); // Obtener nueva descripción

    // Verificar que no esté vacío
    if (nuevaDescripcion.trim().length > 0) {
        var URL = {!! json_encode(url('actualizar-descripcion-factura')) !!};

        $.ajax({
            type: 'get',
            url: URL,
            dataType: 'json',
            data: {
                idsalesitem: id,
                nombre_producto_pos: nuevaDescripcion
            },
            success: function(response) {
                handleSoftRefresh(); // Guardado sin recargar
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar la descripción:', error);
            }
        });

        // Cerrar el modal
        $('#modalEditarDescripcion').modal('hide');
    } else {
        alert('La nueva descripción no puede estar vacía.');
    }
});

    $(document).ready(function() {
        // +fila deshabilitado por estabilidad: no crear filas temporales desde frontend.
        $('#agregar_fila').on('click', function(event) {
            event.preventDefault();
            return false;
        });

        // Autocompletado para el campo de nombre del producto
        $(document).on('focus', '[id^="nombre_producto_pos1"], .update_nombre_producto_pos1', function() {
            var $input = $(this);
            $input.autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ url('autocomplete/nombre') }}",
                        data: {
                            term: request.term
                        },
                        dataType: "json",
                        success: function(data) {
                            var resp = $.map(data, function(obj) {
                                return {
                                    label: obj.nombre_producto,
                                    value: obj.idproducto // Guardar el ID del producto
                                };
                            });
                            response(resp);
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    // Al seleccionar un producto, guardar el ID del producto
                    var $row = $(this).closest('tr');
                    $row.find('.update_nombre_producto_pos1').data('id', ui.item.value);

                    // Crea la linea de inmediato para asignar idsalesitem a los inputs de la fila.
                    ensureTempRowPersisted($row, ui.item.value);
                }
            });
        });

        function setTempRowLineIds($row, idsalesitem, idproducto) {
            if (!idsalesitem) {
                return;
            }

            idproducto = idproducto || '';
            $row.attr('data-row-created', '1');
            $row.find('.update_nombre_producto_pos1')
                .data('id', idsalesitem)
                .data('producto', idproducto)
                .attr('data-id', idsalesitem)
                .attr('data-producto', idproducto);

            $row.find('.update_cantidad_factura, .update_descuento_factura, .update_costo_con_iva, .update_costo_sin_iva_u').each(function() {
                $(this)
                    .data('id', idsalesitem)
                    .data('producto', idproducto)
                    .attr('data-id', idsalesitem)
                    .attr('data-producto', idproducto);
            });

            $row.find('.modificar_flotante, .agregar_exoneracion, .eliminar_fila_factura').each(function() {
                $(this)
                    .data('id', idsalesitem)
                    .attr('data-id', idsalesitem);
            });

            // Si el usuario ya escribio valores, dispara blur para que entren al autosync.
            $row.find('.update_cantidad_factura, .update_costo_con_iva, .update_costo_sin_iva_u, .update_descuento_factura').each(function() {
                var val = String($(this).val() || '').trim();
                if (val.length > 0) {
                    $(this).trigger('blur');
                }
            });
        }

        function ensureTempRowPersisted($row, idproducto, onDone) {
            if (!idproducto) {
                if (typeof onDone === 'function') {
                    onDone(false);
                }
                return;
            }

            var pendingCallbacks = $row.data('__persistCallbacks') || [];
            if (typeof onDone === 'function') {
                pendingCallbacks.push(onDone);
                $row.data('__persistCallbacks', pendingCallbacks);
            }

            function resolvePersistCallbacks(ok) {
                var callbacks = $row.data('__persistCallbacks') || [];
                $row.removeData('__persistCallbacks');
                callbacks.forEach(function(cb) {
                    if (typeof cb === 'function') {
                        cb(ok);
                    }
                });
            }

            var created = String($row.attr('data-row-created') || '') === '1';
            var existingId = String($row.find('.update_cantidad_factura').attr('data-id') || '').trim();
            if (created || existingId.length > 0) {
                resolvePersistCallbacks(true);
                return;
            }

            var isCreating = String($row.attr('data-row-creating') || '') === '1';
            if (isCreating) {
                return;
            }

            var cantidad = $row.find('.update_cantidad_factura').val() || 1;
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};
            $row.attr('data-row-creating', '1');

            $.ajax({
                type: 'get',
                url: URL,
                dataType: 'json',
                data: {
                    sales_item: idproducto,
                    idsale: $('#idsale').val(),
                    cantidad: cantidad,
                },
                success: function(response) {
                    setTempRowLineIds($row, response.idsalesitem, response.idproducto || idproducto);
                    resolvePersistCallbacks(true);
                },
                error: function(xhr, status, error) {
                    console.error('Error al crear la linea temporal:', error);
                    resolvePersistCallbacks(false);
                },
                complete: function() {
                    $row.removeAttr('data-row-creating');
                }
            });
        }

        window.hasPendingTempRowCreates = function() {
            var pending = false;
            $('#tabla_productos tbody tr').each(function() {
                var $row = $(this);
                if (!$row.find('.update_nombre_producto_pos1').length) {
                    return;
                }

                var created = String($row.attr('data-row-created') || '') === '1';
                var creating = String($row.attr('data-row-creating') || '') === '1';
                var selectedProduct = String($row.find('.update_nombre_producto_pos1').data('id') || '').trim();

                if (creating || (!created && selectedProduct.length > 0)) {
                    pending = true;
                    return false;
                }
            });
            return pending;
        };

        window.ensureAllTempRowsPersisted = function(onDone) {
            var candidates = [];

            $('#tabla_productos tbody tr').each(function() {
                var $row = $(this);
                if (!$row.find('.update_nombre_producto_pos1').length) {
                    return;
                }

                var created = String($row.attr('data-row-created') || '') === '1';
                var selectedProduct = String($row.find('.update_nombre_producto_pos1').data('id') || '').trim();
                if (!created && selectedProduct.length > 0) {
                    candidates.push({
                        row: $row,
                        idproducto: selectedProduct
                    });
                }
            });

            if (!candidates.length) {
                if (typeof onDone === 'function') {
                    onDone(true);
                }
                return;
            }

            var remaining = candidates.length;
            var allOk = true;
            candidates.forEach(function(item) {
                ensureTempRowPersisted(item.row, item.idproducto, function(ok) {
                    if (!ok) {
                        allOk = false;
                    }
                    remaining -= 1;
                    if (remaining <= 0 && typeof onDone === 'function') {
                        onDone(allOk);
                    }
                });
            });
        };

        // Guardar el producto en la base de datos al salir del campo de entrada
        $(document).on('blur', '[id^="nombre_producto_pos1"], .update_nombre_producto_pos1', function() {
            var $row = $(this).closest('tr');
            var idproducto = $(this).data('id');
            if (idproducto) {
                ensureTempRowPersisted($row, idproducto);
            } else {
                alert('Primero debes seleccionar un producto.');
            }
        });

        // Manejar la eliminación de filas
        $(document).on('click', '.eliminar_fila_factura', function() {
            if ($(this).data('id')) {
                return;
            }
            $(this).closest('tr').next().remove(); // Eliminar la fila de detalles
            $(this).closest('tr').remove(); // Eliminar la fila principal
        });
    });
</script>
                                        <script>
function toggleRow(id) {
    const row = document.getElementById(`details-row-${id}`);
    if (row.classList.contains('hidden')) {
        row.classList.remove('hidden');
    } else {
        row.classList.add('hidden');
    }
}
</script>
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


                         <div class="col-2" id="firstBlock">
   <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>

    <!-- Campo oculto para enviar el valor -->


    <!-- Campo de selección deshabilitado -->
  <!-- Campo de selección habilitado -->
<select class="form-control form-control-alternative" id="tipo_documento">
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
   <!--<option value="09" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '09' ? 'selected="selected"' : '') }}>Fáctura Electrónica de Exportación</option>-->
</select>

@include('alerts.feedback', ['field' => 'tipo_documento'])

  <input hidden="true"  name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', $sales->tipo_documento ?? '') }}" required>
</div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>

                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $usuario->nombre }}">
                                    
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                  <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}"> 
                                <label class="form-control-label" for="input-codigo_actividad"> {{ __('Código Actividad Cliente') }} &nbsp;&nbsp;&nbsp;&nbsp; <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?</a> </label>
                                <input type="hidden" name="ced_receptor_act" id="ced_receptor_act" class="form-control form-control-alternative{{ $errors->has('ced_receptor_act') ? ' is-invalid' : '' }}" value="{{ $usuario->num_id }}">
                                <!-- Input con datalist para poder escribir o seleccionar -->
                                <input type="text" class="form-control form-control-alternative" id="codigo_actividad" name="codigo_actividad" list="codigo_actividad_list" value="{{ old('codigo_actividad', $usuario->codigo_actividad) }}"> 
                                <datalist id="codigo_actividad_list"> <!-- Las opciones se poblarán dinámicamente desde JS --> </datalist>
                                @include('alerts.feedback', ['field' => 'codigo_actividad'])

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
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio' , $sales->tipo_cambio) }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                            
                                {{--<div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01" {{ ($sales->medio_pago == '01' ? 'selected="selected"' : '') }}>Efectivo</option>
                                        <option value="02" {{ ($sales->medio_pago == '02' ? 'selected="selected"' : '') }}>Tarjeta</option>
                                        <option value="03" {{ ($sales->medio_pago == '03' ? 'selected="selected"' : '') }}>Cheque</option>
                                        <option value="04" {{ ($sales->medio_pago == '04' ? 'selected="selected"' : '') }}>Transferencia – depósito bancario</option>
                                        <option value="05" {{ ($sales->medio_pago == '05' ? 'selected="selected"' : '') }}>Recaudado por terceros</option>
                                        <option value="06" {{ ($sales->medio_pago == '06' ? 'selected="selected"' : '') }}>Sinpe Movil</option>
                                        <option value="07" {{ ($sales->medio_pago == '07' ? 'selected="selected"' : '') }}>Plataforma Digital</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>--}}
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
                                 <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                    <textarea id="observaciones" name="observaciones" class="form-control">{{ $sales->observaciones }}</textarea>
                                </div>
                                
                                 <div class="form-group{{ $errors->has('correo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="correo">{{ __('Correo Principal') }}  </label>
                                    <input type="text" name="correo[]" id="correo" class="form-control form-control-alternative{{ $errors->has('correo') ? ' is-invalid' : '' }}"  value="{{ $usuario->email }}" readonly>
                                    @include('alerts.feedback', ['field' => 'cc_correo'])
                                </div>
                                <div class="form-group text-right">
                                <a href="#" class="btn btn-sm btn-info" data-target="#AddMails" data-toggle="modal" id="Agregar_emails">Emails Adicionales</a>
                            </div>
                     
                                   <div class="form-group{{ $errors->has('cc_correo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="cc_correo">{{ __('Copia de Correo') }}
                                        <a href="#" class="btn-success text-right" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas Copiar uno o mas correos separalos por una coma(,).</b>">¿Necesitas Ayuda?
                                        </a>
                                    </label>
                                   
                                    <textarea id="cc_correo" name="cc_correo[]" class="form-control">{{ $sales->cc_correo }}</textarea>
                                    @include('alerts.feedback', ['field' => 'cc_correo'])
                                </div>

                              <button id="toggleButton" type="button" class="btn btn-success btn-sm">+ Opciones</button>

                            </div>
                            
                            <div class="col-2" id="detailsSection" style="display: none;">
                                         <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                  <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" required>
                                 @foreach ($medio_pagos as $medio)
                                <option value="{{ $medio->id }}"
                                 @if($sales->medioPagos->contains('codigo', $medio->codigo))
                                selected
                                @endif
                                >{{ $medio->nombre }}</option>
                                @endforeach
                                </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago', $sales->referencia_pago) }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                                  <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}" id="combo_actividad">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad Emisor') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad',$sales->idcodigoactv) }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                    <input type="text" name="valor_actividad" id="valor_actividad" value="{{ old('valor_actividad', $sales->idcodigoactv) }}" hidden="true">
                                </div>
                                <div class="form-group{{ $errors->has('exoneracion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="telefono">{{ __('Número Exoneracion') }}</label>
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






                            </div>
                        </div>
                    </div>
                        <div class="row">
                          <!--  <div class="col-4">
  <br><b>Sección de Cambio</b>
   <table class="table table-sm">
    <tbody>
        <tr>
            <td style="font-weight: bold; width: 30%;">{{ __('Efectivo:') }}</td>
            <td style="width: 70%;">
                <input type="text" name="efectivo_dev" id="efectivo_dev" class="form-control form-control-sm" value="0">
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">{{ __('Tarjeta:') }}</td>
            <td>
                <input type="text" name="tarjeta_dev" id="tarjeta_dev" class="form-control form-control-sm" value="0">
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">{{ __('Cambio:') }}</td>
            <td>
                <input type="text" name="cambio_dev" id="cambio_dev" class="form-control" readonly="true" value="0" style="font-size: 1.5rem;">
            </td>
        </tr>
    </tbody>
</table>-->
</div>

                            <div class="col-4">
                                 <br><b>Totales</b>   <br>
                               <table class="table-sm " id="totales_resumen" style="float:right;">
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
            <td><b id="t_documento">{{ number_format((($total_neto - $total_descuento + $total_impuesto - $monto_imp_exonerado_t) + $total_otros_cargos) - $sales->total_abonos_op,2,',','.') }}</b></td>
        </tr>

    @else
        <tr>
            <th scope="col">Total Documento:</th>
            <td><b id="t_documento">{{ number_format(($total_neto - $total_descuento + $total_impuesto - $monto_imp_exonerado_t) + $total_otros_cargos,2,',','.') }}</b></td>
        </tr>
    @endif
  </tbody>
</table>

                            </div>
                            <div class="col-4">

                                <div class="text-center">
                                     <br><b>Envio a MH</b>   <br>
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

                                    <!--<button type="submit" class="btn btn-success mt-4">{{ __('Facturar') }}</button>-->
                                    <button id="guardar-cambios-pos" type="button" class="btn btn-info mt-4">{{ __('Guardar Cambios') }}</button>
                                    <button id="submit-button" type="submit" class="btn btn-success mt-4">{{ __('Facturar') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                 <input type="text" name="correos" id="correos" value="{{ old('correos') }}" hidden="true">
                <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento', $sales->numero_documento) }}" hidden="true">
                <input type="text" name="idsale" id="idsale" value="{{ old('idsale', $sales->idsale) }}" hidden="true">
                <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="{{ old('existe_exoneracion', $sales->tiene_exoneracion) }}" hidden="true">
                <input type="text" name="tot_pos_dev" id="tot_pos_dev" value="{{ old('tot_pos_dev', $total_comprobante) }}" hidden="true">
                <input type="text" name="tot_otros_cargos" id="tot_otros_cargos" value="{{ old('tot_otros_cargos', $total_otros_cargos) }}" hidden="true">
                <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">
                <input type="hidden" name="cliente" id="cliente" value="{{ old('cliente', $sales->idcliente) }}">
                <input type="text" name="usa_lector" id="usa_lector" value="{{ old('usa_lector', $configuracion[0]->usa_lector ) }}" hidden="true">
                <input type="text" name="usa_balanza" id="usa_balanza" value="{{ old('usa_balanza', Auth::user()->config_u[0]->usa_balanza) }}" hidden="true">
                <input type="hidden" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', $sales->idconfigfact) }}">
                <input type="hidden" name="login" id="login" value="{{ old('idconfigfact',  Auth::user()->idconfigfact) }}">
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

@include('modals.addProducts')
@include('modals.addMails')
@include('modals.addExoneracion')
@include('modals.modArticulo')
@include('modals.cargando')
@include('modals.addOtroCargo')
@include('modals.reCalcular')


@endsection

@section('myjs')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>



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
        var detailsSection = document.getElementById('detailsSection');
        var firstBlock = document.getElementById('firstBlock');
        var tableBlock = document.getElementById('tableBlock1');
         var tableBlock2 = document.getElementById('tableBlock');

        // Al cargar la página, la tabla ocupa col-10 y el primer bloque col-2
        tableBlock.classList.add('col-12');
        tableBlock2.classList.add('col-10');
        firstBlock.classList.add('col-2');

        document.getElementById('toggleButton').addEventListener('click', function() {
            if (detailsSection.style.display === 'none') {
                detailsSection.style.display = 'block';
                tableBlock.classList.remove('col-10');
                tableBlock.classList.add('col-8'); // Cambia la tabla a col-8
                tableBlock2.classList.remove('col-10');
                tableBlock2.classList.add('col-8'); // Cambia la tabla a col-8
                firstBlock.classList.remove('col-2');
                firstBlock.classList.add('col-2'); // Se mantiene en col-2
                detailsSection.classList.remove('col-2');
                detailsSection.classList.add('col-2'); // Asegúrate de que el segundo bloque sea col-2
            } else {
                detailsSection.style.display = 'none';
                tableBlock.classList.remove('col-8');
                tableBlock.classList.add('col-10'); // Regresa la tabla a col-10
                tableBlock2.classList.remove('col-8');
                tableBlock2.classList.add('col-10'); // Regresa la tabla a col-10
                firstBlock.classList.remove('col-2');
                firstBlock.classList.add('col-2'); // Se mantiene en col-2
            }
        });
    });
//bloques
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
                "autoWidth": false,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                "scrollX": true,
                order: [[ 0, "asc" ]]
            }
        );
        window.__facturaDataTable = table;

        $('#AddProductos').on('shown.bs.modal', function() {
            if (window.__facturaDataTable) {
                window.__facturaDataTable.columns.adjust().draw(false);
            }
        });
    $(document).ready(function() {
    $('#tabla_productos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "autoWidth": false, // Cambiar a false para usar los anchos definidos en el HTML
        "processing": true,
        "serverSide": false,
        "deferRender": true,
        "paging": false,
        //"scrollY": 200,
        "scrollX": true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "width": "100px", "targets": [2, 3, 4,5,6] }, // Ancho de 50px para las columnas especificadas
            { "width": "30px", "targets": [ 7, 8,9,10,11] },
             { "width": "10px", "targets": [ 11] }, // Ancho de 50px para las columnas especificadas
            { "width": "30px", "targets": [0] }, // Ancho de 50px para las columnas especificadas
            { "width": "400px", "targets": 1 } // Ancho de 100px para la columna "Nombre Producto"
        ]
    });
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


        function normalizarCodigoActividad(valor) {
            var limpio = String(valor || '').trim();
            if (!limpio.length) {
                return '';
            }

            // Si viene del datalist como "CODIGO - DESCRIPCION", solo guardar el codigo.
            if (limpio.indexOf(' - ') !== -1) {
                limpio = limpio.split(' - ')[0].trim();
            }

            return limpio;
        }

        var __isSavingCodigoActividadCliente = false;

        function guardarCodigoActividadCliente(rawValue) {
            var $input = $('#codigo_actividad');
            var codigoActividad = normalizarCodigoActividad(typeof rawValue === 'undefined' ? $input.val() : rawValue);
            var ultimoEnviado = String($input.attr('data-last-sent') || '');

            if (!codigoActividad.length || codigoActividad === ultimoEnviado) {
                return;
            }

            if (__isSavingCodigoActividadCliente) {
                return;
            }

            var cliente = $('#cliente').val();
            var URL = {!! json_encode(url('modificar-actividad-cliente')) !!};

            // Refleja en pantalla el valor que realmente se persiste.
            $input.val(codigoActividad);

            syncPendingDraftsBefore(function() {
                __isSavingCodigoActividadCliente = true;
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{codigo_actividad:codigoActividad,cliente:cliente},
                    success:function(response){
                        $input.attr('data-last-sent', codigoActividad);
                        handleSoftRefresh();
                    },
                    complete:function(){
                        __isSavingCodigoActividadCliente = false;
                    }
                });
            });
        }

        $('#codigo_actividad').on('change blur', function() {
            if (__isFlushingLineDrafts) {
                return;
            }
            guardarCodigoActividadCliente($(this).val());
        });
        
        
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
                            handleSoftRefresh();
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
                    handleSoftRefresh();
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


 
        
        
        function syncIdMovCxcobrarInput(idMov) {
            var $input = $('#idmovcxcobrar');

            if (idMov && String(idMov).trim().length > 0) {
                if (!$input.length) {
                    $('<input>', {
                        type: 'text',
                        name: 'idmovcxcobrar',
                        id: 'idmovcxcobrar',
                        hidden: true
                    }).appendTo('#form_factura');
                    $input = $('#idmovcxcobrar');
                }
                $input.val(idMov);
            } else if ($input.length) {
                $input.val('');
            }
        }

         $('#condición_venta').change(function() {
            var condicion = $(this).val();
            if (condicion === '02') {
                $('#input-p_credito').prop("required", true);
            }else{
                $('#input-p_credito').prop("required", false);
            }
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('modificar-condicion')) !!};
            syncPendingDraftsBefore(function() {
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{condicion:condicion, idsale:idsale},
                    success:function(response){
                        if (response && typeof response.idmovcxcobrar !== 'undefined') {
                            syncIdMovCxcobrarInput(response.idmovcxcobrar);
                        }
                        handleSoftRefresh();
                    },
                    error:function(response){
                        //console.log(response);
                    }
                });
            });
        });

        $(document).on("blur", "#input-p_credito" , function(event) {
            event.preventDefault();
            var dias = $(this).val();
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('modificar-dias-cxc')) !!};
            syncPendingDraftsBefore(function() {
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{dias:dias, idsale:idsale},
                    success:function(response){
                        handleSoftRefresh();
                        //console.log(response);

                    },
                    error:function(response){
                        //console.log(response);
                    }
                });
            });
        });
        $('#agregar_producto').click(function(e) {
            e.preventDefault();
            if( $('.select-checkbox').is(':checked') ) {


              var sales_item = $('#sales_item').val();
              var idsale = $('#idsale').val();
              var cantidad =  null;
              var monto_total =  null;
              var URL = {!! json_encode(url('agregar-linea-factura')) !!};
              $.ajax({
                  type:'get',
                  url: URL,
                  dataType: 'json',
                  data:{sales_item:sales_item, idsale:idsale, monto_total:monto_total, cantidad:cantidad},
                  success:function(response){
                    clearProductModalSelection();
                    if (typeof $ !== 'undefined' && $('#AddProductos').length) {
                        $('#AddProductos').modal('hide');
                    }
                    handleSoftRefresh();
                  }
              });

            } else {
                alert('Debe seleccionar al menos 1 producto.');
            }
        });
        
        
        // Recolectar correos seleccionados en el modal al hacer click en una fila (o al confirmar)
$('#email_datatables').on('click', 'tbody tr', function () {
    // Obtener los valores de los checkboxes marcados dentro de la tabla del modal
    var data = $('#email_datatables').find('[name="seleccion[]"]:checked').map(function () {
        return this.value;
    }).get();

    // Convertir array a cadena CSV
    var str = data.join(',');

    // Guardar en el input oculto como string
    $('#correos').val(str);
});


        $('#agregar_correo').click(function (e) {
    e.preventDefault();

    // Verifica si hay al menos un correo seleccionado
    if ($('[name="seleccion[]"]').filter(':checked').length > 0) {
        var correos = $('#correos').val(); // Debe ser una cadena CSV
        var idsale = $('#idsale').val();

        var URL = {!! json_encode(url('agregar-correo-factura')) !!};

        // Configurar CSRF (si ya lo tienes configurado globalmente, esto puede no ser necesario)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: URL,
            dataType: 'json',
            data: {
                idsale: idsale,
                correos: correos
            },
            success: function(response) {
                handleSoftRefresh();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al guardar los correos.');
            }
        });
    } else {
        alert('Debe seleccionar al menos 1 correo adicional.');
    }
});

        var __isSavingCcCorreo = false;
        var __isSavingObservaciones = false;

        function normalizarCorreosLista(raw) {
            return String(raw || '')
                .replace(/\n+/g, ',')
                .split(',')
                .map(function(item) {
                    return item.trim();
                })
                .filter(function(item) {
                    return item.length > 0;
                })
                .join(',');
        }

        $(document).on('blur', '#cc_correo', function(event) {
            event.preventDefault();

            if (__isFlushingLineDrafts || __isSavingCcCorreo) {
                return;
            }

            var idsale = $('#idsale').val();
            var correos = normalizarCorreosLista($(this).val());
            var ultimoEnviado = String($(this).attr('data-last-sent') || '');

            // Evita solicitudes repetidas si no hubo cambios reales.
            if (correos === ultimoEnviado) {
                return;
            }

            var URL = {!! json_encode(url('editar-cc-correo-pos')) !!};
            var fallbackURL = {!! json_encode(url('editar-observacion-pos')) !!};
            var $field = $(this);
            var payload = {
                idsale: idsale,
                cc_correo: correos
            };

            syncPendingDraftsBefore(function() {
                __isSavingCcCorreo = true;
                $.ajax({
                    type: 'get',
                    url: URL,
                    data: payload,
                    success: function() {
                        $field.val(correos);
                        $field.attr('data-last-sent', correos);
                        handleSoftRefresh();
                    },
                    error: function(xhr) {
                        $.ajax({
                            type: 'get',
                            url: fallbackURL,
                            data: payload,
                            success: function() {
                                $field.val(correos);
                                $field.attr('data-last-sent', correos);
                                handleSoftRefresh();
                            },
                            error: function(fallbackXhr) {
                                console.error(fallbackXhr.responseText || fallbackXhr);
                                alert('Error al guardar la copia de correo.');
                            }
                        });
                    },
                    complete: function() {
                        __isSavingCcCorreo = false;
                    }
                });
            });
        });
        

        function agregarProductoSeleccionadoPos() {
            var idproducto = $('#idproducto_pos').val();
            if ($('#idproducto_pos').val() === "") {
                alert('Primero Debes Seleccionar un Producto.');
                return;
            }

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
                success:function(response){
                    demo.showNotification('top','right', 'Agregado Satisfactoriamente.', 2);
                    clearQuickProductRow();
                    handleSoftRefresh();
                },
                complete:function(){
                  //$('#loadMe').modal('hide');
                }
            });
        }

        $(document).on('click', '.agregar_producto_pos_btn', function(e) {
            e.preventDefault();

            if (__isFlushingLineDrafts) {
                return;
            }

            if (hasPendingLineDrafts() || Object.keys(__lineUpdateTimers).length > 0) {
                runAfterDraftSync(function() {
                    agregarProductoSeleccionadoPos();
                });
                return;
            }

            agregarProductoSeleccionadoPos();
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
                    handleSoftRefresh();
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
                    handleSoftRefresh();
                }
            });
        });

        var POS_LINE_UPDATE_CONFIG = {
            cantidad: {
                selector: '.update_cantidad_factura',
                valueKey: 'cantidad',
                url: {!! json_encode(url('actualizar-cant-factura')) !!}
            },
            descuento: {
                selector: '.update_descuento_factura',
                valueKey: 'porcentaje_descuento',
                url: {!! json_encode(url('actualizar-desc-factura')) !!}
            },
            costoConIva: {
                selector: '.update_costo_con_iva',
                valueKey: 'costo_con_iva',
                url: {!! json_encode(url('actualizar-costo-con-iva')) !!}
            },
            costoSinIva: {
                selector: '.update_costo_sin_iva_u',
                valueKey: 'costo_sin_iva_u',
                url: {!! json_encode(url('actualizar-costo-sin-iva_u')) !!}
            }
        };

        var __lineUpdateTimers = {};
        var __lineUpdateTargets = {};
        var __lineDrafts = {};
        var __lineDraftSaved = {};
        var __lineDraftSeq = 0;
        var __isFlushingLineDrafts = false;

        function hasPendingTempRows() {
            if (typeof window.hasPendingTempRowCreates === 'function') {
                return window.hasPendingTempRowCreates();
            }
            return false;
        }

        function getLineUpdateConfig($input) {
            if ($input.hasClass('update_cantidad_factura')) return POS_LINE_UPDATE_CONFIG.cantidad;
            if ($input.hasClass('update_descuento_factura')) return POS_LINE_UPDATE_CONFIG.descuento;
            if ($input.hasClass('update_costo_con_iva')) return POS_LINE_UPDATE_CONFIG.costoConIva;
            if ($input.hasClass('update_costo_sin_iva_u')) return POS_LINE_UPDATE_CONFIG.costoSinIva;
            return null;
        }

        function buildLineUpdateKey(config, id, idproducto) {
            return config.valueKey + '|' + String(id || '') + '|' + String(idproducto || '');
        }

        function buildLineDraftEntry(inputEl) {
            var $input = $(inputEl);
            var config = getLineUpdateConfig($input);
            if (!config) {
                return null;
            }

            var id = $input.data('id');
            if (!id) {
                return null;
            }

            var idproducto = $input.data('producto');
            var value = $input.val();
            return {
                config: config,
                id: id,
                idproducto: idproducto,
                value: value,
                updateKey: buildLineUpdateKey(config, id, idproducto),
                seq: ++__lineDraftSeq
            };
        }

        function stageLineDraft(inputEl) {
            var entry = buildLineDraftEntry(inputEl);
            if (!entry) {
                return;
            }

            var lastSaved = __lineDraftSaved[entry.updateKey];
            if (lastSaved === entry.value) {
                delete __lineDrafts[entry.updateKey];
                return;
            }

            __lineDrafts[entry.updateKey] = entry;
        }

        function flushPendingLineDrafts(forceRefresh) {
            var draftKeys = Object.keys(__lineDrafts);
            if (!draftKeys.length) {
                return Promise.resolve();
            }

            // Evita carreras entre cambios de la misma linea (precio/cantidad),
            // enviando en el orden real de edicion en vez de paralelo.
            var orderedDrafts = draftKeys
                .map(function(key) {
                    var draft = __lineDrafts[key];
                    return {
                        key: key,
                        draft: draft,
                        seq: (draft && draft.seq) ? draft.seq : 0
                    };
                })
                .sort(function(a, b) {
                    return a.seq - b.seq;
                });

            var chain = Promise.resolve();
            orderedDrafts.forEach(function(item) {
                chain = chain.then(function() {
                    var payload = {
                        idsalesitem: item.draft.id,
                        idproducto: item.draft.idproducto
                    };
                    payload[item.draft.config.valueKey] = item.draft.value;

                    return $.ajax({
                        type: 'get',
                        url: item.draft.config.url,
                        dataType: 'json',
                        data: payload
                    }).then(function() {
                        __lineDraftSaved[item.key] = item.draft.value;
                    });
                });
            });

            return chain
                .then(function() {
                    __lineDrafts = {};
                    if (forceRefresh) {
                        handleSoftRefresh(true);
                    }
                })
                .catch(function(error) {
                    console.error('Error sincronizando borradores POS:', error);
                    throw error;
                });
        }

        function sendLineUpdate(inputEl, forceRefresh) {
            stageLineDraft(inputEl);
        }

        function hasPendingLineDrafts() {
            return Object.keys(__lineDrafts).length > 0;
        }

        function syncPendingDraftsBefore(action, skipTempRowsCheck) {
            if (typeof action !== 'function') {
                return;
            }

            if (!skipTempRowsCheck && typeof window.ensureAllTempRowsPersisted === 'function') {
                window.ensureAllTempRowsPersisted(function(ok) {
                    if (!ok) {
                        alert('No se pudo crear una linea nueva. Revise el producto y reintente.');
                        return;
                    }
                    syncPendingDraftsBefore(action, true);
                });
                return;
            }

            if (__isFlushingLineDrafts) {
                setTimeout(function() {
                    syncPendingDraftsBefore(action, true);
                }, 120);
                return;
            }

            if (hasPendingLineDrafts() || Object.keys(__lineUpdateTimers).length > 0 || hasPendingTempRows()) {
                runAfterDraftSync(action, { refreshAfterSync: false });
                return;
            }

            action();
        }

        function runAfterDraftSync(action, options) {
            var refreshAfterSync = true;
            if (options && options.refreshAfterSync === false) {
                refreshAfterSync = false;
            }

            if ((!options || options.skipTempRows !== true) && typeof window.ensureAllTempRowsPersisted === 'function') {
                window.ensureAllTempRowsPersisted(function(ok) {
                    if (!ok) {
                        alert('No se pudo crear una linea nueva. Revise el producto y reintente.');
                        return;
                    }

                    var nextOptions = $.extend({}, options || {}, { skipTempRows: true });
                    runAfterDraftSync(action, nextOptions);
                });
                return;
            }

            // Convierte cualquier cambio en debounce a borrador inmediato.
            Object.keys(__lineUpdateTimers).forEach(function(timerKey) {
                clearTimeout(__lineUpdateTimers[timerKey]);
                delete __lineUpdateTimers[timerKey];
                var pendingInput = __lineUpdateTargets[timerKey];
                delete __lineUpdateTargets[timerKey];
                if (pendingInput) {
                    sendLineUpdate(pendingInput, false);
                }
            });

            if (__isFlushingLineDrafts) {
                return;
            }

            if (!hasPendingLineDrafts() && !hasPendingTempRows()) {
                action();
                return;
            }

            __isFlushingLineDrafts = true;
            lockSubmitButton();

            flushPendingLineDrafts(refreshAfterSync)
                .then(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    action();
                })
                .catch(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    alert('No se pudieron guardar los cambios pendientes. Intente nuevamente.');
                });
        }

        function scheduleLineUpdate(inputEl, immediate, forceRefresh) {
            var $input = $(inputEl);
            var config = getLineUpdateConfig($input);
            if (!config) {
                return;
            }

            var id = $input.data('id');
            if (!id) {
                return;
            }

            var idproducto = $input.data('producto');
            var timerKey = buildLineUpdateKey(config, id, idproducto);

            if (__lineUpdateTimers[timerKey]) {
                clearTimeout(__lineUpdateTimers[timerKey]);
                delete __lineUpdateTimers[timerKey];
                delete __lineUpdateTargets[timerKey];
            }

            if (immediate) {
                sendLineUpdate(inputEl, forceRefresh);
                return;
            }

            __lineUpdateTimers[timerKey] = setTimeout(function() {
                delete __lineUpdateTimers[timerKey];
                delete __lineUpdateTargets[timerKey];
                sendLineUpdate(inputEl, false);
            }, 380);
            __lineUpdateTargets[timerKey] = inputEl;
        }

        $(document).on('input', '.update_cantidad_factura, .update_descuento_factura, .update_costo_con_iva, .update_costo_sin_iva_u', function() {
            scheduleLineUpdate(this, false);
            updateLineFromEditableFields(this);
            recalcFrontTotalsFromLines();
        });

        $(document).on('keydown', '.update_cantidad_factura, .update_descuento_factura, .update_costo_con_iva, .update_costo_sin_iva_u', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                scheduleLineUpdate(this, true, false);
                updateLineFromEditableFields(this);
                recalcFrontTotalsFromLines();
            }
        });

        $(document).on('blur', '.update_cantidad_factura, .update_descuento_factura, .update_costo_con_iva, .update_costo_sin_iva_u', function() {
            scheduleLineUpdate(this, true);
            updateLineFromEditableFields(this);
            recalcFrontTotalsFromLines();
        });

        $(document).on('click', '.eliminar_fila_factura, .btn-detalles-linea', function(event) {
            var $btn = $(this);

            if ($btn.data('skipDraftSync')) {
                $btn.removeData('skipDraftSync');
                return;
            }

            if ((!hasPendingLineDrafts() && !hasPendingTempRows()) || __isFlushingLineDrafts) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            runAfterDraftSync(function() {
                $btn.data('skipDraftSync', true);
                $btn.trigger('click');
            });
        });

        $(document).on('click', '#Agregar_producto', function(event) {
            if (__isFlushingLineDrafts) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            if (!hasPendingLineDrafts() && !hasPendingTempRows() && Object.keys(__lineUpdateTimers).length === 0) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            runAfterDraftSync(function() {
                if (typeof $ !== 'undefined' && $('#AddProductos').length) {
                    $('#AddProductos').modal('show');
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
                    handleSoftRefresh();
                }
            });
        });
    //omariena - 26-05-2021

// Manejador para el botón de envío
$(document).on("click", "#submit-button", function(event) {
    // Si el botón está habilitado, permite el envío del formulario
            if ($(this).prop('disabled')) {
        event.preventDefault(); // Evitar el comportamiento predeterminado si el botón está deshabilitado
                return;
    }

            if ((!hasPendingLineDrafts() && !hasPendingTempRows()) || __isFlushingLineDrafts) {
                return;
            }

            event.preventDefault();
            __isFlushingLineDrafts = true;
            lockSubmitButton();

            flushPendingLineDrafts(true)
                .then(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    var form = document.getElementById('form_factura');
                    if (form && typeof form.requestSubmit === 'function') {
                        form.requestSubmit(document.getElementById('submit-button'));
                    } else if (form) {
                        form.submit();
                    }
                })
                .catch(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    alert('No se pudieron guardar los cambios pendientes. Intente nuevamente.');
                });
});

        $(document).on('click', '#guardar-cambios-pos', function(event) {
            event.preventDefault();
            if (__isFlushingLineDrafts) {
                return;
            }

            if (!hasPendingLineDrafts()) {
                alert('No hay cambios pendientes por guardar.');
                return;
            }

            __isFlushingLineDrafts = true;
            lockSubmitButton();

            flushPendingLineDrafts(true)
                .then(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    alert('Cambios guardados correctamente.');
                })
                .catch(function() {
                    __isFlushingLineDrafts = false;
                    unlockSubmitButton();
                    alert('No se pudieron guardar los cambios pendientes. Intente nuevamente.');
                });
        });

$(document).on("blur", "#codigo_pos", function(event) {
    // Verificar si el evento se originó en el botón de envío
    if (event.relatedTarget && event.relatedTarget.id === 'submit-button') {
        return; // No hacer nada si el foco se mueve al botón de envío
    }

    event.preventDefault();
    var codigo_pos = $(this).val();
    var lector = $('#usa_lector').val();
    var balanza = $('#usa_balanza').val();

    if (codigo_pos.length > 0) {
        // Deshabilitar el botón de envío
        $('#submit-button').prop('disabled', true);

        // Realizar la lógica de búsqueda o colocación
        if (lector > 0) {
            if (balanza > 0) {
                buscarProducto(codigo_pos, lector, balanza);
            } else {
                colocarProducto(codigo_pos, lector, balanza);
            }
        } else {
            if (balanza > 0) {
                buscarProducto(codigo_pos, lector, balanza);
            } else {
                colocarProducto(codigo_pos, lector, balanza);
            }
        }


    }
});

        $('#input-numero_exoneracion').on("blur", function( e ) {
            e.preventDefault();
            var autorizacion = $(this).val();

            const f = new Date();
            var URL = 'https://api.hacienda.go.cr/fe/ex?autorizacion='+autorizacion;
            var tipo_exoneracion = $('#tipo_exoneracion').val();

            function setExoneracionConsultaEstado(texto, tipo) {
                var $estado = $('#estado_consulta_exoneracion');
                if (!$estado.length) {
                    $estado = $('<small id="estado_consulta_exoneracion" class="d-block mt-2"></small>');
                    $('#input-numero_exoneracion').after($estado);
                }

                $estado.removeClass('text-info text-success text-danger').addClass('text-' + (tipo || 'info'));
                $estado.text(texto || '');

                if (!texto) {
                    $estado.hide();
                } else {
                    $estado.show();
                }
            }

            if (tipo_exoneracion === '04') {
                setExoneracionConsultaEstado('Consultando validez de exoneracion...', 'info');
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
                            setExoneracionConsultaEstado('Validez de exoneracion confirmada.', 'success');
                            alert('Exoneracion Permitida');
                        }else{
                            $('#input-institucion').attr("readonly", true);
                            $('#input-porcentaje_exoneracion').attr("readonly", true);
                            setExoneracionConsultaEstado('No fue posible validar la exoneracion en Hacienda.', 'danger');
                            alert('El numero de la exoneracion no existe en la base de datos de hacienda');
                        }

                    },
                    error:function(response){
                        setExoneracionConsultaEstado('Error consultando validez de exoneracion.', 'danger');
                        alert('error en el servidor de hacienda, no se logro ubicar la informacion');
                    },
                    complete:function() {
                        setTimeout(function() {
                            setExoneracionConsultaEstado('', 'info');
                        }, 3000);
                    }
                });
            }else{
                $('#input-institucion').attr("readonly", false);
                $('#input-porcentaje_exoneracion').attr("readonly", false);
                setExoneracionConsultaEstado('', 'info');
            }



        });


        function finalizeExoneracionSave() {
            var $modal = $('#AddExoneracion');
            $modal.modal('hide');
            $('#form_exoneracion')[0].reset();
            $('#input-institucion').attr('readonly', false);
            $('#input-porcentaje_exoneracion').attr('readonly', false);
            $('#existe_exoneracion').val('01');
            setTimeout(function() {
                handleSoftRefresh();
            }, 120);
        }

        $(document).on("click", "#AgregarExoneracion" , function(event) {
            event.preventDefault();

            syncPendingDraftsBefore(function() {
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
                                        finalizeExoneracionSave();
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
                                        finalizeExoneracionSave();
                                    }
                                });
                            }
                        }
                    });
                }else{
                    alert('El Porcentaje de Exoneración es mayor al permitido');
                }
            });

        });


        $('#AddExoneracion').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');
            $(e.currentTarget).find('input[name="idsaleitem_exo"]').val(id);
        });

        // ARTICULO FLOTANTE
        $('#ModArticulo').on('show.bs.modal', function(e) {
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
                    $('#ModArticulo').modal('hide');
                    $('#form_flotante')[0].reset();
                    setTimeout(function() {
                        handleSoftRefresh();
                    }, 120);
                }
            });
        });




   //cambio

$(document).on("focus", "#efectivo_dev, #tarjeta_dev", function(event) {
    // Al recibir el foco, limpiar el campo para facilitar la entrada de un nuevo valor
    $(this).val(''); // Limpiar el campo
});

// Mantener el resto del código para calcular el cambio y formatear los campos
$(document).on("blur", "#efectivo_dev, #tarjeta_dev", function(event) {
    calcularCambio();
});

function calcularCambio() {
    // Obtener el total del documento
  // Obtiene el valor del primer campo y lo convierte a número, o 0 si no es válido  
var tot_pos_dev = parseFloat($('#tot_pos_dev').val()) || 0;   

// Obtiene el valor del segundo campo y lo convierte a número, o 0 si no es válido  
var tot_otros_cargos = parseFloat($('#tot_otros_cargos').val()) || 0;   

// Suma ambos valores  
var total_documento = tot_pos_dev + tot_otros_cargos;  

 

    // Obtener los valores de efectivo y tarjeta, asegurando que sean números
    var efectivo = parseFloat($('#efectivo_dev').val().replace(/\./g, '').replace(',', '.')) || 0; // Convertir a número
    var tarjeta = parseFloat($('#tarjeta_dev').val().replace(/\./g, '').replace(',', '.')) || 0; // Convertir a número

    // Calcular el total ingresado
    var total_ingresado = efectivo + tarjeta;

    // Calcular el cambio
    var cambio = 0; // Inicializar el cambio
    if (total_ingresado >= total_documento) {
        cambio = total_ingresado - total_documento; // Calcular el cambio solo si el total ingresado es mayor o igual al total del documento
    }

    // Mostrar el cambio en el campo correspondiente
    $('#cambio_dev').val(cambio.toFixed(2)); // Mostrar el cambio con dos decimales

    // Formatear los campos como números
    $('#efectivo_dev').val(efectivo.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#tarjeta_dev').val(tarjeta.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#cambio_dev').val(cambio.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}

// Formatear los campos al recibir el foco
$(document).on("focus", "#efectivo_dev, #tarjeta_dev, #cambio_dev", function(event) {
    // Al recibir el foco, eliminar el formato para facilitar la edición
    var value = $(this).val().replace(/\./g, '').replace(',', '.'); // Eliminar formato
    $(this).val(value); // Establecer el valor sin formato
});

// Formatear los campos al perder el foco
$(document).on("blur", "#efectivo_dev, #tarjeta_dev, #cambio_dev", function(event) {
    var value = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0; // Convertir el valor a número
    $(this).val(value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })); // Formatear el número
});
//cambio
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

        var __isSavingTipoDocumento = false;

        function guardarTipoDocumentoConSync(tipoDocumento, forceSave) {
            if (__isFlushingLineDrafts || __isSavingTipoDocumento) {
                return;
            }

            var $field = $('#tipo_documento');
            var tipo_documento = String(tipoDocumento || $field.val() || '');
            var ultimoEnviado = String($field.attr('data-last-sent') || '');

            if (!forceSave && tipo_documento === ultimoEnviado) {
                return;
            }

            var idsale = $('#idsale').val();
            var idcaja = $('#idcaja').val();
            var idcliente = $('#cliente').val();
            var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};

            syncPendingDraftsBefore(function() {
                __isSavingTipoDocumento = true;
                $.ajax({
                    type: 'get',
                    url: URL,
                    dataType: 'json',
                    data: { tipo_documento: tipo_documento, idsale: idsale, idcaja: idcaja, idcliente: idcliente },
                    success: function(response) {
                        $field.attr('data-last-sent', tipo_documento);
                        handleSoftRefresh();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cambiar el tipo de documento:', error);
                        alert('Ocurrió un error al cambiar el tipo de documento.');
                    },
                    complete: function() {
                        __isSavingTipoDocumento = false;
                    }
                });
            });
        }

        //ACTUALIZAR TIPO DE DOCUMENTO
        $('#tipo_documento').change(function(event) { //15-11-2023 se inserta el traer numero de factura a este evento y se comenta el evento de la linea 1490, se envia a la url el id caja, para recalcular el consecutivo de
         //factura que seigue, para que se actualice tanto, la el tipo de documento como el consecutivo
           event.preventDefault();
           traerNumFactura(APP_URL,APP_URL2);
           guardarTipoDocumentoConSync($(this).val(), false);
        });

function cambiarTipoDocumento(nuevoTipo, APP_URL, o2) {
    $('#tipo_documento').val(nuevoTipo);

    // Lógica que se ejecuta cuando se cambia el tipo de documento
    traerNumFactura(APP_URL, o2);
    guardarTipoDocumentoConSync(nuevoTipo, true);
}

      $('#cliente_serch').on('focus', function() {
        syncPendingDraftsBefore(function() {
            $('#submit-button').prop('disabled', true);
        });
      });

$(document).on("blur", "#cliente_serch", function(event) {
    event.preventDefault();

    if (__isFlushingLineDrafts) {
        return;
    }

    var nombre_cli = $(this).val();
    var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
    var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
    var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};

    syncPendingDraftsBefore(function() {
        if (!nombre_cli || String(nombre_cli).trim().length <= 0) {
            return;
        }

            $.ajax({
                type: 'get',
                url: URL,
                dataType: 'json',
                data: { nombre_cli: nombre_cli },
                success: function(response) {
                    console.log(response);
                    var arreglo = response['success'].length;
                    var tipo_documento = $('#tipo_documento').val();

                    function aplicarActividadCliente(clienteData) {
                        var codigoActividadCli = (clienteData['codigo_actividad'] || '').toString().trim();
                        var $inputActividad = $('#codigo_actividad');

                        // Actualiza identificacion para futuras cargas del datalist.
                        $('#ced_receptor_act').val(clienteData['num_id'] || '');

                        if (codigoActividadCli.length > 0 && codigoActividadCli !== '0') {
                            $inputActividad.val(codigoActividadCli);
                            $inputActividad.attr('data-last-sent', codigoActividadCli);
                        } else {
                            // Si no hay codigo en BD, fuerza recarga de opciones desde datalist/API.
                            $inputActividad.val('');
                            $inputActividad.removeAttr('data-last-sent');
                            $inputActividad.trigger('input');
                        }
                    }

                    if (arreglo > 0) {
                        if (tipo_documento === '01' || tipo_documento === '04') {
                            $('#cliente_serch').val(response['success'][0]['nombre']);
                            $('#cliente').val(response['success'][0]['idcliente']);
                            $('#ced_receptor').val(response['success'][0]['num_id']);
                            $('#exocli').val(response['success'][0]['exocli']);
                            $('#continuar').css("display", "");
                            $('#datos_internos').val(1);
                            aplicarActividadCliente(response['success'][0]);

                            var numId = response['success'][0]['num_id'];
                            var apiUrl = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + numId;

                            $.ajax({
                                type: 'get',
                                url: apiUrl,
                                dataType: 'json',
                                success: function(apiResponse) {
                                    try {
                                        console.log('Respuesta Hacienda:', apiResponse);

                                        if (!apiResponse || !apiResponse.situacion) {
                                            throw 'Respuesta invalida de Hacienda';
                                        }

                                        // Normalizar estado
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

                                        console.log('Estado:', estado);
                                        console.log('Actividades:', actividades);
                                        console.log('Situacion:', situacion);

                                        // Validacion completa
                                        var esValido = true;
                                        var motivos = [];

                                        if (!estadosValidos.includes(estado)) {
                                            esValido = false;
                                            motivos.push('Estado: ' + estado);
                                        }

                                        if (actividades.length === 0) {
                                            esValido = false;
                                            motivos.push('Sin actividades economicas');
                                        }

                                        if (!esValido) {
                                            alert(
                                                'El cliente no es valido para Factura Electronica.\n\n' +
                                                motivos.join('\n') +
                                                '\n\nSe generara como Tiquete.'
                                            );

                                            $('#tipo_documento').val('04'); // Tiquete
                                            cambiarTipoDocumento('04', APP_URL, o2);
                                        } else {
                                            $('#tipo_documento').val('01'); // Factura
                                            cambiarTipoDocumento('01', APP_URL, o2);
                                        }

                                        traerNumFactura(APP_URL, o2);
                                    } catch (validationError) {
                                        console.error('Error validando respuesta de Hacienda:', validationError);
                                        alert(
                                            'No se pudo validar en Hacienda.\n' +
                                            'Se generara como Tiquete por seguridad.'
                                        );
                                        $('#tipo_documento').val('04');
                                        cambiarTipoDocumento('04', APP_URL, o2);
                                        traerNumFactura(APP_URL, o2);
                                    }
                                },
                                error: function(err) {
                                    console.error('Error Hacienda:', err);
                                    alert(
                                        'No se pudo validar en Hacienda.\n' +
                                        'Se generara como Tiquete por seguridad.'
                                    );
                                    $('#tipo_documento').val('04');
                                    cambiarTipoDocumento('04', APP_URL, o2);
                                    traerNumFactura(APP_URL, o2);
                                }
                            });
                            $('#submit-button').prop('disabled', false);
                        }
                        if(tipo_documento === '09'){
                            $('#cliente_serch').val(response['success'][0]['nombre']);
                            $('#cliente').val(response['success'][0]['idcliente']);
                            $('#ced_receptor').val(response['success'][0]['num_id']);
                            $('#exocli').val(response['success'][0]['exocli']);
                            $('#continuar').css("display", "");
                            $('#datos_internos').val(1);
                            aplicarActividadCliente(response['success'][0]);
                             cambiarTipoDocumento('09', APP_URL, o2);
                            traerNumFactura(APP_URL, o2);
                        }

                        if(tipo_documento === '96'){
                            $('#cliente_serch').val(response['success'][0]['nombre']);
                            $('#cliente').val(response['success'][0]['idcliente']);
                            $('#ced_receptor').val(response['success'][0]['num_id']);
                            $('#exocli').val(response['success'][0]['exocli']);
                            $('#continuar').css("display", "");
                            $('#datos_internos').val(1);
                            aplicarActividadCliente(response['success'][0]);
                            cambiarTipoDocumento('96', APP_URL, o2);
                            traerNumFactura(APP_URL, o2);
                        }
                    } else {
                        $('#cliente_serch').val('');
                        $('#cliente').val(1);
                        $('#codigo_actividad').val('');
                        $('#codigo_actividad').removeAttr('data-last-sent');
                    }
                }
            });
    });
});
       ///fin
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
        $('#medio_pago option').on('click', function() {
            var selectedMediosDePago = $('#medio_pago').val(); // Obtiene los valores seleccionados
            // Tu lógica aquí...
            console.log("Medios de pago seleccionados:", selectedMediosDePago);
            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-mediopago-pos-new')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{medio_pago:selectedMediosDePago, idsale:idsale},
                success:function(response){
                    demo.showNotification('top','right', 'Actualizado correctamente.');
                    setTimeout(function(){
                        handleSoftRefresh();
                    }, 1000); // 1000 milisegundos = 1 segundo
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

        $('#cantidad_pos_envia').change(function() {
            if ($(this).val() > 0) {
                $('.agregar_producto_pos_btn').css( "display", "block");
            }else{
                alert('La cantidad debe ser mayor a 0');
                $('.agregar_producto_pos_btn').css( "display", "none");
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
                    handleSoftRefresh();
                }
            });
        });
$(document).on("blur", ".update_nombre_producto_posw" , function(event) {
            var id = $(this).data('id');
            var nombre_producto_pos = $(this).val();

            var URL = {!! json_encode(url('actualizar-descripcion-factura')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{idsalesitem:id, nombre_producto_pos:nombre_producto_pos},
                success:function(response){
                    handleSoftRefresh();
                }
            });
        });

        $(document).on("blur", "#observaciones" , function(event) {
            event.preventDefault();

            if (__isFlushingLineDrafts || __isSavingObservaciones) {
                return;
            }

            var $field = $(this);
            var observacion = $field.val();
            var ultimoEnviado = String($field.attr('data-last-sent') || '');

            if (observacion === ultimoEnviado) {
                return;
            }

            var idsale = $('#idsale').val();
            var URL = {!! json_encode(url('editar-observacion-pos')) !!};
            syncPendingDraftsBefore(function() {
                __isSavingObservaciones = true;
                $.ajax({
                    type:'get',
                    url: URL,
                    dataType: 'json',
                    data:{observacion:observacion, idsale:idsale},
                    success:function(response){
                        $field.attr('data-last-sent', observacion);
                        handleSoftRefresh();
                    },
                    complete:function(){
                        __isSavingObservaciones = false;
                    }
                });
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
                    handleSoftRefresh();
                }
            });
        });

        // Evento al confirmar en el modal
        /*$(document).on('click', '#confirmarMediosPago', function() {
            var mediosPagoMontos = {};
            var totalIngresado = 0;


            $('.monto-medio-pago').each(function() {
                var medioPagoId = $(this).data('medio-pago-id');
                var monto = parseFloat($(this).val());
                if (!isNaN(monto) && monto > 0) {
                    mediosPagoMontos[medioPagoId] = monto;
                    totalIngresado += monto; // Sumar el monto
                }
            });

            var totalComprobante = parseFloat($('#tot_pos_dev').val());

            // Validar que la suma sea igual al total de la factura
            if (totalIngresado !== totalComprobante) {
                alert('La suma de los montos debe ser igual al total de la factura de ' + totalComprobante);
                return; // Salir si no es válido
            }


            // Si es correcto, almacenar los montos en un campo oculto
            $('input[name="medios_pago"]').val(JSON.stringify(mediosPagoMontos)); // Serializar los montos seleccionados para enviarlos
            document.getElementById('form_factura').submit();

            $('#form_factura').submit(); // Envía el formulario

        });*/
$(document).on('click', '#confirmarMediosPago', function() {  
    var mediosPagoMontos = {};  
    var totalIngresado = 0;  

    $('.monto-medio-pago').each(function() {  
        var medioPagoId = $(this).data('medio-pago-id');  
        var monto = parseFloat($(this).val());  

        // Verificar si el monto es válido y mayor a 0  
        if (!isNaN(monto) && monto > 0) {  
            // Agregar el monto al objeto  
            mediosPagoMontos[medioPagoId] = {  
                monto: monto  
            };  
            totalIngresado += monto; // Sumar el monto  
            // Obtener la referencia y asignar null si está vacía  
            var referencia = $(`#referencia_${medioPagoId}`).val();  
            mediosPagoMontos[medioPagoId].referencia = referencia === "" ? null : referencia;  
        }  
    });  

    var totalComprobante = parseFloat($('#tot_pos_dev').val());  
    var valida = totalComprobante - totalIngresado;  
    // Validar que la suma sea igual al total de la factura  
    if (valida > 1) {  
        alert('La suma de los montos debe ser igual al total de la factura de ' + totalComprobante);  
        return; // Salir si no es válido  
    }  

    // Si es correcto, almacenar los montos en un campo oculto  
    $('input[name="medios_pago"]').val(JSON.stringify(mediosPagoMontos)); // Serializar los montos seleccionados para enviarlos  

    // Mostrar overlay para bloquear la interacción
    $('#overlay').show();  // Muestra el overlay

    // Envío del formulario
    document.getElementById('form_factura').submit(); // Envía el formulario

    // Si decides usar AJAX, puedes implementar así.
    // $.ajax({
    //     url: $('#form_factura').attr('action'), // URL del formulario
    //     type: 'POST',
    //     data: $('#form_factura').serialize(), // Datos del formulario
    //     success: function(response) {
    //         $('#overlay').hide(); // Ocultar el overlay si usas AJAX
    //         // Manejar la respuesta
    //     },
    //     error: function(error) {
    //         $('#overlay').hide(); // Ocultar el overlay en error si usas AJAX
    //         console.error(error);
    //     }
    // });
});
    });

    function submitResultdd() {
        var totalComprobante = parseFloat($('#tot_pos_dev').val()); // Obtener el total de la factura.
var nombre_cli = $('#cliente_serch').val();
var URL = {!! json_encode(url('buscar-medios-pagos')) !!};

// Usar totalComprobante para formatear el total
const formattedTotal = new Intl.NumberFormat('es-ES', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
}).format(totalComprobante); // Formatear el total

        // Obtener los medios de pago y sus montos
        $.ajax({
            url: URL,
            type: 'GET',
            dataType: 'json',
            success: function(mediosPago) {
                var modalContent = '<div class="modal fade" id="mediosPagoModal" tabindex="-1" role="dialog">';
                modalContent += '<div class="modal-dialog modal-lg" role="document">';
                modalContent += '<div class="modal-content">';
                modalContent += '<div class="modal-header">';
                modalContent += `<h2 class="modal-title">Total Doc: ${formattedTotal}</h2>`;
                modalContent += `<h5 class="modal-title">Ingrese los montos por medio de pago para el cliente ${nombre_cli}</h5>`;
                modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                modalContent += '<span aria-hidden="true">&times;</span>';
                modalContent += '</button>';
                modalContent += '</div>';
                modalContent += '<div class="modal-body">';

                // Iterar sobre todos los medios de pago
                mediosPago.forEach(function(medioPago) {
                    modalContent += '<div class="form-group">';
                    modalContent += `<label for="monto_${medioPago.id}">${medioPago.nombre} (${medioPago.codigo}):</label>`;
                    modalContent += `<div class="d-flex align-items-start">`; // Flex container for inputs
                    modalContent += `<input type="number" class="form-control monto-medio-pago mr-2" id="monto_${medioPago.id}" data-medio-pago-id="${medioPago.id}" oninput="updateTotal()">`;

                    // Agregar campo de referencia si el método de pago es el adecuado
                    if (['03', '02', '04', '06'].includes(medioPago.codigo)) {
                        modalContent += `<div class="referencia-group" id="referencia-group-${medioPago.id}" style="display: none;">`;
                        modalContent += `<input type="text" class="form-control" id="referencia_${medioPago.id}" placeholder="Referencia" style="width: 150px;">`; // Width for reference input
                        modalContent += '</div>';
                    }

                    modalContent += `</div>`; // Cierre de flex container
                    modalContent += '</div>'; // Cierre de form-group
                });

                modalContent += '<div class="form-group">';
                modalContent += '<label for="total_ingresado">Diferencia:</label>';
                modalContent += '<input type="number" class="form-control" id="total_ingresado" disabled>';
                modalContent += '</div>';

                modalContent += '</div>';
                modalContent += '<div class="modal-footer">';
                modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>';
                modalContent += '<button type="button" class="btn btn-primary" id="confirmarMediosPago" disabled >Confirmar</button>';
                modalContent += '</div>';
                modalContent += '</div>';
                modalContent += '</div>';
                modalContent += '</div>';

                // Agregar el modal al DOM
                $('body').append(modalContent);

                // Mostrar el modal
                $('#mediosPagoModal').modal('show');

                // Actualiza la suma total de montos al cambiar los inputs
                updateTotal();
            },
            error: function(error) {
                console.error(error);
                alert('Error al obtener los medios de pago');
            }
        });

        return false; // Evitar la acción por defecto del formulario
    }
    function submitResult() {
    var totalComprobante = parseFloat($('#tot_pos_dev').val()); // Obtener el total de la factura.
    var nombre_cli = $('#cliente_serch').val();
    var URL = {!! json_encode(url('buscar-medios-pagos')) !!};

    // Usar totalComprobante para formatear el total
    const formattedTotal = new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(totalComprobante); // Formatear el total

    // Obtener los medios de pago y sus montos
    $.ajax({
        url: URL,
        type: 'GET',
        dataType: 'json',
        success: function(mediosPago) {
            var modalContent = '<div class="modal fade" id="mediosPagoModal" tabindex="-1" role="dialog">';
            modalContent += '<div class="modal-dialog modal-sm" role="document">'; // Cambiado a modal-sm para un modal más compacto
            modalContent += '<div class="modal-content">';
            modalContent += '<div class="modal-header">';
            modalContent += `<h5 class="modal-title m-0">${nombre_cli}</h5>`; // Título más compacto
             modalContent += `<h5 class="modal-title"><b>Total Doc: ${formattedTotal}</b></h5>`;
            modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            modalContent += '<span aria-hidden="true">&times;</span>';
            modalContent += '</button>';
            modalContent += '</div>';
            modalContent += '<div class="modal-body">';

            // Iterar sobre todos los medios de pago
            mediosPago.forEach(function(medioPago) {
                modalContent += '<div class="form-group mb-1">'; // Clase mb-1 para márgenes pequeños
                modalContent += `<label for="monto_${medioPago.id}">${medioPago.nombre} (${medioPago.codigo}):</label>`;
                modalContent += `<input type="number" class="form-control monto-medio-pago" id="monto_${medioPago.id}" data-medio-pago-id="${medioPago.id}" oninput="updateTotal()">`; // Mantener un solo input
                // Agregar campo de referencia si el método de pago es el adecuado
                if (['03', '02', '04', '06'].includes(medioPago.codigo)) {
                    modalContent += `<div class="referencia-group" id="referencia-group-${medioPago.id}" style="display: none;">`;
                    modalContent += `<input type="text" class="form-control mt-1" id="referencia_${medioPago.id}" placeholder="Referencia" style="width: 100%;">`; // Width responsive
                    modalContent += '</div>';
                }
                modalContent += '</div>'; // Cierre de form-group
            });

            modalContent += '<div class="form-group mb-1">';
            modalContent += '<label for="total_ingresado">Diferencia:</label>';
            modalContent += '<input type="number" class="form-control" id="total_ingresado" disabled>';
            modalContent += '</div>';

            modalContent += '</div>';
            modalContent += '<div class="modal-footer">';
            modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>';
            modalContent += '<button type="button" class="btn btn-primary" id="confirmarMediosPago" disabled >Confirmar</button>';
            modalContent += '</div>';
            modalContent += '</div>';
            modalContent += '</div>';
            modalContent += '</div>';

            // Agregar el modal al DOM
            $('body').append(modalContent);

            // Mostrar el modal
            $('#mediosPagoModal').modal('show');

            // Actualiza la suma total de montos al cambiar los inputs
            updateTotal();
        },
        error: function(error) {
            console.error(error);
            alert('Error al obtener los medios de pago');
        }
    });

    return false; // Evitar la acción por defecto del formulario
}
    // Función para actualizar el total ingresado
    function updateTotal() {
        var totalIngresado = 0;
        $('.monto-medio-pago').each(function() {
            var monto = parseFloat($(this).val());
            if (!isNaN(monto)) {
                totalIngresado += monto; // Sumar el monto
            }

            // Mostrar el campo de referencia solo si el monto es mayor a 0
            var medioPagoId = $(this).data('medio-pago-id');
            if (['03', '02', '04', '06'].includes($(this).closest('.form-group').find('label').text().match(/\(([^)]+)\)/)[1])) {
                if (monto > 0) {
                    $(`#referencia-group-${medioPagoId}`).show();
                } else {
                    $(`#referencia-group-${medioPagoId}`).hide();
                }
            }
        });
         var totalComprobante = parseFloat($('#tot_pos_dev').val());
          var tot_otros_cargos = parseFloat($('#tot_otros_cargos').val()) || 0;   
         var valida = totalComprobante + tot_otros_cargos - totalIngresado  ; 
 
        $('#total_ingresado').val(valida); // Actualizar el campo mostrado en el modal

        // Verificar que el total ingresado sea igual al total de la factura y habilitar o deshabilitar el botón
       
        var botonConfirmar = $('#confirmarMediosPago');
        
        
            // Validar que la suma sea igual al total de la factura  
           if (valida >= -1 && valida <= 1) { 

        //if (totalIngresado === totalComprobante) {
          botonConfirmar.prop('disabled', false); // Habilitar el botón si suma igual al total
       } else {
           botonConfirmar.prop('disabled', true); // Deshabilitar si no es igual
       }
    }
    /*function submitResult() {
        var nombre_cli = $('#cliente_serch').val();
        if (confirm("¿Desea procesar la factura para el cliente " + nombre_cli + " ?") == false) {
            return false;
        } else {
            $("#loadMe").modal({
                backdrop: "static",
                keyboard: false,
                show: true
            });
            return true;
        }
    }*/



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
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                        $('#cantidad_pos_envia').focus();

                        if (balanza === 0) {
                            $('#cantidad_pos_envia').val(1);
                            $('.agregar_producto_pos_btn').css( "display", "block");
                            $('.agregar_producto_pos_btn:visible').first().trigger("click");
                        }else{
                            $('.agregar_producto_pos_btn').css( "display", "block");
                            $('.agregar_producto_pos_btn:visible').first().trigger("click");
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

$(document).ready(function() {
    var $inputActividad = $('#codigo_actividad');
    var $dataList = $('#codigo_actividad_list');
    var actividadCacheCargada = false;
    var actividadCargando = false;

    function resetActividadList() {
        $dataList.empty().append('<option value="Sin Actividad Definida"></option>');
    }

    function cargarActividadesCliente(forceReload) {
        var valorActual = ($inputActividad.val() || '').trim();
        var requiereCarga = (valorActual === '' || valorActual === '0');

        if (!forceReload && (!requiereCarga || actividadCacheCargada || actividadCargando)) {
            return;
        }

        var idReceptor = ($('#ced_receptor_act').val() || '').trim();
        if (!idReceptor) {
            resetActividadList();
            return;
        }

        actividadCargando = true;
        resetActividadList();
        $dataList.append('<option value="Cargando..."></option>');

        $.ajax({
            type: 'GET',
            url: 'https://api.hacienda.go.cr/fe/ae?identificacion=' + idReceptor,
            dataType: 'json'
        })
        .done(function(response) {
            resetActividadList();

            if (response && typeof response === 'object' && Array.isArray(response.actividades)) {
                response.actividades.forEach(function(act) {
                    var valor = act.codigo || '';
                    var descripcion = act.descripcion || '';
                    if (valor) {
                        $dataList.append('<option value="' + valor + (descripcion ? ' - ' + descripcion : '') + '"></option>');
                    }
                });
            }

            actividadCacheCargada = true;
        })
        .fail(function() {
            resetActividadList();
            actividadCacheCargada = false;
        })
        .always(function() {
            actividadCargando = false;
        });
    }

    // Carga inicial cuando el campo llega vacio o con 0.
    cargarActividadesCliente(false);

    // Si el usuario borra el contenido, recargar opciones.
    $inputActividad.on('input', function() {
        var valor = ($(this).val() || '').trim();
        if (valor === '' || valor === '0') {
            actividadCacheCargada = false;
            cargarActividadesCliente(true);
            return;
        }

        // Si el valor coincide exactamente con una opcion del datalist, persistir de inmediato.
        var coincideOpcion = false;
        $dataList.find('option').each(function() {
            var opcion = ($(this).val() || '').trim();
            if (opcion && opcion === valor) {
                coincideOpcion = true;
                return false;
            }
        });

        if (coincideOpcion && typeof guardarCodigoActividadCliente === 'function') {
            guardarCodigoActividadCliente(valor);
        }
    });

    // Al enfocar con el campo vacio, asegurar que existan opciones para elegir.
    $inputActividad.on('focus', function() {
        var valor = ($(this).val() || '').trim();
        if (valor === '' || valor === '0') {
            cargarActividadesCliente(false);
        }
    });
}); 




</script>
@endsection
