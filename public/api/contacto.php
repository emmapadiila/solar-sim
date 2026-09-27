<?php
require __DIR__ . '/../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

run_api(function () {
    $datos = read_json_body();

    $nombre = trim((string)($datos['name'] ?? ''));
    $email = trim((string)($datos['email'] ?? ''));
    $mensaje = trim((string)($datos['message'] ?? ''));

    if ($nombre === '' || $email === '' || $mensaje === '') {
        json_response(['success' => false, 'message' => 'Por favor, completa todos los campos requeridos (Nombre, Correo Electrónico, Mensaje).'], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'El formato del correo electrónico es inválido.'], 422);
    }

    // SIMULACIÓN: todavía no se envía ningún correo; aquí iría mail() o PHPMailer.
    json_response(['success' => true, 'message' => '¡Gracias! Tu mensaje ha sido enviado exitosamente.']);
});
