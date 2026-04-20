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

@if(!empty($data))
    <div class="row">
        <div class="col-12">
            <div class="card card-chart">
                <div class="card-header ">
                    <div class="row">
                        <div class="col-sm-6 text-left">
                            <h5 class="card-category">Total Ventas</h5>
                            <h2 class="card-title">Reporte de Ventas por Impuesto Tiquete/Facturas.</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table" id="graficos_datatable">
                                <thead class="text-primary">
                                    <tr>
                                        <th rowspan="2">Tarifa</th>
                                        <th colspan="2">Fáctura</th>
                                        <th colspan="2">Tiquete</th>
                                        <th colspan="2">Totales</th>
                                    </tr>

                                    <tr>
                                        <th style="text-align: center;">Monto</th>
                                        <th style="text-align: center;">IVA</th>
                                        <th style="text-align: center;">Monto</th>
                                        <th style="text-align: center;">IVA</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">IVA por Pagar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][4]['monto_iva']  ?? 0.00 }}</td>
                                      
                                        <?php
                                            $factura_neto_l_0 = $data['datos']['tipo_impuesto'][1][1]['monto_neto'] ?? 0.00;
                                            $facturaex_neto_l_0 = $data['datos']['tipo_impuesto'][1][9]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_0 = $data['datos']['tipo_impuesto'][1][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_0 = $data['datos']['tipo_impuesto'][1][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_0 = $data['datos']['tipo_impuesto'][1][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_0 = $datos['tipo_impuesto'][1][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_0 = $data['datos']['tipo_impuesto'][1][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_0 = $data['datos']['tipo_impuesto'][1][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_0 = $data['datos']['tipo_impuesto'][1][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_0 = (($factura_neto_l_0 + $facturaex_neto_l_0 + $tiquete_neto_l_0 + $debito_neto_l_0) - $credito_neto_l_0);
                                            $total_iva_linea_0 = (( $factura_iva_l_0 + $tiquete_iva_l_0 + $debito_iva_l_0 ) - $credito_iva_l_0);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_0 ?? 0.00 }}</td>
                                        <td align="right">{{ $total_iva_linea_0  ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>1.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_1 = $data['datos']['tipo_impuesto'][2][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_1 = $data['datos']['tipo_impuesto'][2][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_1 = $data['datos']['tipo_impuesto'][2][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_1 = $data['datos']['tipo_impuesto'][2][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_1 = $data['datos']['tipo_impuesto'][2][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_1 = $data['datos']['tipo_impuesto'][2][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_1 = $data['datos']['tipo_impuesto'][2][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_1 = $data['datos']['tipo_impuesto'][2][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_1 = (($factura_neto_l_1 + $tiquete_neto_l_1 + $debito_neto_l_1) - $credito_neto_l_1);
                                            $total_iva_linea_1 = (( $factura_iva_l_1 + $tiquete_iva_l_1 + $debito_iva_l_1) - $credito_iva_l_1);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_1 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_1 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>2.00%</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_2 = $data['datos']['tipo_impuesto'][3][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_2 = $data['datos']['tipo_impuesto'][3][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_2 = $data['datos']['tipo_impuesto'][3][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_2 = $data['datos']['tipo_impuesto'][3][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_2 = $data['datos']['tipo_impuesto'][3][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_2 = $data['datos']['tipo_impuesto'][3][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_2 = $data['datos']['tipo_impuesto'][3][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_2 = $data['datos']['tipo_impuesto'][3][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_2 = (($factura_neto_l_2 + $tiquete_neto_l_2 + $debito_neto_l_2) - $credito_neto_l_2);
                                            $total_iva_linea_2 = (( $factura_iva_l_2 + $tiquete_iva_l_2 + $debito_iva_l_2) - $credito_iva_l_2);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_2 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_2 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_3 = $data['datos']['tipo_impuesto'][4][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_3 = $data['datos']['tipo_impuesto'][4][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_3 = $data['datos']['tipo_impuesto'][4][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_3 = $data['datos']['tipo_impuesto'][4][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_3 = $data['datos']['tipo_impuesto'][4][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_3 = $data['datos']['tipo_impuesto'][4][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_3 = $data['datos']['tipo_impuesto'][4][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_3 = $data['datos']['tipo_impuesto'][4][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_3 = (($factura_neto_l_3 + $tiquete_neto_l_3 + $debito_neto_l_3) - $credito_neto_l_3);
                                            $total_iva_linea_3 = (( $factura_iva_l_3 + $tiquete_iva_l_3 + $debito_iva_l_3) - $credito_iva_l_3);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_3 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_3 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_5 = $data['datos']['tipo_impuesto'][5][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_5 = $data['datos']['tipo_impuesto'][5][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_5 = $data['datos']['tipo_impuesto'][5][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_5 = $data['datos']['tipo_impuesto'][5][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_5 = $data['datos']['tipo_impuesto'][5][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_5 = $data['datos']['tipo_impuesto'][5][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_5 = $data['datos']['tipo_impuesto'][5][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_5 = $data['datos']['tipo_impuesto'][5][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_5 = (($factura_neto_l_5 + $tiquete_neto_l_5 + $debito_neto_l_5) - $credito_neto_l_5);
                                            $total_iva_linea_5 = (( $factura_iva_l_5 + $tiquete_iva_l_5 + $debito_iva_l_5) - $credito_iva_l_5);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_5 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_5 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $datos['tipo_impuesto'][6][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $datos['tipo_impuesto'][6][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $datos['tipo_impuesto'][6][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $datos['tipo_impuesto'][6][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_6 = $data['datos']['tipo_impuesto'][6][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_6 = $data['datos']['tipo_impuesto'][6][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_6 = $data['datos']['tipo_impuesto'][6][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_6 = $data['datos']['tipo_impuesto'][6][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_6 = $data['datos']['tipo_impuesto'][6][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_6 = $data['datos']['tipo_impuesto'][6][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_6 = $data['datos']['tipo_impuesto'][6][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_6 = $data['datos']['tipo_impuesto'][6][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_6 = (($factura_neto_l_6 + $tiquete_neto_l_6 + $debito_neto_l_6) - $credito_neto_l_6);
                                            $total_iva_linea_6 = (( $factura_iva_l_6 + $tiquete_iva_l_6 + $debito_iva_l_6) - $credito_iva_l_6);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_6 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_6 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_7 = $data['datos']['tipo_impuesto'][7][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_7 = $data['datos']['tipo_impuesto'][7][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_7 = $data['datos']['tipo_impuesto'][7][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_7 = $data['datos']['tipo_impuesto'][7][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_7 = $data['datos']['tipo_impuesto'][7][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_7 = $data['datos']['tipo_impuesto'][7][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_7 = $data['datos']['tipo_impuesto'][7][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_7 = $data['datos']['tipo_impuesto'][7][3]['monto_iva'] ?? 0.00;
                                            $total_neto_linea_7 = (($factura_neto_l_7 + $tiquete_neto_l_7 + $debito_neto_l_7) - $credito_neto_l_7);
                                            $total_iva_linea_7 = (( $factura_iva_l_7 + $tiquete_iva_l_7 + $debito_iva_l_7) - $credito_iva_l_7);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_7 ?? 0.00 }}</td>
                                        <td align="right">{{  $total_iva_linea_7 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>13.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][4]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_13 = $data['datos']['tipo_impuesto'][8][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_13 = $data['datos']['tipo_impuesto'][8][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_13 = $data['datos']['tipo_impuesto'][8][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_13 = $data['datos']['tipo_impuesto'][8][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_13 = $data['datos']['tipo_impuesto'][8][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_13 = $data['datos']['tipo_impuesto'][8][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_13 = $data['datos']['tipo_impuesto'][8][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_13 = $data['datos']['tipo_impuesto'][8][3]['monto_iva'] ?? 0.00;

                                            $factura_ivax_l_13 = $data['datos']['tipo_impuesto'][8][1]['monto_ivax'] ?? 0.00;
                                             $tiquete_ivax_l_13 = $data['datos']['tipo_impuesto'][8][4]['monto_ivax'] ?? 0.00;
                                              $debito_ivax_l_13 = $data['datos']['tipo_impuesto'][8][2]['monto_ivax'] ?? 0.00;
                                             $credito_ivax_l_13 = $data['datos']['tipo_impuesto'][8][3]['monto_ivax'] ?? 0.00;

                                            $total_neto_linea_13 = (($factura_neto_l_13 + $tiquete_neto_l_13 + $debito_neto_l_13) - $credito_neto_l_13);
                                            $total_iva_linea_13 = (( $factura_iva_l_13 + $tiquete_iva_l_13 + $debito_iva_l_13 ) - $credito_iva_l_13);
                                            $total_ivax_linea_13 = (( $factura_ivax_l_13 + $tiquete_ivax_l_13 + $debito_ivax_l_13 ) - $credito_ivax_l_13);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_13  ?? 0.00 }}</td>
                                        <td align="right">{{ $total_iva_linea_13  ?? 0.00 }}</td>
                                    </tr>
                                    <td>No Sujeto 0%</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][9][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][9][1]['monto_iva']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][10][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][9][4]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $factura_neto_l_0s = $data['datos']['tipo_impuesto'][9][1]['monto_neto'] ?? 0.00;
                                            $factura_iva_l_0s = $data['datos']['tipo_impuesto'][9][1]['monto_iva'] ?? 0.00;
                                            $tiquete_neto_l_0s = $data['datos']['tipo_impuesto'][9][4]['monto_neto'] ?? 0.00;
                                            $tiquete_iva_l_0s = $data['datos']['tipo_impuesto'][9][4]['monto_iva'] ?? 0.00;
                                            $debito_neto_l_0s = $datos['tipo_impuesto'][9][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_0s = $data['datos']['tipo_impuesto'][9][2]['monto_iva'] ?? 0.00;
                                            $credito_neto_l_0s = $data['datos']['tipo_impuesto'][9][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_0s = $data['datos']['tipo_impuesto'][9][3]['monto_iva'] ?? 0.00;

                                            $fac_0s = $data['datos']['tipo_impuesto'][10][1]['monto_neto'] ?? 0.00;
                                            $nc_0s = $data['datos']['tipo_impuesto'][11][1]['monto_neto'] ?? 0.00;


                                            $total_neto_linea_0s = (($factura_neto_l_0s + $tiquete_neto_l_0s + $debito_neto_l_0s + $fac_0s) - $credito_neto_l_0s - $nc_0s );
                                            $total_iva_linea_0s = (( $factura_iva_l_0s + $tiquete_iva_l_0s + $debito_iva_l_0s ) - $credito_iva_l_0s);
                                        ?>
                                        <td align="right">{{ $total_neto_linea_0s ?? 0.00 }}</td>
                                        <td align="right">{{ $total_iva_linea_0s  ?? 0.00 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Notas de Crédito</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <table  class="table" width="100%">
                            <tr>
                                <th rowspan="2">Tarifa</th>
                                <th colspan="2">Nota de Crédito</th>
                                <th colspan="2">Totales</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">Monto</th>
                                <th style="text-align: center;">IVA</th>
                                <th style="text-align: center;">Total</th>
                                <!-- <th style="text-align: center;">IVA por Pagar</th>-->
                            </tr>
                            <tbody>
                                <tbody>
                                    <tr>
                                    
                                        <td>0.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_0 = $data['datos']['tipo_impuesto'][1][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_0 = $data['datos']['tipo_impuesto'][1][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_0 + $credito_iva_l_0 ?? 0.00 }}</td>
                                         <!--<td align="right">{{ $credito_iva_l_0  ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>1.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_1 = $data['datos']['tipo_impuesto'][2][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_1 = $data['datos']['tipo_impuesto'][2][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_1 + $credito_iva_l_1 ?? 0.00 }}</td>
                                         <!--<td align="right">{{  $credito_iva_l_1 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>2.00%</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_2 = $data['datos']['tipo_impuesto'][3][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_2 = $data['datos']['tipo_impuesto'][3][3]['monto_iva'] ?? 0.00;

                                        ?>
                                        <td align="right">{{ $credito_neto_l_2 + $credito_iva_l_2 ?? 0.00 }}</td>
                                         <!--<td align="right">{{  $credito_iva_l_2 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_3 = $data['datos']['tipo_impuesto'][4][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_3 = $data['datos']['tipo_impuesto'][4][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_3 + $credito_iva_l_3 ?? 0.00 }}</td>
                                        <!-- <td align="right">{{  $credito_iva_l_3 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_5 = $data['datos']['tipo_impuesto'][5][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_5 = $data['datos']['tipo_impuesto'][5][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_5 + $credito_iva_l_5 ?? 0.00 }}</td>
                                        <!-- <td align="right">{{  $credito_iva_l_5 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][6][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][6][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_6 = $data['datos']['tipo_impuesto'][6][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_6 = $data['datos']['tipo_impuesto'][6][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_6 + $credito_iva_l_6 ?? 0.00 }}</td>
                                         <!--<td align="right">{{  $credito_iva_l_6 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_7 = $data['datos']['tipo_impuesto'][7][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_7 = $data['datos']['tipo_impuesto'][7][3]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $total_neto_linea_7 + $credito_iva_l_7  ?? 0.00 }}</td>
                                        <!-- <td align="right">{{  $total_iva_linea_7 ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>13.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][3]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][3]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_13 = $data['datos']['tipo_impuesto'][8][3]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_13 = $data['datos']['tipo_impuesto'][8][3]['monto_iva'] ?? 0.00;
                                        ?>
                                      <td align="right">{{ $credito_neto_l_13 +$credito_iva_l_13 ?? 0.00 }}</td>
                                         <!-- <td align="right">{{ $credito_iva_l_13  ?? 0.00 }}</td>-->
                                    </tr>
                                    <tr>
                                        <td>0.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][11][1]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][11][1]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $credito_neto_l_0s = $data['datos']['tipo_impuesto'][11][1]['monto_neto'] ?? 0.00;
                                            $credito_iva_l_0s = $data['datos']['tipo_impuesto'][11][1]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $credito_neto_l_0s  ?? 0.00 }}</td>
                                        <!-- <td align="right">{{ $credito_iva_l_0s  ?? 0.00 }}</td>-->
                                    </tr>
                                </tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Notas de Débito</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <table  class="table" width="100%">
                            <tr>
                                <th rowspan="2">Tarifa</th>
                                <th colspan="2">Nota de Débito</th>
                                <th colspan="2">Totales</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">Monto</th>
                                <th style="text-align: center;">IVA</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">IVA por Pagar</th>
                            </tr>
                            <tbody>
                                <tbody>
                                    <tr>
                                        <td>0.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][1][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_0 = $data['datos']['tipo_impuesto'][1][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_0 = $data['datos']['tipo_impuesto'][1][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_0 ?? 0.00 }}</td>
                                        <td align="right">{{ $debito_iva_l_0  ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>1.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][2][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_1 = $data['datos']['tipo_impuesto'][2][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_1 = $data['datos']['tipo_impuesto'][2][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_1 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_1 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>2.00%</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][3][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_2 = $data['datos']['tipo_impuesto'][3][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_2 = $data['datos']['tipo_impuesto'][3][2]['monto_iva'] ?? 0.00;

                                        ?>
                                        <td align="right">{{ $debito_neto_l_2 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_2 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][4][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_3 = $data['datos']['tipo_impuesto'][4][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_3 = $data['datos']['tipo_impuesto'][4][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_3 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_3 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][5][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_5 = $data['datos']['tipo_impuesto'][5][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_5 = $data['datos']['tipo_impuesto'][5][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_5 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_5 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][6][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][6][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_6 = $data['datos']['tipo_impuesto'][6][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_6 = $data['datos']['tipo_impuesto'][6][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_6 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_6 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>8.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][7][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_7 = $data['datos']['tipo_impuesto'][7][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_7 = $data['datos']['tipo_impuesto'][7][2]['monto_iva'] ?? 0.00;
                                        ?>
                                        <td align="right">{{ $debito_neto_l_7 ?? 0.00 }}</td>
                                        <td align="right">{{  $debito_iva_l_7 ?? 0.00 }}</td>
                                    </tr>
                                    <tr>
                                        <td>13.00 %</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][2]['monto_neto']  ?? 0.00 }}</td>
                                        <td align="right">{{ $data['datos']['tipo_impuesto'][8][2]['monto_iva']  ?? 0.00 }}</td>
                                        <?php
                                            $debito_neto_l_13 = $data['datos']['tipo_impuesto'][8][2]['monto_neto'] ?? 0.00;
                                            $debito_iva_l_13 = $data['datos']['tipo_impuesto'][8][2]['monto_iva'] ?? 0.00;
                                            $otros_cargo = $data['datos']['otros_cargos'] ?? 0.00;

                                        ?>
                                        <td align="right">{{ $debito_neto_l_13  ?? 0.00 }}</td>
                                        <td align="right">{{ $debito_iva_l_13  ?? 0.00 }}</td>
                                    </tr>
                                </tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
$total_mto_final = number_format(( $total_neto_linea_0 +  $total_neto_linea_1 +  $total_neto_linea_2 +  $total_neto_linea_3 +  $total_neto_linea_5 +  $total_neto_linea_6 +  $total_neto_linea_7 + $total_neto_linea_13 + $total_neto_linea_0s),2,',','.');
$total_iva_final = number_format(($total_iva_linea_0 + $total_iva_linea_1 + $total_iva_linea_2 + $total_iva_linea_3 + $total_iva_linea_5 + $total_iva_linea_6 + $total_iva_linea_7 + $total_iva_linea_13 + $total_iva_linea_0s),2,',','.');
//Servicios profesionales variables

$s_profesional_mto = $data['datos_receptor']['clasifica_d151'][4]['total_comprobante'] ?? 0.00;
$s_profesional_imp = $data['datos_receptor']['clasifica_d151'][10]['total_impuesto'] ?? 0.00;
$s_profesional_himp = $data['datos_receptor']['clasifica_d151'][4]['hacienda_imp_creditar'] ?? 0.00;
$s_profesional_gsto = $data['datos_receptor']['clasifica_d151'][4]['hacienda_gasto_aplica'] ?? 0.00;
// Alquileres
$alquileres_mto = $data['datos_receptor']['clasifica_d151'][3]['total_comprobante'] ?? 0.00;
$alquileres_impsto = $data['datos_receptor']['clasifica_d151'][3]['total_impuesto'] ?? 0.00;
$alquileres_hcnda_imp = $data['datos_receptor']['clasifica_d151'][3]['hacienda_imp_creditar'] ?? 0.00;
$alquileres_hcnda_gasto = $data['datos_receptor']['clasifica_d151'][3]['hacienda_gasto_aplica'] ?? 0.00;
// Comisiones
$comisiones_mto = $data['datos_receptor']['clasifica_d151'][5]['total_comprobante'] ?? 0.00;
$comisiones_imp = $data['datos_receptor']['clasifica_d151'][5]['total_impuesto'] ?? 0.00;
$comisiones_hcnda_imp = $data['datos_receptor']['clasifica_d151'][5]['hacienda_imp_creditar'] ?? 0.00;
$comisiones_hcnda_gst = $data['datos_receptor']['clasifica_d151'][5]['hacienda_gasto_aplica'] ?? 0.00;
// Gastos Generales
$gg_mto = $data['datos_receptor']['clasifica_d151'][2]['total_comprobante'] ?? 0.00;
$gg_imp = $data['datos_receptor']['clasifica_d151'][2]['total_impuesto'] ?? 0.00;
$gg_hcnda_imp = $data['datos_receptor']['clasifica_d151'][2]['hacienda_imp_creditar'] ?? 0.00;
$gg_hcnda_gst = $data['datos_receptor']['clasifica_d151'][2]['hacienda_gasto_aplica'] ?? 0.00;
// Intereses
$intereses_mto = $data['datos_receptor']['clasifica_d151'][6]['total_comprobante'] ?? 0.00;
$intereses_imp = $data['datos_receptor']['clasifica_d151'][6]['total_impuesto'] ?? 0.00;
$intereses_hcnda_imp = $data['datos_receptor']['clasifica_d151'][6]['hacienda_imp_creditar'] ?? 0.00;
$intereses_hcnda_gst = $data['datos_receptor']['clasifica_d151'][6]['hacienda_gasto_aplica'] ?? 0.00;
// Otros
$otros_mto = $data['datos_receptor']['clasifica_d151'][7]['total_comprobante'] ?? 0.00;
$otros_imp = $data['datos_receptor']['clasifica_d151'][7]['total_impuesto'] ?? 0.00;
$otros_hcnda_imp = $data['datos_receptor']['clasifica_d151'][7]['hacienda_imp_creditar'] ?? 0.00;
$otros_hcnda_gst = $data['datos_receptor']['clasifica_d151'][7]['hacienda_gasto_aplica'] ?? 0.00;
// Compra neto y iva
$compra_neto = $data['fcompra']['fcompra'][0]['monto_neto']  ?? 0.00;
$compra_iva = $data['fcompra']['fcompra'][0]['monto_iva']  ?? 0.00;
$compra_hcnda_imp = $data['datos_receptor']['clasifica_d151'][1]['hacienda_imp_creditar'] ?? 0.00;
$compra_hcnda_imp_nc = $data['datos_receptor4']['clasifica_d151'][1]['hacienda_imp_creditar'] ?? 0.00;
$compra_hcnda_gst = $data['datos_receptor']['clasifica_d151'][1]['hacienda_gasto_aplica'] ?? 0.00;


//totales para sumatorias y bruto
$t_iva_devengado = ($total_iva_linea_0 + $total_iva_linea_1 + $total_iva_linea_2 + $total_iva_linea_3 + $total_iva_linea_5 + $total_iva_linea_6 + $total_iva_linea_7 + $total_iva_linea_13) ;
$t_iva_creditar = $s_profesional_imp + $alquileres_impsto + $comisiones_imp + $gg_imp + $intereses_imp + $compra_iva - $compra_hcnda_imp_nc;
$t_iva_gasto = 0;
$t_monto_t = ($data['totalmto'][1] + $data['totalmto'][2] + $data['totalmto'][3] + $data['totalmto'][4] + $data['totalmto'][5] + $data['totalmto'][6] + $data['totalmto'][7] + $data['totalmto'][8]) - ($s_profesional_mto + $alquileres_mto + $comisiones_mto + $gg_mto + $intereses_mto + $otros_mto);
$iva_acreditable_doc = 0;
$iva_por_pagar = ($t_iva_devengado - $t_iva_creditar - $data['total_iva_devuelto']) + $iva_acreditable_doc - $total_ivax_linea_13;
$x_fac = (($iva_por_pagar - 2225)*100)/13;
$utilidad = $total_mto_final;


$month_end = strtotime('last day of this month', time());
//echo date('d/m/Y', $month_end);
$date = new DateTime('now');
$date->modify('last day of this month');
//echo $date->format('Y-m-d');
$month_end = $date->format('Y-m-d');
 $fecha_desde = date('Y-m-01');
       // $fecha_hasta = date('Y-m-30');
         $fecha_hasta =  $month_end;


// sumatoria de las ventas
        $otros_cargo1  = DB::table('sales')->select('sales.*')
               ->where([

            ['sales.estatus_sale','=', '2'],
              ['sales.idconfigfact', '=', Auth::user()->idconfigfact],

        ])
        ->whereIn('tipo_documento', ['01', '04'])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
          ->sum('total_otros_cargos');

 $otros_cargo2  = DB::table('sales')->select('sales.*')
               ->where([

            ['sales.estatus_sale','=', '2'],
              ['sales.idconfigfact', '=', Auth::user()->idconfigfact],

        ])
        ->whereIn('tipo_documento', ['03'])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
          ->sum('total_otros_cargos');


$otros_cargos = $otros_cargo1 - $otros_cargo2;



       $ivafact  = DB::table('receptor')->select('receptor.*')
               ->where([

            ['receptor.pendiente','=', '1'],
              ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
             ['receptor.tipo_documento_recibido','=', '01'],
              ['receptor.estatus_hacienda', '=', 'aceptado'],
        ])

        ->whereBetween('fecha_xml_envio', [$fecha_desde, $fecha_hasta])
          ->sum('hacienda_imp_creditar');

  $ivanc  = DB::table('receptor')->select('receptor.*')
               ->where([

            ['receptor.pendiente','=', '1'],
              ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
            ['receptor.tipo_documento_recibido','=', '03'],
             ['receptor.estatus_hacienda', '=', 'aceptado'],
        ])

        ->whereBetween('fecha_xml_envio', [$fecha_desde, $fecha_hasta])
          ->sum('hacienda_imp_creditar');


$t_iva_creditar = $ivafact - $ivanc;
$iva_por_pagar = ($t_iva_devengado - $t_iva_creditar - $data['total_iva_devuelto']) + $iva_acreditable_doc - $total_ivax_linea_13;
$x_fac = (((($iva_por_pagar-2225)*100)/13)+$iva_por_pagar) ;
$utilidad = $total_mto_final;

?>
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Resumen de Totales</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <table  class="table" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="8" align="center" style="font-weight: bold;">Resumen Iva</th>
                                    <th colspan="4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center" colspan="8">(+) IVA Devengado</td>
                                    <td colspan="4">{{ number_format($t_iva_devengado, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="8">(-) IVA Acreditable</td>
                                    <td colspan="4">{{ number_format($t_iva_creditar, 2, '.', ',') }}</td>
                                </tr>

                                <tr>
                                    <td align="center" colspan="8">(-) IVA Devuelto</td>
                                    <td colspan="4">{{ $data['total_iva_devuelto'] ?? 0.00  }}</td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="8">(-) IVA Exonerado</td>
                                    <td colspan="4">{{ $total_ivax_linea_13?? 0.00  }}</td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="8">(+) IVA Acreditable No Doc Electronico</td>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-weight: bold;" colspan="8">(=) IVA por Pagar</td>
                                    <td colspan="4">{{ number_format($iva_por_pagar, 2, '.', ',') }}</td>
                                </tr>
                                @if ($x_fac<0)
                                 <tr>
                                    <td align="center" style="font-weight: bold;" colspan="8">(<>) Por Facturar</td>
                                    <td colspan="4">{{ number_format($x_fac, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td align="center" style="font-weight: bold;" colspan="8">(-) IVA Gasto</td>
                                    <td colspan="4">{{ $t_iva_gasto ?? 0.00  }}</td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-weight: bold;" colspan="8">(=) Venta Neta </td>
                                    <td colspan="4">{{ $utilidad ?? 0.00  }}</td>
                                </tr>
                                 <tr>
                                    <td align="center" style="font-weight: bold;" colspan="8">(=) Otros Cargos </td>
                                    <td colspan="4">{{ number_format($otros_cargos, 2, '.', ',') }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
@endif
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
