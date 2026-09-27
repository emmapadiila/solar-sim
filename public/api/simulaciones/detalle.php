<?php
require __DIR__ . '/../../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('GET');

run_api(function () {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        json_response(['success' => false, 'message' => 'ID de simulación inválido'], 400);
    }

    $simulacion = SimulacionRepository::buscarDeUsuario($id, Auth::id());
    if (!$simulacion) {
        json_response(['success' => false, 'message' => 'Simulación no encontrada'], 404);
    }

    $simulacion['ciudad'] = Calculadora::nombreCiudad($simulacion['ubicacion']);
    json_response(['success' => true, 'simulacion' => $simulacion]);
});
