<?php
require 'conexion.php';

// Número de registros recuperados
$numberofrecords = 500;

if(!isset($_POST['searchTerm'])){

   // Obtener registros a tarves de la consulta SQL
   $stmt = $db->prepare("select * from configuracion ORDER BY  nombre_emisor ASC LIMIT :limit");
   $stmt->bindValue(':limit', (int)$numberofrecords, PDO::PARAM_INT);
   $stmt->execute();
   $lista_productos = $stmt->fetchAll();

}else{
$numberofrecords = 5;

   $search = $_POST['searchTerm'];// Search text

   // Mostrar resultados
   $stmt = $db->prepare("select * from configuracion ORDER BY  nombre_emisor ASC LIMIT :limit");
   $stmt->bindValue(':nombre_emisor', '%'.$search.'%', PDO::PARAM_STR);
   $stmt->bindValue(':limit', (int)$numberofrecords, PDO::PARAM_INT);
   $stmt->execute();
   //Variable en array para ser procesado en el ciclo foreach
   $lista_productos = $stmt->fetchAll();

}

$response = array();

// Leer los datos de MySQL
foreach($lista_productos as $pro){
   $response[] = array(
      "id" => $pro['idconfigfact'],
      "text" => $pro['nombre_emisor']
   );
}

echo json_encode($response);
exit();
?>