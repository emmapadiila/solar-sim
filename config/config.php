<?php
/**
 * Configuración de la aplicación.
 *
 * Los valores por defecto sirven para XAMPP. Para otro entorno, copia
 * config.local.example.php como config.local.php (no se sube a git)
 * o define las variables de entorno SOLARSIM_*.
 */

$config = [
    'app' => [
        'nombre' => 'SolarSim',
        'debug'  => (bool)(getenv('SOLARSIM_DEBUG') ?: false),
        // Minutos sin actividad antes de cerrar la sesión automáticamente
        'sesion_minutos' => (int)(getenv('SOLARSIM_SESION_MINUTOS') ?: 30),
    ],
    'db' => [
        'host'     => getenv('SOLARSIM_DB_HOST') ?: 'localhost',
        'port'     => (int)(getenv('SOLARSIM_DB_PORT') ?: 3306),
        'nombre'   => getenv('SOLARSIM_DB_NAME') ?: 'paneles_solares',
        'usuario'  => getenv('SOLARSIM_DB_USER') ?: 'root',
        'password' => getenv('SOLARSIM_DB_PASS') ?: '',
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}

return $config;
