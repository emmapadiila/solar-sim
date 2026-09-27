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

require SRC_PATH . '/lib/fpdf/fpdf.php';
require SRC_PATH . '/ReporteSimulacion.php';

// Output('D') envía las cabeceras de descarga (application/pdf + attachment)
header('Cache-Control: no-cache, must-revalidate');
(new ReporteSimulacion($simulacion))->generar()->Output('D', 'simulacion_solar_' . $id . '.pdf');
