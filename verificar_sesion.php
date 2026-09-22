<?php
session_start();

function verificarSesion() {
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['logged_in' => false]);
        exit;
    }
    
    echo json_encode([
        'logged_in' => true,
        'usuario' => $_SESSION['usuario']
    ]);
}

verificarSesion();
?> 