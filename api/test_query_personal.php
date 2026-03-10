<?php
// Incluir configuración
require_once('../queries/config.php');

// Compatibilidad JSON
if (!function_exists('json_encode')) {
    require_once('../inc/Services_JSON.php');
    function json_encode($content) {
        $json = new Services_JSON();
        return $json->encode($content);
    }
}

header('Content-Type: text/plain; charset=UTF-8');

echo "=== TEST CONSULTA PERSONAL_MOCK ===\n\n";

// Verificar conexión
if (!isset($link) || !$link) {
    echo "ERROR: No hay conexión a la base de datos (\$link no está definido)\n";
    exit;
}

echo "Conexión a BD: OK\n\n";

// Verificar si existe PERSONAL_MOCK
$checkTable = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $link);
if (mysql_num_rows($checkTable) > 0) {
    echo "Tabla PERSONAL_MOCK: EXISTE\n\n";
} else {
    echo "Tabla PERSONAL_MOCK: NO EXISTE\n\n";
}

// Intentar consultar el funcionario
$codFuncionario = '013926H';
$sql = "SELECT 
    PER_CODIGOFUNCIONARIO as codigo,
    PER_RUT as rut,
    PER_NOMBRE as nombre,
    PER_APELLIDOPATERNO as apellidoPaterno,
    PER_APELLIDOMATERNO as apellidoMaterno,
    PER_GRADO as grado,
    PER_UNIDAD as unidad,
    PER_DEPARTAMENTO as departamento
FROM PERSONAL_MOCK 
WHERE PER_CODIGOFUNCIONARIO = '$codFuncionario'";

echo "SQL ejecutado:\n$sql\n\n";

$result = mysql_query($sql, $link);

if (!$result) {
    echo "ERROR en la consulta: " . mysql_error($link) . "\n";
} else {
    $numRows = mysql_num_rows($result);
    echo "Número de resultados: $numRows\n\n";
    
    if ($numRows > 0) {
        echo "Datos encontrados:\n";
        $row = mysql_fetch_assoc($result);
        print_r($row);
    } else {
        echo "No se encontraron datos para el código: $codFuncionario\n";
    }
}
?>