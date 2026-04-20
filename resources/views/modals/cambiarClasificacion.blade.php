<div class="modal fade" id="cambiarClasificacion" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_clasifica" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title">Cambiar Clasificación</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Clasificación') }}</label>
                <select name="tipo_clasificacion" id="tipo_clasificacion" class="form-control form-control-alternative">
                    @foreach($clasificaciones as $clasifica)
                        <option value="{{ $clasifica->idclasifica }}">{{ $clasifica->descripcion}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
        </div>
        <input type="number" name="idreceptor_modal" id="idreceptor_modal" value="" hidden="true">
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"  id="submitClasificacion">Editar Clasificacion</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>