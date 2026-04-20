<div class="modal fade" id="AddOtroCargo" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="form_add_cargo">
        <div class="modal-header">
          <h5 class="modal-title">Agregar un Cargo Adicional</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group{{ $errors->has('tipo_doc_otro_cargo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_doc_otro_cargo">{{ __('Tipo Documento') }}</label>
                <select class="form-control form-control-alternative" id="tipo_doc_otro_cargo" name="tipo_doc_otro_cargo" value="{{ old('tipo_doc_otro_cargo') }}" required>
                    <option value="0"> -- Seleccione un Tipo --</option>
                    <option value="01"> Contribucion Parafiscal</option>
                    <option value="02"> Timbre de la Cruz Roja</option>
                    <option value="03"> Timbre de Benemerito Cuerpo de Bomberos de Costa Rica</option>
                    <option value="04"> Cobro de un Tercero</option>
                    <option value="05"> Costos de Exportacion</option>
                    <option value="06"> Impuesto de servicio 10%</option>
                    <option value="07"> Timbre de Colegios Profesionales</option>
                    <option value="99"> Otros Cargos</option>
                </select>
                @include('alerts.feedback', ['field' => 'tipo_doc_otro_cargo'])
            </div>
            <div class="form-group{{ $errors->has('identificacion_otro_cargo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="identificacion_otro_cargo">{{ __('Número de Identificación') }}</label>
                <input type="text" name="identificacion_otro_cargo" id="identificacion_otro_cargo" class="form-control form-control-alternative{{ $errors->has('identificacion_otro_cargo') ? ' is-invalid' : '' }}" placeholder="{{ __('Número de Identificación') }}" value="{{ old('identificacion_otro_cargo') }}" required readonly="true">
                @include('alerts.feedback', ['field' => 'identificacion_otro_cargo'])
            </div>
            <div class="form-group{{ $errors->has('nombre_otro_cargo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="nombre_otro_cargo">{{ __('Nombre') }}</label>
                <input type="text" name="nombre_otro_cargo" id="nombre_otro_cargo" class="form-control form-control-alternative{{ $errors->has('nombre_otro_cargo') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre') }}" value="{{ old('nombre_otro_cargo') }}" required readonly="true">
                @include('alerts.feedback', ['field' => 'nombre_otro_cargo'])
            </div>
            <div class="form-group{{ $errors->has('detalle_otro_cargo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="detalle_otro_cargo">{{ __('Detalle') }}</label>
                <textarea name="detalle_otro_cargo" id="detalle_otro_cargo" class="form-control form-control-alternative{{ $errors->has('detalle_otro_cargo') ? ' is-invalid' : '' }}" placeholder="{{ __('Detalle') }}" value="{{ old('detalle_otro_cargo') }}" required maxlength="150"></textarea>

                @include('alerts.feedback', ['field' => 'detalle_otro_cargo'])
            </div>
            <div class="form-group{{ $errors->has('porcentaje_otro_cargo') ? ' has-danger' : '' }}" id="porcentaje_div_otro_cargo" style="display: none;">
                <label class="form-control-label" for="porcentaje_otro_cargo">{{ __('Porcentaje') }}</label><br>
                <input type="checkbox" name="tiene_porcentaje_otro_cargo" id="tiene_porcentaje_otro_cargo" >¿Tiene Porcentaje?<br>
                <input type="number" name="porcentaje_otro_cargo" id="porcentaje_otro_cargo" class="form-control form-control-alternative{{ $errors->has('porcentaje_otro_cargo') ? ' is-invalid' : '' }}" placeholder="{{ __('Porcentaje') }}" value="{{ old('porcentaje_otro_cargo') }}" required readonly="true">
                @include('alerts.feedback', ['field' => 'porcentaje_otro_cargo'])
            </div>
            <div class="form-group{{ $errors->has('monto_otro_cargo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="monto_otro_cargo">{{ __('Monto') }}</label>
                <input type="number" name="monto_otro_cargo" id="monto_otro_cargo" class="form-control form-control-alternative{{ $errors->has('monto_otro_cargo') ? ' is-invalid' : '' }}" placeholder="{{ __('Monto') }}" value="{{ old('monto_otro_cargo') }}" required>
                @include('alerts.feedback', ['field' => 'monto_otro_cargo'])
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="add_cargo">Agregar Cargo</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
