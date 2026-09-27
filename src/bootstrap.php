<?php
/**
 * Punto de arranque común. Toda página y endpoint de public/ lo incluye primero:
 *   require __DIR__ . '/../src/bootstrap.php';
 */

define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

$GLOBALS['config'] = require ROOT_PATH . '/config/config.php';

ini_set('display_errors', $GLOBALS['config']['app']['debug'] ? '1' : '0');
date_default_timezone_set($GLOBALS['config']['app']['zona_horaria']);
error_reporting(E_ALL);

require SRC_PATH . '/helpers.php';
require SRC_PATH . '/Database.php';
require SRC_PATH . '/Auth.php';
require SRC_PATH . '/Calculadora.php';
require SRC_PATH . '/Correo.php';
require SRC_PATH . '/repositories/UsuarioRepository.php';
require SRC_PATH . '/repositories/SimulacionRepository.php';
require SRC_PATH . '/repositories/EstadisticasRepository.php';
require SRC_PATH . '/repositories/MensajeRepository.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('SOLARSIMSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

// Cierre de sesión por inactividad
$minutosSesion = (int)config('app.sesion_minutos', 30);
if (Auth::check() && isset($_SESSION['ultima_actividad']) && time() - $_SESSION['ultima_actividad'] > $minutosSesion * 60) {
    Auth::logout();
    session_start();
    session_regenerate_id(true);
    $unidad = $minutosSesion === 1 ? 'minuto' : 'minutos';
    flash("Tu sesión se cerró tras $minutosSesion $unidad sin actividad. Vuelve a iniciar sesión.", 'info');
}
$_SESSION['ultima_actividad'] = time();
