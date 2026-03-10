<?php
session_start();

// Simular sesión activa
$_SESSION['USUARIO_CODIGOFUNCIONARIO'] = '000000A';

// Compatibilidad JSON
if (!function_exists('json_encode')) {
    require_once('../../inc/Services_JSON.php'); // ← RUTA CORRECTA
    function json_encode($content) {
        $json = new Services_JSON();
        return $json->encode($content);
    }
}

header('Content-Type: application/json; charset=UTF-8');

echo json_encode(array(
    "success" => true,
    "message" => "Test OK - JSON funciona correctamente",
    "timestamp" => date('Y-m-d H:i:s')
));
?>