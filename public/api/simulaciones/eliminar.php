<?php
require __DIR__ . '/../../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

run_api(function () {
    $id = filter_var(read_json_body()['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id) {
        json_response(['success' => false, 'message' => 'ID de simulación inválido'], 400);
    }

    if (!SimulacionRepository::eliminar($id, Auth::id())) {
        json_response(['success' => false, 'message' => 'Simulación no encontrada'], 404);
    }

    json_response(['success' => true, 'message' => 'Simulación eliminada']);
});
