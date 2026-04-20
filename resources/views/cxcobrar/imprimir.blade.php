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
    width: 155px;
    max-width: 155px;
}

td.cantidad,
th.cantidad {
    width: 10px;
    max-width: 10px;
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

	</style>

</head>

    <body>
    	<button class="oculto-impresion" onclick="imprimir()">Imprimir</button>
    	<a href="{{ route('cxcobrar.index')}}" class="oculto-impresion" >Atras</a>
        <div class="ticket">
        	<?php 
            switch ($log_cxcobrar->tipo_mov) {
              case '1':
                $tipo_mov = 'ABONO';
              break;
              case '2':
                $tipo_mov = 'ABONO PARCIAL';
              break;
            }
        	 ?>
            <p class="centrado">
            	<b>{{ $configuracion->nombre_emisor }}</b>
            	<br>Identificacion: {{ $configuracion->numero_id_emisor }}
        		<br>Correo: {{ $configuracion->email_emisor }}
       			<br>Tel: {{ $configuracion->telefono_emisor }}
       			<br>Direccion: {{ $configuracion->direccion_emisor }}
        		<br>Tipo Documento: Abono
        		<br>Tipo Movimiento: {{ $tipo_mov }}
        		<br>Referencia:  {{ $log_cxcobrar->referencia }}
        		<br>Numero de Abono: {{ $log_cxcobrar->num_recibo_abono }}
        		<br>Cliente: {{ $cliente->nombre }}
        		<br>Identificacion: {{ $cliente->num_id }}
        		<br>Telefono: {{ $cliente->telefono }}
        		<br>Correo: {{ $cliente->email }}
            <br><b>Fecha: {{ $log_cxcobrar->fecha_rec_mov }}</b>
            <br><b>Número de Abono: {{ $log_cxcobrar->num_recibo_abono}}</b>
            </p><br>
            <table>
                <thead>
                    <tr>
                        <th class="cantidad"></th>
                        <th class="producto">NUMERO</th>
                        <th class="precio">MONTO</th>
                    </tr>
                </thead>
                <tbody>
                		<tr>
                        	<td class="cantidad"></td>
                        	<td class="producto">{{ $log_cxcobrar->num_recibo_abono }}</td>
                        	<td class="precio"> {{ $log_cxcobrar->monto_abono }} </td>
                    	</tr>
                    <tr>
                        <td class="cantidad"></td>
                        <td class="producto"><i> Cuenta Pendiente Monto Inicial: </i></td>
                        <td class="precio"><b>{{ number_format($mov_cxcobrar->monto_mov,2,',','.') }}</b></td>
                    </tr>
                    <tr>
                        <td class="cantidad"></td>
                        <td class="producto"><i>Total Abonos:</i></td>
                        <td class="precio"><b>{{ number_format($mov_cxcobrar->abono_mov,2,',','.') }}</b></td>
                    </tr>
                    <tr>
                        <td class="cantidad"></td>
                        <td class="producto"><i>Saldo Pendiente:</i></td>
                        <td class="precio"><b>{{ number_format($mov_cxcobrar->saldo_pendiente,2,',','.') }}</b></td>
                    </tr>
                    

                </tbody>
            </table>
            <br><br>
            <p class="centrado">
                Nombre y Firma Recibido Conforme.
                <br>Factura Electronica San Esteban 
                <br>Tel: 2460-17/8309-3816 
                <br>email: fesanesteban@gmail.com.
            </p>
        </div>

<script type="text/javascript">
    	function imprimir() {
  window.print();
}
    </script>
    </body>
    
</html>