<?php
session_start();

// Incluir librería JSON para PHP 5.1.2
require_once "../../inc/Services_JSON.php";
$json = new Services_JSON();

// Incluir tools.php PRIMERO (compatible con PHP 5.1.2)
include_once("../tools.php");

// Deshabilitar salida de errores HTML
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

include_once("../db/dbUsuario.Class.php");
include_once("request.php");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

try {
    $params = rules();
    
    if ($params["code"] == "412") {
        http_response_code(412);
        echo json_encode($params);
        exit;
    }
    
    $objDBUsuario = new dbUsuario();
    $resultado = $objDBUsuario->buscarUsuario($params);
    
    if (isset($resultado['data']) && $resultado['data'] !== false) {
        http_response_code(200);
        echo json_encode($resultado);
    } else {
        http_response_code(404);
        echo json_encode(array(
            "success" => false,
            "message" => "Usuario no encontrado",
            "data" => false
        ));
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error interno del servidor: " . $e->getMessage(),
        "code" => 500
    ));
}
?>