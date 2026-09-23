<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $contrasena = trim($_POST['password'] ?? '');

    try {
        $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena, direccion, edad, rol FROM tbl_usuarios WHERE nombre = ? LIMIT 1");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            if ($contrasena === $usuario['contrasena']) {
                $_SESSION['usuario'] = [
                    'id' => (int)$usuario['id_usuario'],
                    'id_usuario' => (int)$usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'direccion' => $usuario['direccion'],
                    'edad' => (int)$usuario['edad'],
                    'rol' => strtolower(trim($usuario['rol']))
                ];
                $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];

                echo json_encode(['success' => true, 'rol' => $_SESSION['usuario']['rol'], 'usuario' => $_SESSION['usuario']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }
}
?> 