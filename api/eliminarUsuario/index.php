<?php
session_start();
// ✅ FIX: usar dirname(__FILE__) para ruta absoluta
include_once(dirname(__FILE__) . '/../tools.php');
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
        "code"    => 401
    ));
    exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Método no permitido. Use POST.",
        "code"    => 405
    ));
    exit;
}

try {
    // Obtener datos del cuerpo de la petición o POST tradicional
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Si no hay JSON, intentar con $_POST tradicional
    if (!$data && isset($_POST['codigo'])) {
        $data = array('codigo' => $_POST['codigo']);
    }
    
    if (!$data || !isset($data['codigo'])) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Datos inválidos. Se requiere el código del funcionario.",
            "code"    => 400
        ));
        exit;
    }
    
    // ✅ FIX: validar alfanumérico (ej: 013926H), NO is_numeric
    $codigo = trim($data['codigo']);
    if (empty($codigo) || !preg_match('/^[a-zA-Z0-9]+$/', $codigo)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Código de funcionario inválido.",
            "code"    => 400
        ));
        exit;
    }
    
    // Incluir queries con lógica de eliminación atómica
    require_once(dirname(__FILE__) . '/../../queries/eliminar_queries.php');
    
    // ✅ FIX: llamar a la función y retornar resultado
    $resultado = eliminarUsuario($codigo, $_SESSION['user_id']);
    
    if ($resultado['success']) {
        http_response_code(200);
    } else {
        $httpCode = isset($resultado['code']) ? intval($resultado['code']) : 500;
        http_response_code($httpCode);
    }
    
    echo json_encode($resultado);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error interno del servidor: " . $e->getMessage(),
        "code"    => 500
    ));
}
?>