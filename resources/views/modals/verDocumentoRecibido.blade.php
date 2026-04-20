<div class="modal fade" id="showDocument" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-inventario" role="document">
      <div class="modal-content">
        <form id="form_productos">
        <div class="modal-header">
          <h2 class="modal-title" id="modalDocumento"></h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col-10">
                  <h4 class="mb-0"><b id="modalFechadoc"></b></h4>
                  <h4 class="mb-0"><b id="modalClavedoc"></b></h4>
                  <h4 class="mb-0"><b>{{ __('Emisor') }}</b></h4>
                  <h5 class="mb-0" id="modalIdentificacion"></h5>
                  <h5 class="mb-0" id="modalNombre"></h5>
                  <h5 class="mb-0" id="modalCorreo"></h5>
                  <h5 class="mb-0" id="modalActividad"></h5>
                  <h5 class="mb-0" id="modalTipoCambio"></h5>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center" id="recibido_data">
              <thead class="thead-light">
                <tr>
                  <th scope="col">Código Interno</th>
                  <th scope="col">Cabys</th>
                  <th scope="col">Descripción</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Unidad Medida</th>
                  <th scope="col">SubTotal</th>
                  <th scope="col">Tarifa</th>
                  <th scope="col">Monto Impuesto</th>
                  <th scope="col">Total Linea</th>
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
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
