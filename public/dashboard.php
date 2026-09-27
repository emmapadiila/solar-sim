<?php
require __DIR__ . '/../src/bootstrap.php';

$usuario = Auth::requireLogin();

render('layout/header', ['titulo' => 'Dashboard', 'paginaActiva' => 'inicio']);
?>

    <main class="dashboard-content">
        <div class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Bienvenido, <?= e($usuario['nombre']) ?>!</h1>
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
                <a href="calculadora.php" class="main-cta">Iniciar Simulación <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </main>

<?php render('layout/footer'); ?>
