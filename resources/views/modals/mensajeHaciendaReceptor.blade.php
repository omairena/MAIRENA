<div class="modal fade" id="Haciendamodalreceptor" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="hacienda-title"></h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-control-label" for="input-clave">{{ __('Clave del Documento') }}</label>
              <input type="text" name="clave" id="input-clave" class="form-control form-control-alternative{{ $errors->has('clave') ? ' is-invalid' : '' }}" placeholder="{{ __('Número del Documento') }}" value="{{ old('clave') }}" required>
              <input type="text" name="idsale_hacienda" id="idsale_hacienda" hidden="true">
              @include('alerts.feedback', ['field' => 'respuesta_h'])
          </div>
          <div class="form-group">
            <label class="form-control-label" for="input-respuesta_h">{{ __('Respuesta Hacienda') }}</label>
              <pre id="respuesta_h"></pre>
              @include('alerts.feedback', ['field' => 'respuesta_h'])
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>