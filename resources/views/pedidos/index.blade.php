@extends('layouts.app', ['page' => __('Pedidos del Sistema'), 'pageSlug' => 'allpedidos'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <h4 class="card-title">{{ __('Filtro de Busqueda') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="post" action="{{ route('filtro.factura') }}" autocomplete="off" id="filtro_factura">
                    @csrf
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                            <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde') }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                            @include('alerts.feedback', ['field' => 'fecha_desde'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                            <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta') }}" required style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta'])
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Filtrar') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Cotizaciones Construidas') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <div class="dropdown">
                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b style="color: white;">Acciones</b> &nbsp;
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a href="{{ route('pedidos.create') }}" class="dropdown-item">{{ __('Nueva Cotización') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_pedidos_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                @if(Auth::user()->config_u[0]->usa_cotizacion_adicional > 0)
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_1) }}</th>
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_2) }}</th>
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_3) }}</th>
                                @endif
                                <th scope="col">{{ __('Estatus') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($pedidos as $pedido)
                                    <?php
                                        switch ($pedido->estatus_doc) {
                                            case '1':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-danger'>En Edición</button>";
                                            break;
                                            case '2':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-info'>En Espera</button>";
                                            break;
                                            case '3':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-success'>En Factura</button>";
                                            break;
                                            case '4':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-success'>Anulada</button>";
                                            break;
                                        }
                                        $usuario = App\Cliente::find($pedido->idcliente);
                                       // dd($pedido->ped_cli[0]->nombre);
                                    ?>
                                    <tr>
                                        <td>{{ $pedido->numero_documento }}</td>
                                      <td>{{ $usuario->nombre }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_neto_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_descuento_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_impuesto_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_comprobante_ped,2,',','.') }}</td>
                                        @if(Auth::user()->config_u[0]->usa_cotizacion_adicional > 0)
                                            <td class="text-right">{{ $pedido->value_label_aditional_1 }}</td>
                                            <td class="text-right">{{ $pedido->value_label_aditional_2 }}</td>
                                            <td class="text-right">{{ $pedido->value_label_aditional_3 }}</td>
                                        @endif
                                        <td>
                                            <?php echo $estatus; ?>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @switch($pedido->estatus_doc)
                                                        @case(1)
                                                            <a href="{{ route('pedidos.edit', $pedido->idpedido) }}" class="dropdown-item">{{ __('Editar Pedidos') }}</a>
                                                        @break
                                                        @case(2)
                                                            <a href="{{ url('convertir-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Convertir a Factura') }}</a>
                                                            <a href="{{ url('pdf-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                            <a href="{{ url('envia-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>
                                                             <a href="{{ route('pedidos.edit', $pedido->idpedido) }}" class="dropdown-item">{{ __('Editar Pedidos') }}</a>
                                                        @break
                                                        @case(3)
                                                            <a href="{{ url('pdf-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                            <a href="{{ url('envia-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>
                                                        @break
                                                    @endswitch
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
        $('#ver_pedidos_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
    });
</script>
@endsection
