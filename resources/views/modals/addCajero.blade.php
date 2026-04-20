<div class="modal fade" id="AddCajero" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-inventario" role="document">
      <div class="modal-content">
        <form id="form_add_cajero">
        <div class="modal-header">
          <h5 class="modal-title">Asignar Usuario a caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group{{ $errors->has('user_cajero') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="user_cajero">{{ __('Cajero - Usuario') }}</label>
            <select class="form-control form-control-alternative" id="user_cajero" name="user_cajero" value="{{ old('user_cajero') }}" required>
              <option value="0"> -- Seleccione un Usuario --</option>
            </select>
            @include('alerts.feedback', ['field' => 'user_cajero'])
            <input type="number" name="idcaja_modal" id="idcaja_modal" value="{{ old('idcaja_modal') }}" hidden="true">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="add_cajero">Asignar Usuario</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>