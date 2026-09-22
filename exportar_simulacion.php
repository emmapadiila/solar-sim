<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de simulación inválido');
}

$id_simulacion = intval($_GET['id']);
$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    require_once 'conexion.php';
    
    // Obtener detalles de la simulación
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
        WHERE s.id_simulacion = ? AND s.id_usuarioFK = ?
    ");
    
    $stmt->bind_param("ii", $id_simulacion, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die('Simulación no encontrada');
    }
    
    $simulacion = $result->fetch_assoc();
    
    // Generar HTML para el PDF
    $html = generarHTMLSimulacion($simulacion);
    
    // Configurar headers para descarga
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="simulacion_' . $id_simulacion . '.html"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    echo $html;
    
} catch (Exception $e) {
    die('Error al exportar: ' . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

function generarHTMLSimulacion($simulacion) {
    $fecha = date('d/m/Y H:i', strtotime($simulacion['fecha']));
    
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Simulación Solar - ' . $simulacion['id_simulacion'] . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f8f9fa;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #4361ee;
            }
            .header h1 {
                color: #4361ee;
                margin: 0;
                font-size: 28px;
            }
            .header p {
                color: #6c757d;
                margin: 10px 0 0 0;
            }
            .section {
                margin-bottom: 25px;
            }
            .section h2 {
                color: #4361ee;
                border-bottom: 1px solid #e9ecef;
                padding-bottom: 5px;
                margin-bottom: 15px;
            }
            .grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            .item {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #4361ee;
            }
            .item h3 {
                margin: 0 0 5px 0;
                color: #4361ee;
                font-size: 14px;
            }
            .item p {
                margin: 0;
                font-size: 18px;
                font-weight: bold;
                color: #2ecc71;
            }
            .results {
                background: linear-gradient(135deg, #e3f2fd, #bbdefb);
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e9ecef;
                color: #6c757d;
                font-size: 12px;
            }
            @media print {
                body { background: white; }
                .container { box-shadow: none; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🌞 Simulación de Paneles Solares</h1>
                <p>Reporte generado el ' . $fecha . '</p>
            </div>
            
            <div class="section">
                <h2>📋 Información de la Simulación</h2>
                <div class="grid">
                    <div class="item">
                        <h3>Ubicación</h3>
                        <p>' . ucfirst($simulacion['ubicacion']) . '</p>
                    </div>
                    <div class="item">
                        <h3>Estrato</h3>
                        <p>' . $simulacion['estrato'] . '</p>
                    </div>
                    <div class="item">
                        <h3>Consumo Mensual</h3>
                        <p>' . $simulacion['consumo_mensual'] . ' kWh/mes</p>
                    </div>
                    <div class="item">
                        <h3>Área Disponible</h3>
                        <p>' . $simulacion['area_disponible'] . ' m²</p>
                    </div>
                    <div class="item">
                        <h3>Tipo de Energía</h3>
                        <p>' . ucfirst($simulacion['tipo_energia']) . '</p>
                    </div>
                    <div class="item">
                        <h3>ID Simulación</h3>
                        <p>' . $simulacion['id_simulacion'] . '</p>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>📊 Resultados de la Simulación</h2>
                <div class="results">
                    <div class="grid">
                        <div class="item">
                            <h3>⚡ Energía Generada</h3>
                            <p>' . $simulacion['energia_generada'] . ' kWh/mes</p>
                        </div>
                        <div class="item">
                            <h3>💰 Ahorro Mensual</h3>
                            <p>$' . number_format($simulacion['ahorro_mensual'], 0, ',', '.') . '</p>
                        </div>
                        <div class="item">
                            <h3>📅 Ahorro Anual</h3>
                            <p>$' . number_format($simulacion['ahorro_anual'], 0, ',', '.') . '</p>
                        </div>
                        <div class="item">
                            <h3>⏰ Retorno de Inversión</h3>
                            <p>' . $simulacion['retorno_inversion'] . ' años</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>💡 Información Adicional</h2>
                <ul>
                    <li><strong>Consumo promedio:</strong> 350 kWh/mes para una familia de 4 personas</li>
                    <li><strong>Área mínima:</strong> 20 m² para una instalación básica</li>
                    <li><strong>Retorno típico:</strong> 5-8 años dependiendo del consumo</li>
                    <li><strong>Ahorro estimado:</strong> 70-90% en la factura mensual</li>
                </ul>
            </div>
            
            <div class="footer">
                <p>🌱 SolarSim - Transformando la energía del sol en tu ahorro</p>
                <p>Este reporte fue generado automáticamente por el sistema SolarSim</p>
            </div>
        </div>
    </body>
    </html>';
}
?> 