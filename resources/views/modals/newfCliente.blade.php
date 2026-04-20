<div class="modal fade" id="newUsuario" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg-inventario" role="document">
    <div class="modal-content">
      <form method="post" action="{{ route('clientef.jsonstore') }}" autocomplete="off" enctype="multipart/form-data" id="form_new_cliente">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Cliente Factura</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('tipo_id_modal') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="tipo_id_modal">{{ __('Tipo Identificación') }}</label>
                <select class="form-control form-control-alternative" id="tipo_id_modal" name="tipo_id_modal" value="{{ old('tipo_id_modal') }}" required>
                  <option value="01">Cédula Física</option>
                  <option value="02">Cédula Júridica</option>
                  <option value="03">DIMEX</option>
                  <option value="04">NITE</option>
                </select>
                @include('alerts.feedback', ['field' => 'tipo_id_modal'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('ced_receptor') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="ced_receptor">{{ __('Identificación del Receptor') }}</label>
                <input type="text" name="ced_receptor" id="ced_receptor" class="form-control form-control-alternative{{ $errors->has('ced_receptor') ? ' is-invalid' : '' }}" >
                @include('alerts.feedback', ['field' => 'ced_receptor'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('cliente_serch_modal') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="cliente_serch_modal">{{ __('Cliente Receptor') }}</label>
                <input type="text" name="cliente_serch_modal" id="cliente_serch_modal" class="form-control form-control-alternative{{ $errors->has('cliente_serch_modal') ? ' is-invalid' : '' }}">
                @include('alerts.feedback', ['field' => 'cliente_serch_modal'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('codigo_actividad_modal') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="codigo_actividad_modal">{{ __('Código Actividad') }}&nbsp;&nbsp;&nbsp;&nbsp;
                  <a href="https://www.hacienda.go.cr/ATV/frmConsultaSituTributaria.aspx" target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Si deseas ubicar tu código actividad puedes hacerlo en el portal de hacienda introduciendo tu número de Identificación, solo debes presionar click en la pregunta de necesitas ayuda</b>">¿Necesitas Ayuda?
                  </a>
                </label>
                <select class="form-control form-control-alternative" id="codigo_actividad_modal" name="codigo_actividad_modal" value="{{ old('codigo_actividad_modal') }}" required>
                </select>
                @include('alerts.feedback', ['field' => 'codigo_actividad_modal'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                <input type="email" name="email" id="input-email" class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="sistemaoscar01@gmail.com" required>
                @include('alerts.feedback', ['field' => 'email'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('telefono') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="input-telefono">{{ __('Telefono') }}</label>
                <input type="number" name="telefono" id="input-telefono" class="form-control form-control-alternative{{ $errors->has('telefono') ? ' is-invalid' : '' }}" placeholder="{{ __('Telefono') }}" required>
                @include('alerts.feedback', ['field' => 'telefono'])
              </div>
            </div>
            <div class="col-12">
                <div class="form-group{{ $errors->has('direccion') ? ' has-danger' : '' }}">
                    <label class="form-control-label" for="direccion">{{ __('Direccion') }}</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" required >
                </div>
            </div>
          </div>
          <input type="text" name="cliente_hacienda" id="cliente_hacienda" hidden="true">
          <div class="modal-footer">
            <button type="submit" class="btn btn-success"  id="agregar_cliente">Agregar Cliente</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
