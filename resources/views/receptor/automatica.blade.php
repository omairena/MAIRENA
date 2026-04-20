@extends('layouts.app', ['page' => __('Receptor'), 'pageSlug' => 'receptor'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>

</head>
@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <h4 class="card-title">{{ __('Gestionar Masivamente') }}</h4>
                            <h3 class="mb-0" id="encabezado_recepcion"></h3>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @include('alerts.success')
                    <form method="post" action="{{ route('receptor.send') }}" autocomplete="off" enctype="multipart/form-data" onsubmit="return submitResult();">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required disabled="true">
                                        <option value="01">Fáctura Electrónica</option>
                                        <option value="02">Nota de Debito</option>
                                        <option value="03">Nota de Crédito</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('procesar_doc') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-procesar_doc">{{ __('Documento de Recepcion') }}</label>
                                    <select name="procesar_doc" id="procesar_doc" class="form-control form-control-alternative" disabled="true">
                                        <option value="0">-- Seleccione el tipo de Aceptación --</option>
                                        <option value="05">Aceptado</option>
                                        <option value="06">Parcialmente\Aceptado</option>
                                        <option value="07">Rechazado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative" disabled="true">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required disabled="true">
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('detalle_mensaje') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="detalle_mensaje">{{ __('Detalle Mensaje') }}</label>
                                    <textarea  name="detalle_mensaje" id="detalle_mensaje" class="form-control form-control-alternative{{ $errors->has('detalle_mensaje') ? ' is-invalid' : '' }}" placeholder="{{ __('Detalle Mensaje') }}" required readonly="true"></textarea>
                                    @include('alerts.feedback', ['field' => 'detalle_mensaje'])
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('condicion_impuesto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condicion_impuesto">{{ __('Condición del Impuesto') }}</label>
                                    <select name="condicion_impuesto" id="condicion_impuesto" class="form-control form-control-alternative" disabled="true">
                                        <option value="0">Sin Condición</option>
                                        <option value="01">Genera Crédito IVA</option>
                                        <option value="02">Genera Crédito Parcial IVA</option>
                                        <option value="03">Bienes de Capital</option>
                                        <option value="04">Gasto Corriente No Genera Crédito</option>
                                        <option value="05">Proporcionalidad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('factor_credito') ? ' has-danger' : '' }}" style="display: none;" id="factor_cr">
                                    <label class="form-control-label" for="input-factor_credito">{{ __('Fáctor %') }}</label>
                                    <input type="number" name="factor_credito" id="factor_credito" class="form-control form-control-alternative{{ $errors->has('factor_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Fáctor %') }}" value="{{ old('factor_credito') }}" required readonly="true">
                                       
                                    @include('alerts.feedback', ['field' => 'factor_credito'])
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('imp_creditar') ? ' has-danger' : '' }}" style="display: none;" id="imp_cr">
                                    <label class="form-control-label" for="input-imp_creditar">{{ __('Impuesto a Acreditar') }}</label>
                                    <input type="number" name="imp_creditar" id="imp_creditar" class="form-control form-control-alternative{{ $errors->has('imp_creditar') ? ' is-invalid' : '' }}" placeholder="{{ __('Impuesto a Acreditar') }}" value="{{ old('imp_creditar') }}" required readonly="true">
                                    @include('alerts.feedback', ['field' => 'imp_creditar'])
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="form-group{{ $errors->has('gasto_aplica') ? ' has-danger' : '' }}" style="display: none;" id="gasto_ap">
                                    <label class="form-control-label" for="input-gasto_aplica">{{ __('Gasto Aplicable') }}</label>
                                    <input type="number" name="gasto_aplica" id="gasto_aplica" class="form-control form-control-alternative{{ $errors->has('gasto_aplica') ? ' is-invalid' : '' }}" placeholder="{{ __('Gasto Aplicable') }}" value="{{ old('gasto_aplica') }}" required readonly="true">
                                    @include('alerts.feedback', ['field' => 'gasto_aplica'])
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <input type="text" name="idrecepcion" id="idrecepcion" value="{{ old('idrecepcion') }}" hidden="true">
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <input type="text" name="is_masive" id="is_masive" value="0" hidden="true">
                                @if(count($sales) > 0)
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success mt-4">{{ __('Recepcionar') }}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Recepcion Automatica de Documentos') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            @if(count($sales) > 0)
                                <div class="form-check">
                                    <label class="form-check-label" style="border: 4px dashed #00e7c8 !important; height: 35; width: 150px; padding: 5px;">
                                        <input class="form-check-input" type="checkbox" id="checkAll">Seleccionar Todo
                                        <span class="form-check-sign">
                                            <span class="check"></span>
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="ver_receptor_datatable">
                            <thead class=" text-primary">
                                <th scope="col"></th>
                                <th scope="col">{{ __('Numero Receptor') }}</th>
                                <th scope="col">{{ __('Identificacion Emisor') }}</th>
                                <th scope="col">{{ __('Nombre Emisor') }}</th>
                                <th scope="col">{{ __('Consecutivo') }}</th>
                                <th scope="col">{{ __('Fecha Recp') }}</th>
                                <th scope="col">{{ __('Fecha XML') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Comprobante') }}</th>
                                <th scope="col">{{ __('Detalle Mensaje') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <?php
                                    $num_receptor = substr($sale->consecutivo, 10,10);
                                    $num_clave = substr($sale->clave, 31,10);
                                    ?>
                                    <tr>
                                        <td class="center">
                                            <input type="checkbox" class="select-checkbox" name="seleccion[]" value="{{ $sale->idreceptor }}">
                                        </td>
                                        <td><?php echo $num_receptor; ?></td>
                                        <td>{{ $sale->cedula_emisor }}</td>
                                        <td>{{ $sale->nombre_emisor }}</td>
                                       
                                        <td><?php echo $num_clave; ?></td>
                                        <td>{{ $sale->fecha }}</td>
                                        <td>{{ $sale->fecha_xml_envio }}</td>
                                        <td class="text-right">{{ $sale->total_impuesto }}</td>
                                        <td class="text-right">{{ $sale->total_comprobante }}</td>
                                        <td>{{ $sale->detalle_mensaje }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a href="{{ url('donwload-xml-receptor', ['idsales' => $sale->idreceptor]) }}" class="dropdown-item">{{ __('Descargar XML') }}</a>
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
@include('modals.cargando')
@section('myjs')

<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_receptor_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        $('#gasto_aplica').prop('readonly', true);
        $('#imp_creditar').prop('readonly', true);
        $('#factor_credito').prop('readonly', true);

        $('#gasto_aplica').val('0.00');
        $('#imp_creditar').val('0.00');
        
        var maxChecks = 1;
        $(".select-checkbox").change(function () {
            if( $('.select-checkbox').is(':checked') ) {
                if($( "input:checked" ).length >= maxChecks) { 
                    $(".select-checkbox:not(:checked)").prop( "disabled", true );
                    var APP_URL = {!! json_encode(url('/infoReceptor')) !!};
                    traerInfoReceptor($(this).val(), APP_URL);
                    $('#idrecepcion').val($(this).val());
                } else {
                    $(".select-checkbox").prop( "disabled", false );
                }
            } else {
                $(".select-checkbox").prop( "disabled", false );
            }
            
        });

        $('#procesar_doc').change(function() {
            traerNumReceptor(APP_URL);
        }); 

        $('#idcaja').change(function() {
           traerNumReceptor(APP_URL);
        });
            
        $('#checkAll').click(function() {

            $('input:checkbox').not(this).prop('checked', this.checked);

            if (this.checked == true) {

                $(".select-checkbox").prop( "disabled", true );
                $("#is_masive").val(1);

                $('#procesar_doc').attr("disabled", false);
                $('#idcaja').attr("disabled", false);
                $('#actividad').attr("disabled", false);
                $('#detalle_mensaje').attr("readonly", false);
                $('#clasifica_d151').attr("disabled", false);
                $('#condicion_impuesto').attr("disabled", false);

            } else {

                $(".select-checkbox").prop( "disabled", false )
                $("#is_masive").val(0);
                $('#procesar_doc').attr("disabled", true);
                $('#idcaja').attr("disabled", true);
                $('#actividad').attr("disabled", true);
                $('#detalle_mensaje').attr("readonly", true);
                $('#clasifica_d151').attr("disabled", true);
                $('#condicion_impuesto').attr("disabled", true);
                
            }
        });

        $('#condicion_impuesto').change(function() {
            switch($(this).val()){
                case '0':
                    $('#imp_cr').css( "display","none");
                    $('#gasto_ap').css( "display","none");
                    $('#factor_cr').css( "display","none");
                    $('#gasto_aplica').prop('readonly', false);
                    $('#imp_creditar').prop('readonly', false);
                break;
                case '01':
                    $('#imp_cr').css( "display","none");
                    $('#gasto_ap').css( "display","none");
                    $('#factor_cr').css( "display","none");
                break;
                case '02':
                    $('#gasto_aplica').prop('readonly', false);
                    $('#imp_creditar').prop('readonly', false);
                    $('#imp_cr').css( "display","");
                    $('#gasto_ap').css( "display","");
                    $('#factor_cr').css( "display","none");
                break;
                case '03':
                        $('#gasto_aplica').prop('readonly', false);
                        $('#imp_creditar').prop('readonly', false);
                        $('#gasto_ap').css( "display","");
                        $('#imp_cr').css( "display","");
                        $('#factor_cr').css( "display","none");
                break;
                case '04':
                    $('#gasto_ap').css( "display","none");
                    $('#imp_cr').css( "display","none");
                    $('#factor_cr').css( "display","none");
                break;
                case '05':
                        $('#gasto_aplica').prop('readonly', false);
                        $('#imp_creditar').prop('readonly', false);
                        $('#gasto_ap').css( "display","");
                        $('#imp_cr').css( "display","");
                        $('#factor_cr').css( "display","");
                break;
            }
        });
    });
    function submitResult() {
        if ( confirm("¿Desea procesar la Recepcion?") == false ) {
            return false ;
        } else {
            $("#loadMe").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            return true ;
        }
    }
</script>
@endsection