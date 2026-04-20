<div class="modal fade" id="AddProductosInventario" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg-inventario" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="tim-icons icon-simple-remove"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table align-items-center" id="factura_datatables">
              <thead class="thead-light">
                <tr>
                  <th></th>
                  <th scope="col">Código</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Cantidad Disponible</th>
                  <th scope="col">Costo Unitario Sin IVA</th>
                  <th scope="col">Taza IVA</th>
                  <th scope="col">Útilidad</th>
                  <th scope="col">Precio Final unitario venta</th>
                </tr>
              </thead>
              <tbody>
                @foreach($productos as $prod)
                  <tr>
                    <td class="center"><input type="checkbox" name="seleccion[]" value="{{ $prod->idproducto }}"></td>
                    <td class="center">{{ $prod->codigo_producto }}</td>
                    <td class="center">{{ $prod->nombre_producto }}</td>
                    <td class="text-right">{{ $prod->cantidad_stock }}</td>
                    <td class="text-right">{{ $prod->precio_sin_imp }}</td>
                    <td class="text-right">{{ $prod->porcentaje_imp }} %</td>
                    <td class="text-right">{{ $prod->utilidad_producto }} %</td>
                    <td class="text-right">{{ $prod->precio_final }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="click" class="btn btn-success" id="agregar_inventario">Agregar Articulo</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>