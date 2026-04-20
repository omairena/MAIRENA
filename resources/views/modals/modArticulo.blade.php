<div class="modal fade" id="ModArticulo" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_flotante">
        <div class="modal-header">
          <h5 class="modal-title">Edicion de Productos Pos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group{{ $errors->has('codigo_producto') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-codigo_producto">{{ __('Código de Producto') }}</label>
              <input type="text" name="codigo_producto" id="input-codigo_producto" class="form-control form-control-alternative{{ $errors->has('codigo_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Código de Producto') }}" value="{{ old('codigo_producto') }}" required>
              @include('alerts.feedback', ['field' => 'codigo_producto'])
          </div>
          <div class="form-group{{ $errors->has('nombre_producto') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-nombre_producto">{{ __('Nombre del Producto') }}</label>
              <input type="text" name="nombre_producto" id="input-nombre_producto" class="form-control form-control-alternative{{ $errors->has('nombre_producto') ? ' is-invalid' : '' }}" placeholder="{{ __('Nombre del Producto') }}" value="{{ old('nombre_producto') }}" required>
              @include('alerts.feedback', ['field' => 'nombre_producto'])
          </div>
          <div class="form-group{{ $errors->has('cantidad') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-cantidad">{{ __('Cantidad') }}</label>
              <input type="number" step="any" name="cantidad" id="input-cantidad" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }}" placeholder="{{ __('Cantidad') }}" value="{{ old('cantidad') }}" required>
              @include('alerts.feedback', ['field' => 'cantidad'])
          </div>
          <div class="form-group{{ $errors->has('costo_utilidad') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-costo_utilidad">{{ __('Precio Unitario con Utilidad y con IVA') }}</label>
              <input type="number" step="any" name="costo_utilidad" id="input-costo_utilidad" class="form-control form-control-alternative{{ $errors->has('costo_utilidad') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Unitario') }}" value="{{ old('costo_utilidad') }}" required>
              @include('alerts.feedback', ['field' => 'costo_utilidad'])
          </div>
          <div class="form-group{{ $errors->has('tipo_impuesto') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-tipo_impuesto">{{ __('Tipo de Impuestos') }}</label>
              <select class="form-control form-control-alternative" id="tipo_impuesto" name="tipo_impuesto" value="{{ old('tipo_impuesto') }}" required>
                <option value="01">Tarifa 0% (Exento)</option>
                <option value="02">Tarifa reducida 1%</option>
                <option value="03">Tarifa reducida 2%</option>
                <option value="04">Tarifa reducida 4%</option>
                <option value="05">Transitorio 0%</option>
                <option value="06">Transitorio 4%</option>
                <option value="07">Transitorio 8%</option>
                <option value="08">Tarifa general 13%</option>
                <option value="99">Tarifa No Sujeta</option>
              </select>
              @include('alerts.feedback', ['field' => 'tipo_documento'])
          </div>
          <div class="form-group{{ $errors->has('descuento') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-descuento">{{ __('Descuento') }}</label>
              <input type="number" name="descuento" id="input-descuento" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }}" placeholder="{{ __('Descuento') }}" value="0" required>
              @include('alerts.feedback', ['field' => 'descuento'])
          </div>
        </div>
        <input type="text" name="idsalesitem_flot" id="idsalesitem_flot" hidden="true">
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"  id="ModificarFlotanteItem">Agregar Articulo</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>