<?php
// Declaramos las credenciales de conexion
$server = "localhost";
$username = "okgmfvzr_sistema";
$password = "14U~5hUqi(9@";
$dbname = "okgmfvzr_sistema";

// Creamos la conexion MySQL
try{
	//Creamos la variable de conexión $b
   $db = new PDO("mysql:host=$server;dbname=$dbname","$username","$password");
   $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
   die('Error: No se puede conectar a MySQL');
}
?>


