<div class="modal fade" id="recalcularModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar la Lista para Recalcular</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
      </div>
      <div class="modal-body">
            <div class="form-group}">
                <label class="form-control-label" for="seleccion_lista">{{ __('Seleccione la Lista deseada') }}</label>
                <select class="form-control form-control-alternative" id="seleccion_lista" name="seleccion_lista" value="{{ old('seleccion_lista') }}" required>
                    @foreach ($lista_cli as $list )

                        <option value="{{$list->idlist}}">{{$list->descripcion}} / porcentaje: {{$list->porcentaje}} %</option>
                    @endforeach
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="agregar_lista">Recalcular</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
