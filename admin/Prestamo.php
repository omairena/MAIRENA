<?php

   
function connect(){
	//return new mysqli("localhost","root","1234","import");
	
	return new mysqli("localhost","okgmfvzr_sistema","14U~5hUqi(9@","okgmfvzr_sistema");

}
$con = connect();
if (!$con->set_charset("utf8")) {//asignamos la codificaci贸n comprobando que no falle
       die("Error cargando el conjunto de caracteres utf8");
}
?>

﻿<html>
	<head>
		<title>Configuracion de Documentos</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">	
		

	</head>
	<body>
		<div class="container">
			<h1>Configuracion a Modificar</h1>
			<form role="form" method="post" action="principal.php" id="form">
			    
 <div class="row ">
       
          <label >Clave</label>
         
                                            
<input type="password" name="clave"  class="form-control " value=""/>
      </div>
      
		
		  
			<div class="row">
				<div class="form-group">
	            	<input type="submit" class="btn-primary" value="Enviar">
	            </div>
			</div>	            
				
				<div class="row">
					
				</div>
			</form>
			
		</div>
		
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-1.8.3.js"></script>
	</body>
</html>
