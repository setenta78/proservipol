<?php
// Incluir configuración
require_once('../queries/config.php');

header('Content-Type: text/plain; charset=UTF-8');

echo "=== ESTRUCTURA DE FUNCIONARIO ===\n\n";

// Verificar conexión
if (!isset($link) || !$link) {
    echo "ERROR: No hay conexión a la base de datos\n";
    exit;
}

// Describir la tabla
$result = mysql_query("DESCRIBE FUNCIONARIO", $link);

if (!$result) {
    echo "ERROR: " . mysql_error($link) . "\n";
} else {
    echo "Columnas de FUNCIONARIO:\n\n";
    while ($row = mysql_fetch_assoc($result)) {
        $null = $row['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
        $key = $row['Key'] ? ' [' . $row['Key'] . ']' : '';
        echo "- " . $row['Field'] . " (" . $row['Type'] . ") " . $null . $key . "\n";
    }
}

echo "\n\n=== FOREIGN KEYS DE FUNCIONARIO ===\n\n";

$result2 = mysql_query("SHOW CREATE TABLE FUNCIONARIO", $link);
if ($result2) {
    $row = mysql_fetch_assoc($result2);
    echo $row['Create Table'];
}
?>