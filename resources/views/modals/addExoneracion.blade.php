<div class="modal fade" id="AddExoneracion" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form id="form_exoneracion">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Exoneracion</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
              <?php
               use App\Cliente;
              if(empty($usuario)){

      $usuario = Cliente::find(1);
      }
         ?>

        <div class="modal-body">
          <div class="form-group{{ $errors->has('tipo_exoneracion') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-tipo_exoneracion">{{ __('Mótivo de Exoneración') }}</label>
                @if(!empty($usuario->exocli))
<select class="form-control form-control-alternative" id="tipo_exoneracion" name="tipo_exoneracion" value="{{ old('tipo_exoneracion') }}" required>
                  <option value="04">Exenciones Dirección General de Hacienda</option>
                  </select>
                   @else
              <select class="form-control form-control-alternative" id="tipo_exoneracion" name="tipo_exoneracion" value="{{ old('tipo_exoneracion') }}" required>
                <option value="01">Compras autorizadas</option>
                <option value="02">Ventas exentas a diplomáticos</option>
                <option value="03">Autorizado por Ley especial</option>
                <option value="04">Exenciones Dirección General de Hacienda</option>
                <option value="05">Transitorio V</option>
                <option value="06">Transitorio IX</option>
                <option value="07">Transitorio XVII</option>
                <option value="08">Zona Franca </option>
                <option value="09">Exportación articulo 11 RLIVA</option>
                <option value="10">Órgano de las corporaciones municipales </option>
                <option value="11">Dirección General de Hacienda Autorización de Impuesto Local Concreta </option>
                <option value="99">Otros</option>
              </select>
               @endif
              @include('alerts.feedback', ['field' => 'tipo_exoneracion'])
          </div>
            <div class="form-group d-none" id="campo-otros_1">
                <label class="form-control-label" for="tipo_exoneracion_otro">{{ __('Tipo de Exoneración') }}</label>
                <input type="text" name="tipo_exoneracion_otro" id="tipo_exoneracion_otro" class="form-control form-control-alternative" placeholder="{{ __('Tipo de Exoneración') }}" minlength="5">
            </div>
          <div class="form-group{{ $errors->has('numero_exoneracion') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-numero_exoneracion">{{ __('Documento de Exoneración') }}</label>
               @if(!empty($usuario->exocli))
              <input type="text" name="numero_exoneracion" id="input-numero_exoneracion" class="form-control form-control-alternative{{ $errors->has('numero_exoneracion') ? ' is-invalid' : '' }}" placeholder="{{ __('Documento de Exoneración') }}" value="{{ $usuario->exocli }}" required>
               @else
               <input type="text" name="numero_exoneracion" id="input-numero_exoneracion" class="form-control form-control-alternative{{ $errors->has('numero_exoneracion') ? ' is-invalid' : '' }}" placeholder="{{ __('Documento de Exoneración') }}" value="{{ old('Exoneracion') }}" required>
                @endif
              @include('alerts.feedback', ['field' => 'numero_exoneracion'])
          </div>
            <div class="form-group d-none" id="campo-articulo">
                <label class="form-control-label" for="articulo">{{ __('Articulo') }}</label>
                <input type="text" name="articulo" id="articulo" class="form-control form-control-alternative" placeholder="{{ __('Articulo') }}" required>
                @include('alerts.feedback', ['field' => 'articulo'])
            </div>
             <div class="form-group d-none" id="campo-insiso">
                <label class="form-control-label" for="inciso">{{ __('Inciso') }}</label>
                <input type="text" name="inciso" id="inciso" class="form-control form-control-alternative{{ $errors->has('inciso') ? ' is-invalid' : '' }}" placeholder="{{ __('Inciso') }}" value="{{ old('inciso') }}">
                @include('alerts.feedback', ['field' => 'inciso'])
            </div>
            <div class="form-group{{ $errors->has('institucion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="input-institucion">{{ __('Institución Emisora de Exoneración') }}</label>
                <select class="form-control form-control-alternative" id="institucion" name="institucion" required>
                    <option value="01">Ministerio de Hacienda</option>
                    <option value="02">Ministerio de Relaciones Exteriores y Culto</option>
                    <option value="03">Ministerio de Agricultura y Ganadería</option>
                    <option value="04">Ministerio de Economía, Industria y Comercio</option>
                    <option value="05">Cruz Roja Costarricense</option>
                    <option value="06">Benemérito Cuerpo de Bomberos de Costa Rica</option>
                    <option value="07">Asociación Obras del Espíritu Santo</option>
                    <option value="08">Federación Cruzada Nacional de protección al Anciano (Fecrunapa)</option>
                    <option value="09">Escuela de Agricultura de la Región Húmeda (EARTH) </option>
                    <option value="10">Instituto Centroamericano de Administración de Empresas (INCAE)</option>
                    <option value="11">Junta de Protección Social (JPS)</option>
                    <option value="12">Autoridad Reguladora de los Servicios Públicos (Aresep)</option>
                    <option value="99">Otros</option>
              </select>
              @include('alerts.feedback', ['field' => 'institucion'])
            </div>
            <div class="form-group d-none" id="campo-otros_2">
                <label class="form-control-label" for="institucion_otro">{{ __('Nombre de la Institucion') }}</label>
                <input type="text" name="institucion_otro" id="institucion_otro" class="form-control form-control-alternative" placeholder="{{ __('Nombre de la Institucion') }}">
            </div>
          <div class="form-group{{ $errors->has('fecha_exoneracion') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-fecha_exoneracion">{{ __('Fecha Exoneración') }}</label>
              <input type="text" name="fecha_exoneracion" id="input-fecha_exoneracion" class="form-control form-control-alternative{{ $errors->has('fecha_exoneracion') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Exoneración') }}" value="{{ old('fecha_exoneracion') }}" required>
               <!--<input type="text" name="fecha_exoneracionmh" id="input-fecha_exoneracionmh" class="form-control form-control-alternative{{ $errors->has('fecha_exoneracionmh') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Exoneración Hacienda') }}" value="{{ old('fecha_exoneracionmh') }}" required>-->
              @include('alerts.feedback', ['field' => 'fecha_exoneracion'])
          </div>
          <div class="form-group{{ $errors->has('porcentaje_exoneracion') ? ' has-danger' : '' }}">
            <label class="form-control-label" for="input-porcentaje_exoneracion">{{ __('Tarifa Exonerada (%)') }}</label>
              <input type="number" name="porcentaje_exoneracion" id="input-porcentaje_exoneracion" class="form-control form-control-alternative{{ $errors->has('porcentaje_exoneracion') ? ' is-invalid' : '' }}" placeholder="{{ __('Exoneración (%)') }}" value="{{ old('porcentaje_exoneracion') }}" required max="13" min="0">
              @include('alerts.feedback', ['field' => 'porcentaje_exoneracion'])
          </div>
        </div>
        <input type="text" name="idsaleitem_exo" id="idsaleitem_exo" hidden="true">
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="AgregarExoneracion">Agregar Exoneración</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
        // Escuchar cambios en el select de tipo de exoneración
        $('#tipo_exoneracion').change(function() {
            const tipoExoneracion = $(this).val();
            const codigosExoneracionPermitidos = ['02', '03', '06', '07', '08'];

            // Mostrar u ocultar el campo de artículo basado en el tipo de exoneración
            if (codigosExoneracionPermitidos.includes(tipoExoneracion)) {
                $('#campo-articulo').removeClass('d-none');
                $('#campo-insiso').removeClass('d-none');
            } else {
                $('#campo-articulo').addClass('d-none');
                $('#campo-insiso').addClass('d-none');
                $('#articulo').val(''); // Limpia el campo si se oculta
                 $('#insiso').val(''); // Limpia el campo si se oculta
            }

            // Mostrar los campos "Otros" si se selecciona "Otros"
            if (tipoExoneracion === '99') {
                $('#campo-otros_1').removeClass('d-none');
                $('#campo-otros_2').removeClass('d-none');
            } else {
                $('#campo-otros_1').addClass('d-none');
                $('#campo-otros_2').addClass('d-none');
                $('#tipo_exoneracion_otro').val(''); // Limpia los campos si se ocultan
                $('#institucion_otro').val('');
            }
        });
    });
</script>
