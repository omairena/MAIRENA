@extends('layouts.app', ['page' => __('Todos los Clientes'), 'pageSlug' => 'clientes'])
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
                            <h4 class="card-title">{{ __('Clientes') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('cliente.create') }}" class="btn btn-sm btn-primary">{{ __('Agregar Cliente') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="clientes_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Identificación') }}</th>
                                <th scope="col">{{ __('Nombre') }}</th>
                                <th scope="col">{{ __('Email Principal') }}</th>
                                 <th scope="col">{{ __('Email Adicionales') }}</th>
                                <th scope="col">{{ __('Código Actividad') }}</th>
                                <th scope="col">{{ __('Teléfono') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($clientes as $cliente)
                                    <?php
                                    switch ($cliente->tipo_id) {
                                        case '01':
                                           $tipo_ident = 'CF-';
                                        break;
                                        case '02':
                                            $tipo_ident = 'CJ-';
                                        break;
                                        case '03':
                                            $tipo_ident = 'DIMEX-';
                                        break;
                                        case '04':
                                            $tipo_ident = 'NITE-';
                                        break;
                                         case '05':
                                            $tipo_ident = 'EXT-';
                                        break;
                                        case '06':
                                            $tipo_ident = 'NOCONTRIBUYE-';
                                        break;
                                    }

                                     ?>
                                    <tr>
                                        <td>{{ $tipo_ident }}{{ $cliente->num_id }}</td>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->email }} </td>
                                         <td>{{ $cliente->additional_email }} </td>
                                        <td>{{ $cliente->codigo_actividad }}</td>
                                        <td>{{ $cliente->telefono }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ route('cliente.edit', $cliente->idcliente) }}" class="dropdown-item">{{ __('Editar Cliente') }}</a>

                                                        <a href="{{ route('cliente.clasifica', $cliente->idcliente) }}" class="dropdown-item">{{ __('Clasificar Cliente') }}</a>
                                                        @if (Auth::user()->config_u[0]->usa_listaprecio == 1)

                                                            <a href="{{ route('cliente.listacli', $cliente->idcliente) }}" class="dropdown-item">{{ __('Lista de Precio Cliente') }}</a>
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
        var table = $('#clientes_datatable').DataTable(
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

