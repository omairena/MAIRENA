$(document).ready(function() {
 	$('#tipo_cliente').change(function() {
 		if ($(this).val() === '1') {
 			$('#plazos').css( "display", "none");
 			$('#condicion').css( "display", "none");
 		}else{
 			$('#condicion').css( "display", "block");	
 		}
 	});

	$('#condicionventa').change(function() {
 		if ($(this).val() === '01') {
 			$('#plazos').css( "display", "none");
 		}else{
 			$('#plazos').css( "display", "block");	
 		}
 	});
 	
   	$(function () {
  		$('[data-toggle="tooltip"]').tooltip()
	})


	$('#condición_venta').change(function() {
 		if ($(this).val() === '01') {
 			$('#pl_credito').css( "display", "none");
 		}else{
 			$('#pl_credito').css( "display", "block");	
 		}
 	});

 	$('#medio_pago').change(function() {
 		if ($(this).val() === '01') {
 			alert('Selecciono Efectivo');
 			$('#referencia_p').css( "display", "none");
 		}else{
 			$('#referencia_p').css( "display", "block");
 		}
 	});
 	

});