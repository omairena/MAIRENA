@extends('layouts.app', ['page' => __('Crear Factura Electronica'), 'pageSlug' => 'crearFactura'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="{{ asset('black') }}/js/nucleo_app.js"></script>

</head>
@section('content')
@if (Session::has('message'))
   <div class="alert alert-danger">{{ Session::get('message') }}</div>
@endif
@if(Auth::user()->config_u[0]->es_transporte > 0)
    <div class="container-fluid mt--7">
        <div class="row">
          <!--<div class="col-xl-4 order-xl-1">
               <div class="card card-user">
                    <div class="image">
                        <img src="{{ asset('black') }}/img/fondo_header.jpg" alt="...">
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <a href="#">
                               <img class="logo" src="{{ asset('black') }}/img/logo.JPG" alt="Logo" width="125">
                                <h5 class="title">{{ Auth::user()->config_u[0]->nombre_emisor }}</h5>
                            </a>
                            <p class="description">
                              @ {{ Auth::user()->config_u[0]->email_emisor }}
                            </p>
                        </div>
                        <p class="description text-center">
                            {{ Auth::user()->config_u[0]->direccion_emisor }}
                        </p>
                        <p class="description text-center">
                            {{ Auth::user()->config_u[0]->telefono_emisor }}
                        </p>
                    </div>
                </div>
            </div>-->
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h5 class="mb-0">{{ __('Crear Factura Fiscal Taxi') }}</h5><br>
                                <h5 class="mb-0" id="encabezado_factura"></h5>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                      <div class="card-body">
                        <form method="post" action="{{ route('facturar.guardar') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
                            @csrf

                           <!-- <h6 class="heading-small text-muted mb-4">{{ __('Armar Factura Fiscal') }}</h6>-->
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                        <option value="0">-- Seleccione un tipo de documento --</option>
                                        <option value="01" >Fáctura Electrónica</option>
                                        <option value="04"selected="true">Tiquete</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('ced_receptor') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="ced_receptor">{{ __('Identificación del Receptor') }}</label>
                                    <input type="text" name="ced_receptor" id="ced_receptor" class="form-control form-control-alternative{{ $errors->has('ced_receptor') ? ' is-invalid' : '' }}" >
                                    @include('alerts.feedback', ['field' => 'ced_receptor'])
                                </div>
                                <button type="button" class="btn btn-success mt-4">{{ __('Buscar') }}</button>
                                <div class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" required>
                                    @include('alerts.feedback', ['field' => 'cliente'])

                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}" id="input_email" style="display: none;">
                                    <label class="form-control-label" for="email">{{ __('Email Cliente') }}</label>
                                    <input type="text" name="email" id="email" value ="sistemaoscar01@gmail.com" class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01">Efectivo</option>
                                        <option value="02">Tarjeta</option>
                                        <option value="03">Cheque</option>
                                        <option value="04">Transferencia – depósito bancario</option>
                                        <option value="05">Recaudado por terceros</option>
                                        <option value="06">Sinpe Movil</option>
                                        <option value="07">Plataforma Digital</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago') }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                              <div class="border border-gray-300 rounded-lg p-4 bg-white">
            <h2 class="text-xl font-semibold mb-4 text-center">Producto o Servicio a Facturar</h2>

            <div class="form-group{{ $errors->has('productos') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="productos_t">{{ __('Seleccione de Producto/Servicio a Facturar') }}</label>
                <select class="form-control form-control-alternative" id="productos_t" name="productos_t" value="{{ old('productos_t') }}" required>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->idproducto }}">{{ $prod->nombre_producto }}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'productos_t'])
            </div>

            <div class="text-center">
                <label class="form-control-label" for="condicion_iva">{{ __('Desmarque el check(✓) de abajo si el precio a indicar incluye el IVA:') }}</label><br>
                <input type="checkbox" name="condicion_iva" id="condicion_iva" checked="true">
            </div>

            <div class="form-group{{ $errors->has('precio_t') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="precio_t">{{ __('Precio del Producto/Servicio') }}</label>
                <input type="number" name="precio_t" id="precio_t" class="form-control form-control-alternative{{ $errors->has('precio_t') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio del Servicio') }}" value="0" required="true">
                @include('alerts.feedback', ['field' => 'precio_t'])
            </div>

            <div class="form-group text-right">
                <a href="#" class="btn btn-sm btn-success" id="Agregar_producto_taxi">Agregar Producto/Servicio</a>
            </div>
        </div>
                                <div class="table-responsive">
                                    <table class="table align-items-center" id="tabla_productos">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Cant</th>
                                                <th scope="col">Neto</th>
                                                <th scope="col">Descuento</th>
                                                <th scope="col">Impuesto</th>
                                                <th scope="col">¿Tiene exoneración?</th>
                                                <th scope="col">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="tabla_productos">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group text-right">
                                    <h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto"></b></h4>
                                    <h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento"></b> </h4>
                                    <h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto"></b></h4>
                                    <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto"></b></h4>
                                    <h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento"></b></h4>
                                    <input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                    <input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                    <input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="00" hidden="true">
                                    <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">
                                    <input type="hidden" name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}" >
                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">
                                    <input type="text" name="moneda" id="moneda" value="{{ old('moneda', 'CRC') }}" hidden="true">
                                    <input type="text" name="tipo_cambio" id="tipo_cambio" value="{{ old('tipo_cambio', '0') }}" hidden="true">
                                    <input type="text" name="condición_venta" id="condición_venta" value="{{ old('condición_venta', '01') }}" hidden="true">
                                     <input type="text" name="datos_internos" id="datos_internos" hidden="true">
                                    <input type="text" name="cliente_hacienda" id="cliente_hacienda" hidden="true">
                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Facturar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Crear Documento') }}</h3><br>
                                <h3 class="mb-0" id="encabezado_factura"></h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('facturar.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('facturar.guardar') }}" autocomplete="off" enctype="multipart/form-data" id="form_factura">
                            @csrf

                            <a href="#" class="btn btn-sm btn-success" data-target="#AddProductos" data-toggle="modal" style="display: none;" id="Agregar_producto">Buscar Producto</a>
                            <div class="pl-lg-4">
                                <div>
                            	<div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
                                    <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                                    	<option value="0" selected="true">-- Seleccione un tipo de documento --</option>

                                        <option value="04" >Tiquete</option>
                                        <option value="01" >Fáctura Electrónica</option>
                                        <option value="03" >Nota Credito Electrónica</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tipo_documento'])
                                </div>
                                <div class="form-group{{ $errors->has('actividad') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-actividad">{{ __('Código Actividad') }}</label>
                                    <select class="form-control form-control-alternative" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'actividad'])
                                </div>

                                <div style="display: none;" id="divCliente" class="form-group{{ $errors->has('cliente') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-cliente">{{ __('Cliente Receptor') }}</label>
                                    <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="" required >
                                    @include('alerts.feedback', ['field' => 'cliente'])
                                       <input type="button" class="btn btn-sm btn-success" value="+" data-target="#newUsuario" data-toggle="modal" id="New_cliente"/>
                                </div>


                                <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-medio_pago">{{ __('Medio de Pago') }}</label>
                                    <select class="form-control form-control-alternative" id="medio_pago" name="medio_pago" value="{{ old('medio_pago') }}" required>
                                        <option value="01">Efectivo</option>
                                        <option value="02">Tarjeta</option>
                                        <option value="03">Cheque</option>
                                        <option value="04">Transferencia – depósito bancario</option>
                                        <option value="05">Recaudado por terceros</option>
                                        <option value="06">Sinpe Movil</option>
                                        <option value="07">Plataforma Digital</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'medio_pago'])
                                </div>
                                <div class="form-group{{ $errors->has('referencia_pago') ? ' has-danger' : '' }}" id="referencia_p" style="display: none;">
                                    <label class="form-control-label" for="referencia_pago">{{ __('Referencia de Pago') }}</label>
                                    <input type="number" name="referencia_pago" id="referencia_pago" class="form-control form-control-alternative{{ $errors->has('referencia_pago') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia de Pago') }}" value="{{ old('referencia_pago') }}">
                                    @include('alerts.feedback', ['field' => 'referencia_pago'])
                                </div>
                           <div class="border border-gray-300 rounded-lg p-4 bg-white">
            <h5 class="text-xl font-semibold mb-4 text-left">Producto o Servicio a Facturar</h2>

            <div class="form-group{{ $errors->has('productos') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="productos_t">{{ __('Seleccione de Producto/Servicio a Facturar') }}</label>
                <select class="form-control form-control-alternative" id="productos_t" name="productos_t" value="{{ old('productos_t') }}" required>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->idproducto }}">{{ $prod->nombre_producto }}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'productos_t'])
            </div>

            <div class="text-center">
                @php
    $sin_impuesto_pos = isset($configuracion[0]) ? $configuracion[0]->sin_impuesto_pos : 0;
@endphp

@if ($sin_impuesto_pos == 0)
<label class="form-control-label" for="condicion_iva">{{ __('Desmarque el check(✓) de abajo si el precio a indicar NO  incluye el IVA:') }}</label><br>
   
@else
   <label class="form-control-label" for="condicion_iva">{{ __('Marque el check(✓) de abajo si el precio a indicar incluye el IVA:') }}</label><br>  
@endif
                <!--<input type="checkbox" name="condicion_iva" id="condicion_iva" checked="true">-->
                 <input type="checkbox" name="condicion_iva" id="condicion_iva" {{$configuracion[0]->sin_impuesto_pos == false ? 'checked' : ''}}>
            </div>

            <div class="form-group{{ $errors->has('precio_t') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="precio_t">{{ __('Precio del Producto/Servicio') }}</label>
                <input type="number" name="precio_t" id="precio_t" class="form-control form-control-alternative{{ $errors->has('precio_t') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio del Servicio') }}" value="0" required="true">
                @include('alerts.feedback', ['field' => 'precio_t'])
            </div>
<div class="text-left">  
 <input type="checkbox" name="solo_oc" id="solo_oc">  
                <label class="form-control-label" for="solo_oc">{{ __('Solo Otros Cargos?') }}</label>
                
            </div> 
            <div class="form-group text-left">
                <a href="#" class="btn btn-sm btn-success" id="Agregar_producto_transporte">Agregar Producto/Servicio</a>
            </div>
        </div>
                                 </div>
                                 <button type="button" id="toggleButton" class="btn btn-primary btn-sm">Más Opciones</button>

                                <div id="additionalOptions" style="display: none;">
                                <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
                                    <select name="idcaja" id="idcaja" class="form-control form-control-alternative">
                                        @foreach($cajas as $caja)
                                            <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('moneda') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-moneda">{{ __('Tipo de Moneda') }}</label>
                                    <select class="form-control form-control-alternative" id="moneda" name="moneda" value="{{ old('moneda') }}" required>
                                        <option value="CRC">Colon Costaricense</option>
                                        <option value="USD">Dólar Americano</option>
                                        <option value="EUR">Euro</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'moneda'])
                                </div>
                                <div class="form-group{{ $errors->has('tipo_cambio') ? ' has-danger' : '' }}" id="tipo_cambio" style="display: none;">
                                    <label class="form-control-label" for="input-tipo_cambio">{{ __('Tipo de Cambio') }}</label>
                                    <input type="number" name="tipo_cambio" id="input-tipo_cambio" class="form-control form-control-alternative{{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" placeholder="{{ __('Tipo de Cambio') }}" value="{{ old('tipo_cambio') }}">
                                    @include('alerts.feedback', ['field' => 'tipo_cambio'])
                                </div>
                                <div class="form-group{{ $errors->has('condición_venta') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-condición_venta">{{ __('Condición Venta') }}</label>
                                    <select class="form-control form-control-alternative" id="condición_venta" name="condición_venta" value="{{ old('condición_venta') }}" required>
                                        <option value="01">Contado</option>
                                        <option value="02">Crédito</option>
                                        <option value="10">Venta a crédito en IVA hasta 90 días (Artículo 27, LIVA) </option>
                                        <option value="11">Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA)  </option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'condición_venta'])
                                </div>
                                <div class="form-group{{ $errors->has('p_credito') ? ' has-danger' : '' }}" id="pl_credito" style="display: none;">
                                    <label class="form-control-label" for="input-p_credito">{{ __('Plazo Crédito') }}</label>
                                    <input type="number" name="p_credito" id="input-p_credito" class="form-control form-control-alternative{{ $errors->has('p_credito') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito') }}">
                                    @include('alerts.feedback', ['field' => 'p_credito'])
                                </div>

                                </div>
                                <div class="form-group text-right">

                                </div>
                                <div class="table-responsive">
    								<table class="table align-items-center" id="tabla_productos">
    									<thead class="thead-light">
        									<tr>
            									<th scope="col">#</th>
            									<th scope="col">Nombre</th>
            									<th scope="col">Cant</th>
            									<th scope="col">Neto</th>
            									<th scope="col">Descuento</th>
            									<th scope="col">Impuesto</th>
                                                <th scope="col">¿Tiene exoneración?</th>
            									<th scope="col">Total</th>
                                                <th></th>
        									</tr>
    									</thead>
    									<tbody class="tabla_productos">
    									</tbody>
									</table>
								</div>
								<div class="form-group text-right">
                                	<h4 class="mb-0"> Total Neto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_neto"></b></h4>
                                	<h4 class="mb-0"> Total Descuento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_descuento"></b> </h4>
                                	<h4 class="mb-0"> Total Impuesto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_impuesto"></b></h4>
                                    <h4 class="mb-0" id="iva_d" style="display: none;">IVA devuelto: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="iva_devuelto"></b></h4>
                                	<h4 class="mb-0"> Total Documento: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b id="t_documento"></b></h4>
                                	<input type="text" name="totales_fact[]" id="totales_fact" value="{{ old('totales_fact') }}" hidden="true">
                                	<input type="text" name="sales_item" id="sales_item" value="{{ old('sales_item') }}" hidden="true">
                                	<input type="text" name="numero_documento" id="numero_documento" value="{{ old('numero_documento') }}" hidden="true">
                                    <input type="text" name="existe_exoneracion" id="existe_exoneracion" value="00" hidden="true">
                                    <input type="text" name="exoneracion[]" id="exoneracion" value="{{ old('exoneracion') }}" hidden="true">

                                    <input type="text" name="idconfigfact" id="idconfigfact" value="{{ old('idconfigfact', Auth::user()->idconfigfact) }}" hidden="true">

                                </div>
                                <div class="col-12">
                                    <div class="form-group{{ $errors->has('observaciones') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="observaciones">{{ __('Observaciones de Factura') }}</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="text-center">
                                   <!-- <button type="submit" class="btn btn-success mt-4">{{ __('Facturar') }}</button>-->
                                </div>
                            </div>
                            <input type="text" readonly name="cliente" id="cliente" value="{{ old('cliente', $contado[0]->idcliente) }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@include('modals.addProducts')
@include('modals.addExoneracion')
@include('modals.newfCliente')
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">

//bloques
 document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('toggleButton').addEventListener('click', function() {
            var additionalOptions = document.getElementById('additionalOptions');
            if (additionalOptions) { // Verifica que el elemento exista
                if (additionalOptions.style.display === 'none') {
                    additionalOptions.style.display = 'block';
                } else {
                    additionalOptions.style.display = 'none';
                }
            } else {
                console.error('Elemento adicional no encontrado');
            }
        });
    });
//bloques
    $(document).ready(function() {
        var APP_URL = {!! json_encode(url('/ajaxNumFactura')) !!};
        var o2 = {!! json_encode(url('/ajaxSerchCliente')) !!};
        traerNumFactura(APP_URL,o2);
        validaMedioPago();
        validaCondicionVenta();
         $('#Agregar_producto_transporte').css( "display", "none");
        var table = $('#factura_datatables').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
        $('#input-tipo_cambio').val('0.00');



        $('#idcaja').change(function() {
           traerNumFactura(APP_URL,o2);
        });

        $('#tipo_documento').change(function() {
    // Llama a la función traerNumFactura siempre que cambie el tipo de documento
    traerNumFactura(APP_URL, o2);
    cliente
      $('#divCliente').show(); // Muestra el div
    // Obtiene el valor del select
    var tipoDocumentoValue = $(this).val();

    // Verifica si el tipo de documento es '04'
    if (tipoDocumentoValue === '04') {
        // Aquí deberías asegurarte de tener la respuesta disponible o llamarla en este contexto
        // Por ejemplo, puedes hacer una llamada AJAX si es necesario para obtener el 'nombre'

                    $('#cliente_serch').val('CLIENTE DE CONTADO');
                    $('#cliente').val(1);
                    $('#Agregar_producto_transporte').css("display", "");
                    $('#Agregar_producto').css( "display","");


    }
});

        $('[name="seleccion[]"]').click(function() {

            var arr = $('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = arr.join(',');
            $('#sales_item').val(arr);
        });

//omairna 25-06-2021
    //    $('#agregar_producto').click(function(e) {
     //       e.preventDefault();
      //      enviarDatosProducto();
     //   });
      if ($('#moneda').val() != 'CRC') {
            $('#tipo_cambio').css( "display", "block");
        }else{
            $('#tipo_cambio').css( "display", "none");
        }

$('#moneda').change(function() {
            var moneda = $(this).val();
            switch(moneda){
                case 'CRC':
                    $('#tipo_cambio').css( "display", "none");
                    $('#input-tipo_cambio').val('0.00');
                    var tipocambio = 0.00;
                    var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                        $.ajax({
                            type:'get',
                            url: URL,
                            dataType: 'json',
                            data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
                            success:function(response){
                                //console.log(response);
                            },
                            error:function(response){
                                //console.log(response);
                            }
                        });
                break;
                case 'USD':
                    $('#tipo_cambio').css( "display", "block");
                    var URL_USD = 'https://api.hacienda.go.cr/indicadores/tc/dolar';
                    $.ajax({
                        type:'get',

                        url: URL_USD,

                        dataType: 'json',

                        success:function(response){
                            if (response == null) {
                                alert('Conexion fallida con Hacienda');
                            }else{
                                $('#input-tipo_cambio').val(response.venta.valor);
                                $("#input-tipo_cambio").prop('readonly', true);
                                var  tipocambio = response.venta.valor;
                                var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
                                    success:function(response){
                                        //console.log(response);
                                    },
                                    error:function(response){
                                        //console.log(response);
                                    }
                                });
                            }
                        }
                    });
                break;
                case 'EUR':
                    $('#tipo_cambio').css( "display", "block");
                    var URL_EUR = 'https://api.hacienda.go.cr/indicadores/tc/euro';
                    $.ajax({
                        type:'get',

                        url: URL_EUR,

                        dataType: 'json',

                        success:function(response){
                            if (response == null) {
                                alert('Conexion fallida con Hacienda');
                            }else{
                                $('#input-tipo_cambio').val(response.colones);
                                $("#input-tipo_cambio").prop('readonly', true);
                                var tipocambio = response.colones;
                                var URL = {!! json_encode(url('modificar-tipocambio')) !!};
                                $.ajax({
                                    type:'get',
                                    url: URL,
                                    dataType: 'json',
                                    data:{tipocambio:tipocambio, moneda:moneda, idsale:idsale},
                                    success:function(response){
                                        //console.log(response);
                                    },
                                    error:function(response){
                                        //console.log(response);
                                    }
                                });
                            }
                        }
                    });
                break;
            }
        });


        $( "#cliente_serch" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/cliente')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.nombre;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });

          $('#factura_datatables tbody').on('click', 'tr', function () {
            var data = table.$('[name="seleccion[]"]:checked').map(function(){
                return this.value;
            }).get();
            var str = data.join(',');
            $('#sales_item').val(data);
        });
//omairena 25-06-2021
         $('#agregar_producto').click(function(e) {
            e.preventDefault();
            if( $('.select-checkbox').is(':checked') ) {
                enviarDatosProducto();
            } else {
                alert('Debe seleccionar al menos 1 producto.');
            }
        });



        $('#agregar_producto_pos').click(function(e) {
            e.preventDefault();
            var idproducto = $('#idproducto_pos').val();
            var cantidad_pos = $('#cantidad_pos').val();
            var descuento_pos = $('#descuento_pos').val();
            $('#sales_item').val(idproducto);
            $("#form_factura").submit();
        });

    });
        $(document).on("click", "#eliminar_fila" , function(event) {
            event.preventDefault();
            var valor = $('#exoneracion').val();
        });



   $(document).on("blur", "#cliente_serch", function(event) {

    event.preventDefault();

    var nombre_cli = $(this).val().trim();
    var URL = {!! json_encode(url('buscar-cliente-pos')) !!};

    if (nombre_cli.length <= 0) return;

    $.ajax({
        type: 'get',
        url: URL,
        dataType: 'json',
        data: { nombre_cli: nombre_cli },

        success: function(response) {

            if (response.success && response.success.length > 0) {

                var cliente = response.success[0];

                $('#cliente_serch').val(cliente.nombre);
                $('#cliente').val(cliente.idcliente);
                $('#ced_receptor').val(cliente.num_id);
                $('#Agregar_producto_transporte').css("display", "");
                $('#Agregar_producto').css("display", "");
                $('#datos_internos').val(1);

                var tipo_documento = $('#tipo_documento').val();
                var numId = cliente.num_id;

                if (tipo_documento === '01' || tipo_documento === '09') {

                    if (!numId) {
                        $('#tipo_documento').val('04');
                        traerNumFactura(APP_URL, o2);
                        return;
                    }

                    var apiUrl = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + numId;

                    $.ajax({
                        type: 'get',
                        url: apiUrl,
                        dataType: 'json',

                        success: function(apiResponse) {

                            console.log("Respuesta Hacienda:", apiResponse);

                            if (!apiResponse || !apiResponse.situacion) {
                                throw "Respuesta inválida";
                            }

                            // 🔹 Normalizar estado
                            var estado = (apiResponse.situacion.estado || '')
                                .toLowerCase()
                                .replace(/\s+/g, ' ')
                                .trim();

                            var estadosValidos = [
                                'inscrito',
                                'inscrito de oficio'
                            ];

                            var actividades = apiResponse.actividades || [];
                            var situacion = apiResponse.situacion || {};

                            console.log("Estado:", estado);
                            console.log("Actividades:", actividades);

                            // 🔥 VALIDACIÓN
                            var esValido = true;
                            var motivos = [];

                            if (!estadosValidos.includes(estado)) {
                                esValido = false;
                                motivos.push("Estado: " + estado);
                            }

                            if (actividades.length === 0) {
                                esValido = false;
                                motivos.push("Sin actividades económicas");
                            }

                           

                            if (!esValido) {

                                alert(
                                    'El cliente no es válido para Factura Electrónica.\n\n' +
                                    motivos.join('\n') +
                                    '\n\nSe cambiará a Tiquete.'
                                );

                                $('#tipo_documento').val('04');

                            } else {

                                $('#tipo_documento').val('01');
                            }

                            traerNumFactura(APP_URL, o2);
                        },

                        error: function() {

                            alert(
                                'Error al consultar Hacienda.\n' +
                                'Se generará como Tiquete por seguridad.'
                            );

                            $('#tipo_documento').val('04');
                            traerNumFactura(APP_URL, o2);
                        }
                    });

                } else {
                    traerNumFactura(APP_URL, o2);
                }

            } else {

                $('#cliente_serch').val('');
                $('#cliente').val(1);
                $('#Agregar_producto').css("display", "none");
            }
        },

        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:", xhr, status, error);
            alert('Ocurrió un error al buscar el cliente. Intente nuevamente.');
        }
    });
});
        //// fin cambio 30-03-2022

        $(document).on("blur", "#ced_receptor" , function(event) {
            event.preventDefault();
            var num_id = $(this).val();

            var URL = {!! json_encode(url('buscar-identificacion')) !!};
            if ($(this).val().length > 0) {
                $.ajax({
                    type:'GET',
                    url: URL,
                    dataType: 'json',
                    data:{num_id:num_id},
                    success:function(response){
                        //console.log(response);
                        if (response['success'] === true) {
                            $('#cliente_serch').val(response['default'][0]['nombre']);
                            $('#datos_internos').val(1);
                            $('#cliente').val(response['default'][0]['idcliente']);
                        }else{
                            alert('Identificación No Encontrada en nuestra base de datos1');
                            $('#cliente_serch').val('');
                            $('#cliente').val('0');
                            var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + num_id;
                             //var api = 'https://apis.gometa.org/cedulas/' + num_id;
                            $.ajax({
                                type:'GET',
                                url: api,
                                dataType: 'json',
                                data:{num_id:num_id},
                                success:function(response){
                                    //console.log(response);
                                    $('#cliente_hacienda').val(JSON.stringify(response));
                                    $('#datos_internos').val(0);
                                    $('#cliente_serch').val(response['nombre']);
                                    $('#input_email').css('display', 'block');
                                },
                                error:function(response){
                                    alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación1');
                                    $('#cliente_serch').val('');
                                }
                            });
                        }
                    },
                    error:function(response){
                        alert('Identificación No Encontrada en nuestra base de datos2');
                        $('#cliente_serch').val('');
                        var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
                          //var api = 'https://apis.gometa.org/cedulas/' + id;
                        $.ajax({
                            type:'GET',
                            url: api,
                            dataType: 'json',
                            success:function(response){
                                //console.log(response);
                            },
                            error:function(response){
                                alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación2');
                                $('#cliente_serch').val('');
                                $('#ced_receptor').focus();
                            }
                        });
                    }
                });

            }else{

            }
        });

        $(document).on("click", "#Agregar_producto_transporte" , function(event) {
            event.preventDefault();
            var idproducto = $('#productos_t').val();
            var precio_t = $('#precio_t').val();
            var iva = $('#condicion_iva').is(':checked');
            var iva = $('#solo_oc').is(':checked');
            var datos = $('#form_factura').serialize();
            $("#form_factura").submit();
        });

    $(document).on("click", "#Agregar_producto_taxi" , function(event) {
            event.preventDefault();
            var idproducto = $('#productos_t').val();
            var precio_t = $('#precio_t').val();
            var iva = $('#condicion_iva').is(':checked');
            var iva = $('#solo_oc').is(':checked');
            var datos = $('#form_factura').serialize();
            $("#form_factura").submit();
        });
        
        
        $(document).on("blur", "#ced_receptor" , function(event) {
            event.preventDefault();
            var num_id = $(this).val();
            var URL = {!! json_encode(url('buscar-identificacion')) !!};
            if ($(this).val().length > 0) {
                $.ajax({
                    type:'GET',
                    url: URL,
                    dataType: 'json',
                    data:{num_id:num_id},
                    success:function(response){
                        //console.log(response);
                        if (response['success'] === true) {
                            alert('Cliente ya registrado en el sistema.');
                            $('#cliente_serch').val(response['default'][0]['nombre']);
                            $('#datos_internos').val(1);
                            $('#cliente').val(response['default'][0]['idcliente']);
                            $('#tipo_documento').val('01');
                            $('#newUsuario').modal('hide');
                            $('#cliente_serch').focus();
                        }else{
                            alert('Identificación No Encontrada en nuestra base de datos 3');
                            $('#cliente_serch').val('');
                            $('#cliente').val('0');
                            var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + num_id;
                             //var api = 'https://apis.gometa.org/cedulas/' + num_id;

                            $.ajax({
                                type:'GET',
                                url: api,
                                dataType: 'json',
                                data:{num_id:num_id},
                                success:function(response){
                                    //console.log(response);
                                    $('#cliente_hacienda').val(JSON.stringify(response));
                                    $('#datos_internos').val(0);
                                    if (typeof response =='object') {
                                        $('#cliente_serch_modal').val(response.nombre);
                                        $('#tipo_id_modal').val(response.tipoIdentificacion);
                                        if ($.isArray([response.actividades])) {
                                            $('#codigo_actividad_modal').find('option').remove();
                                           if (response.actividades.length > 0) {
                                                response.actividades.forEach(function(act, index) {
                                                    $("#codigo_actividad_modal").append('<option value="'+ act.codigo+'">'+  act.codigo +' - '+ act.descripcion+'</option>');
                                                });
                                            }else{
                                                $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                            }
                                        }else{
                                            $('#codigo_actividad_modal').find('option').remove();
                                            $("#codigo_actividad_modal").append('<option value="112233">112233-Actividad por defecto</option>');
                                        }
                                    }
                                },
                                error:function(response){
                                    alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación3');
                                    $('#cliente_serch').val('');
                                }
                            });

                            

                           
                        }
                    },
                    error:function(response){
                        alert('Identificación No Encontrada en nuestra base de datos4');
                        $('#cliente_serch').val('');
                        var api = 'https://api.hacienda.go.cr/fe/ae?identificacion=' + id;
                         // var api = 'https://apis.gometa.org/cedulas/' + id;
                        $.ajax({
                            type:'GET',
                            url: api,
                            dataType: 'json',
                            success:function(response){
                                console.log(response);
                            },
                            error:function(response){
                                alert('Identificación No Encontrada en el Ministerio de Hacienda verifique la Identificación4');
                                $('#cliente_serch').val('');
                                $('#ced_receptor').focus();
                            }
                        });
                    }
                });

            }else{

            }
        });

</script>
@endsection
