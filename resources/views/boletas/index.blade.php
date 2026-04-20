@extends('layouts.app', ['page' => __('Boletas de Reparacion'), 'pageSlug' => 'allpedidos'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>

@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="card">
               <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <h4 class="card-title">{{ __('Filtro de Busqueda') }}</h4>
                        </div>
                    </div>
                </div>

              <div class="card-body">
                    <form method="post" action="{{ route('filtro.boleta') }}" autocomplete="off" id="filtro_factura">
                    @csrf
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Cliente') }}</label>
                             <input type="text" name="cliente_serch" id="cliente_serch" class="form-control form-control-alternative{{ $errors->has('cliente_serch') ? ' is-invalid' : '' }}" value="">
                                    @include('alerts.feedback', ['field' => 'cliente'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Boleta') }}</label>
                            <input type="text" name="boleta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('boleta') ? ' is-invalid' : '' }}" placeholder="{{ __('Boleta') }}" value="{{ old('Boleta') }}"  style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta'])
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Filtrar') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Boletas de Reparaci贸n') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <div class="dropdown">
                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b style="color: white;">Acciones</b> &nbsp;
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a href="{{ route('boletas.create') }}" class="dropdown-item">{{ __('Nueva Boleta') }}</a>
                                    <a href="{{ route('boletas.limpiar') }}" class="dropdown-item" onclick="return confirm('¿Estas seguro de que deseas limpiar las boletas? Esta accion no se puede deshacer.');">
    Eliminar en Creadas
</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_pedidos_datatable">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('# Documento') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Fecha') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                @if(Auth::user()->config_u[0]->usa_cotizacion_adicional > 0)
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_1) }}</th>
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_2) }}</th>
                                    <th scope="col">{{ __(Auth::user()->config_u[0]->pedido_label_aditional_3) }}</th>
                                @endif
                                <th scope="col">{{ __('Estatus') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($pedidos as $pedido)
                                    <?php
                                        switch ($pedido->estatus_doc) {
                                            case '1':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-danger'>Creada</button>";
                                            break;
                                            case '2':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-info'>En Espera</button>";
                                            break;
                                            case '3':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-success'>Finalizadas</button>";
                                            break;
                                             case '4':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-warning'>Anulada</button>";
                                            break;
                                        }
                                        
                                        	$usuario = App\Cliente::find($pedido->idcliente);
                                    ?>
                                    <tr>
                                        <td>{{ $pedido->numero_documento }}</td>
                                       
                                          <td>{{ $usuario->nombre }}</td>
                                           <td>{{ $pedido->fecha_doc }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_neto_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_descuento_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_impuesto_ped,2,',','.') }}</td>
                                        <td class="text-right">{{ number_format($pedido->total_comprobante_ped,2,',','.') }}</td>
                                        @if(Auth::user()->config_u[0]->usa_cotizacion_adicional > 0)
                                            <td class="text-right">{{ $pedido->value_label_aditional_1 }}</td>
                                            <td class="text-right">{{ $pedido->value_label_aditional_2 }}</td>
                                            <td class="text-right">{{ $pedido->value_label_aditional_3 }}</td>
                                        @endif
                                        <td>
                                            <?php echo $estatus; ?>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @switch($pedido->estatus_doc)
                                                        @case(1)
                                                            <a href="{{ route('boletas.edit', $pedido->idpedido) }}" class="dropdown-item">{{ __('Editar Boleta') }}</a>
                                                            <a href="{{ url('pdf-boleta', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalDatosCierre" data-datos-cierre="{{ $pedido->datos_cierre }}">{{ __('Datos de Reparacion') }}</a>
                                                        @break
                                                        @case(2)
                                                            <a href="{{ url('convertir-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Convertir a Factura') }}</a>
                                                            <a href="{{ url('pdf-boleta', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                           <!-- <a href="{{ url('envia-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>-->
                                                             <a href="{{ route('boletas.edit', $pedido->idpedido) }}" class="dropdown-item">{{ __('Editar Boleta') }}</a>
                                                             <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalDatosCierre" data-datos-cierre="{{ $pedido->datos_cierre }}">{{ __('Datos de Reparacion') }}</a>
                                                        @break
                                                        @case(3)
                                                            <a href="{{ url('pdf-boleta', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                          <!--  <a href="{{ url('envia-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>-->
                                                          <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalDatosCierre" data-datos-cierre="{{ $pedido->datos_cierre }}">{{ __('Datos de Reparacion') }}</a>
                                                        @break
                                                         @case(4)
                                                            <a href="{{ url('pdf-boleta', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>
                                                          <!--  <a href="{{ url('envia-pedido', ['idpedido' => $pedido->idpedido]) }}" class="dropdown-item">{{ __('Enviar Correo') }}</a>-->
                                                          <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalDatosCierre" data-datos-cierre="{{ $pedido->datos_cierre }}">{{ __('Datos de Reparacion') }}</a>
                                                        @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<!-- Modal para mostrar datos de cierre -->
<div class="modal fade" id="modalDatosCierre" tabindex="-1" role="dialog" aria-labelledby="modalDatosCierreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDatosCierreLabel">{{ __('Datos de Cierre') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <textarea id="datosCierreTextArea" readonly style="height: 200px; width: 700px;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cerrar') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        
        $( "#cliente_serch" ).autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete/cliente')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        var resp = $.map(data,function(obj){
                            //console.log(obj);
                            return obj.nombre;
                        });
                        response(resp);
                    }
                });
            },
            minLength: 1
        });
        $(document).ready(function() {
        // Event handler for when the modal is opened
        $('#modalDatosCierre').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var datosCierre = button.data('datos-cierre'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('#datosCierreTextArea').val(datosCierre); // Set the text area value
        });
    });
        
        $('#ver_pedidos_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
    });
</script>
@endsection
