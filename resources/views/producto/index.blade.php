@extends('layouts.app', ['page' => __('Todos los Productos'), 'pageSlug' => 'allproductos'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
</head>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Productos') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('productos.create') }}" class="btn btn-sm btn-primary">{{ __('Agregar Producto') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="producto_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Código') }}</th>
                                <th scope="col">{{ __('Código Cabys') }}</th>
                                <th scope="col">{{ __('Nombre') }}</th>
                                <th scope="col">{{ __('Cantidad Stock') }}</th>
                                <th scope="col">{{ __('Costo') }}</th>
                                <th scope="col">{{ __('Porcentaje Utilidad') }}</th>
                                <th scope="col">{{ __('Unidad Medida') }}</th>
                                <th scope="col">{{ __('Porcentaje de Impuesto') }}</th>
                                <th scope="col">{{ __('Precio sin Impuesto') }}</th>
                                <th scope="col">{{ __('Precio Final') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                    <tr>
                                        <td>{{ $producto->codigo_producto }}</td>
                                        <td>{{ $producto->codigo_cabys }}</td>
                                        <td>{{ $producto->nombre_producto }}</td>
                                        <td>{{ $producto->cantidad_stock }}</td>
                                        <td>{{ $producto->costo }}</td>
                                        <td>{{ $producto->utilidad_producto }}</td>
                                        <td>{{ $producto->productos_unidad[0]->simbolo }}</td>
                                        <td>{{ $producto->porcentaje_imp }} %</td>
                                        <td>{{ $producto->precio_sin_imp }}</td>
                                        <td>{{ $producto->precio_final }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ route('productos.edit', $producto->idproducto) }}" class="dropdown-item">{{ __('Editar Producto') }}</a>
                                                    <a href="{{ route('productos.show', $producto->idproducto) }}" class="dropdown-item">{{ __('Ver Inventario') }}</a>
                                                    <a href="{{ route('productos.cabys', $producto->idproducto) }}" class="dropdown-item">{{ __('Asignar Cabys') }}</a>
                                                    <a href="{{ route('productos.deleted', $producto->idproducto) }}" class="dropdown-item">{{ __('Eliminar') }}</a>
                                                    <a href="{{ route('productos.duplicar', $producto->idproducto) }}" class="dropdown-item">{{ __('Duplicar Producto') }}</a>
                                                    
                                                     
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#producto_datatable').DataTable();
    });
</script>
@endsection
