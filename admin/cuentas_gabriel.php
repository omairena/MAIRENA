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

   
			$inicio = $_POST['inicio']; 
				$fin = $_POST['fin']; 

?>
<!DOCTYPE html>
<html lang="en">
    
    <head>
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
	<form role="form" method="post" action="cuentas_gabriel.php" id="form">
	     <div class="row ">
       
          <label >RANGO FECHAS:</label>
          
                                            
<input type="date" name="inicio"  class="form-control " value="<?php echo $inicio;?>" />
<input type="date" name="fin"  class="form-control " value="<?php echo $fin;?>" />
      </div>

		
      <div class="row">
				<div class="form-group">
	            	<input type="submit" class="btn-primary" value="Consultar">
	            </div>
			</div>	  
			</form> 
	<style>
  .tbl { border-collapse: collapse; width:300px; }
  .tbl th, .tbl td { padding: 5px; border: solid 1px #777; }
  .tbl th { background-color: lightblue; }
  .tbl-separate { border-collapse: separate; border-spacing: 5px;}
</style>
 
Filtro de Datos <input id="searchTerma" type="text" onkeyup="doSearcha()" />
 <table class="tbl" id="myTable">
     <thead>
     <tr>
          <th onclick="sortTable(0, 'int')">ID</th>
    <th onclick="sortTable(1, 'str')">CEDULA</th>
    <th onclick="sortTable(2, 'str')">NOMBRE</th>
    
        
       <th onclick="sortTable(3, 'int')">VENCIMIENTO</th>
        <th onclick="sortTable(4, 'int')" >EMISIONES TOTALES</th>
          <th onclick="sortTable(5, 'int')" >CONF ACTUAL</th>
           <th onclick="sortTable(6, 'int')" >DOCS X EMITIR</th>
         <!--<th>Acciones</th>-->
     </tr>
     </thead>
     <tbody>
          <?php
$id_user=122;
if(empty($inicio)){
     $query=mysqli_query($con," SELECT * FROM configuracion where gnl='$id_user'  ")or die(mysqli_error());
}else{
	    $query=mysqli_query($con," SELECT * FROM configuracion where gnl='$id_user' and fecha_plan between '$inicio' and '$fin'  ")or die(mysqli_error());
	
}   
    
    
    
    
    $i=0;
    while($row=mysqli_fetch_array($query)){

       $idconfigfact=$row['idconfigfact'];
?>
     <tr>
         
     <td> <?php echo $row['idconfigfact'];?></td>
      <td> <?php echo $row['numero_id_emisor'];?></td>
      <td> <?php echo $row['nombre_emisor'];?></td>
     
       <td> <?php echo $row['fecha_plan'];?></td>
       <?php
       $querys=mysqli_query($con,"SELECT count(idsale) as cant FROM sales where idconfigfact='$idconfigfact' ")or die(mysqli_error());
        while($rows=mysqli_fetch_array($querys)){
        $docs=$rows['cant'];
    }
       ?>
       <td> <?php echo $docs;?></td>
       <td> <?php echo $row['docs'];?></td>
       <td> <?php echo $row['docs']-$docs;?></td>
   
  <!--<td> <a class='btn btn-success'  href= 'detalle.php?idsale=<?php echo $row['idsale'];?>'> Detalle </a></td>-->
     
         

                 </tr>
 <?php
    }
    
?>
         
         
         
     </tr>
     </tbody>
     </table>
     
     </html>
     
     <script>
/**
 * Funcion para ordenar una tabla... tiene que recibir el numero de columna a
 * ordenar y el tipo de orden
 * @param int n
 * @param str type - ['str'|'int']
 */
function sortTable(n,type) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
 
  table = document.getElementById("myTable");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc";
 
  /*Make a loop that will continue until no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /*Loop through all table rows (except the first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare, one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /*check if the two rows should switch place, based on the direction, asc or desc:*/
      if (dir == "asc") {
        if ((type=="str" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) > parseFloat(y.innerHTML))) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if ((type=="str" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) < parseFloat(y.innerHTML))) {
          //if so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /*If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

function doSearcha()
        {
            const tableReg = document.getElementById('myTable');
            const searchText = document.getElementById('searchTerma').value.toLowerCase();
            let total = 0;
 
            // Recorremos todas las filas con contenido de la tabla
            for (let i = 1; i < tableReg.rows.length; i++) {
                // Si el td tiene la clase "noSearch" no se busca en su cntenido
                if (tableReg.rows[i].classList.contains("noSearch")) {
                    continue;
                }
 
                let found = false;
                const cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
                // Recorremos todas las celdas
                for (let j = 0; j < cellsOfRow.length && !found; j++) {
                    const compareWith = cellsOfRow[j].innerHTML.toLowerCase();
                    // Buscamos el texto en el contenido de la celda
                    if (searchText.length == 0 || compareWith.indexOf(searchText) > -1) {
                        found = true;
                        total++;
                    }
                }
                if (found) {
                    tableReg.rows[i].style.display = '';
                } else {
                    // si no ha encontrado ninguna coincidencia, esconde la
                    // fila de la tabla
                    tableReg.rows[i].style.display = 'none';
                }
            }
 
            // mostramos las coincidencias
            const lastTR=tableReg.rows[tableReg.rows.length-1];
            const td=lastTR.querySelector("td");
            lastTR.classList.remove("hide", "red");
            if (searchText == "") {
                lastTR.classList.add("hide");
            } else if (total) {
                td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");
            } else {
                lastTR.classList.add("red");
                td.innerHTML="No se han encontrado coincidencias";
            }
        }

</script>
 