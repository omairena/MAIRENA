@extends('layouts.app', ['page' => __('Cajas Creadas'), 'pageSlug' => 'verCajas'])
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Cajas') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter ">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Empresa Caja') }}</th>
                                <th scope="col">{{ __('Código Único') }}</th>
                                <th scope="col">{{ __('Nombre Caja') }}</th>
                                <th scope="col">{{ __('Fecha de Apertura') }}</th>
                                <th scope="col">{{ __('Fecha de Cierre') }}</th>
                                <th scope="col">{{ __('Contado') }}</th>
                                <th scope="col">{{ __('Crédito') }}</th>
                                <th scope="col">{{ __('Total Efectivo') }}</th>
                                <th scope="col">{{ __('Efectivo a Depositar') }}</th>
                                <th scope="col">{{ __('Cobro Por Tarjeta') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($cajas as $caja)
                                    <?php
                                        $cj = \App\Cajas::find($caja->idcaja);
                                    ?>
                                    <tr>
                                        <td>{{ $cj->caja_emp[0]->nombre_emisor }}</td>
                                        <td>{{ str_pad($cj->codigo_unico, 3, "0", STR_PAD_LEFT) }}</td>
                                        <td>{{ $cj->nombre_caja }}</td>
                                        <td>{{ $caja->fecha_apertura_caja }}</td>
                                        <td>{{ $caja->fecha_cierre_caja }}</td>
                                        <td>{{ number_format($caja->ventas_contado,2,',','.') }}</td>
                                        <td>{{ number_format($caja->ventas_credito,2,',','.') }}</td>
                                        <td>{{ number_format($caja->t_efectivo_entrante,2,',','.') }}</td>
                                        <td>{{ number_format($caja->t_efectivo_depositar,2,',','.') }}</td>
                                        <td>{{ number_format($caja->cobro_tarjeta,2,',','.') }}</td>

                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="{{ route('reportes.caja', $caja->idlogcaja) }}">{{ __('Generar Reporte') }}</a>
                                                    <a class="dropdown-item" href="{{ route('pdf.caja', $caja->idlogcaja) }}">{{ __('Reporte en PDF') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        {{ $cajas->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

