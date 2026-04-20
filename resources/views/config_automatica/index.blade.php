@extends('layouts.app', ['page' => __('Configuraciones Automaticas'), 'pageSlug' => 'config_automatica'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Configuraciones Automaticas') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            @if(count($configuracion_autm) === 0)
                                <a href="{{ route('config_automatica.create') }}" class="btn btn-sm btn-primary">{{ __('Crear Configuración Automaticas') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="configuracion_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('ID') }}</th>
                                <th scope="col">{{ __('Caja') }}</th>
                                <th scope="col">{{ __('Empresa') }}</th>
                                <th scope="col">{{ __('Detalle mensaje') }}</th>
                                <th scope="col">{{ __('Estatus') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($configuracion_autm as $config)

                                    <tr>
                                        <td>{{ $config->idconfigautomatica  }}</td>
                                        <td>{{ $config->idcaja }}</td>
                                        <td>{{ $config->nombre_emisor }}</td>
                                        <td>{{ $config->detalle_mensaje }}</td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="{{ route('config_automatica.edit', $config->idconfigautomatica) }}">{{ __('Editar') }}</a>
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
        $('#configuracion_datatable').DataTable(
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
