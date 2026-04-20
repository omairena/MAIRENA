@extends('layouts.app', ['page' => __('User Management'), 'pageSlug' => 'users'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
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
                                <h3 class="mb-0">{{ __('Crear Nuevo Producto/Servicio') }}</h3><br>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('productos.update', $producto->idproducto) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Crear Nuevo Producto/Servicio') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tipo_producto">{{ __('Tipo de Producto') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_producto" name="tipo_producto" value="{{ old('tipo_producto', $producto->tipo_producto) }}" required>
                                        <option value="1" {{ ($producto->tipo_producto == 1 ? 'selected="selected"' : '') }}>Producto</option>
                                        <option value="2" {{ ($producto->tipo_producto == 2 ? 'selected="selected"' : '') }}>Servicio</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('idcodigoactv') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcodigoactv">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="idcodigoactv" name="idcodigoactv" value="{{ old('idcodigoactv', $producto->idcodigoactv) }}" required>
                                        @foreach($actividades as $act)
                                            <option value="{{ $act->idcodigoactv }}" {{ ($producto->idcodigoactv == $act->idcodigoactv ? 'selected="selected"' : '') }}> {{ $act->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idcodigoactv'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-codigo_producto">{{ __('Código Producto') }}</label>
                                    <input type="text" name="codigo_producto" id="input-codigo_producto" class="form-control form-control-alternative{{ $errors->has('codigo_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Producto') }}" value="{{ old('codigo_producto', $producto->codigo_producto) }}">
                                    @include('alerts.feedback', ['field' => 'codigo_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_producto">{{ __('Nombre Producto') }}</label>
                                    <input type="text" name="nombre_producto" id="input-nombre_producto" class="form-control form-control-alternative{{ $errors->has('nombre_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Producto') }}" value="{{ old('nombre_producto', $producto->nombre_producto) }}">
                                    @include('alerts.feedback', ['field' => 'nombre_producto'])
                                </div>                                
                               
                                    <div class="form-group{{ $errors->has('unidad_medida') ? ' has-danger' : '' }}" >
                                    <label class="form-control-label" for="input-idunidadmedida">{{ __('Unidad Medida') }}</label>
                                    <select class="form-control form-control-alternative" id="idunidadmedida" name="idunidadmedida" value="{{ old('idunidadmedida', $producto->idunidadmedida) }}" required>
                                        <option value="0">-- Seleccione una unidad --</option>
                                    @foreach($unidad_medida as $u_m) 
                                        <option value="{{ $u_m->idunidadmedida }}"  {{ ($producto->idunidadmedida == $u_m->idunidadmedida ? 'selected="selected"' : '') }}>{{$u_m->simbolo }} - ({{ $u_m->descripcion }})</option>
                                    @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'idunidadmedida'])
                                </div>
                                
                                <div class="form-group{{ $errors->has('impuesto_iva') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-impuesto_iva">{{ __('Tipo de Impuestos') }}</label>
                                    <select class="form-control form-control-alternative" id="impuesto_iva" name="impuesto_iva" value="{{ old('impuesto_iva') }}" required>
                                        <option value="01" {{ ($producto->impuesto_iva == '01' ? 'selected="selected"' : '') }}>0% (Artículo 32, num 1, RLIVA) CCSS, Municipaliades, ect.</option>
                                        <option value="10" {{ ($producto->impuesto_iva == '10' ? 'selected="selected"' : '') }}>Tarifa Exenta 0% General Art. 8 L.IVA</option>
                                        <option value="11" {{ ($producto->impuesto_iva == '11' ? 'selected="selected"' : '') }}>Tarifa No Sujeta</option>
                                        <option value="09" {{ ($producto->impuesto_iva == '09' ? 'selected="selected"' : '') }}>Tarifa reducida 0.5%</option>
                                        <option value="02" {{ ($producto->impuesto_iva == '02' ? 'selected="selected"' : '') }}>Tarifa reducida 1%</option>
                                        <option value="03" {{ ($producto->impuesto_iva == '03' ? 'selected="selected"' : '') }}>Tarifa reducida 2%</option>
                                        <option value="04" {{ ($producto->impuesto_iva == '04' ? 'selected="selected"' : '') }}>Tarifa reducida 4%</option>
                                        <option value="05" {{ ($producto->impuesto_iva == '05' ? 'selected="selected"' : '') }}>Transitorio 0%</option>
                                        <option value="06" {{ ($producto->impuesto_iva == '06' ? 'selected="selected"' : '') }}>Transitorio 4%</option>
                                        <!--<option value="07" {{ ($producto->impuesto_iva == '07' ? 'selected="selected"' : '') }}>Transitorio 8%</option>-->
                                        <option value="08" {{ ($producto->impuesto_iva == '08' ? 'selected="selected"' : '') }}>Tarifa general 13%</option>
                                        
                                    </select>
                                    @include('alerts.feedback', ['field' => 'impuesto_iva'])
                                </div>
                                <div class="form-group{{ $errors->has('codigo_cabys') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cabys">{{ __('Codigo Cabys') }}</label>
                                    <input type="text" name="codigo_cabys" id="input-codigo_cabys" class="form-control form-control-alternative{{ $errors->has('codigo_cabys') ? ' is-invalid' : '' }}" placeholder="{{ __('codigo_cabys') }}" value="{{ old('codigo_cabys', $producto->codigo_cabys) }}" required="false" readonly >
                                    @include('alerts.feedback', ['field' => 'codigo_cabys'])
                                </div> 
                                <div class="form-group{{ $errors->has('porcentaje_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="porcentaje_imp">{{ __('Porcentaje Impuesto') }}</label>
                                    <input type="number" name="porcentaje_imp" id="porcentaje_imp" class="form-control form-control-alternative{{ $errors->has('porcentaje_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Porcentaje Impuesto') }}" value="{{ old('porcentaje_imp', $producto->porcentaje_imp) }}" readonly>
                                    @include('alerts.feedback', ['field' => 'porcentaje_imp'])
                                </div>
                                <div class="form-group{{ $errors->has('costo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-costo">{{ __('Costo del Producto') }}</label>
                                    <input type="number" step="any" name="costo" id="input-costo" class="form-control form-control-alternative{{ $errors->has('costo') ? ' is-invalid' : '' }}" placeholder="{{ __('Costo del Producto') }}" value="{{ old('costo', $producto->costo) }}">
                                    @include('alerts.feedback', ['field' => 'costo'])
                                </div>
                                <div class="form-group{{ $errors->has('utilidad_producto') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-utilidad_producto">{{ __('Utilidad del Producto') }}</label>
                                    <input type="number" name="utilidad_producto" id="input-utilidad_producto" class="form-control form-control-alternative{{ $errors->has('utilidad_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Utilidad del Producto') }}" value="{{ old('utilidad_producto', $producto->utilidad_producto) }}">
                                    @include('alerts.feedback', ['field' => 'utilidad_producto'])
                                </div>
                                 <label class="form-control-label" for="input-utilidad_producto">{{ __('% de Utilidad del Producto ó Servicio') }} &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>El porcentaje de utilidad se utiliza principalemte en el calculo de precios de productos, en el caso de servicios, recomendamos indicar cero, pues la base del costo en ocaciones es deficil de identificar.</b>">¿Necesitas Ayuda?
                                        </a></label>
                                <div class="form-group{{ $errors->has('precio_sin_imp') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-precio_sin_imp">{{ __('Precio Sin IVA') }}</label>
                                    <input type="number" name="precio_sin_imp" id="input-precio_sin_imp" class="form-control form-control-alternative{{ $errors->has('precio_sin_imp') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Sin IVA') }}" value="{{ old('precio_sin_imp', $producto->precio_sin_imp) }}" readonly="true">
                                    @include('alerts.feedback', ['field' => 'precio_sin_imp'])
                                </div>
                                 <a target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Utiliza este campo para indicar el precio final del producto y el sistema de forma automatica, calculara el costo, con utilidad cero.</b>">¿Necesitas Ayuda?
                                        </a></label>
                                <div class="form-group{{ $errors->has('precio_final') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-precio_final">{{ __('Precio Final') }}</label>
                                    <input type="number" step="any" name="precio_final" id="input-precio_final" class="form-control form-control-alternative{{ $errors->has('precio_final') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Final') }}" value="{{ old('precio_final', $producto->precio_final) }}" >
                                    @include('alerts.feedback', ['field' => 'precio_final'])
                                </div>
                                 
                                <div class="form-group{{ $errors->has('flotante') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="flotante">{{ __('¿Permite modificar precios al facturar?') }}</label>
                                    <select class="form-control form-control-alternative" id="flotante" name="flotante" value="{{ old('flotante', $producto->flotante) }}" required>
                                        <option value="0" {{ ($producto->flotante == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($producto->flotante == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_producto'])
                                </div>
                                <div class="form-group{{ $errors->has('exportable') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="flotante">{{ __('¿Es Exportable?') }}</label>
                                    <select class="form-control form-control-alternative" id="exportable" name="exportable" value="{{ old('exportable') }}" required>
                                        <option value="0" {{ ($producto->exportable == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($producto->exportable == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'exportable'])
                                </div>
                                <div class="form-group{{ $errors->has('partida_arancelaria') ? ' has-danger' : '' }}" id="combo_arancelaria" style="display: none;">
                                    <label class="form-control-label" for="input-partida_arancelaria">{{ __(' Partida Arancelaria') }}</label>
                                    <input type="text" name="partida_arancelaria" id="input-partida_arancelaria" class="form-control form-control-alternative{{ $errors->has('partida_arancelaria') ? ' is-invalid' : '' }}" placeholder="{{ __('Partida Arancelaria') }}" value="{{ old('partida_arancelaria', $producto->partida_arancelaria) }}" min="2" max="12">
                                    @include('alerts.feedback', ['field' => 'partida_arancelaria'])
                                </div>
                               <div class="form-group{{ $errors->has('registro_sanitario') ? ' has-danger' : '' }}">  
    <label class="form-control-label" for="registro_sanitario">{{ __('¿Medicamento con Registro Sanitario?') }}</label>  
    <select class="form-control form-control-alternative" id="reg_med" name="reg_med" value="{{ old('reg_med') }}" required>  
        <option value="0" {{ ($producto->reg_med == 0 ? 'selected="selected"' : '') }}>No</option>  
        <option value="1" {{ ($producto->reg_med == 1 ? 'selected="selected"' : '') }}>Si</option>  
    </select>  
    @include('alerts.feedback', ['field' => 'registro_sanitario'])  
</div>  

<div class="form-group{{ $errors->has('registro_medicamento') ? ' has-danger' : '' }}" id="combo_registro_sanitario" style="display: none;">  
    <label class="form-control-label" for="input-registro_medicamento">{{ __(' Registro Medicamento') }}</label>  
    <input type="text" name="forma" id="input-forma" class="form-control form-control-alternative{{ $errors->has('forma') ? ' is-invalid' : '' }}" placeholder="{{ __('Registro Medicamento') }}" value="{{ old('registro_medicamento', $producto->forma) }}" min="2" max="12">  
    @include('alerts.feedback', ['field' => 'registro_medicamento'])  

    <label class="form-control-label" for="input-forma_farmaceutica">{{ __('Forma Farmaceutica') }}</label>  
    <input type="text" name="forma_farmaceutica" id="forma_farmaceutica" class="form-control form-control-alternative{{ $errors->has('forma_farmaceutica') ? ' is-invalid' : '' }}" >  

    <label class="form-control-label" for="input-forma_farmaceutica">{{ __('ID Forma Farmaceutica') }}</label>  
    <input type="text" name="cod_reg_med" id="cod_reg_med" value="{{ old('registro_medicamento', $producto->cod_reg_med) }}" readonly>  
    @include('alerts.feedback', ['field' => 'cliente'])  
</div>  
                                <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
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
<script type="text/javascript">
 document.addEventListener('DOMContentLoaded', function() {  
        var registroSanitarioSelect = document.getElementById('reg_med');  
        var comboRegistroSanitarioDiv = document.getElementById('combo_registro_sanitario');  

        // Función para mostrar/ocultar el div según la selección  
        function toggleRegistroSanitario() {  
            if (registroSanitarioSelect.value === '1') { // Si "Sí" está seleccionado  
                comboRegistroSanitarioDiv.style.display = 'block';  
            } else {  
                comboRegistroSanitarioDiv.style.display = 'none';  
            }  
        }  

        // Llama a la función al cargar la página  
        toggleRegistroSanitario();  

        // Agrega un evento al select para que responda al cambio  
        registroSanitarioSelect.addEventListener('change', toggleRegistroSanitario);  
    });  
    
    $(document).ready(function() {
     
         $('#exportable').change(function() {  
    if ($(this).val() > 0) { // Si se selecciona "Sí"  
        $('#combo_arancelaria').css("display", "block");  
        $('#combo_arancelaria input').prop("required", true); // Hacer requerido el input  
    } else { // Si se selecciona "No"  
        $('#combo_arancelaria').css("display", "none");  
        $('#combo_arancelaria input').prop("required", false); // Remover la obligatoriedad  
    }  
});    
    $('#reg_med').change(function() {  
    if ($(this).val() > 0) { // Si se selecciona "Sí"  
        $('#combo_registro_sanitario').css("display", "block");  
        $('#combo_registro_sanitario input').prop("required", true); // Hacer requerido el input  
    } else { // Si se selecciona "No"  
        $('#combo_registro_sanitario').css("display", "none");  
        $('#combo_registro_sanitario input').prop("required", false); // Remover la obligatoriedad  
    }  
});
 
       $( "#forma_farmaceutica" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/forma_farmaceutica')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.forma;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
        
        $(document).on("blur", "#forma_farmaceutica", function(event) {  
    event.preventDefault();  
    var forma_farmaceutica = $(this).val();  
    var URL = {!! json_encode(url('buscar-forma_farmaceutica')) !!};  

  if ($(this).val().length <= 0) {
            }else{  

    $.ajax({  
        type: 'get',  
        url: URL,  
        dataType: 'json',  
        data: { forma_farmaceutica: forma_farmaceutica },  
        success: function(response) {  
       
    $('#cod_reg_med').val(response['success'][0]['codigo']);  
   
        },  
        error: function(xhr, status, error) {  
            console.error("Error en la petición AJAX:", xhr, status, error);  
            alert('Ocurrió un error al buscar. Intente nuevamente.');  
        }  
    }); 
            }
 


      
    });
 
$('#impuesto_iva').change(function() {  
    const valoresImpuesto = {  
        '01': '0',  
        '02': '1',  
        '03': '2',  
        '04': '4',  
        '05': '0',  
        '06': '4',  
        '07': '8',  
        '08': '13',  
        '09': '0.50',  
        '10': '0',  
        '11': '0'  
    };  

    const valorSeleccionado = $(this).val();  
    const porcentaje = valoresImpuesto[valorSeleccionado] !== undefined ? valoresImpuesto[valorSeleccionado] : '';  

    $('#porcentaje_imp').val(porcentaje);  
}); 
        $("#input-utilidad_producto").blur(function() {
            var porcentaje_imp = $('#porcentaje_imp').val();
            var costo_prd = parseInt($('#input-costo').val());
            var utl_prod = parseInt($('#input-utilidad_producto').val());
            var precio_sin_imp = ((costo_prd * utl_prod)/100) + costo_prd;
            var imp = (precio_sin_imp * porcentaje_imp)/100;
            var precio_final = precio_sin_imp + imp;
            $('#input-precio_sin_imp').val(precio_sin_imp);
            $('#input-precio_final').val(precio_final);
        });
        
            $("#input-precio_final").blur(function() {
            var porcentaje_imp = $('#porcentaje_imp').val();
            var por_final=(1+(porcentaje_imp/100))
            var costo_prd = parseInt($('#input-precio_final').val());
            var utl_prod = parseInt($('#input-utilidad_producto').val());
            var precio_sin_imp = ((costo_prd / (porcentaje_imp+1)));
            var precio_sin_iva = (costo_prd/por_final).toFixed(2);
            var imp = (precio_sin_imp * porcentaje_imp)/100;
            var precio_final = precio_sin_imp + imp;
            $('#input-precio_sin_imp').val(precio_sin_iva);
            $('#input-precio_final').val(costo_prd);
            $('#input-costo').val(precio_sin_iva);
            $('#input-utilidad_producto').val(0);
            
        });
            
    });
</script>
@endsection
