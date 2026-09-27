<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

$temas = [
    [
        'icono' => 'fa-sun',
        'titulo' => '¿Qué es la energía solar?',
        'texto' => 'Es una fuente de energía renovable que aprovecha la radiación del sol para generar electricidad o calor.',
        'puntos' => ['Renovable e inagotable', 'No produce gases de efecto invernadero', 'Reduce la dependencia de combustibles fósiles', 'Gratuita una vez instalada', 'Requiere poco mantenimiento'],
    ],
    [
        'icono' => 'fa-solar-panel',
        'titulo' => '¿Cómo funcionan los paneles?',
        'texto' => 'Los paneles fotovoltaicos convierten la luz solar directamente en electricidad mediante el efecto fotovoltaico.',
        'puntos' => ['Las células capturan la luz solar', 'Los fotones excitan los electrones del silicio', 'Se genera corriente continua', 'Un inversor la convierte en corriente alterna', 'La energía se usa en casa o se vende a la red'],
    ],
    [
        'icono' => 'fa-house',
        'titulo' => 'Beneficios para tu hogar',
        'texto' => 'Instalar paneles solares ofrece ventajas económicas, ambientales y sociales.',
        'puntos' => ['Ahorro significativo en la factura', 'Mayor valor de la propiedad', 'Independencia energética', 'Protección ante aumentos de tarifas', 'Contribución a la sostenibilidad'],
    ],
    [
        'icono' => 'fa-gauge-high',
        'titulo' => 'Factores de eficiencia',
        'texto' => 'Varios factores influyen en cuánta energía producen los paneles.',
        'puntos' => ['Orientación e inclinación', 'Horas de sol directo en tu ciudad', 'Sombras y obstáculos', 'Calidad y tipo de panel', 'Mantenimiento y limpieza'],
    ],
    [
        'icono' => 'fa-leaf',
        'titulo' => 'Impacto ambiental',
        'texto' => 'Es una de las formas más limpias de generar electricidad.',
        'puntos' => ['Reduce las emisiones de CO2', 'No contamina el aire ni el agua', 'Conserva recursos naturales', 'Ayuda a combatir el cambio climático', 'Promueve la sostenibilidad'],
    ],
    [
        'icono' => 'fa-coins',
        'titulo' => 'Inversión y retorno',
        'texto' => 'Los paneles solares son una inversión que se paga sola con el tiempo.',
        'puntos' => ['Recuperación en 5 a 10 años', 'Vida útil de 25 a 30 años', 'Incentivos tributarios disponibles', 'Valorización de la propiedad', 'Protección contra la inflación energética'],
    ],
];

render('layout/header', ['titulo' => 'Aprende', 'paginaActiva' => 'educativo']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-graduation-cap"></i> Aprende</p>
                    <h1 class="page-title">Energía solar, explicada</h1>
                    <p class="page-subtitle">Lo esencial sobre energía solar, paneles y cómo pueden transformar tu consumo energético.</p>
                </div>
            </div>

            <div class="grid grid--3">
                <?php foreach ($temas as $tema): ?>
                    <article class="card topic-card">
                        <span class="icon-badge"><i class="fas <?= e($tema['icono']) ?>"></i></span>
                        <h3><?= e($tema['titulo']) ?></h3>
                        <p><?= e($tema['texto']) ?></p>
                        <ul class="checklist">
                            <?php foreach ($tema['puntos'] as $punto): ?>
                                <li><?= e($punto) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endforeach; ?>
            </div>

            <section class="section">
                <h2 class="section-title">Recursos adicionales</h2>
                <div class="grid grid--3">
                    <article class="card resource-card">
                        <span class="icon-badge icon-badge--navy"><i class="fas fa-circle-play"></i></span>
                        <h3>Video explicativo</h3>
                        <p class="card__text">Cómo funciona la energía solar, explicado de forma visual.</p>
                        <a href="https://youtu.be/jeT5lZ9t7c0" class="btn btn--secondary btn--sm" target="_blank" rel="noopener">Ver video <i class="fas fa-arrow-up-right-from-square"></i></a>
                    </article>
                    <article class="card resource-card">
                        <span class="icon-badge icon-badge--navy"><i class="fas fa-book-open"></i></span>
                        <h3>Guía sobre paneles solares</h3>
                        <p class="card__text">Instalación, mantenimiento y lo que debes saber antes de invertir.</p>
                        <a href="https://nalelectricos.com.co/paneles-solares-lo-que-debes-saber/" class="btn btn--secondary btn--sm" target="_blank" rel="noopener">Leer guía <i class="fas fa-arrow-up-right-from-square"></i></a>
                    </article>
                    <article class="card resource-card">
                        <span class="icon-badge icon-badge--navy"><i class="fas fa-calculator"></i></span>
                        <h3>Calcula tu potencial solar</h3>
                        <p class="card__text">Aplica lo aprendido y estima el ahorro de tu propia vivienda.</p>
                        <a href="calculadora.php" class="btn btn--primary btn--sm">Ir a la calculadora <i class="fas fa-arrow-right"></i></a>
                    </article>
                </div>
            </section>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['educativo.js']]); ?>
