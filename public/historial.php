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

render('layout/header', ['titulo' => 'Historial de Simulaciones', 'paginaActiva' => 'historial']);
?>

    <main class="historial-content">
        <div class="historial-container">
            <div class="historial-header">
                <h1><i class="fas fa-history"></i> Historial de Simulaciones</h1>
                <p>Revisa todas tus simulaciones de paneles solares</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($simulaciones)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h2>No tienes simulaciones aún</h2>
                    <p>Realiza tu primera simulación para ver el historial aquí</p>
                    <a href="calculadora.php" class="btn-primary">
                        <i class="fas fa-calculator"></i> Ir a Calculadora
                    </a>
                </div>
            <?php else: ?>
                <div class="historial-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Simulaciones</h3>
                            <p><?= count($simulaciones) ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Ahorro Promedio</h3>
                            <p>$<?= number_format($ahorroPromedio, 0, ',', '.') ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Retorno Promedio</h3>
                            <p><?= number_format($retornoPromedio, 1, ',', '.') ?> años</p>
                        </div>
                    </div>
                </div>

                <div class="simulaciones-grid">
                    <?php foreach ($simulaciones as $simulacion): ?>
                        <div class="simulacion-card">
                            <div class="simulacion-header">
                                <div class="simulacion-fecha">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= date('d/m/Y H:i', strtotime($simulacion['fecha'])) ?>
                                </div>
                                <div class="simulacion-ubicacion">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= e(Calculadora::nombreCiudad($simulacion['ubicacion'])) ?>
                                </div>
                            </div>
                            
                            <div class="simulacion-details">
                                <div class="detail-row">
                                    <span class="detail-label">Estrato:</span>
                                    <span class="detail-value"><?= e($simulacion['estrato']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Consumo:</span>
                                    <span class="detail-value"><?= e($simulacion['consumo_mensual']) ?> kWh/mes</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Área:</span>
                                    <span class="detail-value"><?= e($simulacion['area_disponible']) ?> m²</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tipo Energía:</span>
                                    <span class="detail-value"><?= e(ucfirst($simulacion['tipo_energia'])) ?></span>
                                </div>
                            </div>
                            
                            <div class="simulacion-results">
                                <div class="result-item">
                                    <i class="fas fa-solar-panel"></i>
                                    <div>
                                        <span class="result-label">Energía Generada</span>
                                        <span class="result-value"><?= e($simulacion['energia_generada']) ?> kWh/mes</span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-piggy-bank"></i>
                                    <div>
                                        <span class="result-label">Ahorro Mensual</span>
                                        <span class="result-value">$<?= number_format((float)$simulacion['ahorro_mensual'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <span class="result-label">Ahorro Anual</span>
                                        <span class="result-value">$<?= number_format((float)$simulacion['ahorro_anual'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="result-label">Retorno Inversión</span>
                                        <span class="result-value"><?= e($simulacion['retorno_inversion']) ?> años</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="simulacion-actions">
                                <button class="btn-ver" onclick="verDetalle(<?= (int)$simulacion['id_simulacion'] ?>)">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </button>
                                <button class="btn-exportar" onclick="exportarSimulacion(<?= (int)$simulacion['id_simulacion'] ?>)">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['historial.js']]); ?>
