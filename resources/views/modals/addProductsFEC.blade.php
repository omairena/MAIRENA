<div class="modal fade" id="AddProductos" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_productos">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Producto FEC</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group{{ $errors->has('idproducto') ? ' has-danger' : '' }}">
            <select class="js-example-data-array js-example-theme-single form-control" id="idproducto"  name="idproducto" required></select>
            @include('alerts.feedback', ['field' => 'idproducto'])
          </div>
          <div class="form-group{{ $errors->has('codigo_cabys') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-codigo_cabys">{{ __('Código Cabys') }}</label>
              <input type="text" name="codigo_cabys" id="input-codigo_cabys" class="form-control form-control-alternative{{ $errors->has('codigo_cabys') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Cabys') }}" value="{{ old('codigo_cabys') }}" required>
              @include('alerts.feedback', ['field' => 'codigo_producto'])
          </div>
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
              <input type="number" name="cantidad" id="input-cantidad" class="form-control form-control-alternative{{ $errors->has('cantidad') ? ' is-invalid' : '' }}" placeholder="{{ __('Cantidad') }}" value="{{ old('cantidad') }}" required>
              @include('alerts.feedback', ['field' => 'cantidad'])
          </div>
          <div class="form-group{{ $errors->has('precio_unitario') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-precio_unitario">{{ __('Precio Unitario') }}</label>
              <input type="number" step="any" name="precio_unitario" id="input-precio_unitario" class="form-control form-control-alternative{{ $errors->has('precio_unitario') ? ' is-invalid' : '' }}" placeholder="{{ __('Precio Unitario') }}" value="{{ old('precio_unitario') }}" required>
              @include('alerts.feedback', ['field' => 'precio_unitario'])
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
              </select>
              @include('alerts.feedback', ['field' => 'tipo_documento'])
          </div>
          <div class="form-group{{ $errors->has('descuento') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-descuento">{{ __('Descuento') }}</label>
              <input type="number" name="descuento" id="input-descuento" class="form-control form-control-alternative{{ $errors->has('descuento') ? ' is-invalid' : '' }}" placeholder="{{ __('Descuento') }}" value="0" required>
              @include('alerts.feedback', ['field' => 'descuento'])
          </div>
        </div>
        <input type="text" name="productos" id="productos" value="{{ $productos }}" required hidden="true">
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"  id="agregar_producto_fec">Agregar ArticuloS</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>