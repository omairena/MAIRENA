<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura - # {{ $sales->numero_documento }}</title>

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
            margin: 15px;
        }
        .invoice h3 {
            margin-left: 15px;
        }
        .information {
            background-color: #60A7A6;
            color: #FFF;
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
            <td align="left" style="width: 40%;">
                <h3>{{ $cliente->nombre }}</h3>
                <pre>
Identificación: {{ $cliente->num_id }}
Correo: {{ $cliente->email }}
Teléfono: {{ $cliente->telefono }}
Dirrección: {{ $direccion_receptor }}
<br/><br/>
Fecha: {{ $sales->fecha_creada }}
Condición Venta: {{ $condicion }}
Tipo de Pago: {{ $medio_pago }}
Moneda de Pago: {{ $moneda }}
Tipo de Cambio: {{ $tipo_cambio }}
				</pre>
            </td>
            <td align="center">
               <img src="./black/img/{{ $configuracion->logo }}" alt="Logo" width="85" class="logo"/>
            </td>
            <td align="right" style="width: 40%;">

                <h3>{{ $configuracion->nombre_empresa }}</h3>
                <pre>
                    {{ $configuracion->nombre_emisor }}
                    Identificación: {{ $configuracion->numero_id_emisor }}
                    Correo: {{ $configuracion->email_emisor }}
                    Teléfono: {{ $configuracion->telefono_emisor }}
                    Dirrección: {{ $direccion_emisor }}
                </pre>
            </td>
        </tr>

    </table>
</div>


<br/>

<div class="invoice">
    
     @switch($sales->tipo_documento)
        @case(96)
                   @if( $configuracion->usa_op == 1)
                      <h3>Orden de Pedido # {{ $sales->numero_documento }}</h3>
             @endif
                       @if( $configuracion->usa_op == 0)
                       <h3>Factura Regimen Simplificado # {{ $sales->numero_documento }}</h3>
             @endif
             
        
          
        @break
        @case(95)
            <h3>Nota de Credito  # {{ $sales->numero_documento }}</h3>
        @break
        
    @endswitch
    <table class="table" width="100%" style="padding: 20px;">
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
      		@endforeach
        </tbody>
    </table>
</div>
<div class="information" style="position: absolute; bottom: 40;">
	<table width="100%">
        <tr>
        	<td colspan="2">
        	</td>
            <td align="right" style="font-size: 15px;">Observaciones:</td>
            <td align="right" style="font-size: 15px;" colspan="5">{{ $sales->observaciones }}</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 25px;">Total Neto</td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format($sales->total_neto,2,',','.') }}</td>
        </tr>
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 25px;">Total Descuento</td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format($sales->total_descuento,2,',','.') }}</td>
       	 </tr>
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 25px;">Total Impuesto</td>
            <td align="right" class="gray" style="font-size: 25px;">{{ number_format($sales->total_impuesto,2,',','.') }}</td>
        </tr>
        <tr>
        	<td colspan="4"></td>
            <td align="right" style="font-size: 25px;">Total Comprobante</td>
            <td align="right" class="gray" style="font-size: 25px;">{{  number_format($sales->total_comprobante,2,',','.') }}</td>
        </tr>
    </table>
</div>
<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                &copy; {{ date('Y') }} - Factura ELectrónica San Esteban. www.snesteban.com Todos los derechos reservados.
            </td>
            @if( $configuracion->usa_op == 0)
                      
            <td align="right" style="width: 50%;">
                Autorizado mediante la resolucion DGT-R-033-2019 del 27 de junio del 2019 Version 4.3
            </td>
            
             @endif
        </tr>

    </table>
</div>
</body>
</html>