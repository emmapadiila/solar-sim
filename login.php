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

$nombre = trim((string)($data['nombre'] ?? ''));
$password = trim((string)($data['password'] ?? ''));

if ($nombre === '' || $password === '') {
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

    $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena, direccion, edad, rol FROM tbl_usuarios WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
    $stmt->execute(['nombre' => $nombre]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $storedPassword = (string)$usuario['contrasena'];
        $validPassword = password_verify($password, $storedPassword) || ($storedPassword === $password);

        if ($validPassword) {
            $usuario['rol'] = strtolower(trim((string)($usuario['rol'] ?? 'usuario')));
            if ($usuario['rol'] !== 'admin' && $usuario['rol'] !== 'usuario') {
                $usuario['rol'] = 'usuario';
            }

            $sessionUser = [
                'id' => (int)$usuario['id_usuario'],
                'id_usuario' => (int)$usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'direccion' => $usuario['direccion'],
                'edad' => (int)$usuario['edad'],
                'rol' => $usuario['rol']
            ];

            $_SESSION['usuario'] = $sessionUser;
            $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
            session_regenerate_id(true);

            $response['success'] = true;
            $response['message'] = "Inicio de sesión exitoso";
            $response['usuario'] = $sessionUser;
        } else {
            $response['message'] = "Usuario o contraseña incorrectos";
        }
    } else {
        $response['message'] = "Usuario o contraseña incorrectos";
    }
} catch (PDOException $e) {
    $response['message'] = "Error de conexión: " . $e->getMessage();
    error_log("Error en login.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
?> 