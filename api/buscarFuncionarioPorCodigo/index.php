<?php
/**
 * buscarFuncionarioPorCodigo/index.php
 * Busca un usuario PROSERVIPOL registrado por su código de funcionario.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
ob_start();
include_once("../tools.php");
session_start();
include_once("../../inc/config.inc.php");
include_once("../db/dbUsuario.Class.php");
include_once("request.php");
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['FUN_CODIGO'])) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada", "code" => 401));
    exit;
}

$params = rules();
if ($params["code"] == "412") {
    http_response_code(412);
    echo json_encode($params);
    exit;
}

$objDBUsuario = new dbUsuario();
$resultado = $objDBUsuario->buscarUsuarioPorCodigo($params['funCodigo']);

if (isset($resultado['data']) && $resultado['data'] !== false) {
    http_response_code(200);
    echo json_encode($resultado);
} else {
    http_response_code(404);
    echo json_encode(array(
        "success" => false,
        "message" => "Usuario no encontrado",
        "data"    => false
    ));
}
exit;
?>