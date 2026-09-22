<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html'); // Redirigir al login si no hay sesión
    exit();
}

$usuario = $_SESSION['usuario'];
$nombre_usuario = htmlspecialchars($usuario['nombre']);

// Verificar si el usuario ha iniciado sesión y obtener su rol
$is_admin = false;
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin') {
    $is_admin = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SolarSim</title>
    <link rel="stylesheet" href="style.css"> <!-- Reutilizamos el estilo base -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-solar-panel"></i>
                <span>SolarSim</span>
            </a>
            <nav>
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="dashboard.php" class="nav__link">Inicio</a>
                    </li>
                    <li class="nav__item">
                        <a href="calculadora_solar.php" class="nav__link">Simulación</a>
                    </li>
                    <li class="nav__item">
                        <a href="historial_simulaciones.php" class="nav__link">Historial</a>
                    </li>
                    <li class="nav__item">
                        <a href="educativo.html" class="nav__link">Contenido Educativo</a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li class="nav__item">
                        <a href="estadisticas.php" class="nav__link">Estadísticas</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav__item">
                        <a href="about.php" class="nav__link">Acerca de Nosotros</a>
                    </li>
                    <li class="nav__item">
                        <a href="contact.php" class="nav__link">Contacto</a>
                    </li>
                    <li class="nav__item">
                        <a href="#" id="logoutBtn" class="nav__link logout-btn">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-content">
        <div class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Bienvenido, <?php echo $nombre_usuario; ?>!</h1>
                <p class="slogan">"Transformando la energía del sol en tu ahorro"</p>
                <p class="description">Explora el potencial de la energía solar y optimiza tu consumo. Nuestro simulador te ayuda a entender los beneficios y el retorno de inversión de instalar paneles solares en tu hogar o negocio.</p>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <i class="fas fa-lightbulb icon-animated"></i>
                        <h3>Ahorro Energético</h3>
                        <p>Reduce drásticamente tus facturas de electricidad.</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-leaf icon-animated"></i>
                        <h3>Sostenibilidad</h3>
                        <p>Contribuye a un futuro más verde y reduce tu huella de carbono.</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-chart-line icon-animated"></i>
                        <h3>Inversión Inteligente</h3>
                        <p>Genera tu propia energía y obtén un rápido retorno de inversión.</p>
                    </div>
                </div>
                <a href="calculadora_solar.php" class="main-cta">Iniciar Simulación <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </main>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="dashboard.js"></script>
</body>
</html> 