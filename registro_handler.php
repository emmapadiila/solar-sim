<?php
header('Content-Type: application/json');

$response = ['success' => false];

$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

if (!$data) {
    $response['message'] = "No se recibieron datos o el formato es incorrecto";
    echo json_encode($response);
    exit;
}

$required_fields = ['nombre', 'contrasena', 'direccion', 'edad'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        $response['message'] = "El campo " . $field . " es requerido";
        echo json_encode($response);
        exit;
    }
}

if (!is_numeric($data['edad']) || $data['edad'] < 1) {
    $response['message'] = "La edad debe ser un número válido mayor a 0";
    echo json_encode($response);
    exit;
}

try {
    $host = "localhost";
    $dbname = "paneles_solares";
    $username = "root";
    $password = "";

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM tbl_usuarios WHERE nombre = :nombre");
    $stmt->execute(['nombre' => trim($data['nombre'])]);
    
    if ($stmt->rowCount() > 0) {
        $response['message'] = "El nombre de usuario ya existe";
        echo json_encode($response);
        exit;
    }

    $nombre = trim($data['nombre']);
    $contrasena = trim($data['contrasena']);
    $direccion = trim($data['direccion']);
    $edad = intval($data['edad']);
    $rol = 'usuario'; // Rol por defecto

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO tbl_usuarios (nombre, contrasena, direccion, edad, rol) VALUES (:nombre, :contrasena, :direccion, :edad, :rol)");
    
    if ($stmt->execute([
        ':nombre' => $nombre,
        ':contrasena' => $contrasena,
        ':direccion' => $direccion,
        ':edad' => $edad,
        ':rol' => $rol
    ])) {
        $response['success'] = true;
        $response['message'] = "Usuario registrado exitosamente";
        $response['usuario'] = [
            'nombre' => $nombre,
            'direccion' => $direccion,
            'edad' => $edad,
            'rol' => $rol
        ];
    }
} catch(PDOException $e) {
    $response['message'] = "Error en el servidor: " . $e->getMessage();
    error_log("Error en registro_handler.php: " . $e->getMessage());
}

echo json_encode($response);
?> 