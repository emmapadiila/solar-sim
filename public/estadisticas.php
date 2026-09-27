<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireAdmin();

$ahorroPorCiudad = EstadisticasRepository::ahorroPorCiudad();
$puntosAreaEnergia = EstadisticasRepository::puntosAreaEnergia();
$ultimasSimulaciones = EstadisticasRepository::ultimasSimulaciones(10);
$hayDatos = !empty($ahorroPorCiudad);

$promedioArea = $puntosAreaEnergia ? array_sum(array_column($puntosAreaEnergia, 'x')) / count($puntosAreaEnergia) : 0;
$promedioEnergia = $puntosAreaEnergia ? array_sum(array_column($puntosAreaEnergia, 'y')) / count($puntosAreaEnergia) : 0;

$nombresCiudades = [];
foreach (array_keys($ahorroPorCiudad) as $clave) {
    $nombresCiudades[$clave] = Calculadora::nombreCiudad($clave);
}

$datosGraficos = [
    'ciudades'    => $nombresCiudades,
    'ahorro'      => $ahorroPorCiudad,
    'energia'     => EstadisticasRepository::energiaPorCiudad(),
    'retorno'     => EstadisticasRepository::retornoPorCiudad(),
    'areaEnergia' => $puntosAreaEnergia,
];

render('layout/header', ['titulo' => 'Estadísticas', 'paginaActiva' => 'estadisticas', 'usarGraficos' => $hayDatos]);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-chart-pie"></i> Administración</p>
                    <h1 class="page-title">Estadísticas del sistema</h1>
                    <p class="page-subtitle">Promedios de todas las simulaciones guardadas por los usuarios.</p>
                </div>
                <?php if ($hayDatos): ?>
                <div class="page-header__filter">
                    <label class="form-label" for="filterCiudad">Filtrar por ciudad</label>
                    <select id="filterCiudad" class="form-control">
                        <option value="">Todas las ciudades</option>
                        <?php foreach ($nombresCiudades as $clave => $nombre): ?>
                            <option value="<?= e($clave) ?>"><?= e($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!$hayDatos): ?>
                <div class="card">
                    <div class="empty-state">
                        <span class="icon-badge"><i class="fas fa-chart-column"></i></span>
                        <h2>Todavía no hay datos</h2>
                        <p>Las estadísticas aparecerán cuando los usuarios guarden sus primeras simulaciones.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid--2">
                    <section class="card">
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-piggy-bank"></i> Ahorro mensual promedio</h2>
                        </div>
                        <div class="chart-box"><canvas id="graficoAhorros"></canvas></div>
                    </section>

                    <section class="card">
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-bolt"></i> Energía generada promedio</h2>
                        </div>
                        <div class="chart-box"><canvas id="graficoEnergia"></canvas></div>
                    </section>

                    <section class="card">
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-hourglass-half"></i> Retorno de inversión promedio</h2>
                        </div>
                        <div class="chart-box"><canvas id="graficoRetorno"></canvas></div>
                    </section>

                    <section class="card">
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-chart-area"></i> Relación área – energía</h2>
                        </div>
                        <p class="card__text mb-5">
                            Con un área promedio de <strong><?= formato_numero($promedioArea) ?> m²</strong>
                            se generan en promedio <strong><?= formato_numero($promedioEnergia) ?> kWh/mes</strong>.
                        </p>
                        <div class="chart-box"><canvas id="graficoAreaEnergia"></canvas></div>
                    </section>
                </div>

                <section class="card section">
                    <div class="card__header">
                        <h2 class="card__title"><i class="fas fa-clock-rotate-left"></i> Últimas simulaciones</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Ciudad</th>
                                    <th>Estrato</th>
                                    <th class="text-right">Área</th>
                                    <th class="text-right">Ahorro mensual</th>
                                    <th class="text-right">Retorno</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasSimulaciones as $simulacion): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($simulacion['fecha'])) ?></td>
                                    <td><?= e($simulacion['nombre_usuario']) ?></td>
                                    <td><?= e(Calculadora::nombreCiudad($simulacion['ubicacion'])) ?></td>
                                    <td><?= e($simulacion['estrato']) ?></td>
                                    <td class="text-right"><?= formato_numero($simulacion['area_disponible']) ?> m²</td>
                                    <td class="text-right"><?= formato_moneda($simulacion['ahorro_mensual']) ?></td>
                                    <td class="text-right"><?= formato_numero($simulacion['retorno_inversion'], 1) ?> años</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <script>
                    window.ESTADISTICAS = <?= json_encode($datosGraficos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
                </script>
            <?php endif; ?>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => $hayDatos ? ['estadisticas.js'] : []]); ?>
