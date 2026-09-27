<?php
require __DIR__ . '/../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

run_api(function () {
    $datos = read_json_body();

    $nombre = trim((string)($datos['name'] ?? ''));
    $email = trim((string)($datos['email'] ?? ''));
    $asunto = trim((string)($datos['subject'] ?? ''));
    $mensaje = trim((string)($datos['message'] ?? ''));

    if ($nombre === '' || $email === '' || $mensaje === '') {
        json_response(['success' => false, 'message' => 'Completa tu nombre, tu correo y el mensaje.'], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'El correo electrónico no es válido.'], 422);
    }
    if (mb_strlen($nombre) > 100 || mb_strlen($email) > 150 || mb_strlen($asunto) > 150 || mb_strlen($mensaje) > 5000) {
        json_response(['success' => false, 'message' => 'Alguno de los campos es demasiado largo.'], 422);
    }

    // El mensaje queda guardado y el equipo lo revisa en la sección Mensajes (solo administradores).
    MensajeRepository::crear(Auth::id(), $nombre, $email, $asunto !== '' ? $asunto : null, $mensaje);

    json_response(['success' => true, 'message' => "Recibimos tu mensaje. Te responderemos a $email."], 201);
});
