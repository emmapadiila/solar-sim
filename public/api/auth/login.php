<?php
require __DIR__ . '/../../../src/bootstrap.php';

require_method('POST');

run_api(function () {
    $datos = read_json_body();
    $nombre = trim((string)($datos['nombre'] ?? ''));
    $password = trim((string)($datos['password'] ?? '')); // se recorta igual que al registrarse

    if ($nombre === '' || $password === '') {
        json_response(['success' => false, 'message' => 'Por favor complete todos los campos'], 422);
    }

    $usuario = UsuarioRepository::buscarPorNombre($nombre);
    if (!$usuario || !UsuarioRepository::verificarContrasena($usuario, $password)) {
        json_response(['success' => false, 'message' => 'Usuario o contraseña incorrectos'], 401);
    }

    json_response([
        'success' => true,
        'message' => 'Inicio de sesión exitoso',
        'usuario' => Auth::login($usuario),
    ]);
});
