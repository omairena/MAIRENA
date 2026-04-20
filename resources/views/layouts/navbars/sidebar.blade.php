

<div class="sidebar" data="blue">
    <div class="sidebar-wrapper">
        <div class="logo">
           
            <FONT FACE="Candara" SIZE=2 COLOR="white"> Contribuyente: 
 {{Auth::user()->config_u[0]->nombre_empresa}}</FONT>
 <FONT FACE="Candara" SIZE=2 COLOR="black"><br>Usuario Activo:
 {{Auth::user()->name}}</FONT>
        </div>
        <ul class="nav">
        
            <li class="{{ $pageSlug == 'dashboard' ? 'active' : '' }}">  
    <a href="{{ Auth::user()->estatus == 0 ? route('config.index') : route('inicio') }}">  
        <i class="tim-icons icon-settings"></i>  
        <p><b>{{ _('Servicios Activos') }}</b></p>  
    </a>  
</li>  
<li class="{{ $pageSlug == 'dashboard' ? 'active' : '' }}">  
    <a href="{{ route('home.dashboard1') }}">
        <i class="tim-icons icon-components"></i>  
        <p><b>{{ _('Ingresos & Egresos') }}</b></p>  
    </a>  
</li>  
              @if (Auth::user()->config_u[0]->status == 1)
              
            <li>
                <a data-toggle="collapse" href="#laravel-facturar" aria-expanded="true">
                    <i class="fas fa-calculator"></i>
                    <span class="nav-link-text" >{{ __('Facturar') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-facturar">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'crearFactura') class="active " @endif>
                            <a href="{{ route('facturar.create') }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __('Crear Documento') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'allfacturas') class="active " @endif>
                            <a href="{{ route('facturar.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Doc Elect.') }}</p>
                            </a>
                        </li>
                       <!-- <li @if ($pageSlug == 'tiquetes') class="active " @endif>
                            <a href="{{ route('tiquetes.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Tiquetes') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'allfee') class="active " @endif>
                            <a href="{{ route('fee.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver FAC. EXPORT.') }}</p>
                            </a>
                        </li>-->
                         <li @if ($pageSlug == 'allfee') class="active " @endif>
                            <a href="{{ route('fee.create')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Crear FAC. EXPORT.') }}</p>
                            </a>
                        </li>
                            <!-- <li @if ($pageSlug == 'notacreditoall') class="active " @endif>
                            <a href="{{ route('notacredito.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Notas de Crédito') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'notadebitoall') class="active " @endif>
                            <a href="{{ route('notadebito.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Notas de Débito') }}</p>
                            </a>
                        </li>-->
                        <li @if ($pageSlug == 'pedidosall') class="active " @endif>
                            <a href="{{ route('pedidos.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Cotizaciones') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'dailySales') class="active " @endif>
                                <a href="{{ route('reportes.ddaily')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Buscar Venta') }}</p>
                                </a>
                            </li>
                        @if (Auth::user()->es_vendedor == 0)

                            <li @if ($pageSlug == 'simplificado') class="active " @endif>
                                <a href="{{ route('simplificado.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Fact RS/OP.') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'notacreditoregimen') class="active " @endif>
                                <a href="{{ route('notacredito.regimen')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('NC a Fact RS/OP.') }}</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

            </li>
             @endif
             
             <li>
                 
                <a data-toggle="collapse" href="#laravel-masreport" aria-expanded="true">
                    <i class="fab fa-laravel" ></i>
                    <span class="nav-link-text" >{{ __('Reportes Mas Usados') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-masreport">
                    <ul class="nav pl-4">
                       
                        @if (Auth::user()->es_vendedor == 0)

                            <li @if ($pageSlug == 'dailySales') class="active " @endif>
                                <a href="{{ route('reportes.daily')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Venta Diaria') }}</p>
                                </a>
                            </li>
                                    <li @if ($pageSlug == 'reportesComprasProveedor') class="active " @endif>
                                <a href="{{ route('reportes.comprasproveedor')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <FONT  >
 Compras por Proveedor</FONT>
                                </a>
                            </li>
                             <li @if ($pageSlug == 'dailySales') class="active " @endif>
                                <a href="{{ route('reportes.dailyconso')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Venta Consolidada') }}</p>
                                </a>
                            </li>
                            
                            
                        @endif
                         <li @if ($pageSlug == 'reportesSales') class="active " @endif>
                            <a href="{{ route('reportes.sales')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de Venta') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'fecSales') class="active " @endif>
                            <a href="{{ route('fec.sales')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de FEC') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'reportesRecepcion') class="active " @endif>
                            <a href="{{ route('reportes.recepcion')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de Recepción') }}</p>
                            </a>
                        </li>
                         <li @if ($pageSlug == 'rea') class="active " @endif>
                            <a href="{{ route('rea.sales')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de REA') }}</p>
                            </a>
                        </li>
                        @if (Auth::user()->es_vendedor == 0)

                    
                            <li @if ($pageSlug == 'reportesfac') class="active " @endif>
                                <a href="{{ route('reportes.iva')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte de IVA') }}</p>
                                </a>
                            </li>
                           
                            <li @if ($pageSlug == 'reportesIVA') class="active " @endif>
                                <a href="{{ route('reportes.fac')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte Fact PDF') }}</p>
                                </a>
                            </li>
                           
                        @endif
                    </ul>
                </div>

            </li>
            @if (Auth::user()->config_u[0]->status == 1)
             @if (Auth::user()->bancos == 1)
              <li>
                    <a data-toggle="collapse" href="#laravel-bancos" aria-expanded="true">
                        <i class="fas fa-landmark"></i>
                        <span class="nav-link-text" >{{ __('Bancos') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse" id="laravel-bancos">
                        <ul class="nav pl-4">
                            <li @if ($pageSlug == 'crearCuenta') class="active " @endif>
                                <a href="{{ route('bancos.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>{{ __('Cuentas') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'crearIngreso') class="active " @endif>
                                <a href="{{ route('ingresos.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>{{ __('Ingresos') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'crearMasivo') class="active " @endif>
                                <a href="{{ route('trans.index') }}">
                                    <i class="fas fa-clipboard"></i>
                                    <p>{{ __('Ver Ingresos') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'crearEgreso') class="active " @endif>
                                <a href="{{ route('egresos.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>{{ __('Egresos') }}</p>
                                </a>
                            </li>
                             <li @if ($pageSlug == 'crearMasivo') class="active " @endif>
                                <a href="{{ route('t_egresos.index') }}">
                                    <i class="fas fa-clipboard"></i>
                                    <p>{{ __('Ver Egresos') }}</p>
                                </a>
                            </li>
                            
                            <li @if ($pageSlug == 'crearMasivo') class="active " @endif>
                                <a href="{{ route('transferencias.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>{{ __('Transferencias') }}</p>
                                </a>
                            </li>
                             <li @if ($pageSlug == 'reportesbac') class="active " @endif>
                                <a href="{{ route('reportes.banco')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Rep Bancos') }}</p>
                                </a>
                            </li>
                        
                        </ul>
                    </div>

                </li>
              @endif
            @if (Auth::user()->es_vendedor == 0)
            <li>
                    <a data-toggle="collapse" href="#laravel-boletas" aria-expanded="true">
                        <i class="fas fa-clipboard"></i>
                        <span class="nav-link-text" >{{ __('Boletas') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse" id="laravel-boletas">
                        <ul class="nav pl-4">
                             <li @if ($pageSlug == 'pedidosall') class="active " @endif>
                            <a href="{{ route('boletas.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('General') }}</p>
                            </a>
                        </li>
                            <li @if ($pageSlug == 'crearMasivo') class="active " @endif>
                                <a href="{{ route('boletas.create') }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ __('Crear Boleta') }}</p>
                                </a>
                            </li>
                            
                        </ul>
                    </div>

                </li>
                <li>
                    <a data-toggle="collapse" href="#laravel-envio_masivo" aria-expanded="true">
                        <i class="fas fa-calculator"></i>
                        <span class="nav-link-text" >{{ __('Envío Masivo') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse" id="laravel-envio_masivo">
                        <ul class="nav pl-4">
                            <li @if ($pageSlug == 'verMasivo') class="active " @endif>
                                <a href="{{ route('masivo.index') }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ __('Ver Masivo') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'crearMasivo') class="active " @endif>
                                <a href="{{ route('masivo.create') }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ __('Crear Masivo') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'verFacturas') class="active" @endif>  
                <a href="{{ route('facturas.index') }}">  
                    <i class="fas fa-angle-right"></i>  
                    <p>{{ __('Ver Facturas') }}</p>  
                </a>  
            </li>  
                        </ul>
                    </div>

                </li>
                <li>
                    <a data-toggle="collapse" href="#laravel-feCompras" aria-expanded="true">
                        <i class="fa fa-cart-plus" aria-hidden="true"></i>
                        <span class="nav-link-text" >{{ __('Compras') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse" id="laravel-feCompras">
                        <ul class="nav pl-4">
                            <li @if ($pageSlug == 'feCompras') class="active " @endif>
                                <a href="{{ route('fec.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Ver Fact Compras') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'crearCompras') class="active " @endif>
                                <a href="{{ route('fec.create')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Crear Fact Compras') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'receptor') class="active " @endif>
                                <a href="{{ route('receptor.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Recibir Documentos') }}</p>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
            @endif
           <li>
                <a data-toggle="collapse" href="#laravel-inventario" aria-expanded="true">
                    <i class="fas fa-shopping-basket"></i>
                    <span class="nav-link-text" >{{ __('Productos') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-inventario">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'allproductos') class="active " @endif>
                            <a href="{{ route('productos.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Productos') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'newproducto') class="active " @endif>
                            <a href="{{ route('productos.create')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Crear Productos') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'inventarioAll') class="active " @endif>
                            <a href="{{ route('inventario.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Inventario') }}</p>
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            <li>
                <a data-toggle="collapse" href="#laravel-clientes" aria-expanded="true">
                    <i class="far fa-address-card"></i>
                    <span class="nav-link-text" >{{ __('Clientes') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-clientes">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'clientes') class="active " @endif>
                            <a href="{{ route('cliente.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Clientes') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'newcliente') class="active " @endif>
                            <a href="{{ route('cliente.create')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Crear Cliente') }}</p>
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            @if (Auth::user()->es_vendedor == 0)
                <li>
                    <a data-toggle="collapse" href="#laravel-cxp" aria-expanded="true">
                        <i class="far fa-clipboard"></i>
                        <span class="nav-link-text" >{{ __('Cuentas') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse" id="laravel-cxp">
                        <ul class="nav pl-4">
                            <li @if ($pageSlug == 'cxc') class="active " @endif>
                                <a href="{{ route('cxcobrar.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Ver Cuentas por Cobrar') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'cxp') class="active " @endif>
                                <a href="{{ route('cxpagar.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Ver Cuentas por Pagar') }}</p>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
            @endif
            <li>
                <a data-toggle="collapse" href="#laravel-cajas" aria-expanded="true">
                    <i class="fas fa-cash-register"></i>
                    <span class="nav-link-text" >{{ __('Cajas') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-cajas">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'verCajas') class="active " @endif>
                            <a href="{{ route('cajas.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Ver Cajas') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'cierreCajas') class="active " @endif>
                            <a href="{{ route('cajas.vertodas')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Cierre de Cajas') }}</p>
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            <li>
                <a data-toggle="collapse" href="#laravel-reportes" aria-expanded="true">
                    <i class="fab fa-laravel" ></i>
                    <span class="nav-link-text" >{{ __('Reportes') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-reportes">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'reportesSales') class="active " @endif>
                            <a href="{{ route('reportes.sales')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de Venta') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'fecSales') class="active " @endif>
                            <a href="{{ route('fec.sales')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de FEC') }}</p>
                            </a>
                        </li>
                        @if (Auth::user()->es_vendedor == 0)

                            <li @if ($pageSlug == 'dailySales') class="active " @endif>
                                <a href="{{ route('reportes.daily')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Venta Diaria') }}</p>
                                </a>
                            </li>
                             <li @if ($pageSlug == 'dailySales') class="active " @endif>
                                <a href="{{ route('reportes.dailyconso')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Venta Consolidada') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesDventas') class="active " @endif>
                                <a href="{{ route('reportes.dventas')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Rep de D151-Venta') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesDcompras') class="active " @endif>
                                <a href="{{ route('reportes.dcompras')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Rep de D151-Compras') }}</p>
                                </a>
                            </li>
                        @endif
                        <li @if ($pageSlug == 'reportesRecepcion') class="active " @endif>
                            <a href="{{ route('reportes.recepcion')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Reporte de Recepción') }}</p>
                            </a>
                        </li>
                        @if (Auth::user()->es_vendedor == 0)

                            <li @if ($pageSlug == 'reportesComprasProveedor') class="active " @endif>
                                <a href="{{ route('reportes.comprasproveedor')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <FONT  >
 Compras por Proveedor</FONT>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesfac') class="active " @endif>
                                <a href="{{ route('reportes.iva')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte de IVA') }}</p>
                                </a>
                            </li>
                           
                            <li @if ($pageSlug == 'reportesIVA') class="active " @endif>
                                <a href="{{ route('reportes.fac')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte Fact PDF') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesProductos') class="active " @endif>
                                <a href="{{ route('reportes.productos')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Ventas por Producto') }}</p>
                                </a>
                            </li>
						    <li @if ($pageSlug == 'reportesInv') class="active " @endif>
                                <a href="{{ route('excel.inv')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Existencia Inv') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesClientes') class="active " @endif>
                                <a href="{{ route('excel.clientes')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte de Clientes') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesCxc') class="active " @endif>
                                <a href="{{ route('reportes.cxc')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte C X C') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesCxcAbono') class="active " @endif>
                                <a href="{{ route('reportes.cxcabono')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte Abonos CXC') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesCxp') class="active " @endif>
                                <a href="{{ route('reportes.cxp')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Reporte CXP') }}</p>
                                </a>
                            </li>
                        @endif
                        <li @if ($pageSlug == 'reportesRegimen') class="active " @endif>
                            <a href="{{ route('reportes.regimen')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Rep Reg Simplificado') }}</p>
                            </a>
                        </li>
                        @if (Auth::user()->es_vendedor == 0)

                            <li @if ($pageSlug == 'reportesop') class="active " @endif>
                                <a href="{{ route('reportes.op')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Rep Op S/FE') }}</p>
                                </a>
                            </li>
                            <li @if ($pageSlug == 'reportesUtilidad') class="active " @endif>
                                <a href="{{ route('reportes.utilidad')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Calculo de Utilidad') }}</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

            </li>
            @if (Auth::user()->es_vendedor == 0)
            <li>
                <a data-toggle="collapse" href="#laravel-examples" aria-expanded="true">
                    <i class="fas fa-wrench"></i>
                    <span class="nav-link-text" >{{ __('Configuración') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse" id="laravel-examples">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'config') class="active " @endif>
                            <a href="{{ route('config.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Configuración Fiscal') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'profile') class="active " @endif>
                            <a href="{{ route('profile.edit')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Perfil de Usuario') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'users') class="active " @endif>
                            <a href="{{ route('user.index')  }}">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ _('Usuarios') }}</p>
                            </a>
                        </li>
                        @if (Auth::user()->config_u[0]->usa_listaprecio == 1)
                            <li @if ($pageSlug == 'lista_precio') class="active " @endif>
                                <a href="{{ route('list.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Listas de Precios') }}</p>
                                </a>
                            </li>
                        @endif
                        @if (Auth::user()->config_u[0]->config_automatica == 1)
                            <li @if ($pageSlug == 'config_automatica') class="active " @endif>
                                <a href="{{ route('config_automatica.index')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Config. Recepcion') }}</p>
                                </a>
                            </li>
                        @endif
                        @if (Auth::user()->super_admin == 1)
                            <li @if ($pageSlug == 'administrar_terminos') class="active " @endif>
                                <a href="{{ route('terminos.edit')  }}">
                                    <i class="fas fa-angle-right"></i>
                                    <p>{{ _('Adm Terminos') }}</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
             @endif
        </ul>
    </div>
</div>
