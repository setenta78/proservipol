<?php
/**
 * eliminarUsuario/index.php
 * Desactiva un usuario (baja lógica: US_ACTIVO = 0)
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

// Solo ADMINISTRADOR o MESA DE AYUDA pueden eliminar
$tusCodigoSesion = isset($_SESSION['TUS_CODIGO']) ? intval($_SESSION['TUS_CODIGO']) : 0;
if (!in_array($tusCodigoSesion, array(90, 310))) {
    http_response_code(403);
    echo json_encode(array("success" => false, "message" => "No tiene permisos para eliminar usuarios", "code" => 403));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Método no permitido", "code" => 405));
    exit;
}

// Leer fun_codigo desde POST o JSON body
$funCodigo = '';
if (!empty($_POST['fun_codigo'])) {
    $funCodigo = strtoupper(trim($_POST['fun_codigo']));
} else {
    $body = file_get_contents('php://input');
    if ($body) {
        $data = json_decode($body, true);
        if (isset($data['fun_codigo'])) {
            $funCodigo = strtoupper(trim($data['fun_codigo']));
        }
    }
}

if (empty($funCodigo)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Código de funcionario requerido", "code" => 400));
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

$funCodigoEsc = mysql_real_escape_string($funCodigo, $conn);

// Verificar que el usuario existe
$sqlCheck = "SELECT FUN_CODIGO, US_ACTIVO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigoEsc}'";
$resCheck = mysql_query($sqlCheck, $conn);
if (!$resCheck || mysql_num_rows($resCheck) == 0) {
    mysql_close($conn);
    http_response_code(404);
    echo json_encode(array("success" => false, "message" => "Usuario no encontrado", "code" => 404));
    exit;
}

$rowCheck = mysql_fetch_array($resCheck);
if ($rowCheck['US_ACTIVO'] == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "El usuario ya está desactivado"));
    exit;
}

// No permitir que el usuario se elimine a sí mismo
if ($funCodigo === $_SESSION['FUN_CODIGO']) {
    mysql_close($conn);
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "No puede desactivar su propio usuario", "code" => 400));
    exit;
}

$sql = "UPDATE USUARIO SET
            US_ACTIVO = 0,
            US_FECHAMODIFICACION = NOW()
        WHERE FUN_CODIGO = '{$funCodigoEsc}'";

$result = mysql_query($sql, $conn);

if ($result && mysql_affected_rows($conn) > 0) {
    mysql_close($conn);
    echo json_encode(array("success" => true, "message" => "Usuario desactivado exitosamente"));
} else {
    $error = mysql_error($conn);
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Error al desactivar usuario: " . $error));
}
exit;
?>