<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    // Obtener datos JSON del cuerpo de la petición
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos JSON inválidos');
    }
    
    // Validar datos requeridos
    $campos_requeridos = ['ubicacion', 'estrato', 'consumo_mensual', 'area_disponible', 'tipo_energia'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($input[$campo]) || empty($input[$campo])) {
            throw new Exception("Campo requerido faltante: $campo");
        }
    }
    
    // Incluir conexión a la base de datos
    require_once 'conexion.php';
    
    // Obtener ID del usuario
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Insertar simulación
        $stmt_simulacion = $conn->prepare("
            INSERT INTO tbl_simulacion 
            (ubicacion, estrato, area_disponible, consumo_mensual, tipo_energia, id_usuarioFK) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt_simulacion->bind_param(
            "siddss", 
            $input['ubicacion'],
            $input['estrato'],
            $input['area_disponible'],
            $input['consumo_mensual'],
            $input['tipo_energia'],
            $id_usuario
        );
        
        if (!$stmt_simulacion->execute()) {
            throw new Exception('Error al insertar simulación: ' . $stmt_simulacion->error);
        }
        
        $id_simulacion = $conn->insert_id;
        
        // Insertar resultados
        $stmt_resultados = $conn->prepare("
            INSERT INTO tbl_resultados 
            (energia_generada, ahorro_mensual, retorno_inversion, id_simulacionFK) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt_resultados->bind_param(
            "dddi",
            $input['energiaGenerada'],
            $input['ahorroMensual'],
            $input['retornoInversion'],
            $id_simulacion
        );
        
        if (!$stmt_resultados->execute()) {
            throw new Exception('Error al insertar resultados: ' . $stmt_resultados->error);
        }
        
        // Verificar si el usuario ya tiene historial
        $stmt_historial = $conn->prepare("
            SELECT id_historial FROM tbl_historial WHERE id_usuarioFK = ?
        ");
        $stmt_historial->bind_param("i", $id_usuario);
        $stmt_historial->execute();
        $result_historial = $stmt_historial->get_result();
        
        if ($result_historial->num_rows > 0) {
            // Actualizar historial existente
            $historial = $result_historial->fetch_assoc();
            $id_historial = $historial['id_historial'];
            
            $stmt_update = $conn->prepare("
                UPDATE tbl_historial 
                SET total_simulaciones = total_simulaciones + 1 
                WHERE id_historial = ?
            ");
            $stmt_update->bind_param("i", $id_historial);
            $stmt_update->execute();
        } else {
            // Crear nuevo historial
            $stmt_new_historial = $conn->prepare("
                INSERT INTO tbl_historial (total_simulaciones, id_usuarioFK) 
                VALUES (1, ?)
            ");
            $stmt_new_historial->bind_param("i", $id_usuario);
            $stmt_new_historial->execute();
            $id_historial = $conn->insert_id;
        }
        
        // Insertar detalle del historial
        $stmt_detalle = $conn->prepare("
            INSERT INTO tbl_detalle_historial 
            (tipo_accion, descripcion, id_historialFK, id_simulacionFK) 
            VALUES ('creacion', ?, ?, ?)
        ");
        
        $descripcion = "Nueva simulación de paneles solares - Ubicación: " . $input['ubicacion'] . 
                      ", Consumo: " . $input['consumo_mensual'] . " kWh/mes";
        
        $stmt_detalle->bind_param("sii", $descripcion, $id_historial, $id_simulacion);
        $stmt_detalle->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Simulación guardada exitosamente',
            'id_simulacion' => $id_simulacion
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 