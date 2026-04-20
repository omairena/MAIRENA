@extends('layouts.app', ['page' => __('Configuración Fiscal'), 'pageSlug' => 'createconfig'])
@section('content')
<div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Configuración Fiscal') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('config.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('config.update', $config->idconfigfact) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Información Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('nombre_empresa') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_empresa">{{ __('Nombre Empresa') }}</label>
                                    <input type="text" name="nombre_empresa" id="input-nombre_empresa" class="form-control form-control-alternative{{ $errors->has('nombre_empresa') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Empresa') }}" value="{{ old('nombre_empresa', $config->nombre_empresa) }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_empresa'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_id_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_id_emisor">{{ __('Tipo Identificación') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_id_emisor" name="tipo_id_emisor" value="{{ old('tipo_id_emisor', $config->tipo_id_emisor) }}" required>
                                        <option value="01" {{ ($config->tipo_id_emisor == 01 ? 'selected="selected"' : '') }}>Cédula Física</option>
                                        <option value="02" {{ ($config->tipo_id_emisor == 02 ? 'selected="selected"' : '') }}>Cédula Júridica</option>
                                        <option value="03" {{ ($config->tipo_id_emisor == 03 ? 'selected="selected"' : '') }}>DIME</option>
                                        <option value="04" {{ ($config->tipo_id_emisor == 04 ? 'selected="selected"' : '') }}>NITE</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_id_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('numero_id_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-numero_id_emisor">{{ __('Número de Identificación') }}</label>
                                    <input type="number" name="numero_id_emisor" id="input-numero_id_emisor" class="form-control form-control-alternative{{ $errors->has('numero_id_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ old('numero_id_emisor', $config->numero_id_emisor) }}" required>
                                    @include('alerts.feedback', ['field' => 'numero_id_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_emisor">{{ __('Nombre Emisor') }}</label>
                                    <input type="text" name="nombre_emisor" id="input-nombre_emisor" class="form-control form-control-alternative{{ $errors->has('nombre_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Emisor') }}" value="{{ old('nombre_emisor', $config->nombre_emisor) }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_emisor'])
                                </div>

                                <div class="form-group{{ $errors->has('telefono_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-telefono_emisor">{{ __('Teléfono') }}</label>
                                    <input type="number" name="telefono_emisor" id="input-telefono_emisor" class="form-control form-control-alternative{{ $errors->has('telefono_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Teléfono') }}" value="{{ old('telefono_emisor', $config->telefono_emisor) }}" required>
                                    @include('alerts.feedback', ['field' => 'telefono_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('provincia_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-provincia_emisor">{{ __('Provincia') }}</label>
                                    <select name="provincia_emisor" id="provincia_emisor" class="form-control form-control-alternative" value="{{ old('provincia_emisor', $config->provincia_emisor) }}">
                                        <option value="0">-- Seleccione una Provincia --</option>
                                        <option value="1" {{ ($config->provincia_emisor == 1 ? 'selected="selected"' : '') }}>San José</option>
                                        <option value="2"{{ ($config->provincia_emisor == 2 ? 'selected="selected"' : '') }}>Alajuela</option>
                                        <option value="3"{{ ($config->provincia_emisor == 3 ? 'selected="selected"' : '') }}>Cartago</option>
                                        <option value="4"{{ ($config->provincia_emisor == 4 ? 'selected="selected"' : '') }}>Heredia</option>
                                        <option value="5" {{ ($config->provincia_emisor == 5 ? 'selected="selected"' : '') }}>Guanacaste</option>
                                        <option value="6" {{ ($config->provincia_emisor == 6 ? 'selected="selected"' : '') }}>Puntarenas</option>
                                        <option value="7" {{ ($config->provincia_emisor == 7 ? 'selected="selected"' : '') }}>Limón</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('canton_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-canton_emisor">{{ __('Cantones') }}</label>
                                    <select name="canton_emisor" id="canton_emisor" class="form-control form-control-alternative" value="{{ old('canton_emisor', $config->canton_emisor) }}">
                                        <option value='0'>-- Seleccionar un Canton--</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('distrito_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-distrito_emisor">{{ __('Distrito') }}</label>
                                    <select name="distrito_emisor" id="distrito_emisor" class="form-control form-control-alternative" value="{{ old('distrito_emisor', $config->distrito_emisor) }}">
                                        <option value='0'>-- Seleccionar un Distrito--</option>
                                    </select>
                                </div>
                                 <div class="form-group{{ $errors->has('direccion_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-barrio_emisor">{{ __('Barrio') }}</label>
                                    <textarea name="barrio_emisor" id="input-barrio_emisor" class="form-control form-control-alternative{{ $errors->has('barrio_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Barrio') }}" value="{{ old('barrio_emisor') }}" required>{{ $config->barrio_emisor }}</textarea>
                                    @include('alerts.feedback', ['field' => 'barrio_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('direccion_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-direccion_emisor">{{ __('Dirección') }}</label>
                                    <textarea name="direccion_emisor" id="input-direccion_emisor" class="form-control form-control-alternative{{ $errors->has('direccion_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Dirección') }}" value="{{ old('direccion_emisor') }}" required>{{ $config->direccion_emisor }}</textarea>
                                    @include('alerts.feedback', ['field' => 'direccion_emisor'])
                                </div><br><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Credenciales De Conexión Hacienda') }}</h6><br><br>
                                <div class="form-group{{ $errors->has('client_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-client_id">{{ __('Entorno') }}</label>
                                    <select class="form-control form-control-alternative" id="client_id" name="client_id" value="{{ old('client_id', $config->client_id) }}"  required>
                                        <option value="1" {{ ($config->client_id == 1 ? 'selected="selected"' : '') }}>Producción</option>
                                        <option value="2" {{ ($config->client_id == 2 ? 'selected="selected"' : '') }}>Pruebas</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'client_id'])
                                </div>
                                <div class="form-group{{ $errors->has('credenciales_conexion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-credenciales_conexion">{{ __('Usuario Hacienda (Example: cpf-99-9999-9999@stag.comprobanteselectronicos.go.cr)') }}</label>
                                    <input type="text" name="credenciales_conexion" id="input-credenciales_conexion" class="form-control form-control-alternative{{ $errors->has('credenciales_conexion') ? ' is-invalid' : '' }}" placeholder="{{ __('Usuario Hacienda') }}" value="{{ old('credenciales_conexion', $config->credenciales_conexion) }}" required>
                                    @include('alerts.feedback', ['field' => 'credenciales_conexion'])
                                </div>
                                <div class="form-group{{ $errors->has('clave_conexion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-clave_conexion">{{ __('Clave Hacienda') }}</label>
                                    <input type="text" name="clave_conexion" id="input-clave_conexion" class="form-control form-control-alternative{{ $errors->has('clave_conexion') ? ' is-invalid' : '' }}" placeholder="{{ __('Clave Hacienda') }}" value="{{ old('clave_conexion', $config->clave_conexion) }}" required>
                                    @include('alerts.feedback', ['field' => 'clave_conexion'])
                                </div>
                                <div class="form-group{{ $errors->has('ruta_certificado') ? ' has-danger' : '' }}" style="height: 60px;border: 2px dashed #00e7c8 !important;">
                                    <label class="form-control-label" for="input-ruta_certificado">{{ __('Certificado digital') }}</label>
                                    <input type="file" name="ruta_certificado" class="form-control form-control-alternative" id="input-ruta_certificado" value="{{ old('ruta_certificado', $config->ruta_certificado) }}">
                                    <i class="fas fa-paperclip" style="color: #00f2c3;"></i>
                                    @include('alerts.feedback', ['field' => 'ruta_certificado'])
                                </div>
                                <div class="form-group{{ $errors->has('clave_certificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-clave_certificado">{{ __('Clave Certificado') }}</label>
                                    <input type="password" name="clave_certificado" id="input-clave_certificado" class="form-control form-control-alternative{{ $errors->has('clave_certificado') ? ' is-invalid' : '' }}" placeholder="{{ __('Clave Certificado') }}" value="{{ old('clave_certificado', $config->clave_certificado) }}" required>
                                    @include('alerts.feedback', ['field' => 'clave_certificado'])
                                </div>
                                 <div class="form-group{{ $errors->has('fecha_certificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-fecha_certificado">{{ __('Fecha Fin Certificado') }}</label>
                                    <input type="date" name="fecha_certificado" id="input-fecha_certificado" class="form-control form-control-alternative{{ $errors->has('fecha_certificado') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Certificado') }}" value="{{ old('fecha_certificado', $config->fecha_certificado) }}" required>
                                    @include('alerts.feedback', ['field' => 'fecha_certificado'])
                                </div>
                                <div class="form-group{{ $errors->has('sucursal') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-sucursal">{{ __('Sucursal') }}</label>
                                    <select name="sucursal" id="sucursal" class="form-control form-control-alternative" value="{{ old('sucursal', $config->sucursal) }}">
                                        <option value="0">-- Seleccione una Sucursal --</option>
                                        <option value="001" {{ ($config->sucursal == 001 ? 'selected="selected"' : '') }}>Principal</option>
                                        <option value="002" {{ ($config->sucursal == 002 ? 'selected="selected"' : '') }}>Segunda Sucursal</option>
                                        <option value="003" {{ ($config->sucursal == 003 ? 'selected="selected"' : '') }}>Tercera Sucursal</option>
                                        <option value="004" {{ ($config->sucursal == 004 ? 'selected="selected"' : '') }}>Cuarta Sucursal</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('factor_receptor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-factor_receptor">{{ __('Fáctor Receptor') }}</label>
                                    <input type="number" name="factor_receptor" id="input-factor_receptor" class="form-control form-control-alternative{{ $errors->has('factor_receptor') ? ' is-invalid' : '' }}" placeholder="{{ __('Fáctor Receptor') }}" value="{{ old('factor_receptor', $config->factor_receptor) }}" required>
                                    @include('alerts.feedback', ['field' => 'factor_receptor'])
                                </div><br><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Datos de Impresión') }}</h6><br><br>
                                <div class="form-group{{ $errors->has('imprimir_comanda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-imprimir_comanda">{{ __('¿Imprimir Comanda?') }}</label>
                                    <select name="imprimir_comanda" id="imprimir_comanda" class="form-control form-control-alternative" value="{{ old('imprimir_comanda') }}">
                                        <option value="0" {{ ($config->imprimir_comanda == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->imprimir_comanda == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'imprimir_comanda'])
                                </div>
                                 <div class="form-group{{ $errors->has('tipo_moneda_confi') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_moneda_confi">{{ __('Moneda por Principal') }}</label>
                                    <select name="tipo_moneda_confi" id="tipo_moneda_confi" class="form-control form-control-alternative" value="{{ old('tipo_moneda_confi') }}">
                                        <option value="CRC" {{ ($config->tipo_moneda_confi == 'CRC' ? 'selected="selected"' : '') }}>CRC</option>
                                        <option value="USD" {{ ($config->tipo_moneda_confi == 'USD' ? 'selected="selected"' : '') }}>USD</option>
                                        <option value="EUR" {{ ($config->tipo_moneda_confi == 'EUR' ? 'selected="selected"' : '') }}>EURO</option>
                                        
                                    </select>
                                    @include('alerts.feedback', ['field' => 'imprimir_comanda'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_impresora') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_impresora">{{ __('Nombre Impresora') }}</label>
                                    <input type="text" name="nombre_impresora" id="input-nombre_impresora" class="form-control form-control-alternative{{ $errors->has('nombre_impresora') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Impresora') }}" value="{{ old('nombre_impresora', $config->nombre_impresora) }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_impresora'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_lector') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_lector">{{ __('¿Usa Lector?') }}</label>
                                    <select name="usa_lector" id="usa_lector" class="form-control form-control-alternative" value="{{ old('usa_lector') }}" required="true">
                                        <option value="0" {{ ($config->usa_lector == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->usa_lector == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_lector'])
                                </div>
                                <div class="form-group{{ $errors->has('es_transporte') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_transporte">{{ __('¿Es Configuración para Transporte?') }}</label>
                                    <select name="es_transporte" id="es_transporte" class="form-control form-control-alternative" value="{{ old('es_transporte') }}" required="true">
                                        <option value="0" {{ ($config->es_transporte == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->es_transporte == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_transporte'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_balanza') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_balanza">{{ __('¿Usa Balanza?') }}</label>
                                    <select name="usa_balanza" id="usa_balanza" class="form-control form-control-alternative" value="{{ old('usa_balanza') }}" required="true">
                                        <option value="0" {{ ($config->usa_balanza == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->usa_balanza == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_balanza'])
                                </div><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Configuración para Regimen Simplificado') }}</h6><br>
                                <div class="form-group{{ $errors->has('es_simplificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_simplificado">{{ __('¿Es Regimen Simplificado?') }}</label>
                                    <select name="es_simplificado" id="es_simplificado" class="form-control form-control-alternative" value="{{ old('es_simplificado') }}" required="true">
                                        <option value="0" {{ ($config->es_simplificado == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->es_simplificado == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div>
                                <h6 class="heading-small text-muted mb-4">{{ __('Cuenta de Correos Automatico') }}</h6><br>
                                <div class="form-group{{ $errors->has('servidor_email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="servidor_email">{{ __('Servidor') }}</label>
                                    <input type="text" name="servidor_email" id="servidor_email" class="form-control form-control-alternative{{ $errors->has('email_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Servidor Email') }}" value="{{ old('servidor_email', $config->servidor_email) }}" required>
                                    @include('alerts.feedback', ['field' => 'servidor_email'])
                                </div>
                                <div class="form-group{{ $errors->has('email_servidor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="email_servidor">{{ __(' Correo Electronico de Recepcion Automatica') }}</label>
                                    <input type="text" name="email_servidor" id="email_servidor" class="form-control form-control-alternative{{ $errors->has('email_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Correo Electronico de Recepcion Automatica') }}" value="{{ old('email_servidor', $config->email_servidor) }}" required>
                                    @include('alerts.feedback', ['field' => 'email_servidor'])
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="clave_email_servidor">{{ __('Contraseña de Email de recepcion automatica') }}</label>
                                    <input type="password" name="clave_email_servidor" id="clave_email_servidor" class="form-control form-control-alternative{{ $errors->has('clave_email_servidor') ? ' is-invalid' : '' }}" placeholder="{{ __('Contraseña de Email de recepcion automatica') }}" value="{{ old('clave_email_servidor', $config->email_servidor) }}" required>
                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_listaprecio') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_listaprecio">{{ __('¿Usa Lista de Precio?') }}</label>
                                    <select name="usa_listaprecio" id="usa_listaprecio" class="form-control form-control-alternative" value="{{ old('usa_listaprecio') }}" required="true">
                                        <option value="0" {{ ($config->usa_listaprecio == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->usa_listaprecio == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div>
                                <div class="form-group{{ $errors->has('proveedor_sistema') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="proveedor_sistema">{{ __('Numero de Proveedor de Sistema') }}</label>
                                    <input type="text" name="proveedor_sistema" id="proveedor_sistema" class="form-control form-control-alternative{{ $errors->has('proveedor_sistema') ? ' is-invalid' : '' }}" placeholder="{{ __('Numero de Proveedor de Sistema') }}" value="{{ old('proveedor_sistema', $config->proveedor_sistema) }}" required>
                                    @include('alerts.feedback', ['field' => 'proveedor_sistema'])
                                </div>
                              <div class="form-group{{ $errors->has('logo') ? ' has-danger' : '' }}">
    <label class="form-control-label" for="input-logo">{{ __('Logo') }}</label>
    
    <div class="upload-area" style="border: 2px dashed #00e7c8; padding: 20px; text-align: center; border-radius: 5px; margin-top: 10px;">
        <p>Arrastre y suelte su imagen aquí, o haga clic para cargar su logo.</p>
        <input type="file" name="logo" id="input-logo" class="form-control form-control-alternative" style="display: none;" accept="image/*" onchange="updateLabel(this)">
        <label for="input-logo" style="cursor: pointer; display: block; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">Cargar Logo</label>
    </div>
    
    @include('alerts.feedback', ['field' => 'logo'])
</div>
<div class="form-group{{ $errors->has('otros_datos_factura') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-otros_datos_factura">{{ __('Datos Adicionales en Factura') }}</label>
                                    <textarea name="otros_datos_factura" id="input-otros_datos_factura" class="form-control form-control-alternative{{ $errors->has('otros_datos_factura') ? ' is-invalid' : '' }}" placeholder="{{ __('Datos Adicionales en Factura') }}" value="{{ old('otros_datos_factura') }}" required>{{ $config->otros_datos_factura }}</textarea>
                                    @include('alerts.feedback', ['field' => 'direccion_emisor'])
                                </div>
 <h6 class="heading-small text-muted mb-4">{{ __('Auto Copia Correo') }}</h6><br>
                                <div class="form-group{{ $errors->has('auto_copia_email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="auto_copia_email">{{ __('¿Enviar Copia del Email de las facturas a su correo principal?') }}</label>
                                    <select name="auto_copia_email" id="auto_copia_email" class="form-control form-control-alternative" value="{{ old('auto_copia_email') }}" required="true">
                                        <option value="0" {{ ($config->auto_copia_email == 0 ? 'selected="selected"' : '') }}>No</option>
                                        <option value="1" {{ ($config->auto_copia_email == 1 ? 'selected="selected"' : '') }}>Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div>
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
    $(document).ready(function() {
        var provincia = $('#provincia_emisor').val();
        var canton_asignado = pad({!! $config->canton_emisor !!}, 2);
        // Empty the dropdown
        $('#canton_emisor').find('option').not(':first').remove();
        $('#distrito_emisor').find('option').not(':first').remove();
        var APP_URL = {!! json_encode(url('/ajaxCantones')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{provincia:provincia},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var idcanton = response['success'][i].idcanton;
                            var nombre = response['success'][i].nombre;
                            var codigo_canton = response['success'][i].codigo_canton;
                            if (codigo_canton+'' === ''+canton_asignado) {
                                var option = "<option value='"+idcanton+"' selected='selected'>"+codigo_canton+"-"+nombre+"</option>";
                            }else{
                                var option = "<option value='"+idcanton+"'>"+codigo_canton+"-"+nombre+"</option>";
                            }
                            $("#canton_emisor").append(option);
                        }
                    }
                    var canton = $('#canton_emisor').val();
            $('#distrito_emisor').find('option').not(':first').remove();
            var distrito_asignado = pad({!! $config->distrito_emisor !!}, 2);
            var APP_URL2 = {!! json_encode(url('/ajaxDistritos')) !!};
            $.ajax({

                type:'GET',

                url: APP_URL2,

                data:{canton:canton},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var iddistrito = response['success'][i].iddistrito;
                            var nombre = response['success'][i].nombre;
                            var codigo_distrito = response['success'][i].codigo_distrito;
                            if (codigo_distrito+'' === ''+distrito_asignado) {
                                var option = "<option value='"+iddistrito+"' selected='selected'>"+codigo_distrito+"-"+nombre+"</option>";
                            }else{
                                var option = "<option value='"+iddistrito+"'>"+codigo_distrito+"-"+nombre+"</option>";
                            }
                            $("#distrito_emisor").append(option);
                        }
                    }
                }

            });
                }

            });

        $('#provincia_emisor').change(function(e) {
            var provincia = $(this).val();
            var canton_asignado = {!! $config->canton_emisor !!};
            // Empty the dropdown
            $('#canton_emisor').find('option').not(':first').remove();
            $('#distrito_emisor').find('option').not(':first').remove();
            var APP_URL = {!! json_encode(url('/ajaxCantones')) !!};

            $.ajax({

                type:'GET',

                url: APP_URL,

                data:{provincia:provincia},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var idcanton = response['success'][i].idcanton;
                            var nombre = response['success'][i].nombre;
                            var codigo_canton = response['success'][i].codigo_canton;
                            if (codigo_canton+'' === ''+canton_asignado) {
                                var option = "<option value='"+idcanton+"' selected='selected'>"+codigo_canton+"-"+nombre+"</option>";
                            }else{
                                var option = "<option value='"+idcanton+"'>"+codigo_canton+"-"+nombre+"</option>";
                            }
                            $("#canton_emisor").append(option);
                        }
                    }
                }

            });
        });
        $('#canton_emisor').change(function() {
            var canton = $(this).val();
            $('#distrito_emisor').find('option').not(':first').remove();
            var distrito_asignado = {!! str_pad($config->distrito_emisor, 2, "0", STR_PAD_LEFT) !!};
            var APP_URL2 = {!! json_encode(url('/ajaxDistritos')) !!};
            $.ajax({

                type:'GET',

                url: APP_URL2,

                data:{canton:canton},

                dataType: 'json',

                success:function(response){
                    var len = 0;
                    if(response['success'] != null){
                        len = response['success'].length;
                    }
                    if(len > 0){
                        // Read data and create <option >
                        for(var i=0; i<len; i++){
                            var iddistrito = response['success'][i].iddistrito;
                            var nombre = response['success'][i].nombre;
                            var codigo_distrito = response['success'][i].codigo_distrito;
                            if (codigo_distrito+'' === ''+distrito_asignado) {
                                var option = "<option value='"+iddistrito+"' selected='selected'>"+codigo_distrito+"-"+nombre+"</option>";
                            }else{
                                var option = "<option value='"+iddistrito+"'>"+codigo_distrito+"-"+nombre+"</option>";
                            }
                            $("#distrito_emisor").append(option);
                        }
                    }
                }

            });
        });
        $(document).on("blur", "#input-numero_id_emisor" , function(event) {
            var id = $(this).val();
            var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
            $.ajax({
                type:'GET',
                url: URL,
                dataType: 'json',
                success:function(response){
                    if (typeof response =='object') {
                        $('#input-nombre_emisor').val(response.nombre);
                        $('#tipo_id_emisor').val(response.tipoIdentificacion);
                    }
                },
                error:function(response){
                    alert('Identificación No Encontrada');
                    $('#input-nombre_emisor').val('');
                    $('#tipo_id_emisor').val('');
                }
            });
        });

        function pad(n, width, z) {
            var z = z || '0';
            var n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        }
    });
    
    function updateLabel(input) {
        if (input.files && input.files[0]) {
            let fileName = input.files[0].name;
            const uploadArea = document.querySelector('.upload-area p');
            uploadArea.textContent = 'Archivo seleccionado: ' + fileName;
        } else {
            const uploadArea = document.querySelector('.upload-area p');
            uploadArea.textContent = 'Arrastre y suelte su imagen aquí, o haga clic para cargar su logo.';
        }
    }
    
</script>
@endsection
