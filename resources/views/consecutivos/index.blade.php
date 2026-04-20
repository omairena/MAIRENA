@extends('layouts.app', ['page' => __('Consecutivo de Configuracion'), 'pageSlug' => 'config'])
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Consecutivos') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Tipo de Documento') }}</th>
                                <th scope="col">{{ __('Numero de Consecutivo') }}</th>
                                <th scope="col">{{ __('Facturas desde') }}</th>
                                <th scope="col">{{ __('Facturas Hasta') }}</th>
                                <th scope="col">{{ __('Tipo de Compra') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($consecutivos as $consecutivo)
                                <?php 
                                    switch ($consecutivo->tipo_documento) {
                                        case '01':
                                            $document = 'Factura Electronica';
                                        break;
                                        case '02':
                                            $document = 'Nota de Debito';
                                        break;
                                        case '03':
                                            $document = 'Nota de Crédito';
                                        break;
                                        case '04':
                                            $document = 'Tiquete Electronico';
                                        break;
                                        case '05':
                                            $document = 'Mensaje Receptor Aceptado';
                                        break;
                                        case '06':
                                            $document = 'Mensaje Receptor Parcial';
                                        break;
                                        case '07':
                                            $document = 'Mensaje Receptor Rechazado';
                                        break;
                                        case '09':
                                            $document = 'Factura Exportación';
                                        break;
                                        case '08':
                                            $document = 'Factura Electronica de compra';
                                        break;
                                        case '95':
                                            $document = 'Nota de Crédito Regimen Simplificado';
                                        break;
                                        case '96':
                                            $document = 'Factura Regimen Simplificado';
                                        break;
                                        case '97':
                                            $document = 'Cotización / Pedidos';
                                        break;
                                        case '98':
                                            $document = 'Recibos de Cuentas por Cobrar';
                                        break;
                                        case '99':
                                            $document = 'Recibos de Cuentas por Pagar';
                                        break;
                                    }

                                    if ($consecutivo->tipo_compra === 1) {
                                        $compra = 'Parcial';
                                    }else{
                                        $compra = 'Ilimitado';
                                    }
                                 ?>
                                    <tr>
                                        <td>{{ $document }}</td>
                                        <td>{{ str_pad($consecutivo->numero_documento, 10, "0", STR_PAD_LEFT) }}</td>
                                        <td>{{ $consecutivo->doc_desde }}</td>
                                        <td>{{ $consecutivo->doc_hasta }}</td>
                                        <td>{{ $compra }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ route('consecutivo.edit', $consecutivo->idconsecutivo) }}" class="dropdown-item">{{ __('Editar') }}</a>
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
                        {{ $consecutivos->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

