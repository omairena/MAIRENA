<div class="modal fade" id="TotalizarFactura" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg-totalizar">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ventana de totalizar</h5>
      </div>
      <div class="modal-body">
          <form method="post" action="{{ route('pos.totalizar', $sales->idsale) }}" autocomplete="off" enctype="multipart/form-data" id="form_totalizar">
            @csrf
            @method('POST')
            <div class="form-group{{ $errors->has('tipo_documento') ? ' has-danger' : '' }}">
              <label class="form-control-label" for="input-tipo_documento">{{ __('Tipo de Documento') }}</label>
              <select class="form-control form-control-alternative" id="tipo_documento" name="tipo_documento" value="{{ old('tipo_documento') }}" required>
                <option value="0">-- Seleccione un tipo de documento --</option> 
                <option value="01" {{ ($sales->tipo_documento == 01 ? 'selected="selected"' : '') }}>Fáctura Electrónica</option>
                <option value="04" {{ ($sales->tipo_documento == 04 ? 'selected="selected"' : '') }}>Tiquete</option>
              </select>
              @include('alerts.feedback', ['field' => 'tipo_documento'])
            </div>
            <div class="form-group">
              <div style="text-align: left;">
                <b>Sección de Cambio</b><br><br>
                <div class="form-group">
                  <label for="efectivo_dev" style="font-weight: bold;">{{ __('Efectivo:') }}</label>&nbsp;&nbsp;&nbsp;
                  <input type="number" step="any" name="efectivo_dev" id="efectivo_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <label for="tarjeta_dev" style="font-weight: bold;">{{ __('Tarjeta:') }}</label>&nbsp;&nbsp;&nbsp;
                  <input type="number" step="any" name="tarjeta_dev" id="tarjeta_dev" class="form-control" style="width:180px; display: inline !important;" value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <label for="cambio_dev" style="font-weight: bold;">{{ __('Cambio:') }}</label>&nbsp;&nbsp;&nbsp;
                  <input type="number" step="any" name="cambio_dev" id="cambio_dev" class="form-control" style="width:180px; display: inline !important;" readonly="true" value="0">
                </div>
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Facturar</button>
      </div>
    </div>
  </div>
</div>