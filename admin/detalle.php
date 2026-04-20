<?php

   
function connect(){
	//return new mysqli("localhost","root","1234","import");
	
	return new mysqli("localhost","okgmfvzr_sistema","14U~5hUqi(9@","okgmfvzr_sistema");

}
$con = connect();
if (!$con->set_charset("utf8")) {//asignamos la codificaci贸n comprobando que no falle
       die("Error cargando el conjunto de caracteres utf8");
}

    $idsale = $_GET['idsale'];

	
?>


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
        <th>Codigo Producto</th>
        <th>Descripcion</th>
       <th>Cantidad</th>
        <th>Valor Neto</th>
          <th>Valor_impuesto</th>
           <th>Valor_descuento</th>
            <th>Impuesto_prc</th>
           <th>Descuento_prc</th>
           
             <th>Existe_exoneracion</th>
             <th>Costo</th>
              <th>Exo_monto</th>
               <th>Exo</th>
               <th>Fecha Exoneracion</th>
         
     </tr>
     </thead>
     <tbody>
          <?php

 
    $query=mysqli_query($con," 
    
    
    SELECT *
FROM sales_item 

 where idsales = '$idsale'   
    
    
    ")or die(mysqli_error());
    
    
    
    
    $i=1;
    while($row=mysqli_fetch_array($query)){

       
?>
     <tr>
         
     <td> <?php echo $row['idsales'];?></td>
      <td> <?php echo $row['codigo_producto'];?></td>
      <td> <?php echo $row['nombre_producto'];?></td>
       <td> <?php echo $row['cantidad'];?></td>
        <td> <?php echo number_format($row['valor_neto'], 2, ',','.');?></td>
        <td> <?php echo number_format($row['valor_impuesto'], 2, ',','.');?></td>
        <td> <?php echo number_format($row['valor_descuento'], 2, ',','.');?></td>
         <td> <?php echo number_format($row['impuesto_prc'], 2, ',','.');?></td>
        <td> <?php echo number_format($row['descuento_prc'], 2, ',','.');?></td>
       
    <td> <?php echo $row['existe_exoneracion'];?></td>
     <td> <?php echo number_format( $row['costo_utilidad'], 2, ',','.');?></td>
      <td> <?php echo number_format($row['exo_monto'], 2, ',','.');?></td>
       <td> <?php echo $row['exo'];?></td>
        <td> <?php echo $row['fechaex'];?></td>
 
     
         

                 </tr>
 <?php
    }
    
?>
         
         
         
     </tr>
     </tbody>
     </table>