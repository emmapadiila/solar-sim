<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

$equipo = ['Breiner Durán', 'Emma Padilla', 'Sharick Camargo', 'Juan Martínez'];

/** "Emma Padilla" -> "EP" */
function iniciales(string $nombre): string
{
    $partes = preg_split('/\s+/', trim($nombre));
    return mb_strtoupper(mb_substr($partes[0], 0, 1) . mb_substr(end($partes), 0, 1));
}

render('layout/header', ['titulo' => 'Nosotros', 'paginaActiva' => 'about']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-users"></i> Nosotros</p>
                    <h1 class="page-title">Acerca de SolarSim</h1>
                    <p class="page-subtitle">Acercamos la energía solar a los hogares colombianos con información clara y personalizada.</p>
                </div>
            </div>

            <section class="card prose">
                <p>En SolarSim creemos en el poder del sol para transformar el futuro energético de nuestras comunidades. MISO, nuestro Modelo de Simulación Solar, es una herramienta digital creada para empoderar a los usuarios mediante el conocimiento.</p>
                <p>La plataforma permite simular el funcionamiento de paneles solares en hogares de estratos 1 a 3 y calcular el posible ahorro en la factura eléctrica de manera personalizada, sencilla e interactiva. No solo brinda datos: ofrece una experiencia educativa y visual que promueve la adopción consciente de energías limpias.</p>
                <p>Con un enfoque accesible, MISO guía a cada persona en la toma de decisiones informadas sobre su consumo energético, fomentando la sostenibilidad, el ahorro y el respeto por el medio ambiente. El cambio está al alcance de un clic.</p>
            </section>

            <div class="grid grid--2 section">
                <section class="card">
                    <span class="icon-badge"><i class="fas fa-bullseye"></i></span>
                    <h2 class="section-title mt-4">Misión</h2>
                    <p class="card__text">Desarrollar una herramienta digital accesible e interactiva que permita a los usuarios de estratos 1, 2 y 3 simular el funcionamiento de paneles solares en sus hogares, promoviendo el uso consciente de energías limpias, la educación energética y estimaciones claras de ahorro económico.</p>
                </section>
                <section class="card">
                    <span class="icon-badge icon-badge--navy"><i class="fas fa-eye"></i></span>
                    <h2 class="section-title mt-4">Visión</h2>
                    <p class="card__text">Ser el sistema líder en Latinoamérica en concientización, educación y simulación del aprovechamiento solar, facilitando la transición hacia un modelo energético sostenible, justo y accesible para todas las comunidades, especialmente las más vulnerables.</p>
                </section>
            </div>

            <section class="section">
                <h2 class="section-title">Nuestro equipo</h2>
                <div class="grid grid--4">
                    <?php foreach ($equipo as $persona): ?>
                        <div class="card member">
                            <span class="member__avatar" aria-hidden="true"><?= e(iniciales($persona)) ?></span>
                            <h3><?= e($persona) ?></h3>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

<?php render('layout/footer'); ?>
