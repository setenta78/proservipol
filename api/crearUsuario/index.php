<?php
/**
 * crearUsuario/index.php
 * Registra un nuevo usuario en PROSERVIPOL.
 * El funcionario debe existir en FUNCIONARIO (sincronizado desde personal).
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

// Verificar permiso REGISTRAR
if (!isset($_SESSION['REGISTRAR']) || $_SESSION['REGISTRAR'] != 1) {
    echo json_encode(array("success" => false, "message" => "No tiene permisos para registrar usuarios"));
    exit;
}

// Leer y validar parámetros
$funCodigo  = isset($_POST['fun_codigo'])  ? strtoupper(trim($_POST['fun_codigo']))  : '';
$uniCodigo  = isset($_POST['uni_codigo'])  ? intval($_POST['uni_codigo'])  : 0;
$usLogin    = isset($_POST['us_login'])    ? trim($_POST['us_login'])    : '';
$usPassword = isset($_POST['us_password']) ? trim($_POST['us_password']) : '';
$tusCodigo  = isset($_POST['tus_codigo'])  ? intval($_POST['tus_codigo'])  : 0;
$usActivo   = isset($_POST['us_activo'])   ? intval($_POST['us_activo'])   : 1;

if (empty($funCodigo)) {
    echo json_encode(array("success" => false, "message" => "Código de funcionario requerido"));
    exit;
}
if (empty($usLogin)) {
    echo json_encode(array("success" => false, "message" => "Login requerido"));
    exit;
}
if (empty($usPassword)) {
    echo json_encode(array("success" => false, "message" => "Password requerida"));
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
$usPasswordEsc = mysql_real_escape_string($usPassword, $conn);

// Verificar que el funcionario existe en FUNCIONARIO
$sqlFun = "SELECT FUN_CODIGO FROM FUNCIONARIO WHERE FUN_CODIGO = '{$funCodigoEsc}'";
$resFun = mysql_query($sqlFun, $conn);
if (!$resFun || mysql_num_rows($resFun) == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Funcionario no encontrado en el sistema"));
    exit;
}

// Verificar que no existe ya como usuario
$sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigoEsc}'";
$resCheck = mysql_query($sqlCheck, $conn);
if ($resCheck && mysql_num_rows($resCheck) > 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "El funcionario ya tiene un usuario registrado"));
    exit;
}

// Verificar que US_LOGIN no está en uso
$sqlLogin = "SELECT FUN_CODIGO FROM USUARIO WHERE US_LOGIN = '{$usLoginEsc}'";
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

// Insertar nuevo usuario
$sql = "INSERT INTO USUARIO (
            FUN_CODIGO,
            UNI_CODIGO,
            US_LOGIN,
            US_PASSWORD,
            TUS_CODIGO,
            US_FECHACREACION,
            US_ACTIVO
        ) VALUES (
            '{$funCodigoEsc}',
            {$uniCodigo},
            '{$usLoginEsc}',
            '{$usPasswordEsc}',
            {$tusCodigo},
            NOW(),
            {$usActivo}
        )";

$result = mysql_query($sql, $conn);

if ($result) {
    mysql_close($conn);
    echo json_encode(array(
        "success"   => true,
        "message"   => "Usuario creado exitosamente",
        "funCodigo" => $funCodigo
    ));
} else {
    $error = mysql_error($conn);
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Error al crear usuario: " . $error));
}
exit;
?>