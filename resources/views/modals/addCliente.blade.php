<div class="modal fade" id="AddCliente" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_add_cliente">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group{{ $errors->has('clientes') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="clientes">{{ __('Código de Producto') }}</label>
              <select class="form-control form-control-alternative" id="clientes" name="clientes" required>
                @foreach($clientes as $cli)
                  <option value="{{ $cli->idcliente }}">{{ $cli->nombre }}</option>
                @endforeach
              </select>
              @include('alerts.feedback', ['field' => 'clientes'])
          </div>
          <div class="form-group{{ $errors->has('condicion_venta_mod') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="condicion_venta_mod">{{ __('Condición Venta') }}</label>
            <select class="form-control form-control-alternative" id="condicion_venta_mod" name="condicion_venta_mod" value="{{ old('condicion_venta_mod') }}" required>
              <option value="01">Contado</option>
              <option value="02">Crédito</option>
            </select>
            @include('alerts.feedback', ['field' => 'condicion_venta_mod'])
          </div>
          <div class="form-group{{ $errors->has('p_credito_mod') ? ' has-danger' : '' }}" id="pl_credito_mod" style="display: none;">
            <label class="form-control-label" for="input-p_credito_mod">{{ __('Plazo Crédito') }}</label>
            <input type="number" name="p_credito_mod" id="input-p_credito_mod" class="form-control form-control-alternative{{ $errors->has('p_credito_mod') ? ' is-invalid' : '' }}" placeholder="{{ __('Plazo Credito') }}" value="{{ old('p_credito_mod') }}">
            @include('alerts.feedback', ['field' => 'p_credito_mod'])
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
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"  id="agregar_cliente">Agregar Cliente</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
