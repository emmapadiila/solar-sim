<?php
require __DIR__ . '/../../../src/bootstrap.php';

require_method('POST');

run_api(function () {
    $datos = read_json_body();

    foreach (['nombre', 'contrasena', 'direccion', 'edad'] as $campo) {
        if (trim((string)($datos[$campo] ?? '')) === '') {
            json_response(['success' => false, 'message' => "El campo $campo es requerido"], 422);
        }
    }

    $nombre = trim((string)$datos['nombre']);
    $contrasena = trim((string)$datos['contrasena']);
    $direccion = trim((string)$datos['direccion']);
    $edad = filter_var($datos['edad'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 120]]);

    if (mb_strlen($nombre) > 50) {
        json_response(['success' => false, 'message' => 'El nombre no puede superar 50 caracteres'], 422);
    }
    if ($edad === false) {
        json_response(['success' => false, 'message' => 'La edad debe ser un número entre 1 y 120'], 422);
    }
    if (strlen($contrasena) < 6) {
        json_response(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 422);
    }
    if (UsuarioRepository::existeNombre($nombre)) {
        json_response(['success' => false, 'message' => 'El nombre de usuario ya existe'], 409);
    }

    UsuarioRepository::crear($nombre, $contrasena, $direccion, $edad);
    Auth::login(UsuarioRepository::buscarPorNombre($nombre));

    json_response([
        'success'  => true,
        'message'  => 'Usuario registrado exitosamente',
        'redirect' => 'dashboard.php',
    ], 201);
});
