<?php
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error al procesar la solicitud.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $response['message'] = 'Datos JSON inválidos.';
        echo json_encode($response);
        exit;
    }

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $subject = trim($data['subject'] ?? 'Mensaje de Contacto');
    $message = trim($data['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Por favor, completa todos los campos requeridos (Nombre, Correo Electrónico, Mensaje).';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'El formato del correo electrónico es inválido.';
        echo json_encode($response);
        exit;
    }

    // SIMULACIÓN: Siempre devuelve éxito sin intentar enviar el correo
    $response['success'] = true;
    $response['message'] = '¡Gracias! Tu mensaje ha sido enviado exitosamente.';

} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?> 