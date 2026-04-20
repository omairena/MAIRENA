<!doctype html>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura - # {{ $facelectron->clave }}</title>

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
            color: #E7F7FE;
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
            margin: 15px;
        }
        .invoice h3 {
            margin-left: 15px;
        }
        .information {
            background-color: #E7F7FE;
            color: #000000;
        }
        .information .logo {
            margin: 5px;
        }
        .information table {
            padding: 10px;
        }
    </style>

</head>
<body>

<div class="information">
    <table width="100%">
        <tr>
            <td align="left">
                <!-- modificado omairena 12-05-2021 3:53 <img src="./black/img/logo.JPG" alt="Logo" width="85" class="logo"/>-->
                 <img src="./black/img/{{ $configuracion->logo }}" alt="Logo" width="110" class="logo"/>
           
                
             <h3>Obligado Tributario.</h3>
                <h3>{{ $configuracion->nombre_empresa }}</h3>
                <pre>
            {{ $configuracion->nombre_emisor }}
            Identificación: {{ $configuracion->numero_id_emisor }}
            Correo: {{ $configuracion->email_emisor }}
            Teléfono: {{ $configuracion->telefono_emisor }}
            Dirrección: {{ $direccion_emisor }}
                </pre>
            </td>
            
            <td align="left" style="width: 50%;">
                 <h3>Cliente:</h3>
                <h3>{{ $cliente->nombre }}</h3>
                <pre>
Identificación: {{ $cliente->num_id }}
Correo: {{ $cliente->email }}
Teléfono: {{ $cliente->telefono }}
Dirrección: {{ $direccion_receptor }}
Fecha: {{ $fecha_hora }}
Condición Venta: {{ $condicion }}
Plazo Credito: {{ $sales->p_credito }}
Moneda de Pago: {{ $moneda }}
Tipo de Cambio: {{ $tipo_cambio }}



				</pre>
            </td>
            
            
        </tr>

    </table>
</div>
<div class="invoice">
    @switch($facelectron->tipodoc)
        @case(01)
            <h3>Factura Electronica # {{ $facelectron->consecutivo }}</h3>
        @break
        @case(02)
            <h3>Nota de Débito # {{ $facelectron->consecutivo }}</h3>
        @break
        @case(03)
            <h3>Nota de Crédito # {{ $facelectron->consecutivo }}</h3>
        @break
        @case(04)
            <h3>Tiquete Electronico # {{ $facelectron->consecutivo }}</h3>
        @break
        @case('08')
            <h3>Factura Electronica de Compra # {{ $facelectron->consecutivo }}</h3>
        @break
        @case('09')
            <h3>Factura de Exportacion # {{ $facelectron->consecutivo }}</h3>
        @break
    @endswitch
    <table class="table" width="100%" style="padding: 10px;">
        <thead>
        <tr>
            <th style="text-align: left;">CODIGO</th>
            <th style="text-align: left;">NOMBRE</th>
            <th style="text-align: right;">CANTIDAD</th>
             <th style="text-align: right;">UNITARIO</th>
            <th style="text-align: right;">NETO</th>
            <th style="text-align: right;">DESCUENTO</th>
            <th style="text-align: right;">IMPUESTO</th>
        </tr>
        </thead>
        <tbody >
      		@foreach($sales_item as $sale_i)
      			<tr>
      				<td style="text-align: left;">{{ $sale_i->codigo_producto }}</td>
      				<td style="text-align: left;">{{ $sale_i->nombre_producto }}</td>
      				<td style="text-align: right;">{{ $sale_i->cantidad }} {{ $sale_i->prod_sale[0]->productos_unidad[0]->simbolo }}</td>
                    <td style="text-align: right;">{{ number_format(($sale_i->valor_neto/$sale_i->cantidad),2,',','.') }}</td>
      				<td style="text-align: right;">{{ number_format($sale_i->valor_neto,2,',','.') }}</td>
      				<td style="text-align: right;">{{ number_format($sale_i->valor_descuento,2,',','.') }}</td>
      				<td style="text-align: right;">{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
      			    </tr>
                     
                       @if(!empty($sale_i->exo) )
                      <tr>
                       <td style="text-align: left;">--></td>
                       <td style="text-align: left;">Numero Exoneracion -></td>
                       <td style="text-align: left;">{{ $sale_i->exo }}</td>
                       <td style="text-align: left;">Fecha Exoneracion -> -></td>
                       <td style="text-align: left;">{{ $sale_i->fechaex }}</td>
                      </tr>
                       @endif
      		@endforeach
			 
			  </tbody>
			  </thead>
    </table>
	 @if($sales->total_otros_cargos > '0' )
			   <h5>Otros Cargos</h5>
	 <table class="table" table-layout: fixed; width="100%" style="padding: 20px;">
        <thead>
       
			 
        <tr>
            <th style="text-align: left;">Tipo Doc</th>
            <th style="text-align: left;">Identificacion/Nombre</th>
                        <th style="text-align: left;">Detalle</th>
             <th style="text-align: right;">Porcentaje</th>
            <th style="text-align: right;">Monto</th>
            
        </tr>
        </thead>
              @foreach($sales_item_otrocargo as $sale_i)
      			<tr>
      				<td style="text-align: left;">{{ $sale_i->tipo_otrocargo}}</td>
      			
      				<td style="text-align: left;">{{ $sale_i->numero_identificacion }} -> {{ $sale_i->nombre }}</td>
                       <td style="word-wrap:break-word;">    {{ $sale_i->detalle }}</td>
                               
      				<td style="text-align: right;">{{ number_format($sale_i->porcentaje_cargo,2,',','.') }}</td>
      				<td style="text-align: right;">{{ number_format($sale_i->monto_cargo,2,',','.') }}</td>
      				
      			</tr>
      		@endforeach
			    @endif
        </tbody>
    </table>
</div>

	<table width="100%">
        
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong>Total Neto</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format($sales->total_neto,2,',','.') }}</td>
        </tr>
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong>Total Descuento</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format($sales->total_descuento,2,',','.') }}</td>
       	 </tr>
             @if($sales->total_iva_devuelto > '0' or $sales->total_IVA_ex > '0' )
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong>Total Impuesto Neto</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format(($sales->total_impuesto + $sales->total_IVA_ex),2,',','.') }}</td>
        </tr>
          @endif
         @if($sales->total_iva_devuelto > '0' )
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong> - IVA Devuelto</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{  number_format($sales->total_iva_devuelto,2,',','.') }}</td>
        </tr>
        @endif
         @if($sales->total_IVA_ex > '0' )
         <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong> - IVA Exonerado</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{  number_format($sales->total_IVA_ex,2,',','.') }}</td>
        </tr>
           @endif

            <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong>Total Impuesto</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format(($sales->total_impuesto + $sales->total_IVA_ex-$sales->total_IVA_ex-$sales->total_iva_devuelto),2,',','.') }}</td>
        </tr>
 @if($sales->total_otros_cargos > '0' )
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong> + Otros Cargos</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{  number_format($sales->total_otros_cargos,2,',','.') }}</td>
        </tr>
        @endif
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 15px;"><strong>Total Venta</strong></td>
            <td align="right" class="gray" style="font-size: 25px;">{{  number_format($sales->total_comprobante,2,',','.') }}</td>
        </tr>
         
        	 <tr> 
            <td align="right" style="font-size: 15px;">Observaciones:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $sales->observaciones }}</td>
        </tr>
        <tr>
            <td align="right" style="font-size: 15px;">Clave:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $facelectron->clave }}	</td>
            <td colspan="2"></td>
        </tr>
         @if($sales->referencia_sale > '0' )
        <tr>
            <td align="right" style="font-size: 15px;">Doc Referencia Anulado con NC:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $consulta_fac->clave }}	</td>
            <td colspan="2"></td>
        </tr>
         @endif
        @if($sales->ref_clave_sale > '0' )
        <tr>
            <td align="right" style="font-size: 15px;">Doc Referencia:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $sales->ref_clave_sale }}	</td>
            <td colspan="2"></td>
        </tr>
        @endif
       @if($sales->clave_sale > '0' )
        <tr>
            <td align="right" style="font-size: 15px;">Doc Referencia Anulado con esta NC:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $sales->clave_sale }}	</td>
            <td colspan="2"></td>
        </tr>
        @endif
        <tr>
             <td align="right" style="font-size: 15px;">Codigo QR:</td>
        	<td colspan="2">
       <img src="data:image/png;base64, {{ base64_encode($url) }}"> 
      
   
        	</td>
        	 </tr> 
    </table> 
<table style="width: auto; border-collapse: collapse; border: 1px solid #ccc; margin-left: 20;">  
    <thead>  
        <tr>  
            <th colspan="3" style="border: 1px solid #ccc; background-color: #f4f4f4; text-align: center;">MEDIOS DE PAGOS</th>  
        </tr>  
        <tr>  
            <th style="border: 1px solid #ccc; background-color: #eaeaea;">Tipo Pago</th>  
            <th style="border: 1px solid #ccc; background-color: #eaeaea;">Monto Pago</th>  
            <th style="border: 1px solid #ccc; background-color: #eaeaea;">Ref Pago</th>  
        </tr>  
    </thead>  
    <tbody>  
   	<?php 
   
        $medio_pago = $sales->medioPagos()->get();
      
      
      
       if ($medio_pago->isNotEmpty()) {
    // hay al menos un elemento
    if ($medio_pago->count() > 1) {
      $medio_pago = $sales->medioPagos()->get();
    } else {
       switch ($sales->medio_pago) {
            case '01':
              $medio_pago = 'Efectivo';
              break;
              case '02':
              $medio_pago = 'Tarjeta';
              break;
              case '03':
              $medio_pago = 'Cheque';
              break;
              case '04':
              $medio_pago = 'Transferencia – depósito bancario';
              break;
              case '05':
               $medio_pago = 'Recaudado por terceros';
              break;
              case '06':
                $medio_pago = 'Sinpe Movil';
               break;
               case '07':
                $medio_pago = 'Plataforma Digital';
               break;
          }  
    }
} else {
   switch ($sales->medio_pago) {
            case '01':
              $medio_pago = 'Efectivo';
              break;
              case '02':
              $medio_pago = 'Tarjeta';
              break;
              case '03':
              $medio_pago = 'Cheque';
              break;
              case '04':
              $medio_pago = 'Transferencia – depósito bancario';
              break;
              case '05':
               $medio_pago = 'Recaudado por terceros';
              break;
              case '06':
                $medio_pago = 'Sinpe Movil';
               break;
               case '07':
                $medio_pago = 'Plataforma Digital';
               break;
          }  
}
        
       
        	 ?>
        @if(is_iterable($medio_pago))  
            @foreach($medio_pago as $pago)  
                <tr>  
                    <td style="border: 1px solid #ccc;">{{ $pago->nombre }}</td>  
                    <td style="border: 1px solid #ccc;">{{ $pago->pivot->monto }}</td>  
                    <td style="border: 1px solid #ccc;">  
                        @if(!is_null($pago->pivot->referencia))  
                            {{ $pago->pivot->referencia }}  
                        @else  
                            N/A  
                        @endif  
                    </td>  
                </tr>  
            @endforeach  
        @else  
            <tr>  
                <td style="border: 1px solid #ccc;">{{ $medio_pago }}</td>  
                <td style="border: 1px solid #ccc;">{{$sales->total_comprobante}}</td>  
                <td style="border: 1px solid #ccc;">{{ $sales->referencia_pago }}</td>  
            </tr>  
        @endif   
        <?php 
         //dd($medio_pago);
         	 ?>
    </tbody>  
</table>  
<div class="information" style="position: absolute; bottom: 1;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                &copy; {{ date('Y') }} - Factura Electrónica San Esteban. www.snesteban.com. Todos los derechos reservados. Tel: 83093816
            </td>
            <td align="right" style="width: 50%;">
                Autorizado mediante la resolucion MH-RES-0027-2024 del 13 de noviembre de 2024 Version XML 4.4
            </td>
        </tr>

    </table>
</div>
</body>
</html