<?php
require_once('../queries/config.php');
header('Content-Type: text/plain; charset=UTF-8');

echo "=== ESTRUCTURA DE GRADO ===\n\n";

$result = mysql_query("DESCRIBE GRADO", $link);
if ($result) {
    while ($row = mysql_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n\n=== DATOS DE GRADO (primeros 20) ===\n\n";

$result2 = mysql_query("SELECT * FROM GRADO ORDER BY ESC_CODIGO, GRA_CODIGO LIMIT 100000", $link);
if ($result2) {
    while ($row = mysql_fetch_assoc($result2)) {
        echo "ESC_CODIGO: " . $row['ESC_CODIGO'] . " | GRA_CODIGO: " . $row['GRA_CODIGO'] . " - " . (isset($row['GRA_DESCRIPCION']) ? $row['GRA_DESCRIPCION'] : '') . "\n";
    }
}
?>