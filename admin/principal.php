<?php

  	$clave = $_POST['clave']; 
  	
  	if(empty($clave)){
  	    	$clave = $_GET['clave']; 
  	    
  	}
  	
  	if($clave!='Oscar1987**'){
  	    	echo '<script>alert("Clave Incorrecta")</script>';
	  echo "<script>  document.location='/';</script>";
  	}
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuracion de Cuentas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha256-aAr2Zpq8MZ+YA/D6JtRD3xtrwpEz2IqOS+pWD/7XKIw=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha256-OFRAJNoaD8L3Br5lglV7VyLRf0itmoBzWUoM+Sji4/8=" crossorigin="anonymous"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
</head>
	<body>
		<div class="container">
			<h1>Configuracion a Modificar</h1>
			<form role="form" method="post" action="procesa.php" id="form">
			    
  
      <div class="col-md-3 form-group">
		  <select class="select2 form-control" data-rel="chosen"  name="category" id="category">
                            <?php 
                              $categories = mysqli_query($con,"select * from configuracion");
                                while ($row = $categories->fetch_assoc()) {
                                    echo "<option value=".$row["idconfigfact"].">".$row["idconfigfact"]."->".$row["nombre_emisor"]."</option>";
                                }
                            ?>
                        </select>
		  </div>  
			<div class="row">
				<div class="form-group">
	            	<input type="submit" class="btn-primary" value="Enviar">
	            </div>
			</div>	            
				
				<div class="row">
					
				</div>
			</form>
			 <a class="foot-menu-item mbr-fonts-style display-7" href="/sistema/admin/grafico/index.php">Grafico</a>
		 	 <a class="foot-menu-item mbr-fonts-style display-7" href="general.php">Ver Documentos</a>
		 	  <a class="foot-menu-item mbr-fonts-style display-7" href="cuentas.php">Ver Cuentas</a>
		 	  <a class="foot-menu-item mbr-fonts-style display-7" href="mover.php">Mover Archivos</a>
		</div>
		 <script type="text/javascript">
        $('.select2').select2({});
    </script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-1.8.3.js"></script>
	</body>
</html>

 