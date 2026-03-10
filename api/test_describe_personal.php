<?php
// Incluir configuración
require_once('../queries/config.php');

header('Content-Type: text/plain; charset=UTF-8');

echo "=== ESTRUCTURA DE PERSONAL_MOCK ===\n\n";

// Verificar conexión
if (!isset($link) || !$link) {
    echo "ERROR: No hay conexión a la base de datos\n";
    exit;
}

// Describir la tabla
$result = mysql_query("DESCRIBE PERSONAL_MOCK", $link);

if (!$result) {
    echo "ERROR: " . mysql_error($link) . "\n";
} else {
    echo "Columnas de PERSONAL_MOCK:\n\n";
    while ($row = mysql_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n\n=== DATOS DE EJEMPLO ===\n\n";

// Mostrar un registro de ejemplo
$result2 = mysql_query("SELECT * FROM PERSONAL_MOCK LIMIT 1", $link);

if (!$result2) {
    echo "ERROR: " . mysql_error($link) . "\n";
} else {
    if (mysql_num_rows($result2) > 0) {
        $row = mysql_fetch_assoc($result2);
        foreach ($row as $key => $value) {
            echo "$key = $value\n";
        }
    } else {
        echo "No hay datos en PERSONAL_MOCK\n";
    }
}
?>