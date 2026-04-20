<div class="modal fade" id="AddMails" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-inventario" role="document">
      <div class="modal-content">
        <form id="form_productos">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Correos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
       <div class="table">
  <table  id="email_datatables">
    <thead class="thead-light">
      <tr>
        <th></th>
        <th scope="col">Correo</th>
      </tr>
    </thead>
    <tbody>
      @php
        // Verificar que existe additional_email y no est¨¢ vac¨ªo
        $emails = [];
        if (!empty($usuario->additional_email)) {
          $emails = explode(',', $usuario->additional_email);
          // Limpiar espacios en blanco y eliminar vac¨ªos
          $emails = array_map('trim', $emails);
          $emails = array_filter($emails);
        }
      @endphp

      @foreach($emails as $index => $email)
        <tr>
          <td class="center">
            <input type="checkbox" class="select-checkbox" name="seleccion[]" value="{{ $email }}">
          </td>
          <td class="center">{{ $email }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="agregar_correo">Agregar Correo</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
