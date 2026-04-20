
<?php
	$idconfifact = $_POST['category'];
   
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
			   <a class="foot-menu-item mbr-fonts-style display-7" href="https://snesteban.com">Salir</a>
			<form role="form" method="post" action="actualiza.php" id="form">
			    
 <div class="row ">
      <label >Cuenta:</label>
                                        <select class="form-control" id="category" name="category" onchange="load(1);">
                                            
                                            <?php
                                           $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { ?>
                                            <option value="<?php echo $cat['idconfigfact']; ?>"><?php echo $cat['idconfigfact']; ?>--><?php echo $cat['nombre_emisor'];?></option>
                                            <?php } ?>
                                            
                                        </select>
                                    </div>   
     
		 <div class="row ">
       
          <label >Doc Emitidos:</label>
          <?php
            $categories = mysqli_query($con,"select count(idsale) as cant  from sales where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $doc_emitidos=$cat['cant'];
                                            }?>
                                            
<input type="text" name="docemitidos"  class="form-control " value="<?php echo $doc_emitidos; ?>"/>
      </div>
      <div class="row ">
       
          <label >Cant Conf Actual:</label>
          <?php
            $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $docs=$cat['docs'];
                                            }?>
                                            
<input type="text" name="conf_actu"  class="form-control " value="<?php echo $docs; ?>"/>
      </div>
       <div class="row ">
       
          <label >Doc Recepcionados:</label>
          <?php
            $categories = mysqli_query($con,"select count(idreceptor) as cant  from receptor where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $doc_recep=$cat['cant'];
                                            }?>
                                            
<input type="text" name="doc_recp"  class="form-control " value="<?php echo $doc_recep; ?>"/>
      </div>
      <div class="row ">
       
          <label >Fecha Vencimiento:</label>
          <?php
            $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $fecha_plan=$cat['fecha_plan'];
                                            }?>
                                            
<input type="date" name="fecha1"  class="form-control " value="<?php echo $fecha_plan; ?>"/>
      </div>
      
      <div class="row ">
       
          <label >Nueva Fecha de Vencimiento:</label>
         
                                            
<input type="date" name="nfecha"  class="form-control " value="<?php echo $fecha_plan; ?>"/>
      </div>
	   <div class="row ">
       
          <label >Nueva Cantidad:</label>
         
                                            
<input type="text" name="ncant"  class="form-control " value="0"/>
      </div>
      <div class="row ">
       
          <label >Notifica  (0="Si"/ 1="No"):</label>
         
                            <?php
            $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $docs=$cat['noti'];
                                            
                                            }?>                 
<input type="text" name="notifica"  class="form-control " value="<?php echo $docs; ?>"/>
      </div>
       <br>  
       <div class="row ">
       
          <label >Mail Notifica:</label>
         
                            <?php
            $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$idconfifact'");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                           
                                            $mail=$cat['mail_not'];
                                            }?>                 
<input type="text" name="mail_not"  class="form-control " value="<?php echo $mail; ?>"/>
      </div>
      <br>  
			<div class="row">
				<div class="form-group">
	            	<input type="submit" class="btn-primary" value="Enviar">
	            </div>
			</div>	            
				
		
     
			</form>
		</div>
		
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-1.8.3.js"></script>
	</body>
</html>

 