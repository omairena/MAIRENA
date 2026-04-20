<div class="modal fade" id="terminosModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"> CONTRATO DE SERVICIOS Y LIMITACIONES POR EL USO DE LA PLATAFORMA FACTURA ELECTRONICA SAN ESTEBAN PARA EMISIÓN DE COMPROBANTES ELECTRÓNICOS.</h2>
            </div>
            <div class="modal-body" style="height:450px; width:850px;">
                <textarea name="contenido_contrato" id="contenido_contrato" cols="200" rows="200" style="height:350px; width:750px;" readonly></textarea>
            
           
             <h5 class="modal-title"> Terminos y Condiciones del Sistema aceptador por:</h5>
            <a class="modal-title"> {{ Auth::user()->name}}</a>
             <h5 class="modal-title"> Fecha y Hora:</h5>
            <a class="modal-title"> {{ date('d/m/y h:i:s')}}</a>
             </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="aceptar_termino">Aceptar Termino</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
