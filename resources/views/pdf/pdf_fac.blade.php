<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de IVA</title>

    <style type="text/css">
        @page {
            margin:50px;
        }
        body {
            margin: 5px;
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
            margin: 10px;
        }
        .invoice h3 {
            margin-left: 10px;
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

</head>
<body>
<div class="information">
    <table width="100%">
        <thead>
            <tr>
                 <td align="center">
                <!-- modificado omairena 12-05-2021 3:53 <img src="./black/img/logo.JPG" alt="Logo" width="85" class="logo"/>-->
                 <img src="./black/img/{{ $query[0]->logo }}" alt="Logo" width="85" class="logo"/>
            </td>
                <th colspan="8" align="center" style="width: 100%;">
                    <h3>Reporte de Documentos Emitidos</h3>
                       <h3>{{ $query[0]->nombre_emisor }} Identificacion: {{ $query[0]->numero_id_emisor }}</h3>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">Fecha Inicio</td>
                <td align="center">{{ $datoso['fecha_desde'] }}</td>
                <td align="center">Fecha Final</td>
                <td align="center">{{ $datoso['fecha_hasta'] }}</td>
                <td align="center">% Proporcionalidad</td>
                <td align="center">{{ $query[0]->factor_receptor }}</td>
                <td align="center">Act Economica</td>
                <td align="center">{{ $query[0]->codigo_actividad }}</td>
            </tr>
        </tbody>
    </table>
  
</div>
<div class="invoice">
    <table class="table" width="100%">
        <thead>
            <tr>
                <th ></th>
               <th ></th>
                <th ></th>
               <th ></th>
                <th ></th>
            </tr>
            <tr>
               <th scope="col">{{ __('# Documento') }}</th>
                 <th scope="col">{{ __('Tipo Documento') }}</th>
                                <th scope="col">{{ __('Fecha Creaci贸n') }}</th>
                                <th scope="col">{{ __('Nombre Cliente') }}</th>
                                <th scope="col">{{ __('Condici贸n Venta') }}</th>
                                <th scope="col">{{ __('Tipo de Moneda') }}</th>
                                <th scope="col">{{ __('Total Neto') }}</th>
                                <th scope="col">{{ __('Total Descuento') }}</th>
                                <th scope="col">{{ __('Total Impuesto') }}</th>
                                 <th scope="col">{{ __('Total Otros Cargos ') }}</th>
                                <th scope="col">{{ __('Total Documento') }}</th>
                                <th scope="col">{{ __('Estado Hacienda') }}</th>
                                <th scope="col"></th>
            </tr>
            
        </thead>
            <tbody>
                                @foreach ($salest as $sale)
                                 	<?php
                                 		$usuario = App\Cliente::find($sale->idcliente);
                                        if ($sale->condicion_venta === '01') {
                                        	$condicion = 'Contado';
                                        }else{
                                        	$condicion = 'Credito';
                                        }
                                        $valor = App\Facelectron::where('idsales', $sale->idsale)->get();
                                        if (count($valor)) {
                                            if(is_null($valor[0]->estatushacienda)){
                                                $estatus = "<button  type='button' class='btn btn-sm btn-warning'>Sin Consultar</button>";
                                            }else{
                                                switch ($valor[0]->estatushacienda) {
                                                    case 'aceptado':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-success'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'recibido':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-success'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'procesando':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-warning'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'rechazado':
                                                        $estatus ="<button  type='button' class='btn btn-sm btn-danger'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                    case 'pendiente':
                                                          $estatus ="<button  type='button' class='btn btn-sm btn-warning'>".$valor[0]->estatushacienda."</button>";
                                                    break;
                                                }

                                            }
                                        }else{
                                            $estatus ="<button  type='button' class='btn btn-sm btn-warning'>En Proceso</button>";
                                        }

                                      $total_impuesto =  number_format($sale->total_impuesto, 2, '.', '')
                                    ?>
                                    <tr>
                                    	<td>{{ $sale->numero_documento }}</td>
                                    	
                                    	  <td>
                                            @switch($sale->tipo_documento)
                                                @case('01')
                                                    Factura 
                                                @break
                                                @case('02')
                                                    Nota Débito 
                                                @break
                                                @case('03')
                                                   Nota Credito
                                                @break
                                                @case('04')
                                                    Tiquete
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
                                        <td>{{ $sale->fecha_creada }}</td>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $condicion }}</td>
                                        <td>{{ $sale->tipo_moneda }}</td>
                                        <td class="text-right">{{ number_format($sale->total_neto, 2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($sale->total_descuento,  2, '.', ',') }}</td>
                                        <td class="text-right" >{{ number_format($sale->total_impuesto,  2, '.', ',') }}</td>
                                         <td class="text-right" >{{ number_format($sale->total_otros_cargos,  2, '.', ',') }}</td>
                                        <td class="text-right" >{{ number_format($sale->total_comprobante,  2, '.', ',') }}</td>
                                        <td><?php echo $estatus; ?></td>
                                       
                                    </tr>
                                @endforeach
                            </tbody>  
            
                
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
                        $total_ivax_linea_1 = (( $factura_iva_l_1 + $tiquete_iva_l_1 + $debito_iva_l_1) - $credito_iva_l_1);
                    ?>
                   
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
                    
    </table>
</div>
<?php 
//$total_mto_final = number_format(($totalmto[1] + $totalmto[2] + $totalmto[3] + $totalmto[4] + $totalmto[5] + $totalmto[6] + $totalmto[7] + $totalmto[8]),2,',','.');
$total_mto_final = number_format(($total_neto_linea_0 + $total_neto_linea_1 + $total_neto_linea_2 + $total_neto_linea_3  + $total_neto_linea_5 + $total_neto_linea_6 + $total_neto_linea_7+$total_neto_linea_13+$total_neto_linea_0s),2,',','.');
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
 <h3>Total Ingreso: {{ $total_mto_final  ?? 0.00 }} Total IVA: {{ $total_iva_final  ?? 0.00 }} </h3>

<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
          <b>&copy; {{ date('Y') }} - Producto registrado por Oscar Mairena Vargas & Servicios Contables San Silvestre. Servicios Contables San Silvestre Todos los derechos reservados.</b>      
            </td>
            <td align="right" style="width: 50%;">
              <b>Contacto: 8309-3816  www.snesteban.com   omairena@fesanesteban.com</b> 
            </td>
        </tr>

    </table>
</div>
</body>
</html>