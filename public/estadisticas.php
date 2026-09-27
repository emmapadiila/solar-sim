<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireAdmin();

$ahorroPorCiudad = EstadisticasRepository::ahorroPorCiudad();
$puntosAreaEnergia = EstadisticasRepository::puntosAreaEnergia();
$ultimasSimulaciones = EstadisticasRepository::ultimasSimulaciones(10);

$promedioArea = $puntosAreaEnergia ? round(array_sum(array_column($puntosAreaEnergia, 'x')) / count($puntosAreaEnergia)) : 0;
$promedioEnergia = $puntosAreaEnergia ? round(array_sum(array_column($puntosAreaEnergia, 'y')) / count($puntosAreaEnergia)) : 0;

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

render('layout/header', ['titulo' => 'Estadísticas', 'paginaActiva' => 'estadisticas', 'usarGraficos' => true]);
?>

    <main class="stats-content">
        <div class="stats-container">
            <h1 class="page-title"><i class="fas fa-chart-line"></i> Estadísticas del Sistema</h1>

            <!-- Filtros -->
            <div class="stats-filters">
                <div class="filter-group">
                    <label for="filterCiudad">Ciudad:</label>
                    <select id="filterCiudad">
                        <option value="">Todas las ciudades</option>
                        <?php foreach ($nombresCiudades as $clave => $nombre): ?>
                            <option value="<?= e($clave) ?>"><?= e($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="stats-grid">
                <div class="stats-card">
                    <h3><i class="fas fa-piggy-bank"></i> Ahorros Promedio por Ciudad</h3>
                    <canvas id="graficoAhorros"></canvas>
                </div>

                <div class="stats-card">
                    <h3><i class="fas fa-bolt"></i> Energía Generada por Ciudad</h3>
                    <canvas id="graficoEnergia"></canvas>
                </div>

                <div class="stats-card">
                    <h3><i class="fas fa-clock"></i> Retorno de Inversión</h3>
                    <canvas id="graficoRetorno"></canvas>
                </div>

                <div class="stats-card">
                    <h3><i class="fas fa-chart-area"></i> Relación Área-Energía</h3>
                    <div class="stats-summary">
                        <p class="stats-info">
                            <i class="fas fa-info-circle"></i>
                            Con un promedio de <strong><?= $promedioArea ?> m²</strong>,
                            se están generando <strong><?= $promedioEnergia ?> kWh/mes</strong> en promedio.
                        </p>
                    </div>
                    <canvas id="graficoAreaEnergia"></canvas>
                </div>
            </div>

            <!-- Últimas Simulaciones -->
            <div class="ultimas-simulaciones">
                <h3><i class="fas fa-history"></i> Últimas Simulaciones</h3>
                <div class="table-responsive">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Ciudad</th>
                                <th>Estrato</th>
                                <th>Área</th>
                                <th>Ahorro Mensual</th>
                                <th>Retorno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasSimulaciones as $simulacion): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($simulacion['fecha'])) ?></td>
                                <td><?= e($simulacion['nombre_usuario']) ?></td>
                                <td><?= e(Calculadora::nombreCiudad($simulacion['ubicacion'])) ?></td>
                                <td><?= e($simulacion['estrato']) ?></td>
                                <td><?= e($simulacion['area_disponible']) ?> m²</td>
                                <td>$<?= number_format((float)$simulacion['ahorro_mensual'], 0, ',', '.') ?></td>
                                <td><?= number_format((float)$simulacion['retorno_inversion'], 1) ?> años</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        window.ESTADISTICAS = <?= json_encode($datosGraficos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    </script>

<?php render('layout/footer', ['scripts' => ['estadisticas.js']]); ?>
