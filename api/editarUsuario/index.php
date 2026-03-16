<?php
/**
 * editarUsuario/index.php
 * Edita un usuario existente en PROSERVIPOL.
 * Campos editables: UNI_CODIGO, US_LOGIN, US_PASSWORD (opcional), TUS_CODIGO, US_ACTIVO
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */

ob_start();

if (!function_exists('json_encode')) {
    require_once('../../lib/Services_JSON.php');
    function json_encode($data) {
        $json = new Services_JSON();
        return $json->encode($data);
    }
}

session_start();
include_once("../../inc/config.inc.php");

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array("success" => false, "message" => "Método no permitido"));
    exit;
}

// Verificar sesión
if (!isset($_SESSION['FUN_CODIGO'])) {
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada"));
    exit;
}

// Verificar permiso (solo ADMINISTRADOR o MESA DE AYUDA pueden editar)
$tusCodigoSesion = isset($_SESSION['TUS_CODIGO']) ? intval($_SESSION['TUS_CODIGO']) : 0;
if (!in_array($tusCodigoSesion, array(90, 310))) {
    echo json_encode(array("success" => false, "message" => "No tiene permisos para editar usuarios"));
    exit;
}

// Leer y validar parámetros
$funCodigo = isset($_POST['fun_codigo']) ? strtoupper(trim($_POST['fun_codigo'])) : '';
$uniCodigo = isset($_POST['uni_codigo']) ? intval($_POST['uni_codigo']) : 0;
$usLogin   = isset($_POST['us_login'])   ? trim($_POST['us_login'])   : '';
$tusCodigo = isset($_POST['tus_codigo']) ? intval($_POST['tus_codigo']) : 0;
$usActivo  = isset($_POST['us_activo'])  ? intval($_POST['us_activo'])  : 1;
$usPassword = isset($_POST['us_password']) ? trim($_POST['us_password']) : '';

if (empty($funCodigo)) {
    echo json_encode(array("success" => false, "message" => "Código de funcionario requerido"));
    exit;
}
if (empty($usLogin)) {
    echo json_encode(array("success" => false, "message" => "Login requerido"));
    exit;
}
if ($uniCodigo <= 0) {
    echo json_encode(array("success" => false, "message" => "Unidad requerida"));
    exit;
}
if ($tusCodigo <= 0) {
    echo json_encode(array("success" => false, "message" => "Tipo de usuario requerido"));
    exit;
}

// Conexión
$conn = mysql_connect(HOST, USER, PASS);
if (!$conn) {
    echo json_encode(array("success" => false, "message" => "Error de conexión"));
    exit;
}
if (!mysql_select_db(DB, $conn)) {
    echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos"));
    exit;
}
mysql_query("SET NAMES 'utf8'", $conn);

$funCodigoEsc = mysql_real_escape_string($funCodigo, $conn);
$usLoginEsc   = mysql_real_escape_string($usLogin, $conn);

// Verificar que el usuario existe
$sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigoEsc}'";
$resCheck = mysql_query($sqlCheck, $conn);
if (!$resCheck || mysql_num_rows($resCheck) == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Usuario no encontrado"));
    exit;
}

// Verificar que US_LOGIN no está en uso por otro usuario
$sqlLogin = "SELECT FUN_CODIGO FROM USUARIO
             WHERE US_LOGIN = '{$usLoginEsc}' AND FUN_CODIGO != '{$funCodigoEsc}'";
$resLogin = mysql_query($sqlLogin, $conn);
if ($resLogin && mysql_num_rows($resLogin) > 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "El login ya está en uso por otro usuario"));
    exit;
}

// Verificar que UNI_CODIGO existe
$sqlUni = "SELECT UNI_CODIGO FROM UNIDAD WHERE UNI_CODIGO = {$uniCodigo}";
$resUni = mysql_query($sqlUni, $conn);
if (!$resUni || mysql_num_rows($resUni) == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "La unidad especificada no existe"));
    exit;
}

// Verificar que TUS_CODIGO existe y está activo
$sqlTus = "SELECT TUS_CODIGO FROM TIPO_USUARIO WHERE TUS_CODIGO = {$tusCodigo} AND TUS_ACTIVO = 1";
$resTus = mysql_query($sqlTus, $conn);
if (!$resTus || mysql_num_rows($resTus) == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "El tipo de usuario especificado no es válido"));
    exit;
}

// Construir SET dinámico
$setParts = array(
    "UNI_CODIGO = {$uniCodigo}",
    "US_LOGIN = '{$usLoginEsc}'",
    "TUS_CODIGO = {$tusCodigo}",
    "US_ACTIVO = {$usActivo}",
    "US_FECHAMODIFICACION = NOW()"
);

if (!empty($usPassword)) {
    $usPasswordEsc = mysql_real_escape_string($usPassword, $conn);
    $setParts[] = "US_PASSWORD = '{$usPasswordEsc}'";
}

$setClause = implode(", ", $setParts);
$sql = "UPDATE USUARIO SET {$setClause} WHERE FUN_CODIGO = '{$funCodigoEsc}'";

$result = mysql_query($sql, $conn);

if ($result) {
    mysql_close($conn);
    echo json_encode(array("success" => true, "message" => "Usuario actualizado exitosamente"));
} else {
    $error = mysql_error($conn);
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Error al actualizar: " . $error));
}
exit;
?>