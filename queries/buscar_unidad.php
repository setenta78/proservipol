<?php
require_once "config.php";

$param_term = isset($_REQUEST['term']) ? $_REQUEST['term'] : '';
if ($param_term != '') {
    global $link;
    $param_term = mysql_real_escape_string($param_term, $link);
    
    $sqlUnidad = "SELECT UNI_CODIGO, UNI_DESCRIPCION 
                  FROM VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS
                  WHERE UNI_DESCRIPCION LIKE '%$param_term%'";
    
    $result = mysql_query($sqlUnidad, $link);
    
    if ($result && mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            echo "<p data-id='" . htmlspecialchars($row["UNI_CODIGO"]) . "'>" . htmlspecialchars($row["UNI_DESCRIPCION"]) . " - COD: " . htmlspecialchars($row["UNI_CODIGO"]) . "</p>";
        }
    } else {
        echo "<p>No se encontraron coincidencias</p>";
    }
}
?>