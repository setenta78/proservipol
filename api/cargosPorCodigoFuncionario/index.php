<?php
/**
 * cargosPorCodigoFuncionario/index.php
 * Retorna los cargos históricos de un funcionario por su código.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
ob_start();
include_once("../tools.php");
session_start();
include_once("../../inc/config.inc.php");
include_once("../db/dbFuncionario.Class.php");
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
    echo _json_encode($params);
    exit;
}

$objDBFuncionario = new dbFuncionario();
$resultado = $objDBFuncionario->cargosFuncionarioPorCodigo(
    $params['codigoFuncionario'],
    $params['fechaDesde'],
    $params['fechaHasta']
);

http_response_code(200);
echo _json_encode($resultado);
exit;
?>