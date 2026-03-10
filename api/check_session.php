<?php
/**
 * API para verificar el estado de la sesión
 * Retorna JSON indicando si la sesión está activa
 */

// Configuración de headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Iniciar sesión
session_start();

// Tiempo máximo de inactividad (15 minutos)
$max_inactivity = 15 * 60; // 15 minutos en segundos

// Verificar si la sesión existe y está activa
$session_active = false;
$message = '';

if (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO']) && isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($inactive_time < $max_inactivity) {
        // Sesión activa - actualizar tiempo de última actividad
        $_SESSION['LAST_ACTIVITY'] = time();
        $session_active = true;
        $message = 'Sesión activa';
    } else {
        // Sesión expirada por inactividad
        $session_active = false;
        $message = 'Sesión expirada por inactividad';
        
        // Destruir sesión
        session_unset();
        session_destroy();
    }
} else {
    // No hay sesión activa
    $session_active = false;
    $message = 'No hay sesión activa';
}

// Respuesta JSON
$response = array(
    'active' => $session_active,
    'message' => $message,
    'timestamp' => time()
);

echo json_encode($response);
exit;
?>