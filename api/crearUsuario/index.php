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

include_once("../tools.php");
error_reporting(0);
ini_set('display_errors', 0);

// Validar sesión activa
if (!isset($_SESSION['USUARIO_CODIGOFUNCIONARIO'])) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(401);
    echo json_encode(array(
        "success" => false,
        "message" => "Sesión no válida. Por favor, inicie sesión nuevamente.",
        "code" => 401
    ));
    exit;
}

// Incluir configuración y consultas específicas de PROSERVIPOL
require_once "../../queries/config.php";
global $link;
require_once "../../queries/usuario_queries.php";

header('Content-Type: application/json; charset=UTF-8');

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Método no permitido. Use POST.",
        "code" => 405
    ));
    exit;
}

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validar campos obligatorios (compatible con PHP 5.1.2)
    $codFuncionario = isset($data['codFuncionario']) ? trim($data['codFuncionario']) : '';
    $codigoUnidad = isset($data['codigoUnidad']) ? trim($data['codigoUnidad']) : '';
    $tipoUsuario = isset($data['tipoUsuario']) ? trim($data['tipoUsuario']) : '';
    $password = isset($data['password']) ? trim($data['password']) : '';

    if (!$data || 
        empty($codFuncionario) ||
        empty($codigoUnidad) ||
        empty($tipoUsuario) ||
        empty($password)
    ) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Faltan datos obligatorios: codFuncionario, codigoUnidad, tipoUsuario, password.",
            "code" => 400
        ));
        exit;
    }

    // Ejecutar la función de creación/reactivación
    $resultado = crearUsuarioProservipol(
        $codFuncionario,
        $codigoUnidad,
        $tipoUsuario,
        $password,
        $_SESSION['USUARIO_CODIGOFUNCIONARIO']
    );

    // Establecer código de respuesta HTTP basado en el resultado
    if ($resultado['success']) {
        http_response_code(201); // Created
    } else {
        // Usar el código devuelto por la función si existe, sino 400
        $code = isset($resultado['code']) ? $resultado['code'] : 400;
        http_response_code($code);
    }
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error interno del servidor: " . $e->getMessage(),
        "code" => 500
    ));
}
?>