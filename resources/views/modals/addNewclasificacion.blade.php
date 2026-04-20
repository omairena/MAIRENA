<div class="modal fade" id="addClasificacion" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_clasifica" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
           <div class="form-group{{ $errors->has('num_id') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="input-num_id">{{ __('Número de Identificación') }}</label>
                <input type="text" name="num_id" id="num_id" class="form-control form-control-alternative{{ $errors->has('num_id') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ old('num_id', $cliente->num_id) }}" required readonly="true">
                @include('alerts.feedback', ['field' => 'num_id'])
            </div>
            <div class="form-group{{ $errors->has('codigo_actividad') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="input-codigo_actividad">{{ __('Código Actividad') }}&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                    </a>
                </label>
                <select class="form-control form-control-alternative chosen-select" id="actividad" name="codigo_actividad" value="{{ old('codigo_actividad') }}" required>
                    <option value="0"> -- Seleccione una actividad --</option>
                </select>
                @include('alerts.feedback', ['field' => 'codigo_actividad'])
            </div>
            <div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="descripcion">{{ __('Descripción Actividad') }}</label>
                <select class="form-control form-control-alternative" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required readonly="true" >
                </select>
                @include('alerts.feedback', ['field' => 'descripcion'])
            </div>
            <div class="form-group{{ $errors->has('tipo_clasificacion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_clasificacion">{{ __('Clasificación') }}</label>
                <select name="tipo_clasificacion" id="tipo_clasificacion" class="form-control form-control-alternative">
                    @foreach($clasificaciones as $clasifica)
                        <option value="{{ $clasifica->idclasifica }}">{{ $clasifica->descripcion}}</option>
                    @endforeach
                </select>
                @include('alerts.feedback', ['field' => 'tipo_clasificacion'])
            </div>
            <input type="text" name="hidden_descripcion" id="hidden_descripcion" class="form-control form-control-alternative{{ $errors->has('hidden_descripcion') ? ' is-invalid' : '' }}" value="{{ old('hidden_descripcion') }}" required hidden="true">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"  id="AgregarClasificacion">Agregar Clasificacion</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>