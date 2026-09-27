<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

$simulaciones = [];
try {
    $simulaciones = SimulacionRepository::listarPorUsuario(Auth::id());
} catch (Throwable $ex) {
    error_log('[SolarSim] historial: ' . $ex->getMessage());
    $error = 'No se pudo cargar el historial. Intenta de nuevo más tarde.';
}

$total = count($simulaciones);
$ahorroPromedio = $total ? array_sum(array_column($simulaciones, 'ahorro_mensual')) / $total : 0;
$retornoPromedio = $total ? array_sum(array_column($simulaciones, 'retorno_inversion')) / $total : 0;

render('layout/header', ['titulo' => 'Historial', 'paginaActiva' => 'historial']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-clock-rotate-left"></i> Historial</p>
                    <h1 class="page-title">Tus simulaciones</h1>
                    <p class="page-subtitle">Consulta el detalle de cada simulación guardada o descárgala como reporte en PDF.</p>
                </div>
                <a href="calculadora.php" class="btn btn--primary"><i class="fas fa-plus"></i> Nueva simulación</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert--warning mb-5">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (empty($simulaciones)): ?>
                <div class="card">
                    <div class="empty-state">
                        <span class="icon-badge"><i class="fas fa-chart-line"></i></span>
                        <h2>Aún no tienes simulaciones</h2>
                        <p>Cuando guardes una simulación desde la calculadora aparecerá aquí.</p>
                        <a href="calculadora.php" class="btn btn--dark"><i class="fas fa-calculator"></i> Ir a la calculadora</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid--3 mb-5">
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
                            <p class="stat__value stat__value--success"><?= formato_moneda($ahorroPromedio) ?></p>
                        </div>
                    </div>
                    <div class="stat">
                        <span class="icon-badge"><i class="fas fa-hourglass-half"></i></span>
                        <div class="stat__body">
                            <p class="stat__label">Retorno promedio</p>
                            <p class="stat__value"><?= formato_numero($retornoPromedio, 1) ?><span class="stat__unit">años</span></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid--3">
                    <?php foreach ($simulaciones as $simulacion): ?>
                        <article class="card sim-card">
                            <div class="sim-card__header">
                                <div>
                                    <h2 class="sim-card__city"><?= e(Calculadora::nombreCiudad($simulacion['ubicacion'])) ?></h2>
                                    <p class="sim-card__date"><?= date('d/m/Y · H:i', strtotime($simulacion['fecha'])) ?></p>
                                </div>
                                <span class="badge badge--sun">Estrato <?= e($simulacion['estrato']) ?></span>
                            </div>

                            <dl class="data-list">
                                <div><dt>Ahorro mensual</dt><dd class="is-positive"><?= formato_moneda($simulacion['ahorro_mensual']) ?></dd></div>
                                <div><dt>Ahorro anual</dt><dd class="is-positive"><?= formato_moneda($simulacion['ahorro_anual']) ?></dd></div>
                                <div><dt>Consumo</dt><dd><?= formato_numero($simulacion['consumo_mensual']) ?> kWh</dd></div>
                                <div><dt>Generación</dt><dd><?= formato_numero($simulacion['energia_generada'], 1) ?> kWh</dd></div>
                                <div><dt>Área</dt><dd><?= formato_numero($simulacion['area_disponible']) ?> m²</dd></div>
                                <div><dt>Retorno</dt><dd><?= formato_numero($simulacion['retorno_inversion'], 1) ?> años</dd></div>
                            </dl>

                            <div class="sim-card__actions">
                                <button type="button" class="btn btn--secondary btn--sm" onclick="verDetalle(<?= (int)$simulacion['id_simulacion'] ?>)">
                                    <i class="fas fa-eye"></i> Ver detalle
                                </button>
                                <button type="button" class="btn btn--ghost btn--sm" onclick="exportarSimulacion(<?= (int)$simulacion['id_simulacion'] ?>)">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" class="btn btn--danger btn--sm btn--icon" onclick="eliminarSimulacion(<?= (int)$simulacion['id_simulacion'] ?>)" aria-label="Eliminar simulación" title="Eliminar">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['historial.js']]); ?>
