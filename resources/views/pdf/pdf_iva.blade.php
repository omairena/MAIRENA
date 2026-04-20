<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de IVA</title>

    <style type="text/css">
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
        }
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        table {
            font-size: x-small;
        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }
        .invoice table {
            margin: 3px;
        }
        .invoice h3 {
            margin-left: 3px;
        }
        .information {
            background-color: #60A7A6;
            color: #FFF;
        }
        .information .logo {
            margin: 3px;
        }
        .information table {
            padding: 5px;
        }
    </style>
<?php
 $valorr = App\Configuracion::where('idconfigfact',  Auth::user()->idconfigfact)->get();
 
?>
</head>
<body>
<div class="information">
    <table width="100%">
        <thead>
            <tr>
                <th colspan="8" align="center" style="width: 100%;">
                    <h3>Reporte de IVA</h3>
                  
                    <h3>Contribuyente: <?php echo $valorr[0]->nombre_emisor; ?></h3>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">Fecha Inicio</td>
                <td align="center">{{ $fecha_inicio }}</td> 
                <td align="center">Fecha Final</td>
                <td align="center">{{ $fecha_fin }}</td> 
                <td align="center">% Proporcionalidad</td>
                <td align="center">{{$valorr[0]->factor_receptor }}</td>
                <td align="center">Act Economica</td>
                <td align="center">{{ $codigo_actividad }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="invoice">
    <!--<table class="table" width="100%">
        <thead>
            <tr>
                <th colspan="12">
                    <h4 align="center">Débito Fiscal</h4>
                </th>
            </tr>
            <tr>
                <th style="text-align: center;" rowspan="2">Tarifa</th>
                <th style="text-align: center;" colspan="2">Facturas Emitidas</th>
                <th style="text-align: center;" colspan="2">Notas de Debito</th>
                <th style="text-align: center;" colspan="2">Notas de Credito</th>
                <th style="text-align: center;" colspan="2">Tiquetes</th>
                <th style="text-align: center;" colspan="2">Totales</th>
            </tr>
            <tr>
                <th style="text-align: center;">Monto</th>
                <th style="text-align: center;">IVA</th>
                <th style="text-align: center;">Monto</th>
                <th style="text-align: center;">IVA</th>
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
                    <td align="right">{{ $datos['tipo_impuesto'][1][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][1][4]['monto_iva']  ?? 0.00 }}</td>
                    <?php 
                        $factura_neto_l_0 = $datos['tipo_impuesto'][1][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_0 = $datos['tipo_impuesto'][1][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_0 = $datos['tipo_impuesto'][1][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_0 = $datos['tipo_impuesto'][1][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_0 = $datos['tipo_impuesto'][1][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_0 = $datos['tipo_impuesto'][1][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_0 = $datos['tipo_impuesto'][1][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_0 = $datos['tipo_impuesto'][1][3]['monto_iva'] ?? 0.00;
                        
                          $factura_ivax_l_0 = $datos['tipo_impuesto'][1][1]['monto_ivax'] ?? 0.00;
                           $tiquete_ivax_l_0 = $datos['tipo_impuesto'][1][4]['monto_ivax'] ?? 0.00;
                           $debito_ivax_l_0 = $datos['tipo_impuesto'][1][2]['monto_ivax'] ?? 0.00;
                            $credito_ivax_l_0 = $datos['tipo_impuesto'][1][3]['monto_ivax'] ?? 0.00;
                           
                        $total_neto_linea_0 = (($factura_neto_l_0 + $tiquete_neto_l_0 + $debito_neto_l_0) - $credito_neto_l_0);
                        $total_iva_linea_0 = (( $factura_iva_l_0 + $tiquete_iva_l_0 + $debito_iva_l_0 ) - $credito_iva_l_0);
                         $total_ivax_linea_0 = (( $factura_ivax_l_0 + $tiquete_ivax_l_0 + $debito_ivax_l_0 ) - $credito_ivax_l_0);
                    ?>
                    <td align="right">{{ $total_neto_linea_0 ?? 0.00 }}</td>
                    <td align="right">{{ $total_iva_linea_0  ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>1.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][2][4]['monto_iva']  ?? 0.00 }}</td>
                    <?php 
                        $factura_neto_l_1 = $datos['tipo_impuesto'][2][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_1 = $datos['tipo_impuesto'][2][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_1 = $datos['tipo_impuesto'][2][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_1 = $datos['tipo_impuesto'][2][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_1 = $datos['tipo_impuesto'][2][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_1 = $datos['tipo_impuesto'][2][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_1 = $datos['tipo_impuesto'][2][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_1 = $datos['tipo_impuesto'][2][3]['monto_iva'] ?? 0.00;
                        
                         $factura_ivax_l_1 = $datos['tipo_impuesto'][2][1]['monto_ivax'] ?? 0.00;
                          $tiquete_ivax_l_1 = $datos['tipo_impuesto'][2][4]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_1 = $datos['tipo_impuesto'][2][4]['monto_ivax'] ?? 0.00;
                          $debito_ivax_l_1 = $datos['tipo_impuesto'][2][2]['monto_ivax'] ?? 0.00;
                          $credito_ivax_l_1 = $datos['tipo_impuesto'][2][3]['monto_ivax'] ?? 0.00;
                          
                        $total_neto_linea_1 = (($factura_neto_l_1 + $tiquete_neto_l_1 + $debito_neto_l_1) - $credito_neto_l_1);
                        $total_iva_linea_1 = (( $factura_iva_l_1 + $tiquete_iva_l_1 + $debito_iva_l_1) - $credito_iva_l_1);
                        $total_ivax_linea_1 = (( $factura_ivax_l_1 + $tiquete_ivax_l_1 + $debito_ivax_l_1) - $credito_ivax_l_1);
                    ?>
                    <td align="right">{{ $total_neto_linea_1 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_1 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>2.00%</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][3][4]['monto_iva']  ?? 0.00 }}</td>
                     <?php 
                        $factura_neto_l_2 = $datos['tipo_impuesto'][3][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_2 = $datos['tipo_impuesto'][3][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_2 = $datos['tipo_impuesto'][3][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_2 = $datos['tipo_impuesto'][3][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_2 = $datos['tipo_impuesto'][3][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_2 = $datos['tipo_impuesto'][3][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_2 = $datos['tipo_impuesto'][3][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_2 = $datos['tipo_impuesto'][3][3]['monto_iva'] ?? 0.00;
                        
                         $factura_ivax_l_2 = $datos['tipo_impuesto'][3][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_2 = $datos['tipo_impuesto'][3][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_2 = $datos['tipo_impuesto'][3][2]['monto_ivax'] ?? 0.00;
                         $credito_ivax_l_2 = $datos['tipo_impuesto'][3][3]['monto_ivax'] ?? 0.00;
                         
                        $total_neto_linea_2 = (($factura_neto_l_2 + $tiquete_neto_l_2 + $debito_neto_l_2) - $credito_neto_l_2);
                        $total_iva_linea_2 = (( $factura_iva_l_2 + $tiquete_iva_l_2 + $debito_iva_l_2) - $credito_iva_l_2);
                          $total_ivax_linea_2 = (( $factura_ivax_l_2 + $tiquete_ivax_l_2 + $debito_ivax_l_2) - $credito_ivax_l_2);
                        
                    ?>
                    <td align="right">{{ $total_neto_linea_2 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_2 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>4.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][4][4]['monto_iva']  ?? 0.00 }}</td>
                     <?php 
                        $factura_neto_l_3 = $datos['tipo_impuesto'][4][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_3 = $datos['tipo_impuesto'][4][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_3 = $datos['tipo_impuesto'][4][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_3 = $datos['tipo_impuesto'][4][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_3 = $datos['tipo_impuesto'][4][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_3 = $datos['tipo_impuesto'][4][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_3 = $datos['tipo_impuesto'][4][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_3 = $datos['tipo_impuesto'][4][3]['monto_iva'] ?? 0.00;
                        
                         $factura_ivax_l_3 = $datos['tipo_impuesto'][4][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_3 = $datos['tipo_impuesto'][4][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_3 = $datos['tipo_impuesto'][4][2]['monto_ivax'] ?? 0.00;
                         $credito_ivax_l_3 = $datos['tipo_impuesto'][4][3]['monto_ivax'] ?? 0.00;
                        
                        $total_neto_linea_3 = (($factura_neto_l_3 + $tiquete_neto_l_3 + $debito_neto_l_3) - $credito_neto_l_3);
                        $total_iva_linea_3 = (( $factura_iva_l_3 + $tiquete_iva_l_3 + $debito_iva_l_3) - $credito_iva_l_3);
                         $total_ivax_linea_3 = (( $factura_ivax_l_3 + $tiquete_ivax_l_3 + $debito_ivax_l_3) - $credito_ivax_l_3);
                    ?>
                    <td align="right">{{ $total_neto_linea_3 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_3 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>8.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][5][4]['monto_iva']  ?? 0.00 }}</td>
                     <?php 
                        $factura_neto_l_5 = $datos['tipo_impuesto'][5][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_5 = $datos['tipo_impuesto'][5][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_5 = $datos['tipo_impuesto'][5][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_5 = $datos['tipo_impuesto'][5][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_5 = $datos['tipo_impuesto'][5][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_5 = $datos['tipo_impuesto'][5][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_5 = $datos['tipo_impuesto'][5][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_5 = $datos['tipo_impuesto'][5][3]['monto_iva'] ?? 0.00;
                        
                         $factura_ivax_l_5 = $datos['tipo_impuesto'][5][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_5 = $datos['tipo_impuesto'][5][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_5 = $datos['tipo_impuesto'][5][2]['monto_ivax'] ?? 0.00;
                          $credito_ivax_l_5 = $datos['tipo_impuesto'][5][3]['monto_ivax'] ?? 0.00;
                        
                        $total_neto_linea_5 = (($factura_neto_l_5 + $tiquete_neto_l_5 + $debito_neto_l_5) - $credito_neto_l_5);
                        $total_iva_linea_5 = (( $factura_iva_l_5 + $tiquete_iva_l_5 + $debito_iva_l_5) - $credito_iva_l_5);
                         $total_ivax_linea_5 = (( $factura_ivax_l_5 + $tiquete_ivax_l_5 + $debito_ivax_l_5) - $credito_ivax_l_5);
                    ?>
                    <td align="right">{{ $total_neto_linea_5 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_5 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>4.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][6][4]['monto_iva']  ?? 0.00 }}</td>
                     <?php 
                        $factura_neto_l_6 = $datos['tipo_impuesto'][6][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_6 = $datos['tipo_impuesto'][6][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_6 = $datos['tipo_impuesto'][6][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_6 = $datos['tipo_impuesto'][6][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_6 = $datos['tipo_impuesto'][6][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_6 = $datos['tipo_impuesto'][6][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_6 = $datos['tipo_impuesto'][6][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_6 = $datos['tipo_impuesto'][6][3]['monto_iva'] ?? 0.00;
                        
                        
                         $factura_ivax_l_6 = $datos['tipo_impuesto'][6][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_6 = $datos['tipo_impuesto'][6][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_6 = $datos['tipo_impuesto'][6][2]['monto_ivax'] ?? 0.00;
                         $credito_ivax_l_6 = $datos['tipo_impuesto'][6][3]['monto_ivax'] ?? 0.00;
                        
                        
                        
                        $total_neto_linea_6 = (($factura_neto_l_6 + $tiquete_neto_l_6 + $debito_neto_l_6) - $credito_neto_l_6);
                        $total_iva_linea_6 = (( $factura_iva_l_6 + $tiquete_iva_l_6 + $debito_iva_l_6) - $credito_iva_l_6);
                        $total_ivax_linea_6 = (( $factura_ivax_l_6 + $tiquete_ivax_l_6 + $debito_ivax_l_6) - $credito_ivax_l_6);
                    ?>
                    <td align="right">{{ $total_neto_linea_6 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_6 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>8.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][7][4]['monto_iva']  ?? 0.00 }}</td>
                     <?php 
                        $factura_neto_l_7 = $datos['tipo_impuesto'][7][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_7 = $datos['tipo_impuesto'][7][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_7 = $datos['tipo_impuesto'][7][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_7 = $datos['tipo_impuesto'][7][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_7 = $datos['tipo_impuesto'][7][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_7 = $datos['tipo_impuesto'][7][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_7 = $datos['tipo_impuesto'][7][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_7 = $datos['tipo_impuesto'][7][3]['monto_iva'] ?? 0.00;
                        
                         $factura_ivax_l_7 = $datos['tipo_impuesto'][7][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_7 = $datos['tipo_impuesto'][7][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_7 = $datos['tipo_impuesto'][7][2]['monto_ivax'] ?? 0.00;
                         $credito_ivax_l_7 = $datos['tipo_impuesto'][7][3]['monto_ivax'] ?? 0.00;
                        
                        
                        
                        $total_neto_linea_7 = (($factura_neto_l_7 + $tiquete_neto_l_7 + $debito_neto_l_7) - $credito_neto_l_7);
                        $total_iva_linea_7 = (( $factura_iva_l_7 + $tiquete_iva_l_7 + $debito_iva_l_7) - $credito_iva_l_7);
                        $total_ivax_linea_7 = (( $factura_ivax_l_7 + $tiquete_ivax_l_7 + $debito_ivax_l_7) - $credito_ivax_l_7);
                    ?>
                    <td align="right">{{ $total_neto_linea_7 ?? 0.00 }}</td>
                    <td align="right">{{  $total_iva_linea_7 ?? 0.00 }}</td>
                </tr>
                <tr>
                    <td>13.00 %</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][8][4]['monto_iva']  ?? 0.00 }}</td>
                    <?php 
                        $factura_neto_l_13 = $datos['tipo_impuesto'][8][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_13 = $datos['tipo_impuesto'][8][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_13 = $datos['tipo_impuesto'][8][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_13 = $datos['tipo_impuesto'][8][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_13 = $datos['tipo_impuesto'][8][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_13 = $datos['tipo_impuesto'][8][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_13 = $datos['tipo_impuesto'][8][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_13 = $datos['tipo_impuesto'][8][3]['monto_iva'] ?? 0.00;
                        
                          $factura_ivax_l_13 = $datos['tipo_impuesto'][8][1]['monto_ivax'] ?? 0.00;
                         $tiquete_ivax_l_13 = $datos['tipo_impuesto'][8][4]['monto_ivax'] ?? 0.00;
                         $debito_ivax_l_13 = $datos['tipo_impuesto'][8][2]['monto_ivax'] ?? 0.00;
                          $credito_ivax_l_13 = $datos['tipo_impuesto'][8][3]['monto_ivax'] ?? 0.00;
                                             
                        $total_neto_linea_13 = (($factura_neto_l_13 + $tiquete_neto_l_13 + $debito_neto_l_13) - $credito_neto_l_13);
                        $total_iva_linea_13 = (( $factura_iva_l_13 + $tiquete_iva_l_13 + $debito_iva_l_13 ) - $credito_iva_l_13);
                         $total_ivax_linea_13 = (( $factura_ivax_l_13 + $tiquete_ivax_l_13 + $debito_ivax_l_13 ) - $credito_ivax_l_13);
                    ?>
                    <td align="right">{{ $total_neto_linea_13  ?? 0.00 }}</td>
                    <td align="right">{{ $total_iva_linea_13  ?? 0.00 }}</td>
                </tr>
                
                <tr>
                    <td>No sujeto (0%)</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][1]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][1]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][2]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][2]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][3]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][3]['monto_iva']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][4]['monto_neto']  ?? 0.00 }}</td>
                    <td align="right">{{ $datos['tipo_impuesto'][9][4]['monto_iva']  ?? 0.00 }}</td>
                    <?php 
                        $factura_neto_l_0s = $datos['tipo_impuesto'][9][1]['monto_neto'] ?? 0.00;
                        $factura_iva_l_0s = $datos['tipo_impuesto'][9][1]['monto_iva'] ?? 0.00;
                        $tiquete_neto_l_0s = $datos['tipo_impuesto'][9][4]['monto_neto'] ?? 0.00;
                        $tiquete_iva_l_0s = $datos['tipo_impuesto'][9][4]['monto_iva'] ?? 0.00;
                        $debito_neto_l_0s = $datos['tipo_impuesto'][9][2]['monto_neto'] ?? 0.00;
                        $debito_iva_l_0s = $datos['tipo_impuesto'][9][2]['monto_iva'] ?? 0.00;
                        $credito_neto_l_0s = $datos['tipo_impuesto'][9][3]['monto_neto'] ?? 0.00;
                        $credito_iva_l_0s = $datos['tipo_impuesto'][9][3]['monto_iva'] ?? 0.00;
                        
                          $factura_ivax_l_0s = $datos['tipo_impuesto'][9][1]['monto_ivax'] ?? 0.00;
                           $tiquete_ivax_l_0s = $datos['tipo_impuesto'][9][4]['monto_ivax'] ?? 0.00;
                           $debito_ivax_l_0s = $datos['tipo_impuesto'][9][2]['monto_ivax'] ?? 0.00;
                            $credito_ivax_l_0s = $datos['tipo_impuesto'][9][3]['monto_ivax'] ?? 0.00;
                           
                        $total_neto_linea_0s = (($factura_neto_l_0s + $tiquete_neto_l_0s + $debito_neto_l_0s) - $credito_neto_l_0s);
                        $total_iva_linea_0s = (( $factura_iva_l_0s + $tiquete_iva_l_0s + $debito_iva_l_0s ) - $credito_iva_l_0s);
                         $total_ivax_linea_0s = (( $factura_ivax_l_0s + $tiquete_ivax_l_0s + $debito_ivax_l_0s ) - $credito_ivax_l_0s);
                    ?>
                    <td align="right">{{ $total_neto_linea_0s ?? 0.00 }}</td>
                    <td align="right">{{ $total_iva_linea_0s  ?? 0.00 }}</td>
                </tr>
        </tbody>        
         
    </table>-->
    <?php
   // dd($valorr[0]->idconfigfact);
   if ($codigo_actividad == 'TODAS') { 
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
        ['sales.fecha_creada', '>=', $fecha_inicio],  
        ['sales.fecha_creada', '<=', $fecha_fin],  
        ['sales.idconfigfact', '=', $valorr[0]->idconfigfact],  
        ['facelectron.estatushacienda', '=', 'aceptado'],  
        ['sales.estatus_sale', '=', 2], 
        ['sales.tipo_doc_ref' ,'!=', 17], 
         
    ])  
    ->whereIn('sales.tipo_documento', ['01', '02', '03', '04', '09'])  
    ->get();  

} else {  
 
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
        ['sales.fecha_creada', '>=', $fecha_inicio],  
        ['sales.fecha_creada', '<=', $fecha_fin],  
        ['sales.idconfigfact', '=', $valorr[0]->idconfigfact],
        ['sales.idcodigoactv', '=', $codigo_actividad],
        ['facelectron.estatushacienda', '=', 'aceptado'],  
        ['sales.estatus_sale', '=', 2],  
         ['sales.tipo_doc_ref' ,'!=', 17],
    ])  
    ->whereIn('sales.tipo_documento', ['01', '02', '03', '04', '09'])  
    ->get();  
}  


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
           if ($item->tipo_documento == '03') {    
            
            $total_exoneraciones_neto -= $item->valor_neto;   
        }else{
           $total_exoneraciones_neto += $item->valor_neto; 
        }  
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

?>
</div>
<?php 
//$total_mto_final = number_format(($totalmto[1] + $totalmto[2] + $totalmto[3] + $totalmto[4] + $totalmto[5] + $totalmto[6] + $totalmto[7] + $totalmto[8]),2,',','.');
$total_mto_final = ($total_neto_linea_0 + $total_neto_linea_1 + $total_neto_linea_2 + $total_neto_linea_3  + $total_neto_linea_5 + $total_neto_linea_6 + $total_neto_linea_7+$total_neto_linea_13+$total_neto_linea_0s);
//$total_iva_final = number_format(($totaliva[1] + $totaliva[2] + $totaliva[3] + $totaliva[4] + $totaliva[5] + $totaliva[6] + $totaliva[7] + $totaliva[8]),2,',','.');
$total_iva_final = number_format(($total_iva_linea_0 + $total_iva_linea_1 + $total_iva_linea_2 + $total_iva_linea_3  + $total_iva_linea_5 + $total_iva_linea_6 + $total_iva_linea_7+$total_iva_linea_13+$total_iva_linea_0s),2,',','.');

$iva_exonerado= ($total_ivax_linea_0 + $total_ivax_linea_1 + $total_ivax_linea_2 + $total_ivax_linea_3  + $total_ivax_linea_5 + $total_ivax_linea_6 + $total_ivax_linea_7+$total_ivax_linea_13+$total_ivax_linea_0s);

//Servicios profesionales variables
$s_profesional_mto = $datos_receptor['clasifica_d151'][1]['total_comprobante'] ?? 0.00;
$s_profesional_imp = $datos_receptor['clasifica_d151'][1]['total_impuesto'] ?? 0.00;
$s_profesional_himp = $datos_receptor['clasifica_d151'][1]['hacienda_imp_creditar'] ?? 0.00;
$s_profesional_gsto = $datos_receptor['clasifica_d151'][1]['hacienda_gasto_aplica'] ?? 0.00;
// Alquileres
$alquileres_mto = $datos_receptor['clasifica_d151'][3]['total_comprobante'] ?? 0.00;
$alquileres_impsto = $datos_receptor['clasifica_d151'][3]['total_impuesto'] ?? 0.00;
$alquileres_hcnda_imp = $datos_receptor['clasifica_d151'][3]['hacienda_imp_creditar'] ?? 0.00;
$alquileres_hcnda_gasto = $datos_receptor['clasifica_d151'][3]['hacienda_gasto_aplica'] ?? 0.00;
// Comisiones
$comisiones_mto = $datos_receptor['clasifica_d151'][5]['total_comprobante'] ?? 0.00;
$comisiones_imp = $datos_receptor['clasifica_d151'][5]['total_impuesto'] ?? 0.00;
$comisiones_hcnda_imp = $datos_receptor['clasifica_d151'][5]['hacienda_imp_creditar'] ?? 0.00;
$comisiones_hcnda_gst = $datos_receptor['clasifica_d151'][5]['hacienda_gasto_aplica'] ?? 0.00;
// Gastos Generales
$gg_mto = $datos_receptor['clasifica_d151'][2]['total_comprobante'] ?? 0.00;
$gg_imp = $datos_receptor['clasifica_d151'][2]['total_impuesto'] ?? 0.00;
$gg_hcnda_imp = $datos_receptor['clasifica_d151'][2]['hacienda_imp_creditar'] ?? 0.00;
$gg_hcnda_gst = $datos_receptor['clasifica_d151'][2]['hacienda_gasto_aplica'] ?? 0.00;
// Intereses
$intereses_mto = $datos_receptor['clasifica_d151'][6]['total_comprobante'] ?? 0.00;
$intereses_imp = $datos_receptor['clasifica_d151'][6]['total_impuesto'] ?? 0.00;
$intereses_hcnda_imp = $datos_receptor['clasifica_d151'][6]['hacienda_imp_creditar'] ?? 0.00;
$intereses_hcnda_gst = $datos_receptor['clasifica_d151'][6]['hacienda_gasto_aplica'] ?? 0.00;
// Otros
$otros_mto = $datos_receptor['clasifica_d151'][7]['total_comprobante'] ?? 0.00;
$otros_imp = $datos_receptor['clasifica_d151'][7]['total_impuesto'] ?? 0.00;
$otros_hcnda_imp = $datos_receptor['clasifica_d151'][7]['hacienda_imp_creditar'] ?? 0.00;
$otros_hcnda_gst = $datos_receptor['clasifica_d151'][7]['hacienda_gasto_aplica'] ?? 0.00;
// factura electronica Compra neto y iva
$compra_neto = $fcompra['fcompra'][0]['monto_neto']  ?? 0.00;
$compra_iva = $fcompra['fcompra'][0]['monto_iva']  ?? 0.00;
$compra_hcnda_imp = $datos_receptor['clasifica_d151'][1]['hacienda_imp_creditar'] ?? 0.00;
$compra_hcnda_gst = $datos_receptor['clasifica_d151'][1]['hacienda_gasto_aplica'] ?? 0.00;

//totales para sumatorias y bruto
$t_iva_devengado = ($total_iva_linea_0 + $total_iva_linea_1 + $total_iva_linea_2 + $total_iva_linea_3  + $total_iva_linea_5 + $total_iva_linea_6 + $total_iva_linea_7+$total_iva_linea_13 + $total_iva_linea_0s) ;

$t_iva_bruto = ($total_iva_linea_0 + $total_iva_linea_1 + $total_iva_linea_2 + $total_iva_linea_3  + $total_iva_linea_5 + $total_iva_linea_6 + $total_iva_linea_7+$total_iva_linea_13 + $total_iva_linea_0s) - ($s_profesional_imp + $alquileres_impsto + $comisiones_imp + $gg_imp + $intereses_imp + $otros_imp + $compra_iva + $iva_recepcionado);

$t_iva_creditar = $s_profesional_himp + $alquileres_hcnda_imp + $comisiones_hcnda_imp + $gg_hcnda_imp + $intereses_hcnda_imp + $compra_hcnda_imp ;
$t_iva_gasto = $s_profesional_gsto + $alquileres_hcnda_gasto + $comisiones_hcnda_gst + $gg_hcnda_gst + $intereses_hcnda_gst + $otros_hcnda_gst +  $compra_hcnda_gst ;
$t_monto_t = ($total_neto_linea_0 + $total_neto_linea_1 + $total_neto_linea_2 + $total_neto_linea_3  + $total_neto_linea_5 + $total_neto_linea_6 + $total_neto_linea_7+$total_neto_linea_13 + $total_neto_linea_0s) - ($s_profesional_mto + $alquileres_mto + $comisiones_mto + $gg_mto + $intereses_mto + $otros_mto + $compra_neto +$total_receptor );
$iva_acreditable_doc = 0;
$iva_por_pagar = ($t_iva_devengado - $iva_recepcionado) ;
$utilidad = $t_monto_t - $t_iva_gasto;
?>

    <!-- <div class="table-responsive">
                    -  <table class="table" id="documentos_data">
                            <thead>
                                <tr>
                                    <th colspan="26" style="text-align: center;">
                                        <b>Reporte de Compras por Proveedor</b> <br>
                                        <b>Montos en Moneda Extrangera colonizados al Tipo de Cambio del XML</b>
                                    </th>
                                </tr>
                               
                                <tr>
                                    <th colspan="26" style="text-align: center;">
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
                                 
                                    if($recepciones['moneda'] === 'USD'){
                                        
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
                                    $no_sujeto +=  ($recepciones['no_sujeto']['monto'] + $recepciones['11']['monto'] + $recepciones['01']['monto']) * $recepciones['tc'] ;
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
                                    $no_sujeto += ($recepciones['no_sujeto']['monto'] + $recepciones['11']['monto'] + $recepciones['01']['monto']);
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
                                            <td>{{ $recepciones['proveedor'] .' ** '. $recepciones['moneda'].' ** TC ** '. $recepciones['tc']  }}</td>
                                             @if($recepciones['moneda'] === 'USD' )
                                             
                                             <td>{{ number_format( $recepciones['01']['monto'] * $recepciones['tc'] ,2,'.',',') }}</td>
                                           
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
                                            <td>{{ number_format($recepciones['no_sujeto']['monto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_neto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['subtotal_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['exonerado_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['iva_devuelto'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total_iva'] * $recepciones['tc'],2,'.',',')}}</td>
                                            <td>{{ number_format($recepciones['total'] * $recepciones['tc'],2,'.',',')}}</td>
                                            
                                            
                                             
                                             @else
                                             <td>{{ number_format( $recepciones['01']['monto'],2,'.',',')}}</td>
                                           
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
                                            <td>{{ number_format($recepciones['no_sujeto']['monto'],2,'.',',')}}</td>
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
                                        <td><b>{{ number_format($subtotal_neto ,2,'.',',')}}</b></td>
                                        <td><b>{{ number_format($subtotal_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($exonerado_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($iva_devuelto,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($total_iva,2,'.',',') }}</b></td>
                                        <td><b>{{ number_format($total,2,'.',',') }}</b></td>
                                         
                                         
                                        
                                    
                                    
                                    </tr>
                               
                                
                            </tbody>
                        </table>
                    </div>-->
                    
                     <div class="card ">
                         <div class="col-6">
                           
                        
                         <table  ALIGN="left" class="table" width="50%">
                              <thead class=" text-primary">
                                  <tr>
                <th colspan="4" align="center" style="font-weight: bold;">Resumen Compras Del Periodo</th>
               
            </tr>
                                   <tr>
                                <th colspan="2" scope="col">Tipo Impuesto</th>
                                
                                <th scope="col">Base Imponible</th>
                                <th scope="col">IVA</th>
                                <th scope="col">Total</th>
                                 </tr>
                            </thead>
                                 <tfoot> 
                                <tr>
                                     <th colspan="2" style="color:black;">Excento</th>
                                    <th style="color:black;">{{number_format( $excento +$m5 ,2,'.',',')}}</th>
                                     <th style="color:black;">{{number_format( (0) ,2,'.',',')}}</th>
                                      <th style="color:black;">{{number_format( (0) ,2,'.',',')}}</th>
                                    </tr>
                                    <tr>
                                     <th colspan="2" style="color:black;">No Sujeto</th>
                                    <th style="color:black;">{{number_format( $no_sujeto ,2,'.',',')}}</th>
                                     <th style="color:black;">{{number_format( (0) ,2,'.',',')}}</th>
                                      <th style="color:black;">{{number_format( (0) ,2,'.',',')}}</th>
                                    </tr>
                                    
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 0.5%</th>
                                    <th style="color:black;">{{ number_format(($m05) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( ($t05) ,2,'.',',') }}</th>
                                      <th style="color:black;">{{ number_format( ($m05+$t05 ) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 1%</th>
                                    <th style="color:black;">{{ number_format(($m2) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format(($t2) ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format(($t2+$m2) ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 2%</th>
                                    <th style="color:black;">{{ number_format(($m3) ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format($t3 ,2,'.',',') }}</th>
                                       <th style="color:black;">{{ number_format($m3+$t3 ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">IVA Reducido al 4%</th>
                                    <th style="color:black;">{{ number_format($m4 +$m6 ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format($t4+$t6 ,2,'.',',') }}</th>
                                     <th style="color:black;">{{ number_format( $m4+$t4+$t6+$m6 ,2,'.',',') }}</th>
                                    </tr>
                                
                                
                                <tr>
                                      <th colspan="2"  style="color:black;">IVA Reducido al 8%</th>
                                    <th style="color:black;">{{ number_format($m7  ,2,'.',',')}}</th>
                                    <th style="color:black;">{{ number_format( $t7 ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( $m7+$t7 ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                      <th colspan="2"  style="color:black;">IVA Reducido al 13%</th>
                                    <th style="color:black;">{{ number_format($m13 ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( $t13 ,2,'.',',') }}</th>
                                    <th style="color:black;">{{ number_format( $m13+$t13 ,2,'.',',') }}</th>
                                    </tr>
                               
                                <tr>
                                    <th colspan="2"  style="color:black;">Total IVA</th>
                                    <th style="color:black;">{{ number_format($total_iva ,2,'.',',') }}</th>
                                    </tr>
                                
                                <tr>
                                     <th colspan="2"  style="color:black;">Total IVA Devuelto</th>
                                    <th style="color:black;">{{ number_format($iva_devuelto ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total Compra Exonerada DGH</th>
                                    <th style="color:black;">{{ number_format($exonerado_iva ,2,'.',',') }}</th>
                                    </tr>
                                    <tr>
                                     <th colspan="2"  style="color:black;">Total Compras Sin IVA</th>
                                    <th style="color:black;">{{ number_format($total-$total_iva  ,2,'.',',') }}</th>
                                    </tr>
                                <tr>
                                     <th colspan="2"  style="color:black;">Total Compras</th>
                                    <th style="color:black;">{{ number_format($total  ,2,'.',',') }}</th>
                                    </tr>
                           </tfoot>
                        </table>
                  

<!--<div class="invoice">
    <table class="table" width="100%">
        <thead>
            <tr>
                <th align="center">Rubro</th>
                <th align="center">Monto</th>
                <th align="center">IVA Devengado</th>
                <th align="center">IVA a Acreditar</th>
                <th align="center">IVA al Gasto</th>
            </tr>
            <tr>
                <th align="center">IVA Devengado</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">Ventas</td>
                <td align="right">{{ $total_mto_final  ?? 0.00 }}</td>
                <td align="right">{{ $total_iva_final  ?? 0.00 }}</td>
                <td align="right">0.00</td>
                <td align="right">0.00</td>
            </tr>
            
         <!--   <tr>
                <td align="center" style="font-weight: bold;">Credito Directo</td>
            </tr>
            <tr>
                <td>Compra de Mercaderias</td>
                <td align="right">{{ number_format($total_receptor,2,',','.')  ?? 0.00 }}</td>
                <td align="right">{{ number_format($iva_recepcionado,2,',','.') ?? 0.00 }}</td>
                <td align="right">{{ number_format(($total_receptor-$iva_recepcionado),2,',','.') }}</td>
                <td align="right">{{ number_format($compra_hcnda_gst,2,',','.') }}</td>
            </tr>
           
                
        </tbody>
    </table>-->
 
    <table ALIGN="right" class="table" width="50%">
        <thead>
            <tr>
                <th colspan="8" align="center" style="font-weight: bold;">Resumen Del Periodo</th>
                <th colspan="4"></th>
            </tr>
        </thead>
        <tbody>
           
            <tr>
                <td align="center" colspan="8">Ventas</td>
                <td colspan="4">{{number_format( $total_general_neto,2,',','.')  ?? 0.00  }}</td>
            </tr>
            <tr>
                <td align="center" colspan="8">(+) IVA Devengado</td>
                <td colspan="4">{{ number_format($total_general_impuesto,2,',','.')  ?? 0.00  }}</td>
            </tr>
          
            <tr>
                <td align="center" colspan="8">(-) IVA Devuelto</td>
                <td colspan="4">{{ number_format($total_iva_devuelto,2,',','.')  ?? 0.00  }}</td>
            </tr>
            <tr>
            <td align="center" colspan="8">(-) IVA Exonerado</td>
            <td colspan="4">{{ number_format($iva_exonerado,2,',','.') ?? 0.00  }}</td>
            </tr>
            
              <tr>
                <td align="center" colspan="8">(-) IVA Acreditable</td>
                <td colspan="4">{{ number_format($total_iva,2,',','.')  ?? 0.00  }}</td>
            </tr>
            
            <tr>
                <td align="center" colspan="8">(+) IVA Acreditable No Doc Electronico</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td align="center" style="font-weight: bold;" colspan="8">(=) IVA por Pagar</td>
                <td colspan="4">{{ number_format($total_general_impuesto-$total_iva_devuelto-$iva_exonerado-$total_iva,2,',','.')  ?? 0.00 }}</td>
            </tr>
            <tr>
                <td align="center" style="font-weight: bold;" colspan="8">(-) IVA Gasto</td>
                <td colspan="4">{{ number_format($t_iva_gasto,2,',','.')  ?? 0.00  }}</td>
            </tr>
            <tr>
                <td align="center" style="font-weight: bold;" colspan="8">(=) Utilidad del Periodo</td>
                <td colspan="4">{{ number_format($total_general_neto - $total + $total_iva,2,',','.')     }}</td>
            </tr>
        </tbody>
    </table>
    
  </div>
</div>
<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                &copy; {{ date('Y') }} - Producto registrado por Oscar Mairena Vargas & Servicios Contables San Silvestre. Servicios Contables San Silvestre Todos los derechos reservados.
            </td>
            <td align="right" style="width: 50%;">
                ESTE REPORTE CONSTITUYE UN BORRADOR DE LOS IVA, CORROBORE CON SU CONTADOR
            </td>
        </tr>

    </table>
</div>
</body>
</html>