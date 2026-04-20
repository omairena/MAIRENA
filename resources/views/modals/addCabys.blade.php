<div class="modal fade" id="Cabys" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-inventario" role="document">
      <div class="modal-content">
        <form id="form_productos_cabys">
        <div class="modal-header">
          <h5 class="modal-title">Buscar Código Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-6">
              <div class="form-group{{ $errors->has('categoria') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="categoria">{{ __('Categoria') }}</label>
                <select class="form-control form-control-alternative" id="categoria" name="categoria">
                  <option value="0">0 - Productos de la agricultura, silvicultura y pesca</option>
                  <option value="1">1 - Minerales, electricidad, gas y agua</option>
                  <option value="2">2 - Productos alimenticios, bebidas y tabaco; textiles, prendas de vestir y productos de cuero</option>
                  <option value="3">3 - Bienes transportables, excepto productos metálicos, maquinaria y equipo, n.c.p.</option>
                  <option value="4">4 - Productos metálicos, maquinaria y equipo</option>
                  <option value="5">5 - Construcciones y servicios de construcción</option>
                  <option value="6">6 - Servicios de venta y distribución; alojamiento; servicios de suministro de comidas y bebidas; servicios de transporte; servicios de distribución de electricidad, gas y agua</option>
                  <option value="7">7 - Servicios financieros y servicios conexos; servicios inmobiliarios; servicios de arrendamiento financiero (leasing)</option>
                  <option value="8">8 - Servicios prestados a las empresas y servicios de producción</option>
                  <option value="9">9 - Servicios para la comunidad, sociales y personales</option>
                </select>
              </div>
              
            </div>
            <div class="col-6">
              <div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="descripcion">{{ __('Descripción') }}</label>
                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-alternative{{ $errors->has('descripcion') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripción') }}" value="{{ old('descripcion') }}">
              </div>
              <div class="form-group{{ $errors->has('codigo') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="codigo">{{ __('Código Cabys') }}</label>
                <input type="number" name="codigo" id="codigo" class="form-control form-control-alternative{{ $errors->has('codigo') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Cabys') }}" value="{{ old('codigo') }}">
              </div>
            </div>
            <div class="col-12">
              <div class="text-center">
                <button type="button" class="btn btn-success mt-4" id="buscar_cabys">{{ __('Buscar') }}</button>
              </div>
            </div>
          </div><br>
          <div class="table-responsive">
            <table class="table align-items-center" id="codigos_cabys">
              <thead class="thead-light">
                <tr>
                  <th></th>
                  <th scope="col">Categoria</th>
                  <th scope="col">Código</th>
                  <th scope="col">Descripción</th>
                  <th scope="col">Impuesto(%)</th>
                  <th scope="col">Código Tarifa</th>
                </tr>
              </thead> 
              <tbody>
                
              </tbody>
              <tfoot>
                <tr>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="agregar_codigocabys">Agregar Código</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>