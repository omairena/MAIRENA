
@extends('layouts.app', ['page' => __('Configuración Completa'), 'pageSlug' => 'createconfigfull'])

@section('content')

<style>
.step {
    display: none !important;
}

.step.active-step {
    display: block !important;
}

.config-tabs .nav-link {
    cursor: pointer;
}

.config-tabs .nav-link.active {
    font-weight: 600;
}
</style>

<div class="container-fluid mt--7">
<div class="row">
<div class="col-xl-12 order-xl-1">
<div class="card">
<div class="card-header">
    <h3>Configuración Completa</h3>
</div>

<div class="card-body">

<form method="post" action="{{ route('config.store.full') }}" enctype="multipart/form-data" novalidate>
@csrf

<ul class="nav nav-tabs config-tabs mb-4" id="configTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-step1" type="button" role="tab" onclick="goToStep(1)">
            Información Fiscal
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-step2" type="button" role="tab" onclick="goToStep(2)">
            Configuración de Caja
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-step3" type="button" role="tab" onclick="goToStep(3)">
            Actividad
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-step4" type="button" role="tab" onclick="goToStep(4)">
            Recepción Automática
        </button>
    </li>
</ul>


<div class="step" id="step1">


<h4>Información Fiscal</h4>

<!-- 👇 PEGA AQUÍ TODO TU FORM ORIGINAL SIN EL BOTÓN GUARDAR -->

 <h6 class="heading-small text-muted mb-4">{{ __('Información Fiscal') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('nombre_empresa') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_empresa">{{ __('Nombre Empresa') }}</label>
                                    <input type="text" name="nombre_empresa" id="input-nombre_empresa" class="form-control form-control-alternative{{ $errors->has('nombre_empresa') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Empresa') }}" value="{{ old('nombre_empresa') }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_empresa'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_id_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_id_emisor">{{ __('Tipo Identificación') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_id_emisor" name="tipo_id_emisor" value="{{ old('tipo_id_emisor') }}" required>
                                        <option value="01">Cédula Física</option>
                                        <option value="02">Cédula Júridica</option>
                                        <option value="03">DIME</option>
                                        <option value="04">NITE</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_id_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('numero_id_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-numero_id_emisor">{{ __('Número de Identificación') }}</label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="numero_id_emisor" id="input-numero_id_emisor" class="form-control form-control-alternative{{ $errors->has('numero_id_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ old('numero_id_emisor') }}" onblur="consultarCedulaEmisor()" onkeydown="if(event.key==='Enter'){event.preventDefault(); consultarCedulaEmisor();}" required>
                                    @include('alerts.feedback', ['field' => 'numero_id_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_emisor">{{ __('Nombre Emisor') }}</label>
                                    <input type="text" name="nombre_emisor" id="input-nombre_emisor" class="form-control form-control-alternative{{ $errors->has('nombre_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Emisor') }}" value="{{ old('nombre_emisor') }}" required>
                                    @include('alerts.feedback', ['field' => 'nombre_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('telefono_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-telefono_emisor">{{ __('Teléfono') }}</label>
                                    <input type="number" name="telefono_emisor" id="input-telefono_emisor" class="form-control form-control-alternative{{ $errors->has('telefono_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Teléfono') }}" value="{{ old('telefono_emisor') }}" required>
                                    @include('alerts.feedback', ['field' => 'telefono_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('provincia_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-provincia_emisor">{{ __('Provincia') }}</label>
                                    <select name="provincia_emisor" id="provincia_emisor" class="form-control form-control-alternative">
                                        <option value="0">-- Seleccione una Provincia --</option>
                                        <option value="1">San José</option>
                                        <option value="2">Alajuela</option>
                                        <option value="3">Cartago</option>
                                        <option value="4">Heredia</option>
                                        <option value="5">Guanacaste</option>
                                        <option value="6">Puntarenas</option>
                                        <option value="7">Limón</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'provincia_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('canton_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-canton_emisor">{{ __('Cantones') }}</label>
                                    <select name="canton_emisor" id="canton_emisor" class="form-control form-control-alternative">
                                        <option value='0'>-- Seleccionar un Canton--</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'canton_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('distrito_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-distrito_emisor">{{ __('Distrito') }}</label>
                                    <select name="distrito_emisor" id="distrito_emisor" class="form-control form-control-alternative">
                                        <option value='0'>-- Seleccionar un Distrito--</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'distrito_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('barrio_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-barrio_emisor">{{ __('Barrio') }}</label>
                                    <textarea name="barrio_emisor" id="input-barrio_emisor" class="form-control form-control-alternative{{ $errors->has('barrio_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Barrio') }}" value="{{ old('barrio_emisor') }}" required></textarea>
                                    @include('alerts.feedback', ['field' => 'barrio_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('direccion_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-direccion_emisor">{{ __('Dirección') }}</label>
                                    <textarea name="direccion_emisor" id="input-direccion_emisor" class="form-control form-control-alternative{{ $errors->has('direccion_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Dirección') }}" value="{{ old('direccion_emisor') }}" required></textarea>
                                    @include('alerts.feedback', ['field' => 'direccion_emisor'])
                                </div><br><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Credenciales De Conexión Hacienda') }}</h6><br><br>
                                <div class="form-group{{ $errors->has('client_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-client_id">{{ __('Entorno') }}</label>
                                    <select class="form-control form-control-alternative" id="client_id" name="client_id" value="{{ old('client_id') }}" required>
                                        <option value="1">Producción</option>
                                        <option value="2">Pruebas</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'client_id'])
                                </div>
                                <div class="form-group{{ $errors->has('credenciales_conexion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-credenciales_conexion">{{ __('Usuario Hacienda (Example: cpf-99-9999-9999@stag.comprobanteselectronicos.go.cr)') }}</label>
                                    <input type="text" name="credenciales_conexion" id="input-credenciales_conexion" class="form-control form-control-alternative{{ $errors->has('credenciales_conexion') ? ' is-invalid' : '' }}" placeholder="{{ __('Usuario Hacienda') }}" value="{{ 'cpf-02-0641-0122@prod.comprobanteselectronicos.go.cr' }}" required>
                                    @include('alerts.feedback', ['field' => 'credenciales_conexion'])
                                </div>
                                <div class="form-group{{ $errors->has('clave_conexion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-clave_conexion">{{ __('Clave Hacienda') }}</label>
                                    <input type="text" name="clave_conexion" id="input-clave_conexion" class="form-control form-control-alternative{{ $errors->has('clave_conexion') ? ' is-invalid' : '' }}" placeholder="{{ __('Clave Hacienda') }}" value="{{ 'Jd%u0b97EMZS=g2mO-!+' }}" required>
                                    @include('alerts.feedback', ['field' => 'clave_conexion'])
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="input-test">{{ __('Test Credenciales Hacienda') }}</label>
                                    <br>
                                    <button type="button" class="btn btn-warning mt-4 text-right" id="probarCredenciales" onclick="probarCredencialesHacienda()">Test</button>
                                    @include('alerts.feedback', ['field' => 'test'])
                                </div>
                                <div class="form-group{{ $errors->has('ruta_certificado') ? ' has-danger' : '' }}" style="height: 60px;border: 2px dashed #00e7c8 !important;">
                                    <label class="form-control-label" for="input-ruta_certificado">{{ __('Certificado digital') }}</label>
                                    <input type="file" class="form-control" name="ruta_certificado" id="ruta_certificado" value="{{ old('ruta_certificado') }}" required >
                                    <i class="fas fa-paperclip" style="color: #00f2c3;"></i>
                                    @include('alerts.feedback', ['field' => 'ruta_certificado'])
                                </div>
                                <div class="form-group{{ $errors->has('clave_certificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-clave_certificado">{{ __('Clave Certificado') }}</label>
                                    <input type="password" name="clave_certificado" id="input-clave_certificado" class="form-control form-control-alternative{{ $errors->has('clave_certificado') ? ' is-invalid' : '' }}" placeholder="{{ __('Clave Certificado') }}" value="{{ old('clave_certificado') }}" required readonly="true">
                                    @include('alerts.feedback', ['field' => 'clave_certificado'])
                                </div>
                                 <div class="form-group{{ $errors->has('fecha_certificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-fecha_certificado">{{ __('Fecha Vencimiento Certificado') }}</label>
                                    <input type="date" name="fecha_certificado" id="input-fecha_certificado" class="form-control form-control-alternative{{ $errors->has('fecha_certificado') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Certificado') }}" value="{{ '2030-04-14' }}" required>
                                    @include('alerts.feedback', ['field' => 'fecha_certificado'])
                                </div>
                                <div class="form-group{{ $errors->has('sucursal') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-sucursal">{{ __('Sucursal') }}</label>
                                    <select name="sucursal" id="sucursal" class="form-control form-control-alternative" value="{{ old('sucursal') }}">
                                       <!-- <option value="0">-- Seleccione una Sucursal --</option>-->
                                        <option value="001">Principal</option>
                                        <option value="002">Segunda Sucursal</option>
                                        <option value="003">Tercera Sucursal</option>
                                        <option value="004">Cuarta Sucursal</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'sucursal'])
                                </div>
                                <div class="form-group{{ $errors->has('factor_receptor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-factor_receptor">{{ __('Fáctor Receptor') }}</label>
                                    <input type="number" name="factor_receptor" id="input-factor_receptor" class="form-control form-control-alternative{{ $errors->has('factor_receptor') ? ' is-invalid' : '' }}" placeholder="{{ __('Fáctor Receptor') }}" value="{{ '100' }}" readonly required>
                                    @include('alerts.feedback', ['field' => 'factor_receptor'])
                                </div><br><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Credenciales De Acceso') }}</h6><br><br>
                                <div class="form-group{{ $errors->has('email_emisor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email_emisor">{{ __('Email Usuario') }}</label>
                                    <input type="email" name="email_emisor" id="input-email_emisor" class="form-control form-control-alternative{{ $errors->has('email_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ 'osmv789@gmail.com' }}" required>
                                    @include('alerts.feedback', ['field' => 'email_emisor'])
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-password">{{ __('Contraseña de Usuario') }}</label>
                                    <input type="password" name="password" id="input-password" class="form-control form-control-alternative{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Contraseña de Usuario') }}" value="{{ old('password') }}" required>
                                    @include('alerts.feedback', ['field' => 'password'])
                                </div><br><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Datos de Impresión') }}</h6><br><br>
                                <div class="form-group{{ $errors->has('imprimir_comanda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-imprimir_comanda">{{ __('¿Imprimir Comanda?') }}</label>
                                    <select name="imprimir_comanda" id="imprimir_comanda" class="form-control form-control-alternative"  readonly value="{{ old('imprimir_comanda') }}">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'imprimir_comanda'])
                                </div>
                                <div class="form-group{{ $errors->has('nombre_impresora') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-nombre_impresora">{{ __('Nombre Impresora') }}</label>
                                    <input type="text" name="nombre_impresora" id="input-nombre_impresora" class="form-control form-control-alternative{{ $errors->has('nombre_impresora') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre Impresora') }}" value="{{ 'General' }}" readonly required>
                                    @include('alerts.feedback', ['field' => 'nombre_impresora'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_lector') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_lector">{{ __('¿Usa Lector?') }}</label>
                                    <select name="usa_lector" id="usa_lector" class="form-control form-control-alternative" value="{{ old('usa_lector') }}" readonly required="true">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_lector'])
                                </div>
                                <div class="form-group{{ $errors->has('es_transporte') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_transporte">{{ __('¿Es Configuración para Transporte?') }}</label>
                                    <select name="es_transporte" id="es_transporte" class="form-control form-control-alternative" value="{{ old('es_transporte') }}" readonly required="true">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_transporte'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_balanza') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_balanza">{{ __('¿Usa Balanza?') }}</label>
                                    <select name="usa_balanza" id="usa_balanza" class="form-control form-control-alternative" value="{{ old('usa_balanza') }}" readonly required="true">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'usa_balanza'])
                                </div><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Configuración para Regimen Simplificado') }}</h6><br>
                                <div class="form-group{{ $errors->has('es_simplificado') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="es_simplificado">{{ __('¿Es Regimen Simplificado?') }}</label>
                                    <select name="es_simplificado" id="es_simplificado" class="form-control form-control-alternative" value="{{ old('es_simplificado') }}" readonly required="true">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div><br>
                                <h6 class="heading-small text-muted mb-4">{{ __('Cuenta de Correos Automatico') }}</h6><br>
                                <div class="form-group{{ $errors->has('servidor_email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="servidor_email">{{ __('Servidor') }}</label>
                                    <label class="form-control-label" for="servidor_email">{{ __('feisaac.com:143/notls') }}</label>
                                    <input type="text" name="servidor_email" id="servidor_email" class="form-control form-control-alternative{{ $errors->has('email_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Servidor Email') }}" value="{{ 'feisaac.com:143/notls' }}" readonly required>
                                    @include('alerts.feedback', ['field' => 'servidor_email'])
                                </div>
                                <div class="form-group{{ $errors->has('email_servidor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="email_servidor">{{ __(' Correo Electronico de Recepcion Automatica') }}</label>
                                    <input type="text" name="email_servidor" id="email_servidor" class="form-control form-control-alternative{{ $errors->has('email_emisor') ? ' is-invalid' : '' }}" placeholder="{{ __('Correo Electronico de Recepcion Automatica') }}" value="{{ 'pch.online2025@gmail.com' }}"  readonly required>
                                    @include('alerts.feedback', ['field' => 'email_servidor'])
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="clave_email_servidor">{{ __('Contraseña de Email de recepcion automatica') }}</label>
                                    <input type="password" name="clave_email_servidor" id="clave_email_servidor" class="form-control form-control-alternative{{ $errors->has('clave_email_servidor') ? ' is-invalid' : '' }}" placeholder="{{ __('Contraseña de Email de recepcion automatica') }}" value="{{ '123456789' }}" readonly required>
                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group{{ $errors->has('usa_listaprecio') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="usa_listaprecio">{{ __('¿Usa Lista de Precio?') }}</label>
                                    <select name="usa_listaprecio" id="usa_listaprecio" class="form-control form-control-alternative" value="{{ old('usa_listaprecio') }}" readonly required="true">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div>
                                <div class="form-group{{ $errors->has('proveedor_sistema') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="proveedor_sistema">{{ __('Numero de Proveedor de Sistema') }}</label>
                                    <input type="password" name="proveedor_sistema" id="proveedor_sistema" class="form-control form-control-alternative{{ $errors->has('proveedor_sistema') ? ' is-invalid' : '' }}" placeholder="{{ __('Numero de Proveedor de Sistema') }}" value="{{ '206410122' }}" required readonly>
                                    @include('alerts.feedback', ['field' => 'proveedor_sistema'])
                                </div><br>
                                 <div class="form-group{{ $errors->has('docs') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="docs">{{ __('Documentos del Plan') }}</label>
                                    <input type="text" name="docs" id="docs" class="form-control form-control-alternative{{ $errors->has('docs') ? ' is-invalid' : '' }}" placeholder="{{ __('Documentos del Plan') }}" value="20" readonly required>
                                    @include('alerts.feedback', ['field' => 'docs'])
                                </div><br>
                                <div class="form-group{{ $errors->has('auto_copia_email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="auto_copia_email">{{ __('¿Enviar Copia del Email de las facturas a su correo principal?') }}</label>
                                    <select name="auto_copia_email" id="auto_copia_email" class="form-control form-control-alternative" value="{{ old('auto_copia_email') }}" required="true">
                                        <option value="0" >No</option>
                                        <option value="1" >Si</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'es_simplificado'])
                                </div><br>
                                 <div class="form-group{{ $errors->has('mail_not') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="mail_not">{{ __('Correo Cuenta Principal') }}</label>
                                    <input type="text" name="mail_not" id="mail_not" class="form-control form-control-alternative{{ $errors->has('mail_not') ? ' is-invalid' : '' }}" placeholder="{{ __('Correo Cuenta Principal') }}" value="{{ 'servicioscontables@pchconta.com' }}" required>
                                    @include('alerts.feedback', ['field' => 'mail_not'])
                                </div><br>

<!-- TODO lo demás igual: provincia, hacienda, certificado, etc -->

<div class="text-right mt-4">
    <button type="button" class="btn btn-primary" onclick="nextStep(2)">
        Siguiente
    </button>
</div>


</div>
</div>

<!-- ========================= -->

<!-- STEP 2: CAJA -->

<!-- ========================= -->

<div class="step" id="step2" style="display:none;">


<h4>Configuración de Caja</h4>

<div class="form-group">
    <label>Nombre Caja</label>
    <input type="text" name="nombre_caja" class="form-control" required>
</div>

<div class="form-group">
    <label>Código Único</label>
    <input type="text" name="codigo_unico" class="form-control" required>
</div>

<div class="text-between">
    <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Atrás</button>
    <button type="button" class="btn btn-primary" onclick="nextStep(3)">Siguiente</button>
</div>


</div>

<!-- ========================= -->

<!-- STEP 3: ACTIVIDAD -->

<!-- ========================= -->

<div class="step" id="step3" style="display:none;">


<h4>Actividad Principal</h4>

<div class="form-group">
    <label>Consulta por Número de Identificación</label>
    <div class="d-flex" style="gap: 10px;">
        <input type="text" id="actividad_identificacion" class="form-control" readonly>
        <button type="button" class="btn btn-info" id="btn-consultar-actividad" onclick="consultarActividadPorCedula()">Consultar</button>
    </div>
    <small id="actividad_estado" class="form-text text-muted"></small>
</div>

<div class="form-group">
    <label>Código de Actividad</label>
    <input type="text" name="hidden_codigo" id="hidden_codigo" class="form-control" required>
</div>

<div class="form-group">
    <label>Descripción de Actividad</label>
    <input type="text" name="hidden_descripcion" id="hidden_descripcion" class="form-control" required>
</div>

<div class="form-group">
    <label>¿Actividad Principal?</label>
    <select name="principal" class="form-control" required>
        <option value="1" selected>Si</option>
        <option value="0">No</option>
    </select>
</div>

<div class="text-between">
    <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="btn btn-primary" onclick="nextStep(4)">Siguiente</button>
</div>


</div>

<!-- ========================= -->

<!-- STEP 3: RECEPCIÓN -->

<!-- ========================= -->

<div class="step" id="step4" style="display:none;">


<h4>Recepción Automática</h4>

<input type="hidden" name="estatus" value="en espera">

<div class="form-group">
    <label>Punto de Venta (Caja)</label>
    <input type="text" id="display_caja_recepcion" class="form-control" readonly disabled>
    <small class="form-text text-muted">Se usará la caja configurada en el paso anterior.</small>
</div>

<div class="form-group">
    <label>Detalle Mensaje</label>
    <textarea name="detalle_mensaje" class="form-control" readonly>RECEPCIONADAS AUTOMATICAMENTE POR CORREO ELECTRONICO</textarea>
</div>

<div class="form-group">
    <label>Procesar Documento</label>
    <select name="procesar_doc" class="form-control">
        <option value="05" selected>Aceptado</option>
        <option value="06">Parcialmente Aceptado</option>
        <option value="07">Rechazado</option>
    </select>
</div>

<div class="text-between">
    <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Atrás</button>
    <button type="submit" class="btn btn-success">Guardar Todo</button>
</div>


</div>

</form>



</div>
</div>
</div>
</div>
</div>
@endsection

<script>
console.log("JS cargado");

let currentStep = 1;

function showStep(step){

document.querySelectorAll('.step').forEach(el => {
    el.classList.remove('active-step');
    el.style.display = 'none';
});

let target = document.getElementById('step'+step);

if(target){
    target.classList.add('active-step');
    target.style.display = 'block';
    setActiveTab(step);
    if (step === 3) {
        prepararTabActividad();
    }
    if (step === 4) {
        prepararTabRecepcion();
    }
} else {
    console.error("No existe step"+step);
}

}

function setActiveTab(step){
document.querySelectorAll('#configTabs .nav-link').forEach(el => {
    el.classList.remove('active');
    el.setAttribute('aria-selected', 'false');
});

let tab = document.getElementById('tab-step' + step);
if(tab){
    tab.classList.add('active');
    tab.setAttribute('aria-selected', 'true');
}
}

function goToStep(step){
currentStep = step;
showStep(step);
}

function nextStep(step){

console.log("click siguiente", step);

let current = document.getElementById('step' + currentStep);

if(!current){
    console.error('No existe paso actual:', currentStep);
    currentStep = step;
    showStep(step);
    return;
}

let inputs = current.querySelectorAll('input, select, textarea');

for (let input of inputs) {

    if (input.readOnly || input.disabled || input.type === 'hidden') continue;

    if (input.type === 'file') {
        if (input.required && (!input.files || input.files.length === 0)) {
            alert('Debe seleccionar el certificado digital para continuar.');
            input.focus();
            return;
        }
        continue;
    }

    if (!input.checkValidity()) {
        input.reportValidity();
        return;
    }
}

currentStep = step;
showStep(step);

}

function prevStep(step){
currentStep = step;
showStep(step);
}

function consultarCedulaEmisor(){
var input = document.getElementById('input-numero_id_emisor');
if(!input){
    return;
}

var id = (input.value || '').replace(/\D/g, '');
input.value = id;

if (id.length < 9) {
    return;
}

var URL = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + encodeURIComponent(id);

$.ajax({
    type:'GET',
    url: URL,
    dataType: 'json',
    success:function(response){
        if (typeof response === 'object' && response) {
            $('#input-nombre_emisor').val(response.nombre || '');
            $('#input-nombre_empresa').val(response.nombre || '');
            $('#input-nombre_impresora').val(response.nombre || '');
            $('#tipo_id_emisor').val(response.tipoIdentificacion || '');
        }
    },
    error:function(){
        alert('Identificación No Encontrada');
        $('#input-nombre_emisor').val('');
        $('#tipo_id_emisor').val('');
    }
});
}

function prepararTabRecepcion(){
    var nombreCaja = (document.querySelector('input[name="nombre_caja"]') || {}).value || '';
    var codigoUnico = (document.querySelector('input[name="codigo_unico"]') || {}).value || '';
    var display = document.getElementById('display_caja_recepcion');
    if (display) {
        var codigo = codigoUnico ? codigoUnico.toString().padStart(5, '0') + ' - ' : '';
        display.value = codigo + nombreCaja;
    }
}

function prepararTabActividad(){
var numeroInput = document.getElementById('input-numero_id_emisor');
var actividadCedula = document.getElementById('actividad_identificacion');

if (!numeroInput || !actividadCedula) {
    return;
}

var cedula = (numeroInput.value || '').replace(/\D/g, '');
actividadCedula.value = cedula;

if (cedula.length >= 9) {
    consultarActividadPorCedula();
}
}

function extraerActividadDesdeRespuesta(response){
if (!response || typeof response !== 'object') {
    return null;
}

var candidatos = [];

if (Array.isArray(response.actividades)) candidatos = response.actividades;
if (!candidatos.length && Array.isArray(response.actividadesEconomicas)) candidatos = response.actividadesEconomicas;
if (!candidatos.length && Array.isArray(response.actividadEconomica)) candidatos = response.actividadEconomica;
if (!candidatos.length && Array.isArray(response.actividad)) candidatos = response.actividad;

if (!candidatos.length && response.actividad && typeof response.actividad === 'object') {
    candidatos = [response.actividad];
}

var actividad = candidatos.length ? candidatos[0] : response;

var codigo = actividad.codigo || actividad.codigoActividad || actividad.codActividad || actividad.actividad;
var descripcion = actividad.descripcion || actividad.descripcionActividad || actividad.nombre || actividad.detalle || response.nombre;

if (!codigo && !descripcion) {
    return null;
}

return {
    codigo: String(codigo || '').trim(),
    descripcion: String(descripcion || '').trim()
};
}

async function consultarActividadPorCedula(){
var actividadCedula = document.getElementById('actividad_identificacion');
var codigoInput = document.getElementById('hidden_codigo');
var descripcionInput = document.getElementById('hidden_descripcion');
var estado = document.getElementById('actividad_estado');
var btn = document.getElementById('btn-consultar-actividad');

if (!actividadCedula || !codigoInput || !descripcionInput) {
    return;
}

var cedula = (actividadCedula.value || '').replace(/\D/g, '');
actividadCedula.value = cedula;

if (cedula.length < 9) {
    if (estado) estado.textContent = 'Ingrese una identificación válida para consultar actividad.';
    return;
}

if (btn) {
    btn.disabled = true;
    btn.textContent = 'Consultando...';
}
if (estado) estado.textContent = 'Consultando actividad...';

var url = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + encodeURIComponent(cedula);

try {
    var res = await fetch(url, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    });

    if (!res.ok) {
        throw new Error('HTTP ' + res.status);
    }

    var data = await res.json();
    var actividad = extraerActividadDesdeRespuesta(data);

    if (!actividad) {
        if (estado) estado.textContent = 'No se encontró actividad automática. Puede ingresarla manualmente.';
        return;
    }

    if (!codigoInput.value && actividad.codigo) {
        codigoInput.value = actividad.codigo;
    }
    if (!descripcionInput.value && actividad.descripcion) {
        descripcionInput.value = actividad.descripcion;
    }

    if (estado) estado.textContent = 'Actividad cargada desde consulta por identificación.';
} catch (err) {
    console.error('Error consultando actividad por cédula:', err);
    if (estado) estado.textContent = 'No se pudo consultar actividad. Puede completarla manualmente.';
} finally {
    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Consultar';
    }
}
}

function normalizarListaGeo(response) {
if (Array.isArray(response)) return response;
if (response && Array.isArray(response.success)) return response.success;
if (response && Array.isArray(response.data)) return response.data;
if (response && response.success && typeof response.success === 'object') return Object.values(response.success);
if (response && response.data && typeof response.data === 'object') return Object.values(response.data);
if (response && typeof response === 'object') return Object.values(response);
return [];
}

function construirOptionGeo(item, index, tipo) {
if (item == null) return null;

if (typeof item !== 'object') {
    var plano = String(item).trim();
    if (!plano) return null;
    return { value: plano, text: plano };
}

var keysValor = tipo === 'distrito'
    ? ['iddistrito', 'id_distrito', 'distrito_id', 'id', 'codigo_distrito', 'codigo']
    : ['idcanton', 'id_canton', 'canton_id', 'id', 'codigo_canton', 'codigo'];
var keysTexto = tipo === 'distrito'
    ? ['nombre', 'distrito', 'descripcion', 'detalle']
    : ['nombre', 'canton', 'descripcion', 'detalle'];

var value = '';
for (var i = 0; i < keysValor.length; i++) {
    var v = item[keysValor[i]];
    if (v != null && String(v).trim() !== '') {
        value = String(v).trim();
        break;
    }
}

var nombre = '';
for (var j = 0; j < keysTexto.length; j++) {
    var t = item[keysTexto[j]];
    if (t != null && String(t).trim() !== '') {
        nombre = String(t).trim();
        break;
    }
}

var codigo = '';
if (item.codigo_canton != null && String(item.codigo_canton).trim() !== '') {
    codigo = String(item.codigo_canton).trim();
} else if (item.codigo_distrito != null && String(item.codigo_distrito).trim() !== '') {
    codigo = String(item.codigo_distrito).trim();
} else if (item.codigo != null && String(item.codigo).trim() !== '') {
    codigo = String(item.codigo).trim();
}

if (!value) value = codigo || nombre || String(index + 1);
var text = nombre;
if (codigo && nombre) text = codigo + '-' + nombre;
if (!text) text = codigo || value;

return { value: value, text: text };
}

function resetSelect(selectEl) {
if (!selectEl) return;
while (selectEl.options.length > 1) {
    selectEl.remove(1);
}
}

async function cargarCantonesDesdeProvincia(provincia) {
var cantonEl = document.getElementById('canton_emisor');
var distritoEl = document.getElementById('distrito_emisor');
resetSelect(cantonEl);
resetSelect(distritoEl);

if (!provincia || provincia === '0') return;

var endpoint = @json(url('/ajaxCantones'));
var url = endpoint + '?provincia=' + encodeURIComponent(provincia);

try {
    var res = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } });
    if (!res.ok) {
        throw new Error('HTTP ' + res.status);
    }
    var data = await res.json();
    var lista = normalizarListaGeo(data);

    lista.forEach(function(item, idx) {
        var op = construirOptionGeo(item, idx, 'canton');
        if (!op) return;
        var optionEl = document.createElement('option');
        optionEl.value = op.value;
        optionEl.textContent = op.text;
        cantonEl.appendChild(optionEl);
    });

    if (cantonEl.options.length <= 1) {
        alert('No se pudieron cargar cantones para esta provincia.');
    }
} catch (err) {
    console.error('Error cargando cantones:', err);
    alert('Error cargando cantones. Verifica la ruta /ajaxCantones y que retorne JSON.');
}
}

async function cargarDistritosDesdeCanton(canton) {
var distritoEl = document.getElementById('distrito_emisor');
resetSelect(distritoEl);

if (!canton || canton === '0') return;

var endpoint = @json(url('/ajaxDistritos'));
var url = endpoint + '?canton=' + encodeURIComponent(canton);

try {
    var res = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } });
    if (!res.ok) {
        throw new Error('HTTP ' + res.status);
    }
    var data = await res.json();
    var lista = normalizarListaGeo(data);

    lista.forEach(function(item, idx) {
        var op = construirOptionGeo(item, idx, 'distrito');
        if (!op) return;
        var optionEl = document.createElement('option');
        optionEl.value = op.value;
        optionEl.textContent = op.text;
        distritoEl.appendChild(optionEl);
    });

    if (distritoEl.options.length <= 1) {
        alert('No se pudieron cargar distritos para este cantón.');
    }
} catch (err) {
    console.error('Error cargando distritos:', err);
    alert('Error cargando distritos. Verifica la ruta /ajaxDistritos y que retorne JSON.');
}
}

function setClaveCertificadoReadonly(estado) {
var claveCertificado = document.getElementById('input-clave_certificado');
if (!claveCertificado) {
    return;
}
claveCertificado.readOnly = !!estado;
}

async function probarCredencialesHacienda() {
var claveInput = document.getElementById('input-clave_conexion');
var entornoInput = document.getElementById('client_id');
var credencialesInput = document.getElementById('input-credenciales_conexion');
var btn = document.getElementById('probarCredenciales');

var clave_cre = claveInput ? (claveInput.value || '').trim() : '';
var entorno = entornoInput ? String(entornoInput.value || '').trim() : '';
var credenciales = credencialesInput ? (credencialesInput.value || '').trim() : '';

if (!credenciales || !clave_cre) {
    alert('Debe ingresar usuario y clave de Hacienda antes de probar.');
    setClaveCertificadoReadonly(true);
    return;
}

var valor = (entorno === '1') ? 'api-prod' : 'api-stag';
var endpoint = @json(url('/ajaxPcredenciales'));
var query = new URLSearchParams({
    entorno: valor,
    credenciales: credenciales,
    clave_cre: clave_cre
}).toString();
var url = endpoint + '?' + query;

if (btn) {
    btn.disabled = true;
    btn.textContent = 'Probando...';
}

try {
    var res = await fetch(url, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    });

    var rawText = await res.text();
    var payload = null;
    try {
        payload = JSON.parse(rawText);
    } catch (e) {
        payload = null;
    }

    var raw = payload && typeof payload === 'object'
        ? (payload.success ?? payload.message ?? payload.data ?? rawText)
        : rawText;

    var cadena = String(raw || '');
    var cadenaLower = cadena.toLowerCase();

    if (!res.ok || cadenaLower.indexOf('error') > -1) {
        alert(cadena || ('Error probando credenciales. HTTP ' + res.status));
        setClaveCertificadoReadonly(true);
        return;
    }

    if (cadenaLower.indexOf('bearer ') > -1 || cadenaLower.indexOf('token') > -1 || cadenaLower.indexOf('ok') > -1) {
        alert('Credenciales Comprobadas Correctamente');
        setClaveCertificadoReadonly(false);
    } else {
        alert(cadena || 'Credenciales Incorrectas');
        setClaveCertificadoReadonly(true);
    }
} catch (err) {
    console.error('Error probando credenciales:', err);
    alert('No se pudo ejecutar el test de credenciales. Revise conexión y endpoint /ajaxPcredenciales.');
    setClaveCertificadoReadonly(true);
} finally {
    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Test';
    }
}
}

document.addEventListener("DOMContentLoaded", function(){
showStep(1);

var numeroIdInput = document.getElementById('input-numero_id_emisor');
if (numeroIdInput) {
    numeroIdInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    numeroIdInput.addEventListener('change', function() {
        consultarCedulaEmisor();
    });
}

var provinciaEl = document.getElementById('provincia_emisor');
var cantonEl = document.getElementById('canton_emisor');
if (provinciaEl) {
    provinciaEl.addEventListener('change', function() {
        cargarCantonesDesdeProvincia(this.value);
    });
}
if (cantonEl) {
    cantonEl.addEventListener('change', function() {
        cargarDistritosDesdeCanton(this.value);
    });
}
});



if (window.jQuery) {
    $(document).ready(function() {
        $(document).on("blur change", "#input-numero_id_emisor" , function() {
            consultarCedulaEmisor();
        });
    });
}
</script>
