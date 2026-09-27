<?php
// Copia este archivo como config.local.php y ajusta solo lo que cambie en tu entorno.
return [
    'app' => [
        'debug' => true,
    ],
    'db' => [
        'host'     => 'localhost',
        'nombre'   => 'paneles_solares',
        'usuario'  => 'root',
        'password' => '',
    ],
    // Cuenta de Gmail que ENVÍA los avisos de contacto. La contraseña es una
    // "contraseña de aplicación" de Google (16 letras), no la contraseña normal.
    'correo' => [
        'smtp_usuario'  => 'tu-cuenta@gmail.com',
        'smtp_password' => 'xxxx xxxx xxxx xxxx',
    ],
];
