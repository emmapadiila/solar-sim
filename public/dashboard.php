<?php
require __DIR__ . '/../src/bootstrap.php';

$usuario = Auth::requireLogin();

$simulaciones = SimulacionRepository::listarPorUsuario(Auth::id());
$total = count($simulaciones);
$ultima = $simulaciones[0] ?? null;

$ahorroPromedio = $total ? array_sum(array_column($simulaciones, 'ahorro_mensual')) / $total : 0;
$mejorRetorno = $total ? min(array_map('floatval', array_column($simulaciones, 'retorno_inversion'))) : 0;
$energiaPromedio = $total ? array_sum(array_column($simulaciones, 'energia_generada')) / $total : 0;

render('layout/header', ['titulo' => 'Inicio', 'paginaActiva' => 'inicio']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-sun"></i> Panel</p>
                    <h1 class="page-title">Hola, <?= e($usuario['nombre']) ?></h1>
                    <p class="page-subtitle">Este es el resumen de tus simulaciones de energía solar.</p>
                </div>
                <a href="calculadora.php" class="btn btn--primary btn--lg"><i class="fas fa-plus"></i> Nueva simulación</a>
            </div>

            <div class="grid grid--4">
                <div class="stat">
                    <span class="icon-badge icon-badge--navy"><i class="fas fa-calculator"></i></span>
                    <div class="stat__body">
                        <p class="stat__label">Simulaciones</p>
                        <p class="stat__value"><?= $total ?></p>
                    </div>
                </div>
                <div class="stat">
                    <span class="icon-badge icon-badge--green"><i class="fas fa-piggy-bank"></i></span>
                    <div class="stat__body">
                        <p class="stat__label">Ahorro mensual promedio</p>
                        <p class="stat__value stat__value--success"><?= $total ? formato_moneda($ahorroPromedio) : '—' ?></p>
                    </div>
                </div>
                <div class="stat">
                    <span class="icon-badge"><i class="fas fa-bolt"></i></span>
                    <div class="stat__body">
                        <p class="stat__label">Energía promedio</p>
                        <p class="stat__value"><?= $total ? formato_numero($energiaPromedio) . '<span class="stat__unit">kWh/mes</span>' : '—' ?></p>
                    </div>
                </div>
                <div class="stat">
                    <span class="icon-badge icon-badge--navy"><i class="fas fa-hourglass-half"></i></span>
                    <div class="stat__body">
                        <p class="stat__label">Mejor retorno</p>
                        <p class="stat__value"><?= $total ? formato_numero($mejorRetorno, 1) . '<span class="stat__unit">años</span>' : '—' ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid--sidebar section">
                <section class="card">
                    <div class="card__header">
                        <h2 class="card__title"><i class="fas fa-list-check"></i> Cómo funciona</h2>
                    </div>
                    <ol class="steps">
                        <li>
                            <span class="steps__num">1</span>
                            <div>
                                <h3>Ingresa tus datos</h3>
                                <p>Ciudad, estrato, consumo mensual y el área disponible en tu techo.</p>
                            </div>
                        </li>
                        <li>
                            <span class="steps__num">2</span>
                            <div>
                                <h3>Revisa los resultados</h3>
                                <p>Paneles recomendados, ahorro estimado y tiempo de retorno de la inversión.</p>
                            </div>
                        </li>
                        <li>
                            <span class="steps__num">3</span>
                            <div>
                                <h3>Guarda y compara</h3>
                                <p>Tus simulaciones quedan en el historial y puedes descargarlas como reporte.</p>
                            </div>
                        </li>
                    </ol>
                </section>

                <section class="card">
                    <div class="card__header">
                        <h2 class="card__title"><i class="fas fa-clock-rotate-left"></i> Tu última simulación</h2>
                        <?php if ($ultima): ?>
                            <a href="historial.php" class="btn btn--ghost btn--sm">Ver historial <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>

                    <?php if ($ultima): ?>
                        <p class="text-muted mb-5">
                            <?= e(Calculadora::nombreCiudad($ultima['ubicacion'])) ?> · Estrato <?= e($ultima['estrato']) ?>
                            · <?= date('d/m/Y', strtotime($ultima['fecha'])) ?>
                        </p>
                        <div class="highlight mb-5">
                            <p class="highlight__label">Ahorro mensual estimado</p>
                            <p class="highlight__value"><?= formato_moneda($ultima['ahorro_mensual']) ?></p>
                            <p class="highlight__note"><?= formato_moneda($ultima['ahorro_anual']) ?> al año</p>
                        </div>
                        <dl class="data-list data-list--3">
                            <div><dt>Consumo</dt><dd><?= formato_numero($ultima['consumo_mensual']) ?> kWh/mes</dd></div>
                            <div><dt>Generación</dt><dd><?= formato_numero($ultima['energia_generada'], 1) ?> kWh/mes</dd></div>
                            <div><dt>Retorno</dt><dd><?= formato_numero($ultima['retorno_inversion'], 1) ?> años</dd></div>
                        </dl>
                    <?php else: ?>
                        <div class="empty-state">
                            <span class="icon-badge"><i class="fas fa-solar-panel"></i></span>
                            <h3>Aún no tienes simulaciones</h3>
                            <p>Haz tu primera simulación para ver aquí cuánto podrías ahorrar con paneles solares.</p>
                            <a href="calculadora.php" class="btn btn--primary">Empezar ahora</a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

<?php render('layout/footer'); ?>
