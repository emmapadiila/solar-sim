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
    <title>Contacto - SolarSim</title>
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

    <main class="content-page">
        <section class="contact-section">
            <div class="contact-container">
                <h1 class="page-title">Contáctanos</h1>
                <p class="intro-text">¿Tienes preguntas, sugerencias o necesitas soporte? No dudes en contactarnos. Estamos aquí para ayudarte.</p>

                <div class="contact-info-grid">
                    <div class="info-item">
                        <i class="fas fa-envelope icon-animated"></i>
                        <h3>Correo Electrónico</h3>
                        <p><a href="mailto:info@solarsim.com">emma-padillaj@unilibre.edu.co</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone icon-animated"></i>
                        <h3>Teléfono</h3>
                        <p><a href="tel:+1234567890">+57 3225377936</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt icon-animated"></i>
                        <h3>Dirección</h3>
                        <p>Calle 42 A 6B 47</p>
                    </div>
                </div>

                <div class="contact-form-container">
                    <h2>Envíanos un Mensaje</h2>
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Asunto</label>
                            <input type="text" id="subject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="auth-btn">Enviar Mensaje <i class="fas fa-paper-plane"></i></button>
                        <div id="contactMessage" class="message-area"></div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="dashboard.js"></script> 
</body>
</html> 