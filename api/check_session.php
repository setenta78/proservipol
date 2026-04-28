<?php
session_start();

// ============================================
// COMPATIBILIDAD PHP 5.1.2: json_encode/decode
// ============================================
if (!function_exists('json_decode')) {
    require_once('../../inc/Services_JSON.php');
    function json_decode($content, $assoc = false) {
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $json = new Services_JSON();
        }
        return $json->decode($content);
    }
}

if (!function_exists('json_encode')) {
    require_once('../../inc/Services_JSON.php');
    function json_encode($content) {
        $json = new Services_JSON();
        return $json->encode($content);
    }
}

// CRÍTICO: Desactivar salida de errores HTML para no romper el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=UTF-8');

$response = array(
    'active' => false,
    'message' => 'Sesión no encontrada'
);

try {
    // Verificar si existe la variable de sesión principal
    if (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO'])) {
        
        // Verificar tiempo de inactividad si está definido
        $max_inactivity = 15 * 60; // 15 minutos
        
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
            
            if ($inactive_time > $max_inactivity) {
                // Sesión expirada
                session_unset();
                session_destroy();
                $response['message'] = 'Sesión expirada por inactividad';
                echo json_encode($response);
                exit;
            }
        }
        
        // Actualizar tiempo de actividad
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Sesión activa
        $response['active'] = true;
        $response['message'] = 'Sesión activa';
        $response['user'] = array(
            'codigo' => $_SESSION['USUARIO_CODIGOFUNCIONARIO'],
            'perfil' => isset($_SESSION['USUARIO_CODIGOPERFIL']) ? $_SESSION['USUARIO_CODIGOPERFIL'] : null
        );
    } else {
        $response['message'] = 'No hay sesión iniciada';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // En caso de excepción, devolver JSON de error
    $response['message'] = 'Error interno: ' . $e->getMessage();
    echo json_encode($response);
}
?>