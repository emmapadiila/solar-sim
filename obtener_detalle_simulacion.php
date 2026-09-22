<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de simulación inválido']);
    exit();
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
        echo json_encode(['success' => false, 'message' => 'Simulación no encontrada']);
        exit();
    }
    
    $simulacion = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'simulacion' => $simulacion
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalles: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 