@extends('layouts.app', ['page' => __('Receptor'), 'pageSlug' => 'receptor'])
<style type="text/css">
	form p{
  width: 100%;
  height: 100%;
  text-align: center;
  line-height: 170px;
  color: #ffffff;
  font-family: Arial;
}
.input-new{
  position: absolute;
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  outline: none;
  opacity: 0;
}

</style>
<head>
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>
</head>
@section('content')
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Crear Recepcion') }}</h3><br>
                            <h3 class="mb-0" id="encabezado_recepcion"></h3>
                      	</div>
                        <div class="col-4 text-right">
                            <a href="{{ route('receptor.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')
                    <form method="post" action="{{ route('receptor.store') }}" autocomplete="off" enctype="multipart/form-data" onsubmit="return submitResult();">
                                       @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Armar Recepción Fiscal') }}</h6>
                        <div class="pl-lg-4">
                            <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                    <option value="01">Fáctura Electrónica</option>
                                    <option value="02">Nota de Debito</option>
                                    <option value="03">Nota de Crédito</option>
                                </select>
                                @include('alerts.feedback', ['field' => 'tipo_documento'])
                            </div>
                            <div class="form-group{{ $errors->has('procesar_doc') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-procesar_doc">{{ __('Documento de Recepcion') }}</label>
                                <select name="procesar_doc" id="procesar_doc" class="form-control form-control-alternative">
                                    <option value="0">-- Seleccione el tipo de Aceptación --</option>
                                    <option value="05">Aceptado</option>
                                    <option value="06">Parcialmente\Aceptado</option>
                                    <option value="07">Rechazado</option>
                                </select>
                            </div>
                        	<div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                    @foreach($cajas as $caja)
                                        <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                </select>
                                @include('alerts.feedback', ['field' => 'actividad'])
                            </div>
                            <div class="form-group{{ $errors->has('detalle_mensaje') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="detalle_mensaje">{{ __('Detalle Mensaje') }}</label>
                                <textarea  name="detalle_mensaje" id="detalle_mensaje" class="form-control form-control-alternative{{ $errors->has('detalle_mensaje') ? ' is-invalid' : '' }}" placeholder="{{ __('Detalle Mensaje') }}" required></textarea>
                                @include('alerts.feedback', ['field' => 'detalle_mensaje'])
                            </div>
                            <div class="form-group{{ $errors->has('condicion_impuesto') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-condicion_impuesto">{{ __('Condición del Impuesto') }}</label>
                                <select name="condicion_impuesto" id="condicion_impuesto" class="form-control form-control-alternative">
                                    <option value="0">Sin Condición</option>
                                    <option value="01">Genera Crédito IVA</option>
                                    <option value="02">Genera Crédito Parcial IVA</option>
                                    <option value="03">Bienes de Capital</option>
                                    <option value="04">Gasto Corriente No Genera Crédito</option>
                                    <option value="05">Proporcionalidad</option>
                                </select>
                            </div>
                            <div class="form-group{{ $errors->has('factor_credito') ? ' has-danger' : '' }}" style="display: none;" id="factor_cr">
                                <label class="form-control-label" for="input-factor_credito">{{ __('Fáctor %') }}</label>
                                   <input type="number" name="factor_credito" id="factor_credito" class="form-control form-control-alternative{{ $errors->has('factor_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Fáctor %') }}" value="{{ old('factor_credito') }}" required>
                                    @include('alerts.feedback', ['field' => 'factor_credito'])
                            </div>
                            <div class="form-group{{ $errors->has('imp_creditar') ? ' has-danger' : '' }}" style="display: none;" id="imp_cr">
                                <label class="form-control-label" for="input-imp_creditar">{{ __('Impuesto a Acreditar') }}</label>
                                   <input type="number" name="imp_creditar" id="imp_creditar" class="form-control form-control-alternative{{ $errors->has('imp_creditar') ? ' is-invalid' : '' }}" placeholder="{{ __('Impuesto a Acreditar') }}" value="{{ old('imp_creditar') }}" required>
                                    @include('alerts.feedback', ['field' => 'imp_creditar'])
                            </div>
                            <div class="form-group{{ $errors->has('gasto_aplica') ? ' has-danger' : '' }}" style="display: none;" id="gasto_ap">
                                <label class="form-control-label" for="input-gasto_aplica">{{ __('Gasto Aplicable') }}</label>
                                   <input type="number" name="gasto_aplica" id="gasto_aplica" class="form-control form-control-alternative{{ $errors->has('gasto_aplica') ? ' is-invalid' : '' }}" placeholder="{{ __('Gasto Aplicable') }}" value="{{ old('gasto_aplica') }}" required>
                                    @include('alerts.feedback', ['field' => 'gasto_aplica'])
                            </div>
                           	<div class="form-group{{ $errors->has('cargar_documento') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="">{{ __('Seleccionar los Documentos') }}</label>
                                   	<input type="file" multiple class="input-new form-control form-control-alternative{{ $errors->has('cargar_documento') ? ' is-invalid' : '' }}" style="height: 200px;" id="cargar_documento[]" name="cargar_documento[]" required>
  									<p style="height: 200px; border:4px dashed #00e7c8 !important;">Selecciona los documentos en esta area (5 maximos).</p>
                                    @include('alerts.feedback', ['field' => 'cargar_documento'])
                                    <input type="text" name="numero_documento_receptor" id="numero_documento_receptor" value="{{ old('numero_documento_receptor') }}" hidden="true">
                            </div>
                            <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                            <div class="text-center">
                                <button type="submit" class="btn btn-sm btn-success">Enviar Hacienda</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('myjs')
@include('modals.cargando')
<script type="text/javascript">
	$(document).ready(function(){
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        $('#gasto_aplica').prop('readonly', true);
        $('#imp_creditar').prop('readonly', true);
        $('#factor_credito').prop('readonly', true);

        $('#gasto_aplica').val('0.00');
        $('#imp_creditar').val('0.00');
 	 	$('form input').change(function () {
    		if (this.files.length > 15) {
    			alert('Excedio el límite de archivos');
    			$("form input").empty();
    			alert($('#cargar_documento').val());
    		}else{
    			$('form p').text(this.files.length + " Archivo(s) Seleccionado");
    		}
  		});

        $('#procesar_doc').change(function() {
            traerNumReceptor(APP_URL);
            if ($(this).val() > 0) {
                $('#combo_receptor_config').css( "display","");
            }else{
                $('#combo_receptor_config').css( "display", "none");
            }
        }); 

        $('#idcaja').change(function() {
           traerNumReceptor(APP_URL);
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
        if ( confirm("¿Desea procesar la Factura?") == false ) {
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