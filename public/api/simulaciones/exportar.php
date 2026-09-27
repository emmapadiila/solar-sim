<?php
require __DIR__ . '/../../../src/bootstrap.php';

if (!Auth::check()) {
    http_response_code(401);
    exit('Debes iniciar sesión para exportar simulaciones');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID de simulación inválido');
}

$simulacion = SimulacionRepository::buscarDeUsuario($id, Auth::id());
if (!$simulacion) {
    http_response_code(404);
    exit('Simulación no encontrada');
}

header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="simulacion_' . $id . '.html"');
header('Cache-Control: no-cache, must-revalidate');

render('export/simulacion', ['simulacion' => $simulacion]);
