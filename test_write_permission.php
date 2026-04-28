<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('inc/Services_JSON.php'); // Asegurar compatibilidad JSON

if (!isset($_SESSION['access_token'])) {
    die("ERROR: Inicia sesión primero.");
}
$token = $_SESSION['access_token'];
$rutPrueba = "128815791"; // RUT del funcionario que intentas crear

echo "<h2>Prueba de Permisos de Escritura Autentificatic</h2>";
echo "<p>Dominio: des-proservipol.carabineros.cl</p>";
echo "<p>RUT a registrar: $rutPrueba</p><hr>";

$url = 'http://autentificaticapi.carabineros.cl/api/institutional-app-user-from-external-app';

$headers = array(
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $token
);

$data = http_build_query(array('rut' => $rutPrueba));

$options = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => implode("\r\n", $headers),
        'content' => $data,
        'timeout' => 30,
        'ignore_errors' => true 
    ),
    'ssl' => array('verify_peer' => false, 'verify_host' => false)
);

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

$httpCode = 0;
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $h, $m)) {
            $httpCode = $m[1];
            break;
        }
    }
}

echo "<h3>Resultado:</h3>";
echo "<p><strong>Codigo HTTP:</strong> <span style='color:" . ($httpCode == 200 ? 'green' : 'red') . "; font-weight:bold;'>$httpCode</span></p>";

if ($httpCode == 200 || $httpCode == 201) {
    echo "<p style='color:green; font-size:18px;'>¡EXITO! El usuario se registro en Autentificatic.</p>";
    echo "<pre>" . print_r(json_decode($result, true), true) . "</pre>";
} elseif ($httpCode == 404) {
    echo "<p style='color:red; font-size:18px;'>ERROR 404: El dominio NO tiene permisos de escritura registrados en la API.</p>";
    echo "<p>La respuesta cruda fue:</p><pre>" . htmlspecialchars($result) . "</pre>";
    echo "<p><em>Nota: Si el RUT no existiera en Autentificatic, deberiamos recibir un JSON con error 'Funcionario no encontrado'. Al recibir 404 puro o vacio, confirma que la plataforma no esta autorizada para escribir.</em></p>";
} elseif ($httpCode == 401) {
    echo "<p style='color:orange;'>ERROR 401: Token invalido o expirado.</p>";
} else {
    echo "<p style='color:orange;'>ERROR INESPERADO ($httpCode)</p>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
}
?>