<!DOCTYPE html>
<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('black') }}/img/apple-icon.png">
    <link href="{{ asset('black') }}/css/theme.css" rel="stylesheet" />

	<style type="text/css">
* {
    font-size: 13px;
    font-family: 'Poppins';
}

td,
th,
tr,
table {
    border-top: 1px solid black;
    border-collapse: collapse;
}

td.producto,
th.producto {
    width: 135px;
    max-width: 135px;
}

td.cantidad,
th.cantidad {
    width: 30px;
    max-width: 30px;
    word-break: break-all;
}

td.precio,
th.precio {
    width: 90px;
    max-width: 90px;
    word-break: break-all;
    text-align: right;
}

.centrado {
    text-align: left;
    align-content: left;
}

.ticket {
    width: 235px;
    max-width: 235px;
}

img {
    max-width: inherit;
    width: inherit;
}

@media print {
    .oculto-impresion,
    .oculto-impresion * {
        display: none !important;
    }
}
.my-button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.my-button:hover {
  background-color: #6fa8dc;
}
.my-button:active {
  background-color: #a64d79;
}
.my-button_imp {
  padding: 10px 20px;
  background-color: #2986cc;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.my-button_imp:hover {
  background-color: #6fa8dc;
}
.my-button_imp:active {
  background-color: #a64d79;
}
@media print {
        .my-button_imp,
        .my-button,
        .dropdown-item {
            display: none;
        }
    }
	</style>

</head>

    <body>
    	<button class="my-button_imp " onclick="imprimir()">Imprimir</button>
    	<!--<a href="{{ route('punto.create')}}" class="oculto-impresion" >Atras</a>-->
        	 <?php
                    $buscar_usapos = App\User_config::where('idconfigfact', Auth::user()->config_u[0]->idconfigfact)->get();
                    //dd($buscar);
                    if($buscar_usapos[0]->usa_pos === 0){
                 ?>
                 	<a class="my-button" href="{{ route('punto.create')}}">
                     <?php }else{ ?>

                  	<a class="my-button" href="{{ route('pos.create')}}">
               <?php }?>
Regresar al Punto de Venta</a>
<a href="{{ url('pdf-factura', ['idsales' => $sales->idsale]) }}" class="dropdown-item">{{ __('Generar PDF') }}</a>



</body>
</html>
        <div class="ticket">

        	<?php

        		switch ($sales->condicion_venta) {
        case '01':
          $condicion = 'Contado';
          break;
          case '02':
          $condicion = 'Credito';
          break;
          case '10':
          $condicion = 'Venta crédito IVA - 90 días (Art27 LIVA)';
          break;
           case '11':
          $condicion = 'Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA) ';
          break;
      }
      if(!is_null($sales->medio_pago) && !empty($sales->medio_pago)){
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
      } else {
        $medio_pago = $sales->medioPagos()->get();
      }
      
      switch ($sales->tipo_documento) {
        case '01':
          $tipodoc = 'Factura Electronica';
        break;
        case '02':
          $tipodoc = 'Nota de Debito Electronica';
        break;
        case '03':
          $tipodoc = 'Nota de Credito Electronica';
        break;
        case '04':
          $tipodoc = 'Tiquete Electronico';
        break;
        case '08':
          $tipodoc = 'Factura Electronica de Compra';
        break;
        case '09':
          $tipodoc = 'Factura Electronica de exportacion';
        break;
        case '96':
          $tipodoc = 'Factura Regimen Simplificado';
        break;
        case '95':
          $tipodoc = 'Nota Credito Regimen Simplificado';
        break;

      }
      
        	 ?>
            <p class="centrado">
            	<b>{{ $configuracion->nombre_empresa }}</b>
                <br>{{ $configuracion->nombre_emisor }}
            	<br>Identificacion: {{ $configuracion->numero_id_emisor }}
        		<br>Correo: {{ $configuracion->email_emisor }}
       			<br>Tel: {{ $configuracion->telefono_emisor }}
       			<br>Direccion: {{ $configuracion->direccion_emisor }}
                <br>Fecha: {{$sales->fecha_reenvio }}
                 <br>Condicion Venta: {{ $condicion }}
        		@if(is_iterable($medio_pago))  
        		@foreach($medio_pago as $pago)
        		    <br>Tipo Pago:  {{ $pago->nombre }}
        		    <br>Monto Pago:  {{ $pago->pivot->monto }}
        		    @if(!is_null($pago->pivot->referencia))
        		        <br>Ref Pago:  {{ $pago->pivot->referencia }}
        		        
        		    @endif
        		@endforeach
        		@else  
   	            <br>Tipo Pago:  {{ $medio_pago }}
        		<br>Ref Pago:  {{ $sales->referencia_pago }}
@endif  

        		<br>Moneda: {{$sales->tipo_moneda }}
        		<br>Tipo Cambio: {{ $sales->tipo_cambio }}
             @if($sales->tipo_documento == '96')  
    @if($configuracion->usa_op > 0)  
        <br><b>Tipo Documento: Orden de Pedido</b>  
    @else  
        <br><b>Tipo Documento: Factura Regimen Simplificado</b>  
    @endif  
@elseif($sales->tipo_documento == '95')  
    <br><b>Tipo Documento: Anulacion</b>  
@else  
    <br><b>Tipo Documento: {{ $tipodoc }}</b>  
@endif  
              
            
              
        @if($sales->tipo_documento == '96' or $sales->tipo_documento == '95')  
    @if( $configuracion->usa_op == 1)  
        <br><b>Numero de Pedido: {{ $sales->numero_documento }}</b>  
    @else  
        <br><b>Numero de Documento: {{ $sales->numero_documento }}</b>  
    @endif  
@else  
    <br><b>Numero de Documento: {{ $facelectron[0]->consecutivo }}</b>  
@endif  


        		<br>Cliente: {{ $cliente->nombre }}
        		<br>Identificacion: {{ $cliente->num_id }}
        		<br>Telefono: {{ $cliente->telefono }}
        		<br>Correo: {{ $cliente->email }}
            </p><br>
            <table>
                <thead>
                    <tr>

                        <th class="producto">PRODUCTO</th>
                        <th class="precio">MONTO</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                		$total_neto = 0;
                     	$total_oc=0;
        				$total_descuento = 0;
        				$total_comprobante = 0;
       				 	$total_impuesto = 0;
                        $total_IVA_ex = 0;
        				$total_iva_devuelto = 0;
                         $total_impuesto_neto =0;
                         $total_impuesto_c_ex=0;
                         $total_abono=0;
                         $saldo_comprobante=0;
                	?>
                	@foreach($sales_item as $sale_i)
                		<?php
            			if ($sale_i->existe_exoneracion == '00') {
              				if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                  				$total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
              				}
              				$total = $sale_i->valor_neto + ($sale_i->valor_impuesto );
              			//	$total_impuesto_neto = $total_impuesto_neto + $sale_i->valor_impuesto;
            			}else{
              				$exoneracion = \App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
              				$monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
              				if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                				$total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
              				}
              				$total = ($sale_i->valor_neto+ $monto_imp_exonerado)-$total_iva_devuelto;
              				$total_impuesto_c_ex = $total_impuesto_c_ex + $monto_imp_exonerado;

            			}
                        $total_impuesto_neto = $total_impuesto_neto + $sale_i->valor_impuesto;
           				$total_neto = $total_neto + $sale_i->valor_neto + $sale_i->valor_descuento;
                   	    $total_oc = $total_oc + $sales->total_otros_cargos;
                        $total_abono = $sales->total_abonos_op;
            			$total_descuento = $total_descuento + $sale_i->valor_descuento;
                        $total_IVA_ex = $total_IVA_ex + $sale_i->exo_monto;
                        $total_impuesto_c_ex=  $total_impuesto_neto- $total_IVA_ex-$total_iva_devuelto;
            			$total_comprobante = $total_comprobante + $total +	$total_oc ;
                        $saldo_comprobante= $total_comprobante-$total_abono;
                       	$total_comprobante = $sales->total_comprobante;
                        $total_iva_devueltod = $sales->total_iva_devuelto;
                        if(!empty($facelectron[0]->clave)){
                          $calvee= substr( $facelectron[0]->clave, -25);
                       $calveeg= substr( $facelectron[0]->clave,0, 25);
                        }
                		?>
                		<tr>

                        	<td class="producto">{{ $sale_i->nombre_producto }} <br> Cant. {{ $sale_i->cantidad }} X Pr.Unit. {{ $sale_i->costo_utilidad }} + {{ number_format($sale_i->valor_impuesto,2,',','.') }} IVA =</td>
                        	<td class="precio"> {{ number_format($total,2,',','.') }} </td>
                    	</tr>
                	@endforeach
                    <tr>

                        <td class="producto"><i> Total Neto: </i></td>
                        <td class="precio"><b>{{ number_format($total_neto,2,',','.') }}</b></td>
                    </tr>
                    <tr>

                        <td class="producto"><i>Total Descuento:</i></td>
                        <td class="precio"><b>{{ number_format($total_descuento,2,',','.') }}</b></td>
                    </tr>
                    @if($total_IVA_ex > '0' or $total_iva_devuelto > '0')
                    <tr>

                        <td class="producto"><i>Total Impuesto Neto:</i></td>
                        <td class="precio"><b>{{ number_format($total_impuesto_neto,2,',','.') }}</b></td>
                    </tr>
                      @endif
                      @if($total_IVA_ex > '0')
                    <tr>

                        <td class="producto"><i>-Total IVA Exonerado:</i></td>
                        <td class="precio"><b>{{ number_format($total_IVA_ex,2,',','.') }}</b></td>
                    </tr>
                      @endif
                     @if($total_iva_devuelto > '0')
                    <tr>

                        <td class="producto"><i>-IVA Devuelto 4% X Serv Medicos:</i></td>
                        <td class="precio"><b>{{ number_format($total_iva_devueltod,2,',','.') }}</b></td>
                    </tr>
                    @endif
                     <tr>

                        <td class="producto"><i>= Total Impuesto:</i></td>
                        <td class="precio"><b>{{ number_format($total_impuesto_c_ex,2,',','.') }}</b></td>
                    </tr>
                     @if($total_oc > '0')
                     <tr>

                        <td class="producto"><i>Total Otros Cargos:</i></td>
                        <td class="precio"><b>{{ number_format(	$total_oc,2,',','.') }}</b></td>
                    </tr>
                      @endif

                    <tr>

                        <td class="producto"><b><i>Total Comprobante:</i></td>
                        <td class="precio"><b>{{ number_format($total_comprobante,2,',','.') }}</b></td>
                      </tr>
                      @if($total_abono > '0')
                        <tr>

                    <td class="producto"><i>Abono:</i></td>
                    <td class="precio"><b>{{ number_format($total_abono,2,',','.') }}</b></td>
                    </tr>
                    <tr>

                    <td class="producto"><b><i>Saldo Compra:</i></td>
                    <td class="precio"><b>{{ number_format($saldo_comprobante,2,',','.') }}</b></td>
                    </tr>

   @endif
   </tbody>
            </table>


            <table>
            <tbody>
                @if($sales->tipo_documento == '96' or $sales->tipo_documento == '95') 
         
         @else

   @if($sales->referencia_sale > '0' )
        <tr>
          <td align="right" style="font-size: 15px;">Doc Referencia Anulado con NC:</td>
        </tr>
        <tr>
           <td align="right" style="font-size: 15px;" >{{ substr($consulta_fac[0]->clave,0, 25)  }}	</td>
        </tr>
        <tr>
           <td align="right" style="font-size: 15px;" >{{ substr($consulta_fac[0]->clave, -25) }}	</td>
        </tr>

         @endif
        @if($sales->ref_clave_sale > '0' )
        <tr>
            <td align="right" style="font-size: 15px;">Doc Referencia:</td>
        </tr>
        <tr>
            <td align="right" style="font-size: 15px;" colspan="2">{{ substr($sales->ref_clave_sale,0, 25) }}	</td>
        </tr>
        <tr>
            <td align="right" style="font-size: 15px;" colspan="2">{{ substr($sales->ref_clave_sale, -25) }}	</td>
        </tr>
        @endif
         @endif

                </tbody>
            </table>
            <br><br>
            <p class="centrado">
                Nombre y Firma Recibido Conforme.
                <br>
                   @if(!empty($sales->observaciones) )
                <b>Observaciones:</b>
                <br>{{ $sales->observaciones }}</br>

                  <b>
                         @endif
                     @if($sales->tipo_documento == '96' or $sales->tipo_documento == '95' )
                     @if( $configuracion->usa_op !='1')

                      Emisor Acogido al Regimen de Tributacion Simplificada
                       AUTORIZADO MEDIANTE RESOLUCION N° DRT-R-033-2019 VERSION XML: 4.4
                     @endif
                      <br>
                    @endif

                  </b>
                   @if( $configuracion->usa_op == 1)
               <b>Dirección:</b>

                      	 {{ $cliente->direccion }}
                     @endif
                    <br>
                     @if($sales->tipo_documento != '96' or $sales->tipo_documento != '95')
                     <b>  AUTORIZADO MEDIANTE RESOLUCION N° MH-RES-0027-2024<b> Del 13 de noviembre de 2024 <b> VERSION XML: 4.4
                     <br><b>Clave del Documento: </b>
                  @endif




                		<?php
                     if(!empty($facelectron[0]->clave)){
               echo $calveeg;
                     }
	?>

  	<?php

                if(!empty($facelectron[0]->clave)){
                       echo $calvee;

                    $link = App\App_settings::where('idsettings', 1)->get();
                    //dd($link[0]->lin);



                       $contents = QrCode::format('png')->generate($link[0]->lin. $facelectron[0]->clave);

	?>
 <br> <p>Codigo QR:</p>

       <br>  <img src="data:image/png;base64, {{ base64_encode($contents) }}">


        	<?php
        	   }
	?>

                <br>Factura Electronica San Esteban.
                 
            </p>
        </div>

<script type="text/javascript">
    window.print();
    function imprimir() {
      window.print();
    }
    </script>
    </body>

</html>
