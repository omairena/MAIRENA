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
$idconfifact=132;
                                            
 $categories = mysqli_query($con,"select * from facelectron where idconfigfact =$idconfifact  and idfacelectron = 12631");
                                            while ($cat=mysqli_fetch_array($categories)) {
                                              $xml=  $cat['rutaxml'];
                                              $clave=  $cat['clave'];
                                              echo $xml;
                                           $file_example = './sistema/public/XML/132/Tiquete/Envio/Tiquete#50626062300020641012200300001040000000002100000002.xml';
                                           echo $file_example; 
if (file_exists($file_example)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
   // header('Content-Disposition: attachment filename='.$nombre_nuevo_archivo_sin_formato;
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_example));
    ob_clean();
    flush();
    readfile($file_example);
    exit;
}
else {
    echo 'Archivo no disponible.';
}
                                            }


	
//	$clave='Oscar1987**';
	 //echo "<script>  document.location='mover.php?clave=$clave';</script>";
?>