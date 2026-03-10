<?php
// Compatibilidad JSON
if (!function_exists('json_encode')) {
    require_once('../inc/Services_JSON.php');
    function json_encode($content) {
        $json = new Services_JSON();
        return $json->encode($content);
    }
}

header('Content-Type: text/plain; charset=UTF-8');

$url = "http://aplicativos.des-proservipol.carabineros.cl/api/buscarFuncionarioPersonal/?codFuncionario=013926H";

echo "=== TEST file_get_contents() ===\n\n";
echo "URL: " . $url . "\n\n";

// Habilitar errores temporalmente
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Intentando obtener datos...\n\n";

$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: No se pudieron obtener los datos\n";
    echo "Response: NULL o FALSE\n";
} else {
    echo "EXITO: Datos obtenidos correctamente\n\n";
    echo "Longitud de respuesta: " . strlen($response) . " bytes\n\n";
    echo "Contenido:\n";
    echo $response;
}
?>