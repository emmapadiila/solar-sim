<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Calculadora Solar', 'paginaActiva' => 'calculadora', 'usarGraficos' => true]);
?>

    <main class="calculadora-content">
        <div class="calculadora-container">
            <div class="calculadora-header">
                <h1><i class="fas fa-calculator"></i> Calculadora de Ahorro Solar</h1>
                <p>Simula el ahorro en tu factura eléctrica al instalar paneles solares</p>
            </div>

            <div class="info-alert">
                <div class="info-alert-content">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-text">
                        <h3>Requisitos para una instalación eficiente</h3>
                        <p>Para lograr una instalación óptima de paneles solares, considera lo siguiente:</p>
                        <ul>
                            <li>La vivienda debe contar con azotea, tejado o terraza con buena exposición al sol</li>
                            <li>Orientación preferiblemente al norte u occidente</li>
                            <li>Sin sombras de árboles, edificios u otros obstáculos</li>
                            <li>Techos amplios, planos o con inclinación uniforme</li>
                            <li>Estructura del techo que soporte el peso del sistema</li>
                        </ul>
                        <p class="warning-text">No se recomienda instalar en techos muy pequeños, con inclinaciones irregulares, materiales inestables o en zonas con mucha sombra o lluvias constantes.</p>
                    </div>
                </div>
            </div>

            <div class="calculadora-grid">
                <!-- Formulario de entrada -->
                <div class="form-section">
                    <div class="form-card">
                        <h2><i class="fas fa-edit"></i> Datos de Entrada</h2>
                        <form id="calculadoraForm">
                            <div class="form-group">
                                <label for="ubicacion">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </label>
                                <select id="ubicacion" name="ubicacion" required>
                                    <option value="">Selecciona tu ciudad</option>
                                    <?php foreach (Calculadora::CIUDADES as $clave => $ciudad): ?>
                                    <option value="<?= e($clave) ?>"><?= e($ciudad['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="estrato">
                                    <i class="fas fa-layer-group"></i> Estrato Socioeconómico
                                </label>
                                <select id="estrato" name="estrato" required>
                                    <option value="">Selecciona tu estrato</option>
                                    <?php foreach (array_keys(Calculadora::PRECIO_KWH) as $estrato): ?>
                                    <option value="<?= $estrato ?>">Estrato <?= $estrato ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="consumo_mensual">
                                    <i class="fas fa-bolt"></i> Consumo Mensual (kWh)
                                </label>
                                <input type="number" id="consumo_mensual" name="consumo_mensual" 
                                       placeholder="Ej: 350" min="<?= Calculadora::CONSUMO_MIN ?>" max="<?= Calculadora::CONSUMO_MAX ?>" required>
                                <small>Puedes encontrar esta información en tu factura de energía</small>
                            </div>

                            <div class="form-group">
                                <label for="area_disponible">
                                    <i class="fas fa-home"></i> Área Disponible (m²)
                                </label>
                                <input type="number" id="area_disponible" name="area_disponible" 
                                       placeholder="Ej: 50" min="<?= Calculadora::AREA_MIN ?>" max="<?= Calculadora::AREA_MAX ?>" required>
                                <small>Área disponible en tu techo para instalar paneles</small>
                            </div>

                            <div class="form-group">
                                <label for="tipo_energia">
                                    <i class="fas fa-plug"></i> Tipo de Energía Actual
                                </label>
                                <select id="tipo_energia" name="tipo_energia" required>
                                    <option value="">Selecciona el tipo</option>
                                    <?php foreach (Calculadora::TIPOS_ENERGIA as $clave => $nombre): ?>
                                    <option value="<?= e($clave) ?>"><?= e($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn-calcular">
                                <i class="fas fa-calculator"></i> Calcular Ahorro
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="resultados-section">
                    <div class="resultados-card" id="resultadosCard" style="display: none;">
                        <h2><i class="fas fa-chart-line"></i> Resultados de la Simulación</h2>
                        
                        <div class="resultados-grid">
                            <div class="resultado-item">
                                <div class="resultado-icon">
                                    <i class="fas fa-solar-panel"></i>
                                </div>
                                <div class="resultado-content">
                                    <h3>Energía Generada</h3>
                                    <p class="resultado-valor" id="energiaGenerada">0 kWh/mes</p>
                                </div>
                            </div>

                            <div class="resultado-item">
                                <div class="resultado-icon">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                                <div class="resultado-content">
                                    <h3>Ahorro Mensual</h3>
                                    <p class="resultado-valor" id="ahorroMensual">$0</p>
                                </div>
                            </div>

                            <div class="resultado-item">
                                <div class="resultado-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="resultado-content">
                                    <h3>Ahorro Anual</h3>
                                    <p class="resultado-valor" id="ahorroAnual">$0</p>
                                </div>
                            </div>

                            <div class="resultado-item">
                                <div class="resultado-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="resultado-content">
                                    <h3>Retorno de Inversión</h3>
                                    <p class="resultado-valor" id="retornoInversion">0 años</p>
                                </div>
                            </div>

                            <div class="resultado-item">
                                <div class="resultado-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="resultado-content">
                                    <h3>Inversión Necesaria</h3>
                                    <p class="resultado-valor" id="inversionNecesaria">$0</p>
                                </div>
                            </div>
                        </div>

                        <div class="grafico-container">
                            <canvas id="graficoAhorro"></canvas>
                        </div>

                        <!-- Nueva sección: Cantidad de paneles recomendados -->
                        <div class="paneles-recomendados-section">
                            <h2><i class="fas fa-solar-panel"></i> Cantidad de Paneles Recomendados</h2>
                            <div class="panel-recommendation-details">
                                <p id="panelesNecesarios">Paneles necesarios: <span class="valor-panel">0</span></p>
                                <p id="panelesInstalables">Paneles que se pueden instalar: <span class="valor-panel">0</span></p>
                                <p id="coberturaConsumo">Cobertura de consumo: <span class="valor-panel">0%</span></p>
                                <p id="mensajePaneles"></p>
                            </div>
                        </div>

                        <div class="acciones-resultados">
                            <button class="btn-guardar" id="btnGuardar">
                                <i class="fas fa-save"></i> Guardar Simulación
                            </button>
                            <button class="btn-nueva" id="btnNuevaSimulacion">
                                <i class="fas fa-plus"></i> Nueva Simulación
                            </button>
                        </div>
                    </div>

                    <div class="info-card" id="infoCard">
                        <h3><i class="fas fa-info-circle"></i> Información Útil</h3>
                        <ul>
                            <li><strong>Consumo promedio:</strong> 350 kWh/mes para una familia de 4 personas</li>
                            <li><strong>Área mínima:</strong> 20 m² para una instalación básica</li>
                            <li><strong>Retorno típico:</strong> 5-8 años dependiendo del consumo</li>
                            <li><strong>Ahorro estimado:</strong> 70-90% en la factura mensual</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['calculadora.js']]); ?>
