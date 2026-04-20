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

    $cuenta = $_POST['category'];
	$nfecha = $_POST['nfecha'];
	$nfechaf = $_POST['nfechaf'];
	
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
	<form role="form" method="post" action="recepcion.php" id="form">
	     
<div class="row ">
      <label >Cuenta:</label>
      
       <div class="col-md-3 form-group">
		  <select class="select2 form-control" data-rel="chosen"  name="category" id="category">
		       <option value="0">SELECCIONE></option>
                            <?php
                                            if(empty($cuenta)){
                                               $categories = mysqli_query($con,"select * from configuracion ");  
                                            }else{
                                           $categories = mysqli_query($con,"select * from configuracion ");
                                            }
                                            while ($cat=mysqli_fetch_array($categories)) { ?>
                                            <option value="<?php echo $cat['idconfigfact']; ?>"><?php echo $cat['idconfigfact']; ?>--><?php echo $cat['nombre_emisor'];?></option>
                                            <?php } ?>
                        </select>
		  </div>  
		  </div>
                                        
                      
		<div class="row ">
       
          <label >Fecha Inicio:</label>
         
                                            
<input type="date" name="nfecha"  class="form-control " value="<?php echo $nfecha;?>"/>
      </div>
      <div class="row ">
       
          <label >Fecha Final:</label>
         
                                            
<input type="date" name="nfechaf"  class="form-control " value="<?php echo $nfechaf;?>"/>
      </div>
		
      <div class="row">
				<div class="form-group">
	            	<input type="submit" class="btn-primary" value="Enviar">
	            </div>
			</div>	  
			</form>  
	<style>
  .tbl { border-collapse: collapse; width:300px; }
  .tbl th, .tbl td { padding: 5px; border: solid 1px #777; }
  .tbl th { background-color: lightblue; }
  .tbl-separate { border-collapse: separate; border-spacing: 5px;}
</style>
<div class="form-group">
 <input type="text" class="form-control pull-right" style="width:20%" id="search" placeholder="Type to search table...">
</div>
<table class="tbl" id="myTable">
     <thead>
     <tr>
        <th>ID</th>
         <th>Clave</th>
        <th>Numero Factura</th>
        <th>Fecha Doc</th>
        <th>Fecha Recp</th>
       <th>Proveedor</th>
       <th>Contribuyente</th>
        <th>Estado Hacienda</th>
          <th>Monto</th>
          
         <th>Acciones</th>
     </tr>
     </thead>
     <tbody>
          <?php
if($idconfifact>0){
 $query=mysqli_query($con,"     SELECT receptor.idconfigfact,receptor.clave,receptor.idreceptor, receptor.consecutivo_doc_receptor,receptor.fecha,receptor.fecha_xml_envio,receptor.nombre_emisor,receptor.estatus_hacienda,receptor.total_comprobante,configuracion.nombre_emisor as nombre_contr,configuracion.idconfigfact
FROM receptor 
LEFT JOIN configuracion ON receptor.idconfigfact = configuracion.idconfigfact

 where receptor.fecha BETWEEN '$nfecha' AND '$nfechaf' and receptor.idconfigfact ='$idconfifact'  order by receptor.idreceptor desc
  ")or die(mysqli_error());
}else{
 
    $query=mysqli_query($con," 
    
   


    

  SELECT receptor.idconfigfact,receptor.clave,receptor.idreceptor, receptor.consecutivo_doc_receptor,receptor.fecha_xml_envio,receptor.fecha,receptor.nombre_emisor,receptor.estatus_hacienda,receptor.total_comprobante,configuracion.nombre_emisor as nombre_contr,configuracion.idconfigfact
FROM receptor 
LEFT JOIN configuracion ON receptor.idconfigfact = configuracion.idconfigfact

 where receptor.fecha BETWEEN '$nfecha' AND '$nfechaf' order by receptor.idreceptor desc

    
    ")or die(mysqli_error());
}
    
    
    //receptor.fecha_xml_envio
    $i=1;
    while($row=mysqli_fetch_array($query)){

       
?>
     <tr>
         <td> <?php echo $row['idreceptor'];?></td>
     <td> <?php echo $row['clave'];?></td>
      <td> <?php echo $row['consecutivo_doc_receptor'];?></td>
      <td> <?php echo $row['fecha_xml_envio'];?></td>
       <td> <?php echo $row['fecha'];?></td>
       <td> <?php echo $row['nombre_emisor'];?></td>
        <td><?php echo $row['idconfigfact'];?> <?php echo $row['nombre_contr'];?></td>
       <td> <?php echo $row['estatus_hacienda'];?></td>
    <td> <?php echo number_format($row['total_comprobante'], 2, ',','.');?></td>
   
  <td> <a class='btn btn-success'  href= 'detalle.php?idsale=<?php echo $row['idsale'];?>'> Detalle </a></td>
     
         

                 </tr>
 <?php
    }
    
?>
         
         
         
     </tr>
     </tbody>
     </table>
     	
  
     <script>
 // Write on keyup event of keyword input element
 $(document).ready(function(){
 $("#search").keyup(function(){
 _this = this;
 // Show only matching TR, hide rest of them
 $.each($("#mytable tbody tr"), function() {
 if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
 $(this).hide();
 else
 $(this).show();
 });
 });
});
</script>

	 <script type="text/javascript">
        $('.select2').select2({});
    </script>
</body>
</html>