<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Caja - # {{ $log_caja->idlogcaja }}</title>

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
                <h3>{{ $caja->caja_emp[0]->nombre_emisor }}</h3>
                <pre>
Identificación: {{ $caja->caja_emp[0]->numero_id_emisor }}
Correo: {{ $caja->caja_emp[0]->email_emisor }}
Teléfono: {{ $caja->caja_emp[0]->telefono_emisor }}
<br/><br/>
Fecha de Apertura: {{ $log_caja->fecha_apertura_caja }}
Fecha de Cierre: {{ $log_caja->fecha_cierre_caja }}
				</pre>
            </td>
            <td align="center">
                <img src="./black/img/logo.JPG" alt="Logo" width="85" class="logo"/>
            </td>
            <td align="right" style="width: 40%;">

                <h3>Información de Cajero</h3>
                <pre>
                    Nombre: {{ $usuario->name }}
                    Correo: {{ $usuario->email }}
                </pre>
            </td>
        </tr>

    </table>
</div>


<br/>

<div class="invoice">
    <h3>Cierre de Caja # {{ $log_caja->idlogcaja }}</h3>
    <table class="table" width="100%" style="padding: 20px;">
        <thead>
        <tr>
            <th style="text-align: center;">DIA CIERRE</th>
            <th style="text-align: center;">FONDO CAJA</th>
            <th style="text-align: center;">VENTAS DE CONTADO</th>
            <th style="text-align: center;">VENTAS DE CREDITO</th>
            <th style="text-align: center;">RECIBOS DE DINERO</th>
            <th style="text-align: center;">TOTAL EFECT-ENTRANTE</th>
            <th style="text-align: center;">COBROS CON TARJETA</th>
            <th style="text-align: center;">PAGOS DEL DIA</th>
            <th style="text-align: center;">TOTAL EFECT-CAJA</th>
            <th style="text-align: center;">TOTAL EFECT-DEPOSITAR</th>
        </tr>
        </thead>
        <tbody >
      		<tr>
      			<td style="text-align: left;">{{ $log_caja->fecha_cierre_caja }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->fondo_caja,2,',','.') }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->ventas_contado,2,',','.') }}</td>
      			<td style="text-align: right;">{{ number_format($log_caja->ventas_credito,2,',','.') }}</td>
      			<td style="text-align: right;">{{ number_format($log_caja->recibo_dinero,2,',','.') }}</td>
      			<td style="text-align: right;">{{ number_format($log_caja->t_efectivo_entrante,2,',','.') }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->cobro_tarjeta,2,',','.') }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->pago_del_dia,2,',','.') }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->t_efectivo_caja,2,',','.') }}</td>
                <td style="text-align: right;">{{ number_format($log_caja->t_efectivo_depositar,2,',','.') }}</td>
      		</tr>
        </tbody>
    </table>
</div>
<div class="information" style="position: absolute; bottom: 40;">
</div>
<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                &copy; {{ date('Y') }} - Producto registrado por Oscar Mairena Vargas & Servicios Contables San Silvestre. Servicios Contables San Silvestre Todos los derechos reservados.
            </td>
            <td align="right" style="width: 50%;">
                Autorizado mediante la resolucion DGT-R-033-2019 del 27 de junio del 2019 Version 4.3
            </td>
        </tr>

    </table>
</div>
</body>
</html>