@extends('layouts.app', ['page' => __('Ver Cuenta Por Cobrar'), 'pageSlug' => 'showMovimiento'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
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
                                <a href="{{ route('cxcobrar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table tablesorter " id="table-cxcobrar">
                            <thead class=" text-primary">
                                <th scope="col" style="text-align: center;"></th>
                                <th scope="col" style="text-align: center;">{{ __('# Documento') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Fecha') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Monto') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Abono') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Saldo') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Días') }}</th>
                                <th scope="col" style="text-align: center;">{{ __('Estado') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                <?php 
                                $x= 1; $sumatoria_cuentas=0; $dias = 0;
                                ?>
                                @foreach ($mov_cxcobrar as $cxc)
                                <?php 
                                    switch ($cxc->estatus_mov){
                                        case '1':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>Pendiente</button>";
                                        break;
                                        case '2':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-success'>Pagada</button>";
                                        break;
                                        case '3':
                                            $estatus ="<button  type='button' class='btn btn-sm btn-danger'>Rechazo Hacienda</button>";
                                        break;
                                    }
                                 ?>
                                    <tr>
                                        @if($cxc->estatus_mov === 1)
                                            <td style="text-align: center;" id="{{ $cxc->idmovcxcobrar }}"><input type="checkbox" class="select-checkbox" name="seleccion[]" value="{{ $cxc->idmovcxcobrar }}"></td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td class="text-left">{{ $cxc->num_documento_mov }}</td>
                                        <td style="text-align: center;">{{ $cxc->fecha_mov}}</td>
                                        <td class="text-right">{{ number_format($cxc->monto_mov,  2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($cxc->abono_mov,  2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($cxc->saldo_pendiente,  2, '.', ',') }}</td>
                                        <td class="text-right">{{ $cxc->cant_dias_pendientes }}</td>
                                        <td style="text-align: center;"><?php echo $estatus; ?></td>
                                        <td style="text-align: center;">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if($cxc->saldo_pendiente > 0)
                                                        <a href="{{ route('logcxcobrar.crear', $cxc->idmovcxcobrar) }}" class="dropdown-item">{{ __('Agregar Abono') }}</a>
                                                    @endif
                                                    <a href="{{ route('logcxcobrar.show', $cxc->idmovcxcobrar) }}" class="dropdown-item">{{ __('Ver Movimientos') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    if ($cxc->estatus_mov != 3) {
                                        $sumatoria_cuentas += $cxc->saldo_pendiente;
                                        $dias += ($cxc->cant_dias_pendientes/$x)/100;
                                        $x++;
                                    }
                                    ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" style="text-align: right;"><button class="btn btn-success pull-right" id="boton_cerrar_cuenta" style="display: none;">Calcular Cuentas</button></td>
                                </tr>
                            </tfoot>
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
                                <h5 class="title">{{ $cxcobrar->cxcobrar_cli[0]->nombre }}</h5>
                            </a>
                            <p class="description">
                                Actividad : {{ $cxcobrar->cxcobrar_cli[0]->codigo_actividad }}
                                <br>
                                Razón: {{ $cxcobrar->cxcobrar_cli[0]->razon_social }}
                            </p>
                        </div>
                    </p>
                    <div class="card-description">
                        {{ $cxcobrar->cxcobrar_cli[0]->direccion }}
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
<input type="text" name="cxcobrar" id="cxcobrar" value="{{ old('cxcobrar') }}" hidden="true">
@include('modals.cerrarCuentas')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#table-cxcobrar').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('#table-cxcobrar tbody').on('click', 'tr', function () {
            var variable = [];
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                variable.push(this.value);
                return this.value;
            }).get();
            var str = data.join(',');
            $('#cxcobrar').val(data);
            var valor = $('#cxcobrar').val();
            if (variable.length > 1) {
                $('#boton_cerrar_cuenta').css('display', 'block');
            }else{
                $('#boton_cerrar_cuenta').css('display', 'none');
            }
            
        });

        $(document).on("click", "#boton_cerrar_cuenta" , function(event) {
            event.preventDefault();
            var datos = $('#cxcobrar').val();
            var URL = {!! json_encode(url('/ajaxCuentacierre')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                data:{datos:datos},
                success:function(response){
                    //console.log(response);
                    $('#monto_cuenta').val(response['success']);
                    $('#monto_cuenta').attr('readonly', 'true');
                    $('#cxcobrar_modal').val(datos);
                },
                error:function(response){
                    //console.log(response);
                }
            });
            $('#CerrarCuentas').modal('show');
        });

        $('#monto_abonado').on("blur", function( e ) {
            e.preventDefault();
            var mto_abonado = $(this).val();
            var mto_cuenta = $('#monto_cuenta').val();
            var total_mnto =  mto_cuenta - mto_abonado;
            if (total_mnto > 0) {
                alert('El monto es menor a la cuenta se agregara como abono a la ultima cuenta.');
            }else{
                if (total_mnto === 0) {
                    alert('El monto es exacto cuenta(s) cuadradas.');
                    $('#monto_abonado').attr('readonly', 'true');
                }else{
                    alert('Colocar un monto menor a la cuenta(s) o en su defecto igual al valor.');
                }
            }
        });
    });
</script>
@endsection

