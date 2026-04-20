@extends('layouts.app', ['pageSlug' => 'dashboard'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
<input type="number" name="acepto_terms" id="acepto_terms" value="{{ old('acepto_terminos', $terminos->acepto_terminos) }}" hidden="false">
<?php 
 if(Auth::user()->super_admin == 1){
  $valor = App\Configuracion::where('gnl', Auth::user()->id)->get();
//dd($valor->idconfigfact);
 $e=Auth::user()->f_contador;
 $docs=1000; 
//dd($e);

}else{
   
   $valor = App\Configuracion::where('idconfigfact',  Auth::user()->idconfigfact)->get();
  $e=$valor[0]->fecha_plan;
  $docs=$valor[0]->docs; 
  // dd($valor[0]->fecha_plan);
}
//dd($valor);
$i=0;
foreach( $valor as $val  ){
$valo = App\Sales::where('idconfigfact',$val->idconfigfact)->get();
//dd(count($valo));
$i=$i+count($valo);
}
$b=Auth::user()->factura;
$C=$i-$b;
$d=$docs-$C;
echo 'Su cuenta a Emitido:<b> '. $C .'</b> Documentos Electronicos (FACT/TIQ/NC/ND). Su plan Vence el: <b>'.$e.'</b>. Documentos por Emitir en plan vigente: <b>'.$d.'</b><br>';

if(Auth::user()->super_admin == 1){
  $valorr = App\Configuracion::where('gnl', Auth::user()->id)->get();
//dd($valor->idconfigfact);
}else{
   
   $valorr = App\Configuracion::where('idconfigfact',  Auth::user()->idconfigfact)->get();
}
//dd($valor);
$ir=0;
foreach( $valorr as $valr  ){
$valor = App\Receptor::where('idconfigfact',$valr->idconfigfact)->get();
//dd(count($valo));
$ir=$ir + count($valor);
}

$br=Auth::user()->receptor;
$Cr=$ir-$br;


echo 'Su cuenta a Recepcionado:<b> '. $Cr .'</b> Documentos Electronicos (FACT/NC/ND).';

 ?>


     <?php
   // dd($valorr[0]->idconfigfact);
  
   // Obtener la fecha actual  
$fecha_actual = new DateTime();  

// Establecer $fecha_inicio como el primer día del mes actual  
$fecha_desde = $fecha_actual->modify('first day of this month')->format('Y-m-d 00:00:00'); // Asegúrate de incluir la hora, si es necesario  

// Establecer $fecha_fin como el último día del mes actual  
$fecha_hasta = $fecha_actual->modify('last day of this month')->format('Y-m-d 23:59:59'); // Incluye la hora hasta el final del día  

// Configuración de la consulta  

$query = \DB::table('sales')  
    ->select('sales.*', 'sales_item.*', 'sales.tipo_documento', 'sales.tipo_moneda',  
             'sales.tipo_cambio', 'configuracion.factor_receptor',  
             'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto',  
             'facelectron.estatushacienda')  
    ->leftJoin('sales_item', 'sales.idsale', '=', 'sales_item.idsales')  
    ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')  
    ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')  
    ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')  
    ->where([  
        ['sales.fecha_creada', '>=', $fecha_desde],  
        ['sales.fecha_creada', '<=', $fecha_hasta],  
        ['sales.idconfigfact', '=', Auth::user()->idconfigfact],  
        ['facelectron.estatushacienda', '=', 'aceptado'],  
        ['sales.estatus_sale', '=', 2],  
        ['sales.tipo_doc_ref' ,'!=', 17],
    ])  
    ->whereIn('sales.tipo_documento', ['01', '02', '03', '04', '09'])  
    ->get();  

// Inicializar totales  
$totales = [];  
$total_general_neto = 0;  
$total_general_impuesto = 0;  
$total_exoneraciones_neto = 0;  
$total_otros_cargos = 0;  
$total_iva_devuelto = 0; // Inicializar total para el IVA devuelto  

foreach ($query as $item) {  
    // Acumula el total_iva_devuelto  
    $total_iva_devuelto += $item->total_iva_devuelto ?? 0;  

    // Verificar si existe exoneración  
    if ($item->existe_exoneracion == '01') {  
        if (isset($item->valor_neto)) {  
            $total_exoneraciones_neto += $item->valor_neto;   
        }  
        continue;  
    }  

    // Determinar el valor neto y el impuesto  
    $valor_neto = $item->valor_neto ?? 0;  
    $valor_impuesto = $item->valor_impuesto ?? 0;  

    if ($item->tipo_documento == '03') {  
        $valor_neto = -$valor_neto;  
        $valor_impuesto = -$valor_impuesto;  
    }   

    // Multiplicar por el tipo de cambio si la moneda no es CRC  
    if ($item->tipo_moneda !== 'CRC') {  
        $valor_neto *= $item->tipo_cambio;  
        $valor_impuesto *= $item->tipo_cambio;  
    }  

    // Agrupar por tipo_impuesto  
    $tipo_impuesto = $item->tipo_impuesto;  

    // Inicializa la estructura para el tipo_impuesto si no existe  
    if (!isset($totales[$tipo_impuesto])) {  
        $totales[$tipo_impuesto] = [  
            'impuesto_prc' => $item->impuesto_prc,  
            'total_neto' => 0,  
            'total_impuesto' => 0,  
        ];  
    }  

    // Sumar los totales para el tipo de impuesto  
    $totales[$tipo_impuesto]['total_neto'] += $valor_neto;  
    $totales[$tipo_impuesto]['total_impuesto'] += $valor_impuesto;  

    // Sumar a los totales generales  
    $total_general_neto += $valor_neto;  
    $total_general_impuesto += $valor_impuesto;  

    // Sumar total_otros_cargos considerando el tipo de documento  
    if ($item->tipo_documento == '03') {  
        $total_otros_cargos -= $item->total_otros_cargos ?: 0;  
    } else {  
        $total_otros_cargos += $item->total_otros_cargos ?: 0;  
    }  
}  

// Generar la tabla  
echo '<table class="table" width="100%">';  
echo '<thead class="hover:bg-gray-50">';  
echo '<tr>';  
echo '<th align="left">Código Tarifa</th>';  
echo '<th align="left">Impuesto (%)</th>';  
echo '<th align="left">Total Neto</th>';  
echo '<th align="left">Total Impuesto</th>';  
echo '</tr>';  
echo '</thead>';  
echo '<tbody>';  

foreach ($totales as $tipo_impuesto => $totales_item) {  
    switch ($tipo_impuesto) {  
        case '10':  
            $descripcion_tarifa = 'Exento 0%';  
            break;  
        case '02':  
            $descripcion_tarifa = 'IVA 1%';  
            break;  
        case '03':  
            $descripcion_tarifa = 'IVA 2%';  
            break;  
        case '04':  
            $descripcion_tarifa = 'IVA 4%';  
            break;  
        case '05':  
            $descripcion_tarifa = 'IVA 0% T';  
            break;  
        case '06':  
            $descripcion_tarifa = 'IVA 4% T';  
            break;  
        case '07':  
            $descripcion_tarifa = 'IVA 8%';  
            break;  
        case '08':  
            $descripcion_tarifa = 'IVA 13%';  
            break;  
        case '01':  
            $descripcion_tarifa = 'Tarifa 0% (Artículo 32, num 1, RLIVA)';  
            break;  
        case '11':  
            $descripcion_tarifa = 'Tarifa 0% sin derecho a crédito';  
            break;  
        default:  
            $descripcion_tarifa = 'Desconocido';  
            break;  
    }  

    echo '<tr class="hover:bg-gray-50">';  
    echo '<td align="left">' . htmlspecialchars($descripcion_tarifa) . '</td>';  
    echo '<td align="left">' . htmlspecialchars($totales_item['impuesto_prc']) . '</td>';  
    echo '<td align="left">' . htmlspecialchars(number_format($totales_item['total_neto'], 2)) . '</td>';  
    echo '<td align="left">' . htmlspecialchars(number_format($totales_item['total_impuesto'], 2)) . '</td>';  
    echo '</tr>';  
}  

// Agregar fila para total_iva_devuelto  
echo '<tr class="font-bold bg-gray-100">';  
echo '<td align="left"><b>Total IVA Devuelto</b></td>';  
echo '<td align="left"></td>'; // Espacio vacío para la columna de impuesto  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_iva_devuelto, 2)) . '</b></td>';  
echo '<td align="left"></td>'; // Espacio vacío para total de impuesto  
echo '</tr>';  

// Agregar fila de totales generales  
echo '<tr class="font-bold bg-gray-100">';  
echo '<td align="left"><b>Total General</b></td>';  
echo '<td align="left"></td>';  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_general_neto, 2)) . '</b></td>';  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_general_impuesto, 2)) . '</b></td>';  
echo '</tr>';  

// Agregar fila para total de exoneraciones  
echo '<tr class="font-bold bg-gray-100">';  
echo '<td align="left"><b>Total Exoneraciones</b></td>';  
echo '<td align="left"></td>';  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_exoneraciones_neto, 2)) . '</b></td>';  
echo '<td align="left"></td>';  
echo '</tr>';  

// Agregar fila para total de otros cargos  
echo '<tr class="font-bold bg-gray-100">';  
echo '<td align="left"><b>Total Otros Cargos</b></td>';  
echo '<td align="left"></td>';  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_otros_cargos, 2)) . '</b></td>';  
echo '<td align="left"></td>';  
echo '</tr>';  

// Calcular y mostrar el total final  
$total_final = $total_general_neto + $total_exoneraciones_neto + $total_otros_cargos + $total_general_impuesto - $total_iva_devuelto;  

// Agregar fila para total final  
echo '<tr class="font-bold bg-gray-100">';  
echo '<td align="left"><b>Gran Total</b></td>';  
echo '<td align="left"></td>';  
echo '<td align="left"><b>' . htmlspecialchars(number_format($total_final, 2)) . '</b></td>';  
echo '<td align="left"></td>';  
echo '</tr>';  

echo '</tbody>';  
echo '</table>';


$ivanc = DB::table('receptor')->select(  
        DB::raw('SUM(hacienda_imp_creditar) as total_hacienda_imp_creditar'),  
        DB::raw('SUM(total_comprobante) as total_comprobante'),  
        DB::raw('SUM(total_impuesto) as total_impuesto')  
    )  
    ->where([  
        ['receptor.pendiente', '=', '1'],  
        ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],  
        ['receptor.tipo_documento_recibido', '=', '03'],  
        ['receptor.estatus_hacienda', '=', 'aceptado'],  
    ])  
    ->whereBetween('fecha_xml_envio', [$fecha_desde, $fecha_hasta])  
    ->first(); // Usa first() para obtener una fila con los resultados  


$iva = DB::table('receptor')->select(  
        DB::raw('SUM(hacienda_imp_creditar) as total_hacienda_imp_creditar'),  
        DB::raw('SUM(total_comprobante) as total_comprobante'),  
        DB::raw('SUM(total_impuesto) as total_impuesto')  
    )  
    ->where([  
        ['receptor.pendiente', '=', '1'],  
        ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],  
        ['receptor.tipo_documento_recibido', '!=', '03'],  
        ['receptor.estatus_hacienda', '=', 'aceptado'],  
    ])  
    ->whereBetween('fecha_xml_envio', [$fecha_desde, $fecha_hasta])  
    ->first(); // Usa first() para obtener una fila con los resultados  
    
// Accediendo a los datos  
$totalHaciendaImpCreditarnc = $ivanc->total_hacienda_imp_creditar;  
$totalComprobantenc = $ivanc->total_comprobante;  
$totalImpuestonc = $ivanc->total_impuesto;  
$totalComprobante = $iva->total_comprobante; 
$totalImpuesto = $iva->total_impuesto; 
$total_recepcio=$totalComprobante-$totalImpuesto-$totalComprobantenc;
  $ivanc  = DB::table('receptor')->select('receptor.*')
               ->where([

            ['receptor.pendiente','=', '1'],
              ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
            ['receptor.tipo_documento_recibido','=', '03'],
             ['receptor.estatus_hacienda', '=', 'aceptado'],
        ])

        ->whereBetween('fecha_xml_envio', [$fecha_desde, $fecha_hasta])
          ->sum('hacienda_imp_creditar');



?>
  <div class="col-lg-6">  
    <div class="card card-chart">  
        <div class="card-header">  
            <h5 class="card-category text-center">Resumen de Totales</h5> <!-- Centrar el título -->  
        </div>  
        <div class="card-body">  
            <div class="col-md-12">  
                <table class="table text-center" width="100%"> <!-- Añadido text-center aquí -->  
                    <thead>  
                        <tr>  
                            <th colspan="8" style="font-weight: bold;">Resumen Iva</th>  
                            <th colspan="4"></th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                        <tr>  
                            <td colspan="8">(+) IVA Devengado</td>  
                            <td colspan="4">{{ number_format($total_general_impuesto, 2, '.', ',') }}</td>  
                        </tr>  
                        <tr>  
                            <td colspan="8">(-) IVA Acreditable</td>  
                            <td colspan="4">{{ number_format($totalImpuesto - $totalImpuestonc, 2, '.', ',') }}</td>  
                        </tr>  
                        <tr>  
                            <td colspan="8">(-) IVA Devuelto</td>  
                            <td colspan="4">{{ $total_iva_devuelto ?? 0.00 }}</td>  
                        </tr>  
                        <tr>  
                            <td colspan="8">(-) IVA Exonerado</td>  
                            <td colspan="4">{{ 0 ?? 0.00 }}</td>  
                        </tr>  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(=) IVA por Pagar</td>  
                            <td colspan="4">{{ number_format($total_general_impuesto - ($totalImpuesto - $totalImpuestonc) - $total_iva_devuelto, 2, '.', ',') }}</td>  
                        </tr>  
                        @if (($total_general_impuesto - ($totalImpuesto - $totalImpuestonc) - $total_iva_devuelto) < 0)  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(<>) Por Facturar</td>  
                            <td colspan="4">{{ number_format(($total_general_impuesto - ($totalImpuesto - $totalImpuestonc) - $total_iva_devuelto) * 100 / 13, 2, '.', ',') }}</td>  
                        </tr>  
                        @endif  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(=) Venta Neta </td>  
                            <td colspan="4">{{ number_format($total_general_neto, 2, '.', ',') }} </td>  
                        </tr>  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(=) Total Compras y Gastos </td>  
                            <td colspan="4">{{ number_format($totalComprobante - $totalComprobantenc - $totalImpuesto, 2, '.', ',') }} </td>  
                        </tr>  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(=) Perdida o Ganancia </td>  
                            <td colspan="4">{{ number_format($total_general_neto - ($totalComprobante - $totalComprobantenc - $totalImpuesto), 2, '.', ',') }}</td>  
                        </tr>  
                        <tr>  
                            <td style="font-weight: bold;" colspan="8">(=) Otros Cargos </td>  
                            <td colspan="4">{{ number_format(0, 2, '.', ',') }} </td>  
                        </tr>  
                    </tbody>  
                </table>  
            </div>  
        </div>  
    </div>  
</div>  
    </div>

@include('modals.terminosyCondiciones')

@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" defer></script>
<script type="text/javascript">
    $(document).ready(function() {
        //$.noConflict();
        var contrato = @json($contrato);
        var acepto_terminos = $('#acepto_terms').val();
        //console.log(contrato);
        if(acepto_terminos == 0){

            //alert('modal de terminos y condiciones');
            $('#terminosModal').modal('show');
            $('#contenido_contrato').val(contrato.value);
        }

        $('#graficos_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]]
            }
        );

        $('#terminosModal').on('hidden.bs.modal', function () {
            location.reload();
        })

        $('#aceptar_termino').click(function(e) {
            e.preventDefault();
            var URL = {!! json_encode(url('aceptar-terminos-condiciones')) !!};
            $.ajax({
                type:'get',
                url: URL,
                dataType: 'json',
                success:function(response){
                    location.reload();
                }
            });
        });
    });

    
    //
</script>
@endsection
