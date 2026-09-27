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
        // Zona horaria de las fechas que muestra la app (correos, PDF, historial)
        'zona_horaria' => getenv('SOLARSIM_ZONA_HORARIA') ?: 'America/Bogota',
    ],
    'db' => [
        'host'     => getenv('SOLARSIM_DB_HOST') ?: 'localhost',
        'port'     => (int)(getenv('SOLARSIM_DB_PORT') ?: 3306),
        'nombre'   => getenv('SOLARSIM_DB_NAME') ?: 'paneles_solares',
        'usuario'  => getenv('SOLARSIM_DB_USER') ?: 'root',
        'password' => getenv('SOLARSIM_DB_PASS') ?: '',
    ],
    // Aviso por correo de los mensajes de contacto (SMTP de Gmail).
    // Sin usuario/contraseña SMTP no se envía nada: el mensaje solo queda en la sección Mensajes.
    'correo' => [
        'destino'          => getenv('SOLARSIM_CORREO_DESTINO') ?: 'pemma9962@gmail.com',
        'smtp_host'        => getenv('SOLARSIM_SMTP_HOST') ?: 'smtp.gmail.com',
        'smtp_puerto'      => (int)(getenv('SOLARSIM_SMTP_PUERTO') ?: 587),
        'smtp_seguridad'   => getenv('SOLARSIM_SMTP_SEGURIDAD') ?: 'tls', // 'tls' (587), 'ssl' (465) o '' (sin cifrar)
        'smtp_usuario'     => getenv('SOLARSIM_SMTP_USUARIO') ?: '',
        'smtp_password'    => getenv('SOLARSIM_SMTP_PASSWORD') ?: '',
        'remitente_nombre' => 'SolarSim',
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}

return $config;
