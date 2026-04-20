<?php  
ini_set('display_errors', 1);  
ini_set('display_startup_errors', 1);  
error_reporting(E_ALL);   

require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta  

// Cargar las variables de entorno desde el archivo .env  
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);  
$dotenv->load();  

// Configuración de la base de datos desde el archivo .env  
$servername = $_ENV['DB_HOST'];  
$username = $_ENV['DB_USERNAME'];  
$password = $_ENV['DB_PASSWORD'];  
$dbname = $_ENV['DB_DATABASE'];  

// Crear conexión  
$conn = new mysqli($servername, $username, $password, $dbname);  

// Comprobar conexión  
if ($conn->connect_error) {  
    echo json_encode(['error' => 'La conexión falló: ' . $conn->connect_error]);  
    exit;  
}  

// Establecer la codificación de la conexión a UTF-8  
$conn->set_charset("utf8mb4");  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Verificar si el dato fue enviado  
    if (isset($_POST['numeroDocumento'])) {  
        $numeroDocumento = $conn->real_escape_string(trim($_POST['numeroDocumento']));  
        if (empty($numeroDocumento)) {  
            echo json_encode(['error' => 'Número de documento no proporcionado.']);  
            exit;  
        }  
        
        // Mostrar el número de documento recibido para depuración  
        error_log("Número de documento recibido: " . $numeroDocumento);  

        // Preparar la consulta  
        $stmt = $conn->prepare("  
            SELECT f.tipodoc, f.estatushacienda, c.nombre, s.fecha_reenvio, s.total_comprobante, s.total_impuesto, g.nombre_emisor  
            FROM facelectron AS f  
            LEFT JOIN sales AS s ON f.idsales = s.idsale   
            LEFT JOIN clientes AS c ON s.idcliente = c.idcliente  
            LEFT JOIN configuracion AS g ON f.idconfigfact = g.idconfigfact  
            WHERE f.clave = ?  
        ");  

        if ($stmt === false) {  
            echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $conn->error]);  
            exit;  
        }  

        $stmt->bind_param("s", $numeroDocumento);  
        
        // Ejecutar la consulta  
        if (!$stmt->execute()) {  
            echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $stmt->error]);  
            exit;  
        }  

        $comprobanteResult = $stmt->get_result();  

        // Inicializar un array para almacenar resultados  
        $resultado = [];  

        // Comprobar si hay resultados  
        if ($comprobanteResult->num_rows > 0) {  
            // Obtener resultados como un array asociativo  
            $resultado = $comprobanteResult->fetch_assoc();  

            // Escapar caracteres especiales en los campos que pueden causar problemas  
            $resultado['nombre'] = htmlspecialchars($resultado['nombre'], ENT_QUOTES, 'UTF-8');  
            $resultado['nombre_emisor'] = htmlspecialchars($resultado['nombre_emisor'], ENT_QUOTES, 'UTF-8');  

            // Depurar los valores para ver si hay caracteres problemáticos  
            error_log("Nombre: " . $resultado['nombre']);  
            error_log("Nombre Emisor: " . $resultado['nombre_emisor']);  
        } else {  
            $resultado = ['error' => 'No se encontró el comprobante.'];  
        }  

        // Devolver el resultado como un JSON  
        header('Content-Type: application/json');  
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);  

        $stmt->close();  
    } else {  
        echo json_encode(['error' => 'numeroDocumento no está definido.']);  
    }  
} else {  
    echo json_encode(['error' => 'Solicitud inválida.']);  
}  

$conn->close();  
?>