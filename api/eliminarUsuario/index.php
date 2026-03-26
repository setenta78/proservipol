<?php
session_start();
// Incluir tools.php PRIMERO (compatible con PHP 5.1.2)
include_once("../tools.php");
// Deshabilitar salida de errores HTML
error_reporting(0);
ini_set('display_errors', 0);
// Validar sesión activa
if (!isset($_SESSION['user_id'])) {
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
// Solo permitir método DELETE o POST
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Método no permitido. Use DELETE o POST.",
        "code" => 405
    ));
    exit;
}
try {
    // Obtener datos del cuerpo de la petición
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Datos JSON inválidos",
            "code" => 400
        ));
        exit;
    }
    // Validar datos
    $params = rules($data);
    if ($params["code"] == "412") {
        http_response_code(412);
        echo json_encode($params);
        exit;
    }
    // Agregar usuario que elimina el registro
    $params['usuarioEliminacion'] = $_SESSION['user_id'];
    $objDBUsuario = new dbUsuario();
    $resultado = $objDBUsuario->eliminarUsuario($params);
    if ($resultado['success']) {
        http_response_code(200);
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode($resultado);
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