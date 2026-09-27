<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Acerca de Nosotros', 'paginaActiva' => 'about']);
?>

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
                            <img src="assets/images/breiner.jpg" alt="Miembro del equipo 1">
                            <h3>Breiner Duran</h3>
                        </div>
                        <div class="member-card">
                            <img src="assets/images/emma.jpg" alt="Miembro del equipo 2">
                            <h3>Emma padilla</h3>
                        </div>
                        <div class="member-card">
                            <img src="assets/images/sharick.jpg" alt="Miembro del equipo 3">
                            <h3>Sharick camargo</h3>
                        </div>
                        <div class="member-card">
                            <img src="assets/images/juan.jpg" alt="Miembro del equipo 4">
                            <h3>Juan Martinez</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php render('layout/footer'); ?>
