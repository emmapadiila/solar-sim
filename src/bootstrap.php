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
error_reporting(E_ALL);

require SRC_PATH . '/helpers.php';
require SRC_PATH . '/Database.php';
require SRC_PATH . '/Auth.php';
require SRC_PATH . '/Calculadora.php';
require SRC_PATH . '/repositories/UsuarioRepository.php';
require SRC_PATH . '/repositories/SimulacionRepository.php';
require SRC_PATH . '/repositories/EstadisticasRepository.php';

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
