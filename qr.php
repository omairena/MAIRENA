<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Factura Electronica San Esteban</title>  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            background-color: #f4f4f9;  
            color: #333;  
            margin: 0;  
            padding: 20px;  
        }  
        h2 {  
            color: #4a4e69;  
        }  
        #consultaForm {  
            background: #fff;  
            padding: 20px;  
            border-radius: 5px;  
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  
            max-width: 400px;  
            margin: auto;  
        }  
        label {  
            display: block;  
            margin-bottom: 8px;  
            font-weight: bold;  
        }  
        input[type="text"] {  
            width: 100%;  
            padding: 10px;  
            border: 1px solid #ccc;  
            border-radius: 4px;  
            margin-bottom: 10px;  
        }  
        button {  
            background-color: #4a4e69;  
            color: white;  
            border: none;  
            padding: 10px;  
            border-radius: 4px;  
            cursor: pointer;  
            width: 100%;  
            font-size: 16px;  
        }  
        button:hover {  
            background-color: #333;  
        }  
        #resultado {  
            margin-top: 20px;  
            background: #fff;  
            padding: 15px;  
            border-radius: 5px;  
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  
        }  
        #resultado h3 {  
            color: #4a4e69;  
        }  
        #resultado p {  
            margin: 5px 0;  
        }  
    </style>  
</head>  
<body>  
<h2>Factura Electronica San Esteban.</h2>  
<h2>Consulta de Comprobante</h2>  

<form id="consultaForm">  
    <label for="numeroDocumento">Número de Documento:</label>  
    <input type="text" id="numeroDocumento" name="numeroDocumento" required>  
    <button type="submit">Consultar</button>  
</form>  

<div id="resultado"></div>  

<script>  
$(document).ready(function() {  
    $("#consultaForm").on("submit", function(event) {  
        event.preventDefault(); // Previene que el formulario se envíe de forma tradicional  

        var numeroDocumento = $("#numeroDocumento").val();  

        $.ajax({  
            type: "POST",  
            url: "consultar.php",  
            data: { numeroDocumento: numeroDocumento },  
            dataType: "json",  
            success: function(response) {  
                if (response.error) {  
                    $("#resultado").html("<p>Error: " + response.error + "</p>");  
                } else {  
                    var tipoDocumento;  
                    if (response.tipodoc === "01") {  
                        tipoDocumento = "Factura Electrónica";  
                    } else if (response.tipodoc === "02") {  
                        tipoDocumento = "Nota de Débito";  
                    } else if (response.tipodoc === "03") {  
                        tipoDocumento = "Nota de Crédito";  
                    } else if (response.tipodoc === "04") {  
                        tipoDocumento = "Tiquete";  
                    } else {  
                        tipoDocumento = "Tipo de documento desconocido";  
                    }  
                    // Dar formato a los montos  
                    var montoTotal = parseFloat(response.total_comprobante).toLocaleString('es-CR', { style: 'currency', currency: 'CRC' });  
                    var impuestoTotal = parseFloat(response.total_impuesto).toLocaleString('es-CR', { style: 'currency', currency: 'CRC' });  
                    var version = "4.4";
                    // Mostrar los resultados  
                    $("#resultado").html("<h3>Datos del Comprobante</h3>" +  
                        "<p>Tipo de Documento: " + tipoDocumento + "</p>" +  
                        "<p>Fecha de Emisión: " + response.fecha_reenvio + "</p>" +  
                        "<p>Nombre Emisor: " + response.nombre_emisor + "</p>" +  
                        "<p>Estado MH: " + response.estatushacienda + "</p>" +  
                        "<p>Nombre Receptor: " + response.nombre + "</p>" +  
                        "<p>Monto Total: " + montoTotal + "</p>" +  
                        "<p>Impuesto Total: " + impuestoTotal + "</p>"+
                         "<p>Versión de XML: " + version + "</p>"
                       
                    );  
                }  
            },  
            error: function(jqXHR, textStatus, errorThrown) {  
    console.error("Error en la consulta:", textStatus, errorThrown);  
    console.log("Detalles de la respuesta:", jqXHR);  
    $("#resultado").html("<p>Error al realizar la consulta. Detalle: " + textStatus + "</p>");  
}  
        });  
    });  
});  

function loadDataFromURL() {  
    const urlParams = new URLSearchParams(window.location.search);  
    const numeroDocumento = urlParams.get('numeroDocumento');  

    if (numeroDocumento) {  
        document.getElementById('numeroDocumento').value = numeroDocumento;  
    }  
}  

// Cargar datos al cargar la página  
window.onload = loadDataFromURL;  
</script>  

</body>  
</html>  
<h2>Factura Electronica San Esteban.</h2> 
<h2>Tels: 8309-3816.</h2>  