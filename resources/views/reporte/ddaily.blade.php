@extends('layouts.app', ['page' => __('Busqueda de Ventas'), 'pageSlug' => 'dailySales'])
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
                            <h4 class="card-title">{{ __('Filtro de Busqueda.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">   
                    <form method="post" action="{{ route('filtro.ddaily') }}" autocomplete="off">
                    @csrf
                            <label class="form-control-label" for="input-fecha_desde">{{ __('Fecha Desde') }}</label>
                            <input type="date" name="fecha_desde" id="input-fecha_desde" class="form-control" placeholder="{{ __('Fecha Desde') }}" value="{{ old('fecha_desde', $fecha['desde']) }}" required style="display: inline !important; width: 40% !important; margin-right: 40px;">
                            @include('alerts.feedback', ['field' => 'fecha_desde'])
                            <label class="form-control-label" for="input-fecha_hasta">{{ __('Fecha Hasta') }}</label>
                            <input type="date" name="fecha_hasta" id="input-fecha_hasta" class="form-control form-control-alternative{{ $errors->has('fecha_hasta') ? ' is-invalid' : '' }}" placeholder="{{ __('Fecha Hasta') }}" value="{{ old('fecha_hasta', $fecha['hasta']) }}" required style="display: inline !important; width: 40% !important;">
                            @include('alerts.feedback', ['field' => 'fecha_hasta']) 
                        <div class="col-12">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Generar Reporte') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Resultados de la Busqueda') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="documentos_data">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Tipo de Documento') }}</th>
                                <th scope="col">Numero Documento</th>
                               <!-- <th scope="col">Condición Venta</th>-->
                                <th scope="col">Fecha Documento</th>
                                <th scope="col">Identificacion Cliente</th>
                                <th scope="col">Nombre Cliente</th>
                              <!--  <th scope="col">Estado Documento</th>-->
                               
                                
                                <th scope="col"> Total Comprobante</th>
                                 <th scope="col">Observaciones</th>
                                 <th scope="col">Acciones</th>
                            </thead>
                            <tbody>
                                <?php 
                                    $masivo_exento_0 = 0.00000;
                                    $masivo_reducida_1 = 0.00000;
                                    $masivo_reducida_2 = 0.00000;
                                    $masivo_reducida_4 = 0.00000;
                                    $masivo_transitorio_0 = 0.00000;
                                    $masivo_transitorio_4 = 0.00000;
                                    $masivo_transitorio_8 = 0.00000;
                                    $masivo_gravado_13 = 0.00000;
                                    $masivo_no_sujeto = 0.00000;
                                    $masivo_total_iva = 0.00000;

                                    $masivo_total_otroc = 0.00000;
                                    $masivo_descuento = 0.00000;
                                    $masivo_devuelto = 0.00000;
                                    $masivo_exonerado = 0.00000;
                                    $masivo_total_comprobante = 0.00000;
                                ?>
                                @foreach($callback as $document)
                                    <?php
                                        if ($document['tipo_documento'] != '03' and $document['tipo_documento'] != '95') {

                                           
                                            $masivo_total_comprobante += $document['total_comprobante'];

                                        } else {

                                            
                                            $masivo_total_comprobante = $masivo_total_comprobante - $document['total_comprobante'];
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            @switch($document['tipo_documento'])
                                                @case('01')
                                                    Venta 
                                                @break
                                                @case('02')
                                                    Nota Débito 
                                                @break
                                                @case('03')
                                                   Anulacion
                                                @break
                                                @case('04')
                                                    Venta
                                                @break
                                                @case('08')
                                                    Compra
                                                @break
                                                @case('09')
                                                    Venta
                                                @break
                                                
                                                @case('96')
                                                    Venta
                                                @break
                                                @case('95')
                                                     Anulacion 
                                                @break
                                                
                                               
                                               
                                               
                                            @endswitch
                                        </td>
                                        <td>{{ $document['numero_documento']}}</td>
                                      <!--  <td>
                                            @switch($document['condicion'])
                                                @case('01')
                                                    Contado
                                                @break
                                                @case('02')
                                                    Crédito
                                                @break
                                            @endswitch
                                        </td>-->
                                        <td>{{ $document['fecha_documento']}}</td>
                                        <td>{{ $document['identificacion_cliente']}}</td>
                                        <td>{{ $document['nombre_cliente']}}</td>
                                      <!--  <td>{{ $document['estado_doc']}}</td>-->
                                      
                                        <td>{{ $document['total_comprobante']}}</td>
                                         <td>{{ $document['observaciones']}}</td>
                                       <td> <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                         <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a href="{{ route('facturar.imprimir', ['id' => $document['idsale']]) }}" class="dropdown-item">{{ __('Imprimir') }}</a>
                                         </div>
                                         </div></td>
                                    </tr>
                                @endforeach                        
                            </tbody>
                           <!-- <tfoot> 
                                <tr>
                                    <th style="color:black;">Total Comprobantes:</td>
                                    <th style="color: black; text-align: left;"><i><b>{{ number_format($resultados['facturas'],2,'.',',') }}</b></i></th>
                                    <th colspan="9"></th>
                                    
                                </tr>
                                <tr>
                                    <th style="color:black;">Total Notas de Credito:</th>
                                    <th style="color: black; text-align: left;" colspan="2"><i><b>{{ number_format($resultados['notas_credito'],2,'.',',') }}</b></i></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th style="color:black;">Total Final:</th>
                                    <th style="color: black; text-align: left;" colspan="2"><i><b>{{ number_format(($resultados['facturas'] - $resultados['notas_credito']) ,2,'.',',') }}</b></i></th>
                                    <th colspan="9"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>-->
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection
@section('myjs')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#documentos_data').DataTable(
            {     
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]],
                dom: 'Bfrtip',
                buttons: {
                    buttons: [
                    {
                    extend: 'excelHtml5',
                     title: 'Reporte de Ingresos',
                    footer: true,
                    customize: (xlsx, config, dataTable) => {
                        let sheet = xlsx.xl.worksheets['sheet1.xml'];
                        let footerIndex = $('sheetData row', sheet).length;
                        let $footerRows = $('tr', dataTable.footer());

                        // If there are more than one footer rows
                        if ($footerRows.length > 1) {
                            // First header row is already present, so we start from the second row (i = 1)
                            for (let i = 1; i < $footerRows.length; i++) {
                                // Get the current footer row
                                let $footerRow = $footerRows[i];

                                // Get footer row columns
                                let $footerRowCols = $('th', $footerRow);

                                // Increment the last row index
                                footerIndex++;

                                // Create the new header row XML using footerIndex and append it at sheetData
                                $('sheetData', sheet).append(`
                                    <row r="${footerIndex}">
                                        ${$footerRowCols.map((index, el) => `
                                            <c t="inlineStr" r="${String.fromCharCode(65 + index)}${footerIndex}" s="2">
                                                <is>
                                                    <t xml:space="preserve">${$(el).text()}</t>
                                                </is>
                                            </c>
                                        `).get().join('')}
                                    </row>
                                `);
                            }
                        }
                    }
                    }],
                    dom:{
                        button:{
                            tag:"button",
                            className:"btn btn-success mt-4"
                        },
                        buttonLiner: {
                            tag: null
                        } 
                    }
                }
        }
    );
});
</script>
@endsection