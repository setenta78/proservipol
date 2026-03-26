<?php
session_start();
// Validar sesión activa
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(401);
    echo json_encode(array(
        "message" => "Sesión no válida. Por favor, inicie sesión nuevamente.",
        "code" => 401
    ));
    exit;
}
include_once("../tools.php");
include_once("../db/dbFuncionario.Class.php");
include_once("request.php");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json; charset=UTF-8');
try {
    $params = rules();
    if ($params["code"] != "412") {
        $objDBFuncionario = new dbFuncionario();
        $resultado = $objDBFuncionario->cargosFuncionarioPorCodigo(
            $params['codigoFuncionario'], 
            $params['fechaDesde'], 
            $params['fechaHasta']
        );
        echo _json_encode($resultado);
    } else {
        http_response_code(412);
        echo _json_encode($params);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "message" => "Error interno del servidor: " . $e->getMessage(),
        "code" => 500
    ));
}
?>