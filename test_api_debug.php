<?php
// BORRAR ESTE ARCHIVO DESPUÉS DE LA PRUEBA
session_start();

$token = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : '';

if (empty($token)) {
    echo "NO HAY TOKEN EN SESION. Inicia sesion en el sistema primero y luego vuelve a esta URL.";
    exit;
}

$rut        = '134989483';
$url        = 'http://autentificaticapi.carabineros.cl/api/institutional-app-user-from-external-app';
$dataString = 'rut=' . urlencode($rut);

$headers = array(
    'Host: autentificaticapi.carabineros.cl',
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($dataString),
    'Connection: close',
    'Origin: http://des-proservipol.carabineros.cl',
    'Authorization: Bearer ' . $token
);

$options = array(
    'http' => array(
        'method'           => 'POST',
        'header'           => implode("\r\n", $headers),
        'content'          => $dataString,
        'timeout'          => 30,
        'ignore_errors'    => true,
        'protocol_version' => 1.0
    ),
    'ssl' => array(
        'verify_peer'       => false,
        'verify_host'       => false,
        'allow_self_signed' => true
    )
);

$context  = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

echo "<​pre>";
echo "TOKEN (primeros 30 chars): " . substr($token, 0, 30) . "...\n\n";
echo "URL: $url\n";
echo "BODY ENVIADO: $dataString\n\n";

echo "=== RESPONSE HEADERS ===\n";
if (isset($http_response_header) && is_array($http_response_header)) {
    foreach ($http_response_header as $h) echo htmlspecialchars($h) . "\n";
} else {
    echo "SIN HEADERS - posible error de red o DNS\n";
}

echo "\n=== RESPONSE BODY ===\n";
if ($response === false) {
    echo "FALSE - No se pudo conectar\n";
} elseif (trim($response) === '') {
    echo "(respuesta vacia)\n";
} else {
    echo htmlspecialchars($response) . "\n";
}
echo "<​/pre>";
?>
