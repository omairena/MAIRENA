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
                            <button class="btn btn-sm btn-success" type="button" id="agregar_producto_pos" style="display: none;">+</button>
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
                                        <button class="btn btn-success" type="submit" id="agregar_producto_pos" style="display: none;">Agregar Producto</button>
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
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleRow('{{ $sale_i->idsalesitem }}')">+ Detalles</button>
                   
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

<div class="mb-1">
  <button type="button" id="agregar_fila" class="btn btn-success btn-sm">
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
                location.reload(); // Recargar la página si es necesario
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
        // Función para agregar una nueva fila
        $('#agregar_fila').click(function() {
            var newRow = `
                <tr>
                    <td colspan="5">
                        <input type="text" name="nombre_producto_pos1" class="form-control form-control-alternative update_nombre_producto_pos1" data-id="" data-producto="">
                        <button type="button" class="btn btn-info btn-sm btn-icon modificar_flotante" data-target="#ModArticulo" data-toggle="modal">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                            <i class="far fa-trash-alt"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-sm btn-icon agregar_exoneracion" data-target="#AddExoneracion" data-toggle="modal">
                            <i class="fas fa-file-alt"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleRow('')">+ Detalles</button>
                    </td>
                    <td class="text-right">
                        <input type="number" name="cantidad" class="form-control form-control-alternative update_cantidad_factura" data-id="">
                    </td>
                    @if(Auth::user()->config_u[0]->p_siva == 1)
                    <td class="text-right">
                        <input type="number" step="any" name="costo_sin_iva_u" class="form-control form-control-alternative update_costo_sin_iva_u" data-id="">
                    </td>
                    <td class="text-right">
                        <input type="number" name="descuento" class="form-control form-control-alternative update_descuento_factura" data-id="">
                    </td>
                    @endif
                    <td class="text-right">
                        <input type="number" step="any" name="costo_con_iva" class="form-control form-control-alternative update_costo_con_iva" data-id="">
                    </td>
                </tr>
                <tr class="hidden">
                    <td colspan="4">Cod: </td>
                    <td class="text-right">IVA = 0.00</td>
                    <td class="text-right">Exonerado?: No</td>
                    <td class="td-actions text-right">
                        <button type="button" class="btn btn-danger btn-sm btn-icon eliminar_fila_factura">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tabla_productos tbody').append(newRow);
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
                }
            });
        });

        // Guardar el producto en la base de datos al salir del campo de entrada
        $(document).on('blur', '[id^="nombre_producto_pos1"], .update_nombre_producto_pos1', function() {
            var $row = $(this).closest('tr');
            var idproducto = $(this).data('id');
            var cantidad = $row.find('.update_cantidad_factura').val();
            var URL = {!! json_encode(url('agregar-linea-factura')) !!};

            if (idproducto) {
                $.ajax({
                    type: 'get',
                    url: URL,
                    dataType: 'json',
                    data: {
                        sales_item: idproducto,
                        idsale: $('#idsale').val(), // Asegúrate de que este ID esté disponible
                        cantidad: cantidad,
                    },
                    success: function(response) {
                       location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al agregar el producto:', error);
                    }
                });
            } else {
                alert('Primero debes seleccionar un producto.');
            }
        });

        // Manejar la eliminación de filas
        $(document).on('click', '.eliminar_fila_factura', function() {
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
   <option value="09" {{ (old('tipo_documento', $sales->tipo_documento ?? '') == '09' ? 'selected="selected"' : '') }}>Fáctura Electrónica de Exportación</option>
</select>

@include('alerts.feedback', ['field' => 'tipo_documento'])

  <input hidden="true"  name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', $sales->tipo_documento ?? '') }}" required>
</div>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>

                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="{{ $usuario->nombre }}">
                                    
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                </div>
                                  <!--<div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad Cliente') }} &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                                        </a></label>
                                        
                                         <input type="hidden" name="ced_receptor_act" id="ced_receptor_act" class="form-control form-control-alternative{{ $errors->has('ced_receptor_act') ? ' is-invalid' : '' }}" value="{{ $usuario->num_id }}">
                                    <select class="form-control form-control-alternative" id="codigo_actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" >
                                        <option value="{{ $usuario->codigo_actividad }}"> {{ $usuario->codigo_actividad }} </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'codigo_actividad'])
                                </div>-->
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
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "asc" ]]
            }
        );
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


 $('#codigo_actividad').change(function() {
          var codigo_actividad = $(this).val();
          var cliente = $('#cliente').val();
          var URL = {!! json_encode(url('modificar-actividad-cliente')) !!};
          $.ajax({
            type:'get',
            url: URL,
            dataType: 'json',
            data:{codigo_actividad:codigo_actividad,cliente:cliente},
            success:function(response){
              //console.log(response);
              location.reload();
            }
          });
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
              var monto_total =  null;
              var URL = {!! json_encode(url('agregar-linea-factura')) !!};
              $.ajax({
                  type:'get',
                  url: URL,
                  dataType: 'json',
                  data:{sales_item:sales_item, idsale:idsale, monto_total:monto_total, cantidad:cantidad},
                  success:function(response){
                    location.reload();
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
                location.reload();
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
// Deshabilitar el botón de envío cuando el input tiene el foco
$(document).on("focus", ".update_costo_con_iva", function(event) {
    $('#submit-button').prop('disabled', true);
});

// Manejar el evento blur para realizar la llamada AJAX
$(document).on("blur", ".update_costo_con_iva", function(event) {
    var id = $(this).data('id');
    var idproducto = $(this).data('producto');
    var costo_con_iva = $(this).val();
    var URL = {!! json_encode(url('actualizar-costo-con-iva')) !!};

    // Realizar la llamada AJAX
    $.ajax({
        type: 'get',
        url: URL,
        dataType: 'json',
        data: { idsalesitem: id, idproducto: idproducto, costo_con_iva: costo_con_iva },
        success: function(response) {
            // Manejar la respuesta de la llamada AJAX
            console.log(response);
            // Refrescar la página después de la actualización
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error en la llamada AJAX:", error);
            // Habilitar el botón en caso de error
            $('#submit-button').prop('disabled', false);
        }
    });
});

// Manejador para el botón de envío
$(document).on("click", "#submit-button", function(event) {
    // Si el botón está habilitado, permite el envío del formulario
    if (!$(this).prop('disabled')) {
        // Aquí puedes agregar cualquier lógica adicional antes de enviar el formulario
        // Por ejemplo, puedes validar el formulario aquí si es necesario
        // Si todo está bien, el formulario se enviará automáticamente
    } else {
        event.preventDefault(); // Evitar el comportamiento predeterminado si el botón está deshabilitado
    }
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

            const f = new Date();
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

        //ACTUALIZAR TODOS LOS CAMPOS ON BLUR
         $('#tipo_documento').change(function(event) { //15-11-2023 se inserta el traer numero de factura a este evento y se comenta el evento de la linea 1490, se envia a la url el id caja, para recalcular el consecutivo de
         //factura que seigue, para que se actualice tanto, la el tipo de documento como el consecutivo
           traerNumFactura(APP_URL,APP_URL2);
           event.preventDefault();
           var tipo_documento = $(this).val();
           var idsale = $('#idsale').val();
            var idcaja = $('#idcaja').val();
            var idcliente = $('#cliente').val();
            // var numero_documento = $('#numero_documento').val();
           var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data: { tipo_documento: tipo_documento, idsale: idsale, idcaja: idcaja, idcliente: idcliente },
               success:function(response){
               location.reload();
                }
            });
        });
function cambiarTipoDocumento(nuevoTipo, APP_URL, o2) {
    $('#tipo_documento').val(nuevoTipo);
    $('#tipo_documento').trigger('change'); // Esto activará el evento change

    // Lógica que se ejecuta cuando se cambia el tipo de documento
    traerNumFactura(APP_URL, o2);
    var tipo_documento = $('#tipo_documento').val();
    var idsale = $('#idsale').val();
    var idcaja = $('#idcaja').val();
    var idcliente = $('#cliente').val();
    var URL = {!! json_encode(url('editar-tipodoc-pos')) !!};

    $.ajax({
        type: 'get',
        url: URL,
        dataType: 'json',
        data: { tipo_documento: tipo_documento, idsale: idsale, idcaja: idcaja, idcliente: idcliente },
        success: function(response) {
            location.reload(); // Considera si realmente necesitas recargar la página
        },
        error: function(xhr, status, error) {
            console.error('Error al cambiar el tipo de documento:', error);
            alert('Ocurrió un error al cambiar el tipo de documento.'); // Mensaje de error para el usuario
        }
    });
}

      $('#cliente_serch').on('focus', function() {
    $('#submit-button').prop('disabled', true);
      });

$(document).on("blur", "#cliente_serch", function(event) {
    event.preventDefault();
    var nombre_cli = $(this).val();
    var URL = {!! json_encode(url('buscar-cliente-pos')) !!};
    var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
    var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};

    if ($(this).val().length <= 0) {
        // No hacer nada si el campo está vacío
    } else {
        $.ajax({
            type: 'get',
            url: URL,
            dataType: 'json',
            data: { nombre_cli: nombre_cli },
            success: function(response) {
                console.log(response);
                var arreglo = response['success'].length;
                var tipo_documento = $('#tipo_documento').val();

                if (arreglo > 0) {
                    if (tipo_documento === '01' || tipo_documento === '04') {
                        $('#cliente_serch').val(response['success'][0]['nombre']);
                        $('#cliente').val(response['success'][0]['idcliente']);
                        $('#ced_receptor').val(response['success'][0]['num_id']);
                        $('#exocli').val(response['success'][0]['exocli']);
                        $('#continuar').css("display", "");
                        $('#datos_internos').val(1);

                        var numId = response['success'][0]['num_id'];
                        var apiUrl = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + numId;

                        $.ajax({
                            type: 'get',
                            url: apiUrl,
                            dataType: 'json',
                            success: function(apiResponse) {
                                var estado = apiResponse.situacion.estado;
                                if (estado !== 'Inscrito' && estado !== 'Inscrito de Oficio') {
                                    alert('El estado del cliente no es válido para la emision de Facturas Electronicas, por lo tanto cambiamos el tipo de documento a Tiquete. El Estado actual Tributario del cliente es: ' + estado);
                                    cambiarTipoDocumento('04', APP_URL, o2);
                                } else {
                                    $('#tipo_documento').val('01');
                                    cambiarTipoDocumento('01', APP_URL, o2);
                                    traerNumFactura(APP_URL, o2);
                                }
                            },
                            error: function() {
                                alert('Error al consultar el estado en Hacienda. El estado del cliente no es válido para la emision de Facturas Electronicas, por lo tanto cambiamos el tipo de documento a Tiquete.');
                                cambiarTipoDocumento('04', APP_URL, o2);
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
                        cambiarTipoDocumento('96', APP_URL, o2);
                        traerNumFactura(APP_URL, o2);
                    }
                } else {
                    $('#cliente_serch').val('');
                    $('#cliente').val(1);
                }
            }
        });
    }
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
                        location.reload();
                    }, 1000); // 1000 milisegundos = 1 segundo
                }

            });
         });

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

$(document).ready(function() { var $inputActividad = $('#codigo_actividad'); var valorActual = $inputActividad.val();
// Si el valor es 0 o está vacío, consultar la API para obtener actividades 
if (valorActual === '0' || !valorActual || valorActual === '') { var idReceptor = $('#ced_receptor_act').val() || ''; 
if (!idReceptor) { // Si no tienes id, dejar la datalist con la opción por defecto y salir 
var dataList = $('#codigo_actividad_list'); dataList.empty().append('<option value="Sin Actividad Definida"></option>'); return; } 
// Preparar datalist 
var dataList = $('#codigo_actividad_list'); dataList.empty(); dataList.append('<option value="Sin Actividad Definida"></option>'); 
var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + idReceptor; 
// Indicador de carga 
dataList.append('<option value="Cargando..."></option>'); 
$.ajax({ type: 'GET', url: URL, dataType: 'json', success: function(response) { 
    // Limpiar carga 
    dataList.empty(); dataList.append('<option value="Sin Actividad Definida"></option>'); 
    if (response && typeof response === 'object' && Array.isArray(response.actividades)) { if (response.actividades.length > 0) { response.actividades.forEach(function(act) { var valor = act.codigo; var descripcion = act.descripcion ?? ''; 
    // En datalist, el value es lo que se escribe 
    dataList.append('<option value="' + valor + (descripcion ? ' - ' + descripcion : '') + '"></option>'); }); } } 
    // Restaurar valor anterior si existe 
    if (valorActual && valorActual !== '0') { $inputActividad.val(valorActual); } else { $inputActividad.val(''); } }, error: function() { dataList.empty().append('<option value="Sin Actividad Definida"></option>'); } }); } }); 


</script>
@endsection
