<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$usuario = $_SESSION['usuario'];
$nombre_usuario = htmlspecialchars($usuario['nombre']);
$is_admin = false;
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin') {
    $is_admin = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Solar - SolarSim</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-solar-panel"></i>
                <span>SolarSim</span>
            </a>
            <nav>
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="dashboard.php" class="nav__link">Inicio</a>
                    </li>
                    <li class="nav__item">
                        <a href="calculadora_solar.php" class="nav__link active">Calculadora</a>
                    </li>
                    <li class="nav__item">
                        <a href="historial_simulaciones.php" class="nav__link">Historial</a>
                    </li>
                    <li class="nav__item">
                        <a href="educativo.html" class="nav__link">Contenido Educativo</a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li class="nav__item">
                        <a href="estadisticas.php" class="nav__link">Estadísticas</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav__item">
                        <a href="about.php" class="nav__link">Acerca de Nosotros</a>
                    </li>
                    <li class="nav__item">
                        <a href="contact.php" class="nav__link">Contacto</a>
                    </li>
                    <li class="nav__item">
                        <a href="#" id="logoutBtn" class="nav__link logout-btn">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

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
                                    <option value="bogota">Bogotá</option>
                                    <option value="medellin">Medellín</option>
                                    <option value="cali">Cali</option>
                                    <option value="barranquilla">Barranquilla</option>
                                    <option value="cartagena">Cartagena</option>
                                    <option value="bucaramanga">Bucaramanga</option>
                                    <option value="pereira">Pereira</option>
                                    <option value="manizales">Manizales</option>
                                    <option value="ibague">Ibagué</option>
                                    <option value="villavicencio">Villavicencio</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="estrato">
                                    <i class="fas fa-layer-group"></i> Estrato Socioeconómico
                                </label>
                                <select id="estrato" name="estrato" required>
                                    <option value="">Selecciona tu estrato</option>
                                    <option value="1">Estrato 1</option>
                                    <option value="2">Estrato 2</option>
                                    <option value="3">Estrato 3</option>
                                    <option value="4">Estrato 4</option>
                                    <option value="5">Estrato 5</option>
                                    <option value="6">Estrato 6</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="consumo_mensual">
                                    <i class="fas fa-bolt"></i> Consumo Mensual (kWh)
                                </label>
                                <input type="number" id="consumo_mensual" name="consumo_mensual" 
                                       placeholder="Ej: 350" min="50" max="2000" required>
                                <small>Puedes encontrar esta información en tu factura de energía</small>
                            </div>

                            <div class="form-group">
                                <label for="area_disponible">
                                    <i class="fas fa-home"></i> Área Disponible (m²)
                                </label>
                                <input type="number" id="area_disponible" name="area_disponible" 
                                       placeholder="Ej: 50" min="10" max="500" required>
                                <small>Área disponible en tu techo para instalar paneles</small>
                            </div>

                            <div class="form-group">
                                <label for="tipo_energia">
                                    <i class="fas fa-plug"></i> Tipo de Energía Actual
                                </label>
                                <select id="tipo_energia" name="tipo_energia" required>
                                    <option value="">Selecciona el tipo</option>
                                    <option value="convencional">Convencional</option>
                                    <option value="renovable">Renovable</option>
                                    <option value="mixta">Mixta</option>
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
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="calculadora_solar.js"></script>
</body>
</html> 