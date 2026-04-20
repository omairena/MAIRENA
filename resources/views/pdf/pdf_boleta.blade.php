<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Boleta - # {{ $pedido->numero_documento }}</title>

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
            color: #0a0a0a;
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
            background-color: #f2fcfc;
            color: #0a0a0a;
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
      <h3 align="center"> <img src="./black/img/{{ $configuracion->logo }}" alt="Logo" width="100" class="logo"/>
   Boleta de Reparacion de Electrodomesticos.</h3>
   <h3 align="center"><b>Boleta - # {{ $pedido->numero_documento }}</b></h3>
    <table width="95%">
        <tr>
        
            <td align="left" style="width: 50%;">
           
<strong>Cliente: {{ $cliente->nombre }}</strong><br>
Identificación: {{ $cliente->num_id }}<br>
Correo: {{ $cliente->email }}<br>
Teléfono: {{ $cliente->telefono }}<br>
Dirrección: {{ $cliente->direccion }}<br>

Fecha: {{ $pedido->fecha_doc }}<br>
            
            </td>
            
            <td align="left" style="width: 50%;">

                
           
                   <b> {{ $configuracion->nombre_empresa }}<br>
                    Ced: {{ $configuracion->numero_id_emisor }}<br>
                    Correo: {{ $configuracion->email_emisor }} <br>
                    Tel: {{ $configuracion->telefono_emisor }} <br>
                    Dirección: {{ $configuracion->direccion_emisor }}</b>
             
            </td>
            
        </tr>
        <tr>
         <td align="center" style="width: 100%;">

                
           
              <STRONG> Datos del Articulo </STRONG>    
             
            </td>
              </tr>
<tr>
        
            <td align="left" style="width: 50%;">
           
<strong>Descripcion: {{ $pedido->descripcion }}</strong><br>
Marca: {{  $pedido->marca }}<br>
Modelo: {{  $pedido->modelo }}<br>
Serie: {{  $pedido->serie }}<br>

            </td>
            <td align="left" style="width: 50%;">

                
Factura: {{ $pedido->factura }}<br>
Fecha Venta: {{ $pedido->fecha_venta }}<br>
En Garantia: {{ $pedido->tiene_garantia }}<br>
             
            </td>
            
        </tr>
        <tr>
  <td align="left" style="width: 100%;">
    <strong>Creado por: </strong>
   <?php 
                
                    $usuario = App\User::find($pedido->user);
                    //dd($usuario->email);
                   echo $usuario->name;
                ?>
</td>     
           </tr>  
            
    </table>
</div>


<br/>
<center> <STRONG> Detalle de Repuesto y Servicio </STRONG>  </center> 
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
<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
     <tr>
        	
            <td align="left" style="font-size: 15px;"><b>Observaciones:</b></td>
            
            <td align="left" style="font-size: 15px;" colspan="5">{{ $pedido->observaciones }}</td>
            
        </tr>
         <tr>
        	
            <td align="left" style="font-size: 15px;"><b>Falla:</b></td>
            
            <td align="left" style="font-size: 15px;" colspan="5">{{ $pedido->falla }}</td>
            
        </tr>
         <tr>
        	
            <td align="left" style="font-size: 15px;"><b>Accesorios:</b></td>
            
            <td align="left" style="font-size: 15px;" colspan="5">{{ $pedido->accesorios }}</td>
            
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
         <tr>
            <td colspan="6"> <b>SI EL ARTICULO NO ES RETIRADO A MAS TARDAR 30 DIAS DESPUES DE SU REVISION, CON SU RESPECTIVA BOLETA,LA EMPRESA PROCEDERA AL DESARME DEL
ARTICULO SIN NINGUNA RESPONSABILIDAD. TODA REVISION SE COBRA Y MANO DE OBRA TIENE UN MES DE GARANTIA.</b></td> 
             </tr> 
    </table>
   
</div>

</body>
</html>