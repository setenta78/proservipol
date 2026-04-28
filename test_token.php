<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// CARGA MANUAL DE LIBRERÍA JSON PARA PHP 5.1.2
// ==========================================
if (!function_exists('json_decode')) {
    require_once('inc/Services_JSON.php');
    function json_decode($content, $assoc = false) {
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $json = new Services_JSON();
        }
        return $json->decode($content);
    }
}
if (!function_exists('json_encode')) {
    if (!class_exists('Services_JSON')) {
        require_once('inc/Services_JSON.php');
    }
    function json_encode($content) {
        $json = new Services_JSON();
        return $json->encode($content);
    }
}

// Validar sesión
if (!isset($_SESSION['access_token'])) {
    die("ERROR: No hay access_token en la sesión. Por favor inicia sesión en el sistema primero (<a href='login.php'>Ir al Login</a>).");
}

$token = $_SESSION['access_token'];
$url = 'http://autentificaticapi.carabineros.cl/api/auth/validate-token';

echo "<h3>1. Diagnostico del Token</h3>";
echo "<p><strong>Token encontrado:</strong> " . substr($token, 0, 40) . "...</p>";

// Preparar petición
$headers = array(
    'Accept: application/json',
    'Authorization: Bearer ' . $token
);

$options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => implode("\r\n", $headers),
        'timeout' => 15,
        'ignore_errors' => true 
    ),
    'ssl' => array(
        'verify_peer' => false,
        'verify_host' => false
    )
);

$context = stream_context_create($options);

echo "<hr><h3>2. Llamada a la API Externa de AUTENTIFICATIC</h3>";
echo "<p>URL: $url</p>";

$result = @file_get_contents($url, false, $context);
$httpCode = 0;

// Capturar código HTTP
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $h, $m)) {
            $httpCode = $m[1];
            break;
        }
    }
}

echo "<p><strong>Codigo HTTP Respuesta:</strong> $httpCode</p>";

if ($result === FALSE) {
    echo "<p style='color:red'><strong>Error de conexion:</strong> No se pudo conectar con la API externa.</p>";
} else {
    echo "<h4>Respuesta Cruda (Raw):</h4>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";

    // Intentar decodificar
    $data = json_decode($result, true);
    
    echo "<h4>Respuesta Decodificada:</h4>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    echo "<hr><h3>3. Conclusion</h3>";
    
    if ($httpCode == 200 && is_array($data) && isset($data['success'])) {
        echo "<p style='color:green; font-weight:bold; font-size:16px;'>El token de la sesion es VALIDO y la conexion funciona.</p>";
        echo "<p>Dominio registrado: <strong>" . (isset($data['success']['domain']) ? $data['success']['domain'] : 'No especificado') . "</strong></p>";
        echo "<p>RUT del token: <strong>" . (isset($data['success']['rut']) ? $data['success']['rut'] : 'No especificado') . "</strong></p>";
        echo "<hr><p style='color:blue; font-weight:bold;'>DIAGNOSTICO:</p>";
        echo "<p>El token es valido pero al efectuar el alta de un usuario da error 404 'Plataforma no encontrada', significa que:</p>";
        echo "<ul>";
        echo "<li>Usuario y Password funcionan correctamente.</li>";
        echo "<li>El dominio <strong><code>des-proservipol.carabineros.cl</code></strong> NO esta registrado en la API de Autentificatic como una aplicacion que puede <strong>crear y eliminar usuarios</strong>.</li>";
        echo "<li>Es probable que solo este registrado para <strong>login</strong> (autenticacion), pero no para gestion de usuarios.</li>";
        echo "</ul>";
        echo "<p><strong>Solucion:</strong> Hay que contactar al equipo de Autentificatic y solicitar que registren el dominio para el endpoint <code>/api/institutional-app-user-from-external-app</code>.</p>";
        
    } elseif ($httpCode == 401) {
        echo "<p style='color:red'>El token es INVALIDO o expiro. Cierra sesion y vuelve a entrar.</p>";
    } else {
        echo "<p style='color:orange'>Respuesta inesperada ($httpCode). Revisa los logs de la API externa.</p>";
    }
}
?>