<?php
require __DIR__ . '/../../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

if (!Auth::isAdmin()) {
    json_response(['success' => false, 'message' => 'Solo los administradores pueden gestionar mensajes'], 403);
}

run_api(function () {
    $id = filter_var(read_json_body()['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || !MensajeRepository::marcarLeido($id)) {
        json_response(['success' => false, 'message' => 'Mensaje no encontrado'], 404);
    }

    json_response(['success' => true]);
});
