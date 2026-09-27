<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Calculadora', 'paginaActiva' => 'calculadora', 'usarGraficos' => true]);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-calculator"></i> Calculadora</p>
                    <h1 class="page-title">Calcula tu ahorro solar</h1>
                    <p class="page-subtitle">Simula cuánto ahorrarías en tu factura eléctrica instalando paneles solares en tu vivienda.</p>
                </div>
            </div>

            <div class="grid grid--sidebar">
                <div class="stack">
                    <form id="calculadoraForm" class="card" novalidate>
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-sliders"></i> Datos de tu vivienda</h2>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="ubicacion"><i class="fas fa-location-dot"></i> Ciudad</label>
                                <select id="ubicacion" name="ubicacion" class="form-control" required>
                                    <option value="">Selecciona</option>
                                    <?php foreach (Calculadora::CIUDADES as $clave => $ciudad): ?>
                                    <option value="<?= e($clave) ?>"><?= e($ciudad['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="estrato"><i class="fas fa-layer-group"></i> Estrato</label>
                                <select id="estrato" name="estrato" class="form-control" required>
                                    <option value="">Selecciona</option>
                                    <?php foreach (array_keys(Calculadora::PRECIO_KWH) as $estrato): ?>
                                    <option value="<?= $estrato ?>">Estrato <?= $estrato ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="consumo_mensual"><i class="fas fa-bolt"></i> Consumo mensual (kWh)</label>
                            <input type="number" id="consumo_mensual" name="consumo_mensual" class="form-control"
                                   placeholder="Ej: 350" min="<?= Calculadora::CONSUMO_MIN ?>" max="<?= Calculadora::CONSUMO_MAX ?>" required>
                            <small class="form-hint">Aparece en tu factura de energía. Un hogar de 4 personas consume unos 350 kWh.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="area_disponible"><i class="fas fa-house"></i> Área disponible en el techo (m²)</label>
                            <input type="number" id="area_disponible" name="area_disponible" class="form-control"
                                   placeholder="Ej: 40" min="<?= Calculadora::AREA_MIN ?>" max="<?= Calculadora::AREA_MAX ?>" required>
                            <small class="form-hint">Superficie libre de sombras. Cada panel ocupa unos 1,7 m².</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tipo_energia"><i class="fas fa-plug"></i> Tipo de energía actual</label>
                            <select id="tipo_energia" name="tipo_energia" class="form-control" required>
                                <option value="">Selecciona</option>
                                <?php foreach (Calculadora::TIPOS_ENERGIA as $clave => $nombre): ?>
                                <option value="<?= e($clave) ?>"><?= e($nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn--dark btn--lg btn--block"><i class="fas fa-calculator"></i> Calcular ahorro</button>
                    </form>

                    <details class="disclosure">
                        <summary><i class="fas fa-circle-info"></i> Requisitos para una instalación eficiente</summary>
                        <div class="disclosure__body">
                            <ul class="checklist">
                                <li>Azotea, tejado o terraza con buena exposición al sol.</li>
                                <li>Orientación preferiblemente al norte u occidente.</li>
                                <li>Sin sombras de árboles, edificios u otros obstáculos.</li>
                                <li>Techo amplio, plano o con inclinación uniforme.</li>
                                <li>Estructura capaz de soportar el peso del sistema.</li>
                            </ul>
                            <div class="alert alert--warning mt-4">
                                <i class="fas fa-triangle-exclamation"></i>
                                <span>No se recomienda en techos muy pequeños, con inclinaciones irregulares, materiales inestables o en zonas con mucha sombra o lluvias constantes.</span>
                            </div>
                        </div>
                    </details>
                </div>

                <div>
                    <!-- Estado inicial -->
                    <section class="card" id="infoCard">
                        <div class="empty-state">
                            <span class="icon-badge"><i class="fas fa-solar-panel"></i></span>
                            <h2>Tus resultados aparecerán aquí</h2>
                            <p>Completa los datos de tu vivienda y pulsa <strong>Calcular ahorro</strong> para ver la estimación.</p>
                        </div>
                        <dl class="data-list">
                            <div><dt>Retorno típico</dt><dd>5 – 8 años</dd></div>
                            <div><dt>Ahorro habitual</dt><dd>70 – 90 % de la factura</dd></div>
                        </dl>
                    </section>

                    <!-- Resultados -->
                    <section class="card oculto" id="resultadosCard">
                        <div class="card__header">
                            <h2 class="card__title"><i class="fas fa-chart-line"></i> Resultados de la simulación</h2>
                        </div>

                        <div class="highlight">
                            <p class="highlight__label">Ahorro mensual estimado</p>
                            <p class="highlight__value" id="ahorroMensual">$0</p>
                            <p class="highlight__note"><span id="ahorroAnual">$0</span> al año</p>
                        </div>

                        <div class="grid grid--3 mt-5">
                            <div class="stat">
                                <div class="stat__body">
                                    <p class="stat__label">Energía generada</p>
                                    <p class="stat__value" id="energiaGenerada">0</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat__body">
                                    <p class="stat__label">Inversión estimada</p>
                                    <p class="stat__value" id="inversionNecesaria">$0</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat__body">
                                    <p class="stat__label">Retorno de inversión</p>
                                    <p class="stat__value" id="retornoInversion">0</p>
                                </div>
                            </div>
                        </div>

                        <h3 class="section-title mt-5">Paneles recomendados</h3>
                        <dl class="data-list data-list--3">
                            <div id="panelesNecesarios"><dt>Necesarios</dt><dd class="valor-panel">0</dd></div>
                            <div id="panelesInstalables"><dt>Caben en tu techo</dt><dd class="valor-panel">0</dd></div>
                            <div id="coberturaConsumo"><dt>Cobertura</dt><dd class="valor-panel">0 %</dd></div>
                        </dl>
                        <div class="alert alert--info mt-4">
                            <i class="fas fa-circle-info"></i>
                            <span id="mensajePaneles"></span>
                        </div>

                        <h3 class="section-title mt-5">Consumo vs. generación</h3>
                        <div class="chart-box">
                            <canvas id="graficoAhorro"></canvas>
                        </div>

                        <div class="btn-group mt-5">
                            <button type="button" class="btn btn--primary" id="btnGuardar"><i class="fas fa-floppy-disk"></i> Guardar simulación</button>
                            <button type="button" class="btn btn--secondary" id="btnNuevaSimulacion"><i class="fas fa-rotate-left"></i> Nueva simulación</button>
                            <a href="historial.php" class="btn btn--ghost oculto" id="btnVerHistorial">Ver en historial <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['calculadora.js']]); ?>
