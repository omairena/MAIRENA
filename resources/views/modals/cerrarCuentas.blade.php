<div class="modal fade" id="CerrarCuentas" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg-totalizar">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ventana de Cerrar Cuentas</h5>
      </div>
      <div class="modal-body">
          <form method="post" action="{{ route('cxcobrar.cierre') }}" autocomplete="off" enctype="multipart/form-data" id="form_cerrar_cuenta">
            @csrf
            @method('POST')
            <div class="form-group{{ $errors->has('idcaja') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="input-idcaja">{{ __('Punto de Ventas') }}</label>
              <select name="idcaja" id="idcaja" class="form-control form-control-alternative" required="true">
                @foreach($cajas as $caja)
                  <option value="{{ $caja->idcaja }}">{{ str_pad($caja->codigo_unico, 5, "0", STR_PAD_LEFT) }} - {{ $caja->nombre_caja }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group{{ $errors->has('medio_pago') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="medio_pago">{{ __('Medio de Pago') }}</label>
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
            <div class="form-group{{ $errors->has('monto_cuenta') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="monto_cuenta">{{ __('Monto de Cuentas') }}</label>
              <input type="number" name="monto_cuenta" id="monto_cuenta" class="form-control form-control-alternative{{ $errors->has('monto_cuenta') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto de Cuentas') }}" value="{{ old('monto_cuenta') }}" readonly="true">
              @include('alerts.feedback', ['field' => 'monto_cuenta'])
            </div>
            <div class="form-group{{ $errors->has('monto_abonado') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="monto_abonado">{{ __('Monto Total de Abono') }}</label>
              <input type="number" name="monto_abonado" id="monto_abonado" class="form-control form-control-alternative{{ $errors->has('monto_abonado') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto Total de Abono') }}" value="{{ old('monto_abonado') }}" required="true">
              @include('alerts.feedback', ['field' => 'monto_abonado'])
            </div>
            <div class="form-group{{ $errors->has('referencia') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="referencia">{{ __('Referencia') }}</label>
              <input type="text" name="referencia" id="referencia" class="form-control form-control-alternative{{ $errors->has('referencia') ? ' is-invalid' : '' }}" placeholder="{{ __('Referencia') }}" value="{{ old('referencia') }}" required="true">
              @include('alerts.feedback', ['field' => 'referencia'])
            </div>
          <p>NOTA: Si el abono es menor al monto total de las facturas pendientes, se agregara como abono a la factura mas reciente a la fecha.</p>
          <input type="text" name="cxcobrar_modal" id="cxcobrar_modal" value="{{ old('cxcobrar_modal') }}" hidden="true">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Cerrar Cuentas</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
