<?php 
// conection.php 
function connect() { 
// Credenciales de la base de datos 
$host = 'localhost'; $user = 'okgmfvzr_sistema'; $password = '14U~5hUqi(9@'; $dbName = 'okgmfvzr_sistema'; $port = 3306; 
// Crear la conexión 
$conn = new mysqli($host, $user, $password, $dbName, $port); 
// Verificar conexión 
if ($conn->connect_error) { 
    // Evita exponer detalles en producción; aquí mostramos para desarrollo 
    die('Error de conexión (' . $conn->connect_errno . '): ' . $conn->connect_error); 
} 
    // Establecer UTF-8 
    if (!$conn->set_charset('utf8')) { die('Error cargando el conjunto de caracteres utf8: ' . $conn->error); } return $conn;
}
?>