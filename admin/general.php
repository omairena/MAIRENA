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

	<form role="form" method="post" action="general.php" id="form">
	     
<div class="row ">
      <label >Cuenta:</label>
                                        <select class="form-control" id="category" name="category" onchange="load(1);">
                                             <option value="">TODAS</option>
                                            <?php
                                            if(empty($cuenta)){
                                               $categories = mysqli_query($con,"select * from configuracion ");  
                                            }else{
                                           $categories = mysqli_query($con,"select * from configuracion where idconfigfact = '$cuenta' ");
                                            }
                                            while ($cat=mysqli_fetch_array($categories)) { ?>
                                            <option value="<?php echo $cat['idconfigfact']; ?>"><?php echo $cat['idconfigfact']; ?>--><?php echo $cat['nombre_emisor'];?></option>
                                            <?php } ?>
                                            
                                        </select>
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

<table class="tbl" id="myTable">
     <thead>
     <tr>
        <th>ID</th>
        <th>Numero Factura</th>
        <th>Fecha</th>
       <th>Cliente</th>
        <th>Estado Hacienda</th>
          <th>Monto</th>
           <th>Msj Hacienda</th>
         <th>Acciones</th>
     </tr>
     </thead>
     <tbody>
          <?php
if($cuenta>0){
 
    $query=mysqli_query($con," 
    
    
    SELECT sales.idsale,sales.numero_documento,sales.total_comprobante,sales.fecha_creada, clientes.nombre,facelectron.estatushacienda,facelectron.mensajehacienda
FROM sales 
LEFT JOIN clientes ON sales.idcliente = clientes.idcliente
LEFT JOIN facelectron ON sales.idsale = facelectron.idsales
 where sales.fecha_creada BETWEEN '$nfecha' AND '$nfechaf' and sales.idconfigfact='$cuenta'   
    
    
    ")or die(mysqli_error());
}else{
    
    $query=mysqli_query($con," 
    
    
    SELECT sales.idsale,sales.numero_documento,sales.total_comprobante,sales.fecha_creada, clientes.nombre,facelectron.estatushacienda,facelectron.mensajehacienda
FROM sales 
LEFT JOIN clientes ON sales.idcliente = clientes.idcliente
LEFT JOIN facelectron ON sales.idsale = facelectron.idsales
 where sales.fecha_creada BETWEEN '$nfecha' AND '$nfechaf'    
    
    
    ")or die(mysqli_error());
} 
    
    
    
    $i=1;
    while($row=mysqli_fetch_array($query)){

       
?>
     <tr>
         
     <td> <?php echo $row['idsale'];?></td>
      <td> <?php echo $row['numero_documento'];?></td>
      <td> <?php echo $row['fecha_creada'];?></td>
       <td> <?php echo $row['nombre'];?></td>
       <td> <?php echo $row['estatushacienda'];?></td>
    <td> <?php echo number_format($row['total_comprobante'], 2, ',','.');?></td>
    <td> <?php echo $row['mensajehacienda'];?></td>
  <td> <a class='btn btn-success'  href= 'detalle.php?idsale=<?php echo $row['idsale'];?>'> Detalle </a></td>
     
         

                 </tr>
 <?php
    }
    
?>
         
         
         
     </tr>
     </tbody>
     </table>