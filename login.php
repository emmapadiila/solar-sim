<?php
header('Content-Type: application/json');
session_start();

// Limpiar cualquier salida anterior
ob_clean();

// Destruir la sesión anterior si existe
if (isset($_SESSION)) {
    session_destroy();
    session_start();
}

$response = ['success' => false];

// Obtener datos del POST
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

if (!$data) {
    $response['message'] = "No se recibieron datos o el formato es incorrecto";
    echo json_encode($response);
    exit;
}

$nombre = trim($data['nombre'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($nombre) || empty($password)) {
    $response['message'] = "Por favor complete todos los campos";
    echo json_encode($response);
    exit;
}

try {
    $host = "localhost";
    $db = "paneles_solares";
    $user = "root";
    $pass = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta para obtener el usuario incluyendo el rol
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena, direccion, edad, rol FROM tbl_usuarios WHERE nombre = :nombre");
    $stmt->execute(['nombre' => $nombre]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && $password === $usuario['contrasena']) {
        // Limpiar datos sensibles
        unset($usuario['contrasena']);
        
        // Asegurar que el rol esté definido y sea válido
        if (!isset($usuario['rol']) || empty($usuario['rol'])) {
            $usuario['rol'] = 'usuario'; // Rol por defecto
        }
        
        // Normalizar el rol
        $usuario['rol'] = strtolower(trim($usuario['rol']));
        
        // Verificar que el rol sea válido
        if ($usuario['rol'] !== 'admin' && $usuario['rol'] !== 'usuario') {
            $usuario['rol'] = 'usuario'; // Forzar rol por defecto si no es válido
        }
        
        // Guardar usuario en sesión
        $_SESSION['usuario'] = $usuario;
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        
        $response['success'] = true;
        $response['message'] = "Inicio de sesión exitoso";
        $response['usuario'] = $usuario;
        
        // Log para depuración
        error_log("Usuario logueado: " . $usuario['nombre'] . " con rol: " . $usuario['rol']);
    } else {
        $response['message'] = "Usuario o contraseña incorrectos";
    }
} catch(PDOException $e) {
    $response['message'] = "Error de conexión: " . $e->getMessage();
    error_log("Error en login.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
?> 