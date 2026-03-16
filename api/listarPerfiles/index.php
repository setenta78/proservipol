<?php
/**
 * listarPerfiles/index.php
 * Lista los tipos de usuario activos desde TIPO_USUARIO
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
ob_start();
include_once("../tools.php");
session_start();
include_once("../../inc/config.inc.php");
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['FUN_CODIGO'])) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada", "code" => 401));
    exit;
}

$conn = mysql_connect(HOST, USER, PASS);
if (!$conn) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error de conexión", "code" => 500));
    exit;
}
if (!mysql_select_db(DB, $conn)) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos", "code" => 500));
    exit;
}
mysql_query("SET NAMES 'utf8'", $conn);

$sql = "SELECT
            TUS_CODIGO,
            TUS_DESCRIPCION,
            VALIDAR,
            VALIDAR_OIC,
            REGISTRAR,
            CONSULTAR_UNIDAD,
            CONSULTAR_PERFIL
        FROM TIPO_USUARIO
        WHERE TUS_ACTIVO = 1
        ORDER BY TUS_CODIGO";

$result = mysql_query($sql, $conn);

if (!$result) {
    $error = mysql_error($conn);
    mysql_close($conn);
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error en consulta: " . $error));
    exit;
}

$perfiles = array();
while ($row = mysql_fetch_array($result)) {
    $perfiles[] = array(
        "tusCodigo"       => (int)$row["TUS_CODIGO"],
        "tusDescripcion"  => utf8_encode($row["TUS_DESCRIPCION"]),
        "validar"         => (int)$row["VALIDAR"],
        "validarOic"      => (int)$row["VALIDAR_OIC"],
        "registrar"       => (int)$row["REGISTRAR"],
        "consultarUnidad" => (int)$row["CONSULTAR_UNIDAD"],
        "consultarPerfil" => (int)$row["CONSULTAR_PERFIL"]
    );
}

mysql_close($conn);

echo json_encode(array(
    "success" => true,
    "data"    => $perfiles,
    "total"   => count($perfiles)
));
exit;
?>