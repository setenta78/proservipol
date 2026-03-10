<?php
require_once('../queries/config.php');
header('Content-Type: text/plain; charset=UTF-8');

echo "=== ESTRUCTURA DE ESCALAFON ===\n\n";

$result = mysql_query("DESCRIBE ESCALAFON", $link);
if ($result) {
    while ($row = mysql_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n\n=== DATOS DE ESCALAFON ===\n\n";

$result2 = mysql_query("SELECT * FROM ESCALAFON ORDER BY ESC_CODIGO", $link);
if ($result2) {
    while ($row = mysql_fetch_assoc($result2)) {
        echo "ESC_CODIGO: " . $row['ESC_CODIGO'] . " - " . (isset($row['ESC_DESCRIPCION']) ? $row['ESC_DESCRIPCION'] : '') . "\n";
    }
}
?>