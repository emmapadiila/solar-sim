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
    'educativo'    => ['educativo.php',    'Aprende'],
    'estadisticas' => ['estadisticas.php', 'Estadísticas', 'admin'],
    'mensajes'     => ['mensajes.php',     'Mensajes', 'admin'],
    'about'        => ['about.php',        'Nosotros'],
    'contacto'     => ['contacto.php',     'Contacto'],
];

require __DIR__ . '/head.php';
?>
<body>
    <header class="site-header">
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <span class="logo__mark"><i class="fas fa-sun"></i></span>
                <span><?= e(config('app.nombre')) ?></span>
            </a>
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="menuPrincipal">
                <i class="fas fa-bars"></i>
            </button>
            <nav id="menuPrincipal">
                <ul class="nav__list">
                    <?php foreach ($enlaces as $clave => $enlace): ?>
                        <?php if (($enlace[2] ?? null) === 'admin' && !Auth::isAdmin()) continue; ?>
                        <li class="nav__item">
                            <a href="<?= $enlace[0] ?>" class="nav__link<?= ($paginaActiva ?? '') === $clave ? ' active' : '' ?>"><?= e($enlace[1]) ?></a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav__item">
                        <a href="#" id="logoutBtn" class="nav__link logout-btn"><i class="fas fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <?php if ($aviso = flash_pull()): ?>
        <div class="container">
            <div class="alert alert--<?= e($aviso['tipo']) ?> flash" role="status">
                <i class="fas fa-circle-info"></i>
                <span><?= e($aviso['mensaje']) ?></span>
            </div>
        </div>
    <?php endif; ?>
