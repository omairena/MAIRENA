@extends('layouts.app', ['page' => __('Configuraciones Creadas'), 'pageSlug' => 'config'])
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
                            <h4 class="card-title">{{ __('Listas de Precios') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('list.create') }}" class="btn btn-sm btn-primary">{{ __('Crear Lista') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="configuracion_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Id') }}</th>
                                <th scope="col">{{ __('Descripcion') }}</th>
                                <th scope="col">{{ __('Porcentaje') }}</th>
                                <th scope="col">{{ __('Estatus') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($lista as $lists)

                                    <tr>
                                        <td>{{ $lists->idlist }}</td>
                                        <td>{{ $lists->descripcion }}</td>
                                        <td>{{ $lists->porcentaje }}</td>
                                        <td>{{ $lists->estatus }}</td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ route('list.edit', $lists->idlist) }}" class="dropdown-item">{{ __('Editar Lista') }}</a>

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
