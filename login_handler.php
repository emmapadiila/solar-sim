<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $contrasena = trim($_POST['password'] ?? '');

    if ($nombre === '' || $contrasena === '') {
        echo json_encode(['success' => false, 'message' => 'Complete todos los campos']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena, direccion, edad, rol FROM tbl_usuarios WHERE LOWER(nombre) = LOWER(?) LIMIT 1");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $storedPassword = (string)$usuario['contrasena'];
            $validPassword = password_verify($contrasena, $storedPassword) || ($storedPassword === $contrasena);

            if ($validPassword) {
                $usuario['rol'] = strtolower(trim((string)($usuario['rol'] ?? 'usuario')));
                if ($usuario['rol'] !== 'admin' && $usuario['rol'] !== 'usuario') {
                    $usuario['rol'] = 'usuario';
                }

                $_SESSION['usuario'] = [
                    'id' => (int)$usuario['id_usuario'],
                    'id_usuario' => (int)$usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'direccion' => $usuario['direccion'],
                    'edad' => (int)$usuario['edad'],
                    'rol' => $usuario['rol']
                ];
                $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
                session_regenerate_id(true);

                echo json_encode(['success' => true, 'rol' => $_SESSION['usuario']['rol'], 'usuario' => $_SESSION['usuario']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        error_log('Error login_handler.php: ' . $e->getMessage());
    }
}
?> 