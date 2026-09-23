<?php
header('Content-Type: application/json');
session_start();
ob_clean();

$response = ['success' => false];

$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

if (!is_array($data)) {
    $response['message'] = "No se recibieron datos o el formato es incorrecto";
    echo json_encode($response);
    exit;
}

$required_fields = ['nombre', 'contrasena', 'direccion', 'edad'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
        $response['message'] = "El campo " . $field . " es requerido";
        echo json_encode($response);
        exit;
    }
}

if (!is_numeric($data['edad']) || (int)$data['edad'] < 1) {
    $response['message'] = "La edad debe ser un número válido mayor a 0";
    echo json_encode($response);
    exit;
}

$contrasena = trim((string)$data['contrasena']);
if (strlen($contrasena) < 6) {
    $response['message'] = "La contraseña debe tener al menos 6 caracteres";
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

    $nombre = trim((string)$data['nombre']);
    $direccion = trim((string)$data['direccion']);
    $edad = (int)$data['edad'];
    $rol = 'usuario';

    $stmt = $conn->prepare("SELECT 1 FROM tbl_usuarios WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
    $stmt->execute(['nombre' => $nombre]);

    if ($stmt->fetch()) {
        $response['message'] = "El nombre de usuario ya existe";
        echo json_encode($response);
        exit;
    }

    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO tbl_usuarios (nombre, contrasena, direccion, edad, rol) VALUES (:nombre, :contrasena, :direccion, :edad, :rol)");
    $stmt->execute([
        ':nombre' => $nombre,
        ':contrasena' => $hashedPassword,
        ':direccion' => $direccion,
        ':edad' => $edad,
        ':rol' => $rol
    ]);

    $response['success'] = true;
    $response['message'] = "Usuario registrado exitosamente";
    $response['usuario'] = [
        'nombre' => $nombre,
        'direccion' => $direccion,
        'edad' => $edad,
        'rol' => $rol
    ];
} catch (PDOException $e) {
    $response['message'] = "Error en el servidor: " . $e->getMessage();
    error_log("Error en registro_handler.php: " . $e->getMessage());
}

echo json_encode($response);
?> 