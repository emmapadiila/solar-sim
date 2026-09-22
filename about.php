<?php
session_start();

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
    <title>Acerca de Nosotros - SolarSim</title>
    <link rel="stylesheet" href="style.css">
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
                        <a href="calculadora_solar.php" class="nav__link">Calculadora</a>
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

    <main class="content-page">
        <section class="about-section">
            <div class="about-container">
                <h1 class="page-title">Acerca de SolarSim</h1>
                <p class="intro-text">En SolarSIM, creemos firmemente en el poder del sol para transformar el futuro energético de nuestras comunidades. MISO, nuestro Modelo de Simulación Solar, es una herramienta digital creada para empoderar a los usuarios mediante el conocimiento. Esta plataforma permite simular el funcionamiento de paneles solares en hogares de estratos 1 a 3, calculando el posible ahorro en la factura eléctrica de manera personalizada, sencilla e interactiva. SolarSIM no solo brinda datos; ofrece una experiencia educativa y visual que promueve la adopción consciente de energías limpias. Gracias a su enfoque accesible y amigable, MISO guía a cada persona en la toma de decisiones informadas sobre su consumo energético, fomentando la sostenibilidad, el ahorro económico y el respeto por el medio ambiente. A través de tecnología, simulaciones y un diseño intuitivo, buscamos acercar la energía solar a todos los hogares, demostrando que el cambio está al alcance de un clic y que el futuro verde comienza hoy.</p>

                <div class="mission-vision">
                    <div class="mv-item">
                        <h2>Nuestra Misión</h2>
                        <p>Desarrollar una herramienta digital accesible e interactiva que permita a los usuarios de estratos 1, 2 y 3 simular el funcionamiento de paneles solares en sus hogares, promoviendo el uso consciente de energías limpias, fomentando la educación energética y brindando estimaciones claras de ahorro económico a través de la energía solar.
                        </p>
                    </div>
                    <div class="mv-item">
                        <h2>Nuestra Visión</h2>
                        <p>Ser el sistema líder en Latinoamérica en la concientización, educación y simulación del aprovechamiento solar, facilitando la transición hacia un modelo energético sostenible, justo y accesible para todas las comunidades, especialmente las más&nbsp;vulnerables.
                        </p>
                    </div>
                </div>

                <div class="team-section">
                    <h2>Nuestro Equipo</h2>
                    <div class="team-members">
                        <div class="member-card">
                            <img src="images/breiner.jpg" alt="Miembro del equipo 1">
                            <h3>Breiner Duran</h3>
                        </div>
                        <div class="member-card">
                            <img src="images/emma.jpg" alt="Miembro del equipo 2">
                            <h3>Emma padilla</h3>
                        </div>
                        <div class="member-card">
                            <img src="images/sharick.jpg" alt="Miembro del equipo 3">
                            <h3>Sharick camargo</h3>
                        </div>
                        <div class="member-card">
                            <img src="images/juan.jpg" alt="Miembro del equipo 4">
                            <h3>Juan Martinez</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="dashboard.js"></script> <!-- Reutilizamos el script para el logout -->
</body>
</html> 