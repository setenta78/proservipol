<?php
/**
 * SISTEMA DE APLICACIONES DE PROSERVIPOL
 * Guardar Token y Establecer Sesión
 * @author Ingeniero C.P.R. Denis Quezada Lemus
 * @compatibility PHP 5.1.2 + MySQL 5.0.77
 * @date 2025
 */

// Iniciar sesión (SIN middleware_auth.php)
session_start();

// Incluir librería JSON para PHP 5.1.2
require_once 'inc/Services_JSON.php';
$json = new Services_JSON();

// Función para responder con JSON
function responderJSON($data, $httpCode = 200) {
    global $json;
    header('Content-Type: application/json; charset=UTF-8');
    echo $json->encode($data);
    exit;
}

// Log de inicio
error_log("[" . date('Y-m-d H:i:s') . "] save_token.php - Inicio");

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("[" . date('Y-m-d H:i:s') . "] Error: Método no permitido");
    responderJSON(array('success' => false, 'message' => 'Método no permitido'), 405);
}

// Obtener datos del POST (compatible PHP 5.1.2)
$access_token = isset($_POST['access_token']) ? trim($_POST['access_token']) : '';
$expires_at = isset($_POST['expires_at']) ? trim($_POST['expires_at']) : '';
$token_type = isset($_POST['token_type']) ? trim($_POST['token_type']) : '';
$codigo_funcionario = isset($_POST['codigo_funcionario']) ? trim($_POST['codigo_funcionario']) : '';

error_log("[" . date('Y-m-d H:i:s') . "] Código funcionario recibido: " . $codigo_funcionario);

// Validar campos requeridos
if (empty($access_token) || empty($expires_at) || empty($codigo_funcionario)) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error: Datos incompletos");
    responderJSON(array('success' => false, 'message' => 'Datos incompletos'), 400);
}

// Conectar a la base de datos
require_once 'queries/config.php';
if (!$link) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error: No se pudo conectar a la BD");
    responderJSON(array('success' => false, 'message' => 'Error de conexión a BD'), 500);
}

// Sanitizar código de funcionario
$codigo_funcionario_safe = mysql_real_escape_string($codigo_funcionario, $link);

// Consultar usuario en BD local
$sql = "SELECT US_ACTIVO, TUS_CODIGO, UNI_CODIGO FROM USUARIO WHERE FUN_CODIGO = '$codigo_funcionario_safe'";
error_log("[" . date('Y-m-d H:i:s') . "] Ejecutando query: " . $sql);

$result = mysql_query($sql, $link);
if (!$result) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error SQL: " . mysql_error($link));
    responderJSON(array('success' => false, 'message' => 'Error en consulta: ' . mysql_error($link)), 500);
}

$row = mysql_fetch_array($result);
if (!$row) {
    error_log("[" . date('Y-m-d H:i:s') . "] Usuario no encontrado en BD local");
    responderJSON(array('success' => false, 'message' => 'Usuario no registrado en sistema Aplicativos'), 404);
}

// Validar que el usuario esté activo
if ($row['US_ACTIVO'] != 1) {
    error_log("[" . date('Y-m-d H:i:s') . "] Usuario inactivo");
    responderJSON(array('success' => false, 'message' => 'Usuario inactivo'), 403);
}

// Convertir a entero para comparación
$tus_codigo = (int)$row['TUS_CODIGO'];
$uni_codigo = (int)$row['UNI_CODIGO'];

// Validar perfil permitido (solo 90 y 310 para Aplicativos)
if (!in_array($tus_codigo, array(90, 310))) {
    error_log("[" . date('Y-m-d H:i:s') . "] Perfil no autorizado: " . $tus_codigo);
    responderJSON(array('success' => false, 'message' => 'No tiene permisos para acceder a este sistema.'), 403);
}

// ==========================================
// NUEVO: REGISTRAR ACCESO EN BITÁCORA
// ==========================================
$ip_usuario = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
$fecha_actual = date('Y-m-d H:i:s');

// Escapar valores para el INSERT
$ip_safe = mysql_real_escape_string($ip_usuario, $link);

$sqlBitacora = "INSERT INTO BITACORA_USUARIO (
    FUN_CODIGO, 
    UNI_CODIGO, 
    US_FECHAHORA_INICIO, 
    US_DIRECCION_IP, 
    TUS_CODIGO, 
    US_EVENTO
) VALUES (
    '$codigo_funcionario_safe',
    $uni_codigo,
    '$fecha_actual',
    '$ip_safe',
    $tus_codigo,
    'INGRESO AL SISTEMA'
)";

$resBitacora = mysql_query($sqlBitacora, $link);

if ($resBitacora) {
    error_log("[" . date('Y-m-d H:i:s') . "] Bitácora registrada: Ingreso de $codigo_funcionario desde IP $ip_usuario");
} else {
    // Si falla la bitácora, logueamos el error pero NO detenemos el login
    error_log("[" . date('Y-m-d H:i:s') . "] ADVERTENCIA: No se pudo registrar en bitácora: " . mysql_error($link));
}
// ==========================================

// Establecer variables de sesión
$_SESSION['access_token'] = $access_token;
$_SESSION['expires_at'] = $expires_at;
$_SESSION['token_type'] = $token_type;
$_SESSION['USUARIO_CODIGOFUNCIONARIO'] = $codigo_funcionario;
$_SESSION['USUARIO_CODIGOPERFIL'] = $tus_codigo;
$_SESSION['HORA_INICIO'] = $fecha_actual;

// Log de éxito
error_log("[" . date('Y-m-d H:i:s') . "] Usuario autenticado exitosamente: " . $codigo_funcionario);
error_log("[" . date('Y-m-d H:i:s') . "] - Perfil: " . $tus_codigo);

mysql_close($link);

// Responder con éxito
responderJSON(array('success' => true));
?>