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

// Obtener historial de simulaciones
require_once 'conexion.php';

$id_usuario = $_SESSION['usuario']['id_usuario'];
$simulaciones = [];

try {
    $stmt = $conn->prepare("
        SELECT 
            s.id_simulacion,
            s.fecha,
            s.ubicacion,
            s.estrato,
            s.area_disponible,
            s.consumo_mensual,
            s.tipo_energia,
            r.energia_generada,
            r.ahorro_mensual,
            r.ahorro_anual,
            r.retorno_inversion
        FROM tbl_simulacion s
        LEFT JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK
        WHERE s.id_usuarioFK = ?
        ORDER BY s.fecha DESC
    ");
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $simulaciones[] = $row;
    }
    
} catch (Exception $e) {
    $error = "Error al cargar el historial: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Simulaciones - SolarSim</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <a href="calculadora_solar.php" class="nav__link">Calculadora</a>
                    </li>
                    <li class="nav__item">
                        <a href="historial_simulaciones.php" class="nav__link active">Historial</a>
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

    <main class="historial-content">
        <div class="historial-container">
            <div class="historial-header">
                <h1><i class="fas fa-history"></i> Historial de Simulaciones</h1>
                <p>Revisa todas tus simulaciones de paneles solares</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($simulaciones)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h2>No tienes simulaciones aún</h2>
                    <p>Realiza tu primera simulación para ver el historial aquí</p>
                    <a href="calculadora_solar.php" class="btn-primary">
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
                            <p><?php echo count($simulaciones); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Ahorro Promedio</h3>
                            <p>$<?php 
                                $ahorro_promedio = array_sum(array_column($simulaciones, 'ahorro_mensual')) / count($simulaciones);
                                echo number_format($ahorro_promedio, 0, ',', '.');
                            ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Retorno Promedio</h3>
                            <p><?php 
                                $retorno_promedio = array_sum(array_column($simulaciones, 'retorno_inversion')) / count($simulaciones);
                                echo number_format($retorno_promedio, 1, ',', '.') . ' años';
                            ?></p>
                        </div>
                    </div>
                </div>

                <div class="simulaciones-grid">
                    <?php foreach ($simulaciones as $simulacion): ?>
                        <div class="simulacion-card">
                            <div class="simulacion-header">
                                <div class="simulacion-fecha">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($simulacion['fecha'])); ?>
                                </div>
                                <div class="simulacion-ubicacion">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo ucfirst(htmlspecialchars($simulacion['ubicacion'])); ?>
                                </div>
                            </div>
                            
                            <div class="simulacion-details">
                                <div class="detail-row">
                                    <span class="detail-label">Estrato:</span>
                                    <span class="detail-value"><?php echo $simulacion['estrato']; ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Consumo:</span>
                                    <span class="detail-value"><?php echo $simulacion['consumo_mensual']; ?> kWh/mes</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Área:</span>
                                    <span class="detail-value"><?php echo $simulacion['area_disponible']; ?> m²</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tipo Energía:</span>
                                    <span class="detail-value"><?php echo ucfirst($simulacion['tipo_energia']); ?></span>
                                </div>
                            </div>
                            
                            <div class="simulacion-results">
                                <div class="result-item">
                                    <i class="fas fa-solar-panel"></i>
                                    <div>
                                        <span class="result-label">Energía Generada</span>
                                        <span class="result-value"><?php echo $simulacion['energia_generada']; ?> kWh/mes</span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-piggy-bank"></i>
                                    <div>
                                        <span class="result-label">Ahorro Mensual</span>
                                        <span class="result-value">$<?php echo number_format($simulacion['ahorro_mensual'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <span class="result-label">Ahorro Anual</span>
                                        <span class="result-value">$<?php echo number_format($simulacion['ahorro_anual'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="result-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="result-label">Retorno Inversión</span>
                                        <span class="result-value"><?php echo $simulacion['retorno_inversion']; ?> años</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="simulacion-actions">
                                <button class="btn-ver" onclick="verDetalle(<?php echo $simulacion['id_simulacion']; ?>)">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </button>
                                <button class="btn-exportar" onclick="exportarSimulacion(<?php echo $simulacion['id_simulacion']; ?>)">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="historial_simulaciones.js"></script>
</body>
</html> 