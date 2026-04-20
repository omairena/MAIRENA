<div class="modal fade" id="newBanco" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg-inventario" role="document">
    <div class="modal-content">
      <form method="post" action="{{ route('bancos.jsonstoreb') }}" autocomplete="off" enctype="multipart/form-data" id="form_new_bancos">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Cuenta Banco</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
           
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('ced_receptor') ? ' has-danger' : '' }}">
                <label class="form-control-label" for="cuenta_bancos">{{ __('Cuenta IBAN') }}</label>
                <input type="text" placeholder="BCR_CR123456789" name="cuenta_bancos" id="cuenta_bancos" class="form-control form-control-alternative{{ $errors->has('cuenta_bancos') ? ' is-invalid' : '' }}" >
                @include('alerts.feedback', ['field' => 'cuenta_bancos'])
              </div>
            </div>
          </div>
         
          <div class="modal-footer">
            <button type="submit" class="btn btn-success"  id="agregar_cliente">Agregar Cuenta</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
