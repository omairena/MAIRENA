@extends('layouts.app', ['page' => __('Ver Cuenta Por Pagar'), 'pageSlug' => 'showMovimiento'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Ver Cuenta') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cxpagar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table tablesorter ">
                            <thead class=" text-primary">
                                <th scope="col" style="text-align: center;">{{ __('# Documento') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Fecha') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Monto') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Abono') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Saldo') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Días') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Estado') }}</th>
                                <th scope="col" style="text-align: center;"></th>
                            </thead>
                            <tbody>
                                <?php $x= 1; $sumatoria_cuentas=0; $dias = 0; ?>
                                @foreach ($mov_cxpagar as $cxp)
                                    <?php 
                                        switch ($cxp->estatus_mov){
                                            case '1':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>Pendiente</button>";
                                        break;
                                        case '2':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-success'>Pagada</button>";
                                        break;
                                    }
                                 ?>
                                    <tr>
                                        <td class="text-right">{{ $cxp->num_documento_mov }}</td>
                                        <td style="text-align: center;">{{ $cxp->fecha_mov}}</td>
                                        <td class="text-right">{{ $cxp->monto_mov }}</td>
                                        <td class="text-right">{{ $cxp->abono_mov }}</td>
                                        <td class="text-right">{{ $cxp->saldo_pendiente }}</td>
                                        <td class="text-right">{{ $cxp->cant_dias_pendientes }}</td>
                                        <td style="text-align: center;"><?php echo $estatus; ?></td>
                                        <td style="text-align: center;">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if($cxp->saldo_pendiente > 0)
                                                        <a href="{{ route('logcxpagar.crear', ['idmovcxpagar' => $cxp->idmovcxpagar]) }}" class="dropdown-item">{{ __('Agregar Abono') }}</a>
                                                    @endif
                                                    <a href="{{ route('logcxpagar.show', ['idmovcxpagar' => $cxp->idmovcxpagar]) }}" class="dropdown-item">{{ __('Ver Movimientos') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    $sumatoria_cuentas += $cxp->saldo_pendiente;
                                    $dias += ($cxp->cant_dias_pendientes/$x)/100;
                                    $x++;
                                    ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-user">
                <div class="card-body">
                    <p class="card-text">
                        <div class="author">
                            <div class="block block-one"></div>
                            <div class="block block-two"></div>
                            <div class="block block-three"></div>
                            <div class="block block-four"></div>
                            <a href="#">
                                <img class="avatar" src="{{ asset('black') }}/img/default-avatar.png" alt="">
                                <h5 class="title">{{ $cxpagar->cxpagar_cli[0]->nombre }}</h5>
                            </a>
                            <p class="description">
                                Actividad : {{ $cxpagar->cxpagar_cli[0]->codigo_actividad }}
                                <br>
                                Razón: {{ $cxpagar->cxpagar_cli[0]->razon_social }}
                            </p>
                        </div>
                    </p>
                    <div class="card-description">
                        {{ $cxpagar->cxpagar_cli[0]->direccion }}
                        <br>

                    </div>
                </div>
                <div class="card-footer">
                    <div class="button-container">
                        <button class="btn btn-icon btn-round btn-facebook">
                            <i class="fab fa-facebook"></i>
                        </button>
                        <button class="btn btn-icon btn-round btn-twitter">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-icon btn-round btn-google">
                            <i class="fab fa-google-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Resumen de Cuenta') }}</h3><br>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-6">
                            <p>
                                Saldo General del Cliente: {{ $sumatoria_cuentas }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                Cantidad de dias promedio : {{ $dias }}
                            </p>
                            
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection

