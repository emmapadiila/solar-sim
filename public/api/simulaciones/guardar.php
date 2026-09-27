<?php
require __DIR__ . '/../../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

run_api(function () {
    [$datos, $error] = Calculadora::validar(read_json_body());
    if ($error) {
        json_response(['success' => false, 'message' => $error], 422);
    }

    // Los resultados se recalculan aquí: no se confía en los valores enviados por el navegador.
    $resultados = Calculadora::calcular($datos);
    $idSimulacion = SimulacionRepository::crear(Auth::id(), $datos, $resultados);

    json_response([
        'success'       => true,
        'message'       => 'Simulación guardada exitosamente',
        'id_simulacion' => $idSimulacion,
    ], 201);
});
