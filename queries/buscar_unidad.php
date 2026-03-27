<?php
require_once("../inc/config.inc.php");
require_once("config.php");

$term = isset($_GET['term']) ? $_GET['term'] : '';

if (strlen($term) > 0) {
    // Consulta a la vista de árbol de unidades
    $query = "SELECT UNI_DESCRIPCION, UNI_CODIGO 
              FROM VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
              WHERE UNI_DESCRIPCION LIKE '%" . mysql_real_escape_string($term) . "%' 
              ORDER BY UNI_DESCRIPCION ASC 
              LIMIT 0, 20";
    
    $result = mysql_query($query, $link);
    
    if (!$result) {
        echo "<p>Error en la consulta</p>";
        exit;
    }

    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_array($result)) {
            // CORRECCIÓN: Solo mostramos la descripción, sin el código al final
            echo "<p data-id='" . $row['UNI_CODIGO'] . "'>" . htmlspecialchars($row['UNI_DESCRIPCION']) . "</p>";
        }
    } else {
        echo "<p>No hay coincidencias</p>";
    }
} else {
    echo "<p>Ingrese al menos un carácter</p>";
}
?>