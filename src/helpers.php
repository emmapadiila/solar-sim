<?php
/**
 * Funciones de apoyo usadas por páginas, plantillas y endpoints.
 */

/** Escapa un valor para imprimirlo en HTML. */
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/** Valor de configuración con notación de puntos: config('db.host'). */
function config(string $clave, $defecto = null)
{
    $valor = $GLOBALS['config'];
    foreach (explode('.', $clave) as $parte) {
        if (!is_array($valor) || !array_key_exists($parte, $valor)) {
            return $defecto;
        }
        $valor = $valor[$parte];
    }
    return $valor;
}

/** Incluye una plantilla de templates/ pasándole variables. */
function render(string $plantilla, array $datos = []): void
{
    extract($datos, EXTR_SKIP);
    require TEMPLATES_PATH . '/' . $plantilla . '.php';
}

/** Envía una respuesta JSON y termina la ejecución. */
function json_response(array $datos, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Responde 405 si el método HTTP no es el esperado. */
function require_method(string $metodo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        header('Allow: ' . $metodo);
        json_response(['success' => false, 'message' => 'Método no permitido'], 405);
    }
}

/** Lee el cuerpo JSON de la petición; responde 400 si no es válido. */
function read_json_body(): array
{
    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos)) {
        json_response(['success' => false, 'message' => 'No se recibieron datos o el formato es incorrecto'], 400);
    }
    return $datos;
}

/**
 * Ejecuta el cuerpo de un endpoint JSON y convierte cualquier excepción no
 * controlada en un 500 genérico (el detalle solo va al log del servidor).
 */
function run_api(callable $handler): void
{
    try {
        $handler();
    } catch (Throwable $ex) {
        error_log('[SolarSim] ' . $_SERVER['SCRIPT_NAME'] . ': ' . $ex->getMessage());
        $mensaje = config('app.debug') ? $ex->getMessage() : 'Error en el servidor';
        json_response(['success' => false, 'message' => $mensaje], 500);
    }
}
