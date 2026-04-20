@extends('layouts.app', ['page' => __('Compras por Proveedor.'), 'pageSlug' => 'dailySales'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">  
</head>
@section('content')
<?php
// Obtener el nombre de la configuración  
$configuracion = DB::table('configuracion')  
    ->where('idconfigfact', Auth::user()->idconfigfact)  
    ->first();  

if ($configuracion) {  
    $nombreConfiguracion = $configuracion->nombre_emisor; // Asumiendo que la columna se llama 'nombre'  
} else {  
    $nombreConfiguracion = 'Configuración no encontrada';  
}
?>
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
                    <form method="post" action="{{ route('filtro.comprasproveedor') }}" autocomplete="off">
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
                            <h4 class="card-title">{{ __('Documentos Construidos') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="documentos_data">
                            <thead>
                                <tr>
                                    <th colspan="28" style="text-align: center;">
                                        <b>Reporte de Compras por Proveedor para: </b> {{$nombreConfiguracion}} <br>
                                        <b>Montos en Moneda Extrangera colonizados al Tipo de Cambio del XML</b>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="14" style="text-align: center;">
                                        <b>Fecha Desde: </b> {{$fecha['desde']}}
                                    </th>
                                    <th colspan="14" style="text-align: center;">
                                        <b>Fecha Hasta: </b> {{$fecha['hasta']}}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="28" style="text-align: center;">
                                        <b>Compras</b>
                                    </th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th colspan="2">Tarifas</th>
                                    <th >0</th>
                                    <th colspan="2">0.5</th>
                                    <th colspan="2">1</th>
                                    <th colspan="2">2</th>
                                    <th colspan="2">4</th>
                                    <th colspan="2">Trans 0</th>
                                    <th colspan="2">Trans 4</th>
                                    <th colspan="2">Trans 8</th>
                                    <th colspan="2">13</th>
                                    <th>No sujetas</th>
                                    <th>Otros Cargos</th>
                                    <th>SubTotal Neto</th>
                                    <th>SubTotal IVA</th>
                                    <th>Exonerado IVA</th>
                                    <th>Iva Devuelto</th>
                                    <th>Total IVA</th>
                                    <th>Total</th>
                                </tr>
                                <tr>
                                    <th>Codigo de Actividad</th>
                                     <th>Clasificacion</th>
                                     <th>Cedula</th>
                                    <th>Proveedor</th>
                                    <th>Monto</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>Monto</th>
                                    <th>IVA</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                    <th>.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $excento= 0.00000;
                                    $m13= 0.00000;
                                    $t13= 0.00000;
                                    $m05= 0.00000;
                                    $t05= 0.00000;
                                    $m2= 0.00000;
                                    $t2= 0.00000;
                                    $m3= 0.00000;
                                    $t3= 0.00000;
                                    $m4= 0.00000;
                                    $t4= 0.00000;
                                    $m5= 0.00000;
                                    $t5= 0.00000;
                                    $m6= 0.00000;
                                    $t6= 0.00000;
                                    $m7= 0.00000;
                                    $t7= 0.00000;
                                    $no_sujeto= 0.00000;
                                    $otros_cargos= 0.00000;
                                    $subtotal_neto= 0.00000;
                                    $subtotal_iva= 0.00000;
                                    $exonerado_iva= 0.00000;
                                    $iva_devuelto= 0.00000;
                                    $total_iva= 0.00000;
                                    
                                    $total= 0.00000;
                                        ?>
                                @for($i=0; $i < count($array_final); $i++)
                                    @foreach($array_final[$i]['proveedores']['recepciones'] as $recepciones)
                                     <?php
                                 
                                 if(isset($recepciones['moneda'])){
                  $mod=$recepciones['moneda'];
                  }else{
                  $mod='CRC';
                  }
                 
                  
                                    if($mod === 'USD'){
                                        
                                        $excento += $recepciones['10']['monto'] * $recepciones['tc'] ;    
                                    $m13 +=  $recepciones['13']['monto'] * $recepciones['tc'] ; 
                                    $t13 +=  $recepciones['13']['iva'] * $recepciones['tc'] ; 
                                    $m05 +=  $recepciones['09']['monto'] * $recepciones['tc'] ; 
                                    $t05 +=  $recepciones['09']['iva'] * $recepciones['tc'] ; 
                                    $m2 +=  $recepciones['02']['monto'] * $recepciones['tc'] ; 
                                    $t2 +=  $recepciones['02']['iva'] * $recepciones['tc'] ; 
                                    $m3 +=  $recepciones['03']['monto'] * $recepciones['tc'] ; 
                                    $t3 +=  $recepciones['03']['iva'] * $recepciones['tc'] ;
                                    $m4 +=  $recepciones['04']['monto'] * $recepciones['tc'] ; 
                                    $t4 +=  $recepciones['04']['iva'] * $recepciones['tc'] ;
                                    $m5 +=  $recepciones['05']['monto'] * $recepciones['tc'] ; 
                                    $t5 +=  $recepciones['05']['iva'] * $recepciones['tc'] ;
                                    $m6 +=  $recepciones['06']['monto'] * $recepciones['tc'] ; 
                                    $t6 +=  $recepciones['06']['iva'] * $recepciones['tc'] ;
                                    $m7 +=  $recepciones['07']['monto'] * $recepciones['tc'] ; 
                                    $t7 +=  $recepciones['07']['iva'] * $recepciones['tc'] ;
                                    $no_sujeto +=  (($recepciones['no_sujeto']['monto'])+($recepciones['01']['monto'])+($recepciones['11']['monto'])) * $recepciones['tc'] ;
                                    $otros_cargos +=  $recepciones['otros_cargos'] * $recepciones['tc'] ;
                                    $subtotal_neto += $recepciones['subtotal_neto'] * $recepciones['tc'] ;
                                    $subtotal_iva += $recepciones['subtotal_iva'] * $recepciones['tc'];
                                    $exonerado_iva += $recepciones['exonerado_iva'] * $recepciones['tc'];
                                    $iva_devuelto += $recepciones['iva_devuelto'] * $recepciones['tc'];
                                    $total_iva += $recepciones['total_iva'] * $recepciones['tc'];
                                    $total += $recepciones['total'] * $recepciones['tc'];
                                    
                                    
                                    
                                    
                                    }else{
                                    $excento += $recepciones['10']['monto'];
                                    $m13 += $recepciones['13']['monto'];
                                    $t13 += $recepciones['13']['iva'];
                                    $m05 += $recepciones['09']['monto'];
                                    $t05 += $recepciones['09']['iva'];
                                    $m2 += $recepciones['02']['monto'];
                                    $t2 += $recepciones['02']['iva'];
                                    $m3 += $recepciones['03']['monto'];
                                    $t3 += $recepciones['03']['iva'];
                                    $m4 += $recepciones['04']['monto'];
                                    $t4 += $recepciones['04']['iva'];
                                    $m5 += $recepciones['05']['monto'];
                                    $t5 += $recepciones['05']['iva'];
                                    $m6 += $recepciones['06']['monto'];
                                    $t6 += $recepciones['06']['iva'];
                                    $m7 += $recepciones['07']['monto'];
                                    $t7 += $recepciones['07']['iva'];
                                    $no_sujeto += (($recepciones['no_sujeto']['monto'])+($recepciones['01']['monto'])+($recepciones['11']['monto']));
                                    $otros_cargos +=  $recepciones['otros_cargos'];
                                    $subtotal_neto += $recepciones['subtotal_neto'];
                                    
                                    $subtotal_iva += $recepciones['subtotal_iva'];
                                    $exonerado_iva += $recepciones['exonerado_iva'];
                                    $iva_devuelto += $recepciones['iva_devuelto'];
                                    $total_iva += $recepciones['total_iva'];
                                    $total += $recepciones['total'];
                                    
                                    }
                                   
                                      ?>
                                        <tr>
                                            <td>{{ $array_final[$i]['actividad'] }}</td>
                                             <td>{{ $recepciones['clasificacion'] }}</td>
                                             <td>{{ $recepciones['identificacion'] }}</td>
                                              @if($mod === 'USD' )
                                            <td>{{ $recepciones['proveedor'] .' ** '. $recepciones['moneda'].' ** TC ** '. $recepciones['tc']  }}</td>
                                            
                                             
                                             <td>{{ number_format( $recepciones['10']['monto'] * $recepciones['tc'] ,2,'.',',') }}</td>
                                           
                                            <td>{{ number_format($recepciones['09']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['09']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            
                                            <td>{{ number_format($recepciones['02']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['02']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['03']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['03']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['04']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['04']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['05']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['05']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['06']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['06']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['07']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['07']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['13']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['13']['iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format((($recepciones['no_sujeto']['monto'])+($recepciones['01']['monto'])+($recepciones['11']['monto'])) * $recepciones['tc'],2,'.',',')}}</td>
                                             <td>{{ number_format($recepciones['otros_cargos'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_neto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['exonerado_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['iva_devuelto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total'] * $recepciones['tc'],2,'.',',')}}</td>
                                            
                                            
                                             
                                             @else
                                              <td>{{ $recepciones['proveedor'] .' ** '. $recepciones['moneda'].' ** TC ** '. $recepciones['tc']  }}</td>
                                             <td>{{ number_format( $recepciones['10']['monto'],2,'.',',')}}</td>
                                           
                                            <td>{{ number_format($recepciones['09']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['09']['iva'],2,'.',',')}}</td>
                                            
                                            <td>{{ number_format($recepciones['02']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['02']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['03']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['03']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['04']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['04']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['05']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['05']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['06']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['06']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['07']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['07']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['13']['monto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['13']['iva'],2,'.',',')}}</td>
                                            <td>{{ number_format((($recepciones['no_sujeto']['monto'])+($recepciones['01']['monto'])+($recepciones['11']['monto'])),2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['otros_cargos'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_neto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['exonerado_iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['iva_devuelto'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total_iva'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total'],2,'.',',')}}</td>
                                               @endif
                                            

                                            </tr>
                                    @endforeach
                                     @endfor
                                    <tr>
                                        <td></td>
                                         <td></td>
                                        <td></td>
                                        <td><b>Total Compras</b></td>
                                     
                                        <td><b>{{ number_format($excento,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($m05 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($t05 ,2,'.',',')}}</b></td>
                                        
                                        <td><b>{{ number_format($m2 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($t2,2,'.',',') }}</b></td>
                                       
                                        <td><b>{{ number_format($m3 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($t3 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($m4,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($t4,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($m5 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($t5,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($m6,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($t6,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($m7,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($t7,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($m13,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($t13 ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($no_sujeto ,2,'.',',')}}</b></td>
                                         <td><b>{{ number_format($otros_cargos ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($subtotal_neto ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($subtotal_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($exonerado_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($iva_devuelto,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($total_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($total,2,'.',',') }}</b></td>
                                         
                                         
                                        
                                    
                                    
                                    </tr>
                               
                                
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection
@section('myjs')

<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="{{ asset('black') }}/js/buttons.html5.js"></script>
<script type="text/javascript">
 var nombreConfiguracion = "{{ $nombreConfiguracion }}"; // Asegúrate de que esté escapado correctamente  
    $(document).ready(function() {
        $('#documentos_data').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                "ordering": false,
               "pageLength": 5,
                dom: 'Bfrtip',
                buttons: {
                    buttons: [
                    {
                    extend: 'excelHtml5',
                     title: 'Reporte de Compras y Gastos - ' + nombreConfiguracion, // Usar la variable aquí  
                    footer: true,
                    customize: (xlsx, config, dataTable) => {
                        //Apply styles, Center alignment of text and making it bold.
                        var sSh = xlsx.xl['styles.xml'];
                        var lastXfIndex = $('cellXfs xf', sSh).length - 1;

                        var n1 = '<numFmt formatCode="##0.0000%" numFmtId="300"/>';
                        var s2 = '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyFont="1" applyFill="0" applyBorder="0" xfId="0" applyAlignment="1">' +
                            '<alignment horizontal="center"/></xf>';

                        sSh.childNodes[0].childNodes[0].innerHTML += n1;
                        sSh.childNodes[0].childNodes[5].innerHTML += s2;

                        var greyBoldCentered = lastXfIndex + 1;

                        //Merge cells as per the table's colspan
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var dt = $('#tblReport').DataTable();
                        var frColSpan = $(dt.table().header()).find('th:nth-child(1)').prop('colspan');
                        var srColSpan = $(dt.table().header()).find('th:nth-child(2)').prop('colspan');
                        var columnToStart = 2;

                        var mergeCells = $('mergeCells', sheet);
                        mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            attr: {
                                ref: 'A1:' + toColumnName(frColSpan) + '1'
                            }
                        }));

                        mergeCells.attr('count', mergeCells.attr('count') + 1);

                        var columnToStart = 2;

                        while (columnToStart <= frColSpan) {
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: toColumnName(columnToStart) + '2:' + toColumnName((columnToStart - 1) + srColSpan) + '2'
                                }
                            }));
                            columnToStart = columnToStart + srColSpan;
                            mergeCells.attr('count', mergeCells.attr('count') + 1);
                        }

                        //Text alignment to center and apply bold
                        $('row:nth-child(1) c:nth-child(1)', sheet).attr('s', greyBoldCentered);
                        for (i = 0; i < frColSpan; i++) {
                            $('row:nth-child(2) c:nth-child(' + i + ')', sheet).attr('s', greyBoldCentered);
                        }

                        function _createNode(doc, nodeName, opts) {
                            var tempNode = doc.createElement(nodeName);
                            if (opts) {
                                if (opts.attr) {
                                    $(tempNode).attr(opts.attr);
                                }
                                if (opts.children) {
                                    $.each(opts.children, function (key, value) {
                                        tempNode.appendChild(value);
                                    });
                                }
                                if (opts.text !== null && opts.text !== undefined) {
                                    tempNode.appendChild(doc.createTextNode(opts.text));
                                }
                            }
                            return tempNode;
                        }

                        //Function to fetch the cell name
                        function toColumnName(num) {
                            for (var ret = '', a = 1, b = 26; (num -= a) >= 0; a = b, b *= 26) {
                                ret = String.fromCharCode(parseInt((num % b) / a) + 65) + ret;
                            }
                            return ret;
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
