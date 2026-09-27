<?php
require __DIR__ . '/../../../src/bootstrap.php';

Auth::requireLoginApi();
require_method('POST');

run_api(function () {
    [$datos, $error] = Calculadora::validar(read_json_body());
    if ($error) {
        json_response(['success' => false, 'message' => $error], 422);
    }

    json_response(['success' => true, 'resultados' => Calculadora::calcular($datos)]);
});
