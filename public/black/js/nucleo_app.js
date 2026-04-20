function traerNumFactura(url,para2) {
    var idconfigfact = $('#idconfigfact').val();
    var tipo_documento = $('#tipo_documento').val();
    var idcaja = $('#idcaja').val();
    $.ajax({
        type:'GET',
        url:url,
        data:{idconfigfact:idconfigfact, tipo_documento:tipo_documento, idcaja:idcaja},
        dataType: 'json',
        success:function(response){
            var option = "Documento # " + response['success']['numero_factura'];
            $("#encabezado_factura").empty();
            $("#encabezado_factura").append(option);
            $('#numero_documento').val(response['success']['numero_factura']);
            var arreglo = response['success']['codigo_actividad'].length;
            if (arreglo > 0) {
                $('#combo_actividad').css( "display", "block");
                $('#actividad').find('option').remove();
                var cod_act = $('#valor_actividad').val();
                $(response['success']['codigo_actividad']).each(function(data) {
                    var validacion = '';
                    validacion += '<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'"';
                    if (cod_act == response['success']['codigo_actividad'][data].idcodigoactv) {
                        validacion +=  ' selected="true">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>';
                    }else{
                        validacion +=  '>'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>';
                    }
                   	$("#actividad").append(validacion);
                });
                if (tipo_documento != '04' && tipo_documento != '96') {  
                    var cliente = $('#cliente').val();
                    $.ajax({
                        type:'GET',
                        url:para2,
                        data:{cliente:cliente},
                        dataType: 'json',
                        success:function(response){
                            if (response.result === 1) {
                               // $('#cliente').val(response.default[0].idcliente);
                               $('#cliente').val("");
                               $('#cliente_serch').val("");
                               // $('#cliente_serch').val(response.default[0].nombre);
                                alert('Debes seleccionar un cliente. ');
                                $('#cliente_serch').focus();
                            }
                        }
                    });
                }
            }else{
                $('#actividad').find('option').remove();
                $('#combo_actividad').css( "display", "none");
            }
        }
    });
}

function traerNumMasivo(url) {
    var idconfigfact = $('#idconfigfact').val();
    var tipo_documento = $('#tipo_documento').val();
    var idcaja = $('#idcaja').val();
    $.ajax({
        type:'GET',
        url:url,
        data:{idconfigfact:idconfigfact, tipo_documento:tipo_documento, idcaja:idcaja},
        dataType: 'json',
        success:function(response){
            var option = "Documento # " + response['success']['numero_factura'];
            $("#encabezado_factura").empty();
            $("#encabezado_factura").append(option);
            $('#numero_documento').val(response['success']['numero_factura']);
            var arreglo = response['success']['codigo_actividad'].length;
            if (arreglo > 0) {
                $('#combo_actividad').css( "display", "block");
                $('#actividad').find('option').remove();
                $(response['success']['codigo_actividad']).each(function(data) {
                    $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                });
            }else{
                $('#actividad').find('option').remove();
                $('#combo_actividad').css( "display", "none");
            }
        }
    });
}

function traerNumReceptor(url) {
    var idconfigfact = $('#idconfigfact').val();
    var tipo_documento = $('#procesar_doc').val();
    var idcaja = $('#idcaja').val();
    $.ajax({
        type:'GET',
        url:url,
        data:{idconfigfact:idconfigfact, tipo_documento:tipo_documento, idcaja:idcaja},
        dataType: 'json',
        success:function(response){
            console.log(response);
            var option = "Documento # " + response['success']['numero_factura'];
            $("#encabezado_recepcion").empty();
            $("#encabezado_recepcion").append(option);
            $('#numero_documento_receptor').val(response['success']['numero_factura']);
            var factor = $('#factor_credito').val(response['success']['factor'].factor_receptor);
            factor.prop('readonly', true);
            var arreglo = response['success']['codigo_actividad'].length;
            if (arreglo > 0) {
                $('#combo_actividad').css( "display", "block");
                $('#actividad').find('option').remove();
                $(response['success']['codigo_actividad']).each(function(data) {
                    $("#actividad").append('<option value="'+response['success']['codigo_actividad'][data].idcodigoactv+'">'+ response['success']['codigo_actividad'][data].codigo_actividad+' - '+ response['success']['codigo_actividad'][data].descripcion+'</option>');
                });
            }else{
                $('#actividad').find('option').remove();
                $('#combo_actividad').css( "display", "none");
            }
        }
    });
}

function validaMedioPago(){
	if ($('#medio_pago').val() === '01') {
        $('#referencia_p').css( "display", "none");
    }else{
        $('#referencia_p').css( "display", "block");
    }
}

function validaCondicionVenta(){
	if ($('#condición_venta').val() === '01') {
        $('#pl_credito').css( "display", "none");
    }else{
        $('#pl_credito').css( "display", "block");
    }
}
function valorMoneda(parametro){
	switch(parametro){
        case 'CRC':
            $('#tipo_cambio').css( "display", "none");
            $('#input-tipo_cambio').val('0.00');
        break;
        case 'USD':
            $('#tipo_cambio').css( "display", "block");
            var URL_USD = 'https://api.hacienda.go.cr/indicadores/tc/dolar';
            $.ajax({
                type:'get',
                url: URL_USD,
                dataType: 'json',
                success:function(response){
                    if (response == null) {
                        alert('Conexion fallida con Hacienda');
                    }else{
                        $('#input-tipo_cambio').val(response.venta.valor);
                        $("#input-tipo_cambio").prop('readonly', true);
                    }
                }
            });
        break;
        case 'EUR':
            $('#tipo_cambio').css( "display", "block");
            var URL_EUR = 'https://api.hacienda.go.cr/indicadores/tc/euro';
            $.ajax({
                type:'get',
                url: URL_EUR,
                dataType: 'json',
                success:function(response){
                    if (response == null) {
                        alert('Conexion fallida con Hacienda');
                    }else{
                        $('#input-tipo_cambio').val(response.colones);
                        $("#input-tipo_cambio").prop('readonly', true);
                    }
               	}
            });
        break;
    }

}


function enviarDatosProducto(){
	var datos = $('#form_factura').serialize();
    $('#AddProductos').modal('hide');
    $("#form_factura").submit();
}

function irCodigoPos(codigo_pos,url){
	var lector = $('#usa_lector').val();
    if ($(this).val().length <= 0) {
    }else{
        $.ajax({
            type:'get',
            url: url,
            dataType: 'json',
            data:{codigo_pos:codigo_pos},
            success:function(response){
                var arreglo = response['success'].length;
                if (arreglo > 0) {
                    if (lector > 0) {
                       	$('#idproducto_pos').val(response['success'][0]['idproducto']);
                        $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                        $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                        $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                        $('#cantidad_pos_envia').focus();
                        $('#cantidad_pos_envia').val(1);
                        $('#agregar_producto_pos').css( "display", "block");
                        $("#agregar_producto_pos").trigger("click");
                    }else{
                        $('#idproducto_pos').val(response['success'][0]['idproducto']);
                        $('#codigo_pos').val(response['success'][0]['codigo_producto']);
                        $('#nombre_pos').val(response['success'][0]['nombre_producto']);
                        $('#disponible_pos').val(response['success'][0]['cantidad_stock']);
                        $('#cantidad_pos').prop('readonly', false);
                        $('#descuento_pos').prop('readonly', false);
                            }
                }else{
                    $('#disponible_pos').prop('readonly', true);
                    $('#cantidad_pos').prop('readonly', true);
                    $('#descuento_pos').prop('readonly', true);
                }
            },
            complete : function(xhr, status) {
                if (lector > 0) {

                }else{
                    $('#cantidad_pos_envia').focus();
               	}
            }
        });
    }
}

//SECCIOn DE EDICIOn

function valorMonedaEdit(parametro,url){
	switch(parametro){
       	case 'CRC':
         	$('#tipo_cambio').css( "display", "none");
            $('#input-tipo_cambio').val('0.00');
            var tipocambio = 0.00;
            $.ajax({
                type:'get',
                url: '/modificar-tipocambio',
                dataType: 'json',
                data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
                success:function(response){
                },
                error:function(response){
                }
            });
        break;
        case 'USD':
            $('#tipo_cambio').css( "display", "block");
            var URL_USD = 'https://api.hacienda.go.cr/indicadores/tc/dolar';
            $.ajax({
                type:'get',
                url: URL_USD,
                dataType: 'json',
                success:function(response){
                    if (response == null) {
                        alert('Conexion fallida con Hacienda');
                    }else{
                        $('#input-tipo_cambio').val(response.venta.valor);
                        $("#input-tipo_cambio").prop('readonly', true);
                        var  tipocambio = response.venta.valor;
                        $.ajax({
                            type:'get',
                			url: '/modificar-tipocambio',
                            dataType: 'json',
                            data:{tipocambio:tipocambio, moneda:moneda,idsale:idsale},
                            success:function(response){
                            },
                            error:function(response){
                            }
                        });
                    }
                }
            });
        break;
        case 'EUR':
            $('#tipo_cambio').css( "display", "block");
            var URL_EUR = 'https://api.hacienda.go.cr/indicadores/tc/euro';
            $.ajax({
                type:'get',
                url: URL_EUR,
                dataType: 'json',
                success:function(response){
                    if (response == null) {
                        alert('Conexion fallida con Hacienda');
                    }else{
                        $('#input-tipo_cambio').val(response.colones);
                        $("#input-tipo_cambio").prop('readonly', true);
                        var tipocambio = response.colones;
                        $.ajax({
                            type:'get',
                			url: '/modificar-tipocambio',
                            dataType: 'json',
                            data:{tipocambio:tipocambio, moneda:moneda, idsale:idsale},
                            success:function(response){
                            },
                            error:function(response){
                            }
                        });
                    }
                }
            });
        break;
    }
}

//Abonos de la CXC
function traerNumAbono(url) {
    var tipo_documento = 98;
    var idcaja = $('#idcaja').val();
    $.ajax({
        type:'GET',
        url:url,
        data:{tipo_documento:tipo_documento, idcaja:idcaja},
        dataType: 'json',
        success:function(response){
            //console.log(response);
            $('#input-num_recibo_abono').val(response['success']['numero_abono']);
        }
    });
}

//Abonos de la CXP
function traerNumCuentaxp(url) {
    var tipo_documento = 99;
    var idcaja = $('#idcaja').val();
    $.ajax({
        type:'GET',
        url:url,
        data:{tipo_documento:tipo_documento, idcaja:idcaja},
        dataType: 'json',
        success:function(response){
            //console.log(response);
            $('#input-num_recibo_abono').val(response['success']['numero_abono']);
        }
    });
}

function validaFacelectron(id, url) {
    $.ajax({
        type:'GET',
        url:url,
        data:{idsale:id},
        dataType: 'json',
        success:function(response){
            console.log(response);
            if (response['result'] > 0) {
                window.location.reload();
            }
        }
    });
}

function traerInfoReceptor(idreceptor, url) {

    $.ajax({
        type:'GET',
        url:url,
        data:{idreceptor:idreceptor},
        dataType: 'json',
        success:function(response){
            console.log(response);
            $('#tipo_documento').val(response['success']['tipo_documento_recibido']);
            $('#procesar_doc').val(response['success']['tipo_documento']);
            $('#procesar_doc').attr("disabled", false);

            $('#idcaja').val(response['success']['idcaja']);
            $('#idcaja').attr("disabled", false);

            //$('#actividad').val(response['success']['idcaja']);
            $('#actividad').attr("disabled", false);

            $('#detalle_mensaje').val(response['success']['detalle_mensaje']);
            $('#detalle_mensaje').attr("readonly", false);

            $('#clasifica_d151').val(response['success']['clasifica_d151']);
            $('#clasifica_d151').attr("disabled", false);

            $('#condicion_impuesto').val(response['success']['condicion_impuesto']);
            $('#condicion_impuesto').attr("disabled", false);
        }
    });
}

function traerNombreCliente(valores) {
    //console.log(valores);
    var nombre_cli = valores.nombre_cli;
    if (nombre_cli.length <= 0) {
    }else{

        $.ajax({
            type:'get',
            url: valores.URL,
            dataType: 'json',
            data:{nombre_cli:nombre_cli},
            success:function(response){
                //console.log(response);
                var arreglo = response['success'].length;
                var tipo_documento = $('#tipo_documento').val();
                if (arreglo > 0) {

                    if (response['success'][0]['num_id'] != 100000000 && tipo_documento === '04') {

                        $('#tipo_documento').val('01');
                        traerNumFactura(valores.APP_URL,valores.o2);
                    }
                    if (response['success'][0]['num_id'] === 100000000 && tipo_documento === '01' || response['success'][0]['num_id'] === 100000000 && tipo_documento === '96' || response['success'][0]['num_id'] === 100000000 && tipo_documento === '09') {

                        alert('seleccionar otro tipo de documento');
                        $('#tipo_documento').val('04');
                        $('#cliente_serch').val(response['success'][0]['nombre']);
                        //alert(desde);
                        //console.log(desde);
                        switch (valores.desde) {
                            case 'POS':
                                $('#telefono').val('');
                                $('#direccion').val('');
                            break;
                            case 'FACT':
                                $('#cliente').val(response['success'][0]['idcliente']);
                                $('#ced_receptor').val(response['success'][0]['num_id']);
                                $('#datos_internos').val(1);
                            break;
                            case 'PED':
                            break;
                        }

                        traerNumFactura(valores.APP_URL,valores.o2);

                    } else {

                        $('#cliente_serch').val(response['success'][0]['nombre']);
                        $('#cliente').val(response['success'][0]['idcliente']);
                        $('#telefono').val(response['success'][0]['telefono']);
                        $('#direccion').val(response['success'][0]['direccion']);
                        traerNumFactura(valores.APP_URL,valores.o2);
                    }
                } else {
                    var dataItem = {
                        nombre_cli: 'CONTADO',
                        desde: valores.desde,
                        URL: valores.URL,
                        APP_URL: valores.APP_URL,
                        o2: valores.o2,
                    }
                    traerNombreCliente(dataItem);
                }
            }
        });
    }
}

function editarNombreCliente(valores) {
    //console.log(valores);
    $.ajax({
        type:'get',
        url: valores.URL,
        dataType: 'json',
        data:{nombre_cli:valores.nombre_cli, idsale:valores.idsale},
        success:function(response){
            var arreglo = response['success'].length;
            var tipo_documento = $('#tipo_documento').val();
            if (arreglo > 0) {
                if (response['success'][0]['num_id'] === 100000000 && tipo_documento === '01') {
                        alert('seleccionar otro tipo de documento');
                        $('#tipo_documento').focus();
                }else{
                    if (response['success'][0]['num_id'] != 100000000 && tipo_documento === '04' || response['success'][0]['num_id'] === 100000000 && tipo_documento === '96' || response['success'][0]['num_id'] === 100000000 && tipo_documento === '09') {
                        var value = '01';
                        $.ajax({
                            type:'get',
                            url: '/editar-tipodoc-pos',
                            dataType: 'json',
                            data:{tipo_documento:value, idsale:valores.idsale},
                            success:function(datos){
                            }
                        });
                    }
                    $('#cliente_serch').val(response['success'][0]['nombre']);
                    $('#cliente').val(response['success'][0]['idcliente']);
                    location.reload();
                }
            }else{
                var dataItem = {
                    nombre_cli: 'CONTADO',
                    desde: valores.desde,
                    URL: valores.URL,
                    idsale:valores.idsale
                }
                editarNombreCliente(dataItem);
            }
        }
    });
}
