<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pedido - # {{ $pedido->numero_documento }}</title>

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
            background-color: #7d8b8a;
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
         <h3 align="center">Cotización de Productos y Servicios.</h3>
            <td align="left" style="width: 40%;">
           
                <h3>Cliente: {{ $cliente->nombre }}</h3>
                <pre>
Identificación: {{ $cliente->num_id }}
Correo: {{ $cliente->email }}
Teléfono: {{ $cliente->telefono }}
Dirrección: {{ $cliente->direccion }}
<br/><br/>
Fecha: {{ $pedido->fecha_doc }}
                </pre>
            </td>
            <td align="center">
               
                <img src="./black/img/{{ $configuracion->logo }}" alt="Logo" width="150" class="logo"/>
            </td>
            
        </tr>

    </table>
</div>


<br/>

<div class="invoice">
    <table class="table" width="100%" style="padding: 20px;">
        <thead>
        <tr>
            <th style="text-align: center;">CODIGO</th>
            <th style="text-align: center;">NOMBRE</th>
            <th style="text-align: center;">CANTIDAD</th>
            <th style="text-align: center;">NETO</th>
            <th style="text-align: center;">DESCUENTO</th>
            <th style="text-align: center;">IMPUESTO</th>
        </tr>
        </thead>
        <tbody >
            @foreach($pedido_items as $sale_i)
                <?php 
                
                    $producto = App\Productos::find($sale_i->idproducto);
                ?>
                <tr>
                    <td style="text-align: left;">{{ $producto->codigo_producto }}</td>
                    <td style="text-align: left;">{{ $sale_i->nombre_proc }}</td>
                    <td style="text-align: right;">{{ $sale_i->cantidad_ped }}</td>
                    <td style="text-align: right;">{{ number_format($sale_i->valor_neto,2,',','.') }}</td>
                    <td style="text-align: right;">{{ number_format($sale_i->valor_descuento,2,',','.') }}</td>
                    <td style="text-align: right;">{{ number_format($sale_i->valor_impuesto,2,',','.') }}</td>
                </tr>
            @endforeach
             
        </tbody>
    </table>
   
</div>
<div class="information" style="position: absolute; bottom: 80;">
    <table width="100%">
     <tr>
        	
            <td align="left" style="font-size: 15px;"><b>Observaciones:</b></td>
            </tr>
               <tr>
            <td align="left" style="font-size: 15px;" colspan="5">{{ $pedido->observaciones }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 15px;">Total Neto</td>
            <td align="right" class="gray" style="font-size: 15px;">{{ number_format($pedido->total_neto_ped,2,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 15px;">Total Descuento</td>
            <td align="right" class="gray" style="font-size: 15px;">{{ number_format($pedido->total_descuento_ped,2,',','.') }}</td>
         </tr>
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 15px;">Total Impuesto</td>
            <td align="right" class="gray" style="font-size: 15px;">{{ number_format($pedido->total_impuesto_ped,2,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td align="right" style="font-size: 15px;">Total Comprobante</td>
            <td align="right" class="gray" style="font-size: 15px;">{{  number_format($pedido->total_comprobante_ped,2,',','.') }}</td>
        </tr>
    </table>
</div>
<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
        <td align="left" style="width: 30%;">

                
                <pre>
                   <b> Razon Social: {{ $configuracion->nombre_empresa }}
                    Constribuyente:  {{ $configuracion->nombre_comercial }}  Ced: {{ $configuracion->numero_id_emisor }}
                    Correo: {{ $configuracion->email_emisor }} Tel: {{ $configuracion->telefono_emisor }} Dirección: {{ $configuracion->direccion_emisor }}</b>
                </pre>
            </td>
            
            
        </tr>

    </table>
</div>
</body>
</html>