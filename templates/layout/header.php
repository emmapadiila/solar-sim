<?php
/**
 * Cabecera de las páginas autenticadas: <head>, apertura de <body> y menú.
 * @var string $titulo
 * @var string $paginaActiva  clave del enlace a resaltar en el menú
 * @var bool   $usarGraficos
 */

$enlaces = [
    'inicio'       => ['dashboard.php',    'Inicio'],
    'calculadora'  => ['calculadora.php',  'Calculadora'],
    'historial'    => ['historial.php',    'Historial'],
    'educativo'    => ['educativo.php',    'Contenido Educativo'],
    'estadisticas' => ['estadisticas.php', 'Estadísticas', 'admin'],
    'about'        => ['about.php',        'Acerca de Nosotros'],
    'contacto'     => ['contacto.php',     'Contacto'],
];

require __DIR__ . '/head.php';
?>
<body>
    <header>
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-solar-panel"></i>
                <span><?= e(config('app.nombre')) ?></span>
            </a>
            <nav>
                <ul class="nav__list">
                    <?php foreach ($enlaces as $clave => $enlace): ?>
                        <?php if (($enlace[2] ?? null) === 'admin' && !Auth::isAdmin()) continue; ?>
                        <li class="nav__item">
                            <a href="<?= $enlace[0] ?>" class="nav__link<?= ($paginaActiva ?? '') === $clave ? ' active' : '' ?>"><?= e($enlace[1]) ?></a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav__item">
                        <a href="#" id="logoutBtn" class="nav__link logout-btn">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
