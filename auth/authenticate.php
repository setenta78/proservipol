<?php
header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL & ~E_NOTICE);
session_start();

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('success' => false, 'message' => 'Método no permitido'));
    exit;
}

// Leer cuerpo JSON
$input = json_decode(file_get_contents('php://input'), true);
$rut = trim($input['rut'] ? $input['rut'] : '');
$password = trim($input['password'] ? $input['password'] : '');

if (empty($rut) || empty($password)) {
    http_response_code(400);
    echo json_encode(array('success' => false, 'message' => 'RUT y contraseña son obligatorios'));
    exit;
}

// === Paso 1: Autenticar contra Autentificatic API ===
define('AUTENTIFICATIC_API_URL', 'http://autentificaticapi.carabineros.cl');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, AUTENTIFICATIC_API_URL . '/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'username' => $rut,
    'password' => $password
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    // Devolver un mensaje específico basado en la respuesta de Autentificatic
    $errorMessage = 'Credenciales inválidas';
    if ($response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['errors'])) {
            $errorMessage = $responseData['errors'];
        } elseif (isset($responseData['message'])) {
            $errorMessage = $responseData['message'];
        }
    }
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => $errorMessage));
    exit;
}

$data = json_decode($response, true);
$access_token = isset($data['success']['access_token']) ? $data['success']['access_token'] : '';
$codigo_funcionario = isset($data['success']['user']['codigo_funcionario']) ? $data['success']['user']['codigo_funcionario'] : '';

if (empty($access_token) || empty($codigo_funcionario)) {
    error_log("Autentificatic response missing token or codigo_funcionario: " . print_r($data, true));
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Respuesta inválida de Autentificatic'));
    exit;
}

// === Paso 2: Validar en base de datos local (US_ACTIVO = 1 y perfil 90/310) ===
require_once '../queries/config.php';

$sql = "SELECT US_ACTIVO, TUS_CODIGO FROM USUARIO WHERE FUN_CODIGO = '$codigo_funcionario'";
$result = mysql_query($sql, $link);
$row = mysql_fetch_array($result);

if (!$row || $row['US_ACTIVO'] != 1 || !in_array($row['TUS_CODIGO'], array(90, 310))) {
    error_log("Usuario no autorizado localmente: FUN_CODIGO=$codigo_funcionario");
    http_response_code(403);
    echo json_encode(array('success' => false, 'message' => 'Usuario inactivo o sin permisos para este sistema'));
    exit;
}

// === Paso 3: Iniciar sesión ===
$_SESSION['access_token'] = $access_token;
$_SESSION['USUARIO_CODIGOFUNCIONARIO'] = $codigo_funcionario;
$_SESSION['USUARIO_CODIGOPERFIL'] = $row['TUS_CODIGO'];

// === Respuesta exitosa ===
echo json_encode(array(
    'success' => true,
    'message' => 'Autenticación exitosa',
    'user' => array(
        'codigo_funcionario' => $codigo_funcionario,
        'perfil' => $row['TUS_CODIGO']
    )
));
?>