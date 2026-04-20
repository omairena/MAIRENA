<?php
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
	
//$con = new mysqli("localhost","root","","chartjs10"); // Conectar a la BD
$sql = "select MONTH(fecha_creada) Mes, COUNT(idsale) total_mes from sales where idconfigfact= '$cuenta' and fecha_creada BETWEEN '$nfecha' AND '$nfechaf'  GROUP BY Mes  "; // Consulta SQL

$query = $con->query($sql); // Ejecutar la consulta SQL
$data = array(); // Array donde vamos a guardar los datos
while($r = $query->fetch_object()){ // Recorrer los resultados de Ejecutar la consulta SQL
    $data[]=$r; // Guardar los resultados en la variable $data
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Administrativo FE SAN ESTEBAN</title>
    <script src="chart.min.js"></script>
</head>
<body>
<h1>Consumo de Documentos por Cuenta</h1>
  <a class="foot-menu-item mbr-fonts-style display-7" href="/sistema/admin/Prestamo.php">Salir</a>
    <a class="foot-menu-item mbr-fonts-style display-7" href="index1.php">Grafico General</a>
	<form role="form" method="post" action="index.php" id="form">
	     <div class="row ">
       
          <label >Doc Emitidos:</label>
          <?php
            $categories = mysqli_query($con,"select count(idsale) as cant  from sales where idconfigfact = '$cuenta' and fecha_creada BETWEEN '$nfecha' AND '$nfechaf' ");
                                            while ($cat=mysqli_fetch_array($categories)) { 
                                            
                                            $doc_emitidos=$cat['cant'];
                                            }?>
                                            
<input type="text" name="docemitidos"  class="form-control " value="<?php echo $doc_emitidos; ?>"/>
      </div>
<div class="row ">
      <label >Cuenta:</label>
                                        <select class="form-control" id="category" name="category" onchange="load(1);">
                                            
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
<canvas id="chart1" style="width:100%;" height="100"></canvas>
<script>
var ctx = document.getElementById("chart1");
var data = {
        labels: [ 
        <?php foreach($data as $d):?>
        "Mes: <?php echo $d->Mes?> Cant Docs: <?php echo $d->total_mes;?>", 
        
        <?php endforeach; ?>
        ],
        datasets: [{
            label: 'Cant Docs' , 
            data: [
               
        <?php foreach($data as $d):?>
         
        <?php echo $d->total_mes;?>, 
        
        <?php endforeach; ?>
            ],
            backgroundColor: "#3898db",
            borderColor: "#9b59b6",
            borderWidth: 2
        }]
        
    };
    
    
         
        
        
var options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    };
var chart1 = new Chart(ctx, {
    type: 'bar', /* valores: line, bar*/
    data: data,
    options: options
});
</script>
</body>
</html>