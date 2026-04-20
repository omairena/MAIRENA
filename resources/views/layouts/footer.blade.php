<footer class="footer">
    <div class="container-fluid">
        <ul class="nav">
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="#nosotrosModal" data-toggle="modal">
                    {{ _('Acerca de Nosotros') }}
                </a>
            </li>
             <li class="nav-item">
                <a href="" class="nav-link" data-target="#terminosfinal" data-toggle="modal">
                    {{ _('Terminos y Condiciones') }}
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="#informacionModal" data-toggle="modal">
                    {{ _('Video Tutoriales') }}
                </a>
            </li>
        </ul>
        <div class="copyright">
            &copy; {{ now()->year }} {{ _('Producto registrado por') }}
            <a href="#" target="_blank">{{ _('Oscar Silvestre Mairena Vargas') }}</a> &amp;
            <a href="#" target="_blank">{{ _('Factura Electronica San Esteban') }}</a> {{ _('Derechos Reservados') }}.
        </div>
    </div>
</footer>
@include('modals.nosotros')
@include('modals.informacion')
@include('modals.terminos')