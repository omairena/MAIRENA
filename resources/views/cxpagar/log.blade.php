@extends('layouts.app', ['page' => __('Ver Movimientos de Cuenta'), 'pageSlug' => 'showMovimiento'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Ver Movimientos') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cxpagar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table tablesorter ">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('# Recibo') }}</th>
                                <th scope="col">{{ __('Fecha') }}</th>
                                <th scope="col">{{ __('Monto') }}</th>
                                <th scope="col">{{ __('Tipo Movimiento') }}</th>
                                <th scope="col">{{ __('Referencia') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($log_cxpagar as $cxp)
                                    <tr>
                                        <td class="text-right">{{ $cxp->num_recibo_abono }}</td>
                                        <td>{{ $cxp->fecha_rec_mov}}</td>
                                        <td class="text-right">{{ $cxp->monto_abono }}</td>
                                        <td class="text-right">{{ $cxp->tipo_mov }}</td>
                                        <td class="text-right">{{ $cxp->referencia }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="#" id="buscar_movimiento{{ $cxp->idlogcxpagar }}" data-id="{{ $cxp->idmovcxcobrar }}" class="dropdown-item ver_movimiento" data-target="#VerMovimiento" data-toggle="modal">{{ __('Ver Movimiento') }}</a>
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
</div>
@endsection

