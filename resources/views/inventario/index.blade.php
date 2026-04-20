@extends('layouts.app', ['page' => __('Movimiento de Inventario'), 'pageSlug' => 'inventarioAll'])
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
                            <h4 class="card-title">{{ __('Movimiento de Inventario') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('inventario.create') }}" class="btn btn-sm btn-primary">{{ __('Nuevo Movimiento') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter" id="inventario_datatable">
                            <thead class=" text-primary">
                                <th scope="col" class="text-center">{{ __('# Movimiento') }}</th>
                            	<th scope="col" class="text-center">{{ __('# Documento') }}</th>
                                <th scope="col" class="text-center">{{ __('Proveedor') }}</th>
                                <th scope="col" class="text-center">{{ __('Tipo de Movimiento') }}</th>
                                <th scope="col" class="text-center">{{ __('Observaciones') }}</th>
                                <th scope="col" class="text-center">{{ __('Estatus del Movimiento') }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($inventario as $inv)
                                    <?php 
                                        if ($inv->tipo_movimiento === 1) {
                                            $flecha = "<i class='fas fa-angle-double-up' style='color: #04f704;'></i> Entrada";
                                        }else{
                                            $flecha = "<i class='fas fa-angle-double-down' style='color: #f70404;'></i> Salida";
                                        }

                                        if ($inv->estatus_movimiento === 1) {
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Proceso</button>";
                                        }else{
                                            $estatus ="<button  type='button' class='btn btn-sm btn-success'>Finalizado</button>";
                                        }
                                        if ($inv->idcliente > 0) {
                                            $nombre_cliente = $inv->proveedores[0]->nombre;
                                        }else{
                                            $nombre_cliente = 'Sin Proveedor';
                                        }
                                     ?>
                                    <tr>
                                        <td>{{ $inv->idinventario }}</td>
                                    	<td>{{ $inv->num_documento ?? 0 }}</td>
                                        <td>{{ $nombre_cliente }}</td>
                                        <td class="text-center"><?php echo $flecha; ?></td>
                                        <td class="text-center">{{ $inv->observaciones }}</td>
                                        <td class="text-center"><?php echo $estatus; ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if($inv->estatus_movimiento === 1)
                                                        <a href="{{ route('inventario.edit', $inv->idinventario) }}" class="dropdown-item">{{ __('Editar Movimiento') }}</a>
                                                        <a href="{{ route('inventario.delete', $inv->idinventario) }}" class="dropdown-item">{{ __('Eliminar Movimiento') }}</a>
                                                    @else
                                                        <a href="{{ route('inventario.show', $inv->idinventario) }}" class="dropdown-item">{{ __('Ver Movimiento') }}</a>
                                                    @endif
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
        $('#inventario_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]]
            }
        );
    });
</script>
@endsection

