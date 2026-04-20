<?php

   
function connect(){
	//return new mysqli("localhost","root","1234","import");
	
	return new mysqli("localhost","okgmfvzr_sistema","14U~5hUqi(9@","okgmfvzr_sistema");

}
$con = connect();
if (!$con->set_charset("utf8")) {//asignamos la codificaci贸n comprobando que no falle
       die("Error cargando el conjunto de caracteres utf8");
}


	$idconfifact = $_POST['category'];
	$nfecha = $_POST['nfecha'];
	$ncant = $_POST['ncant'];
	$docemitidos = $_POST['docemitidos'];
$conf_actual = $_POST['conf_actu'];	

if($ncant==0){
    echo $conf_actual;
     $ncant_act=$conf_actual;
    $docemitidos=0;
   
}else{
    $ncant_act=$ncant;
}
$originalDate = $nfecha;
$newDate = date("Y-m-d", strtotime($originalDate));
$mail_not = $_POST['mail_not'];
$notifica = $_POST['notifica'];
//echo $newDate;
	mysqli_query($con,"update configuracion set fecha_plan='$newDate',docs=$ncant_act+$docemitidos, noti = '$notifica', mail_not = '$mail_not'  where idconfigfact='$idconfifact'")or die(mysqli_error());
	
	
	$clave='Oscar1987**';
	 echo "<script>  document.location='principal.php?clave=$clave';</script>";
?>