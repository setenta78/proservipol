<?php
require_once "config.php";

function obtenerPerfiles()
{
    global $link;
    $sql = "SELECT TUS_CODIGO, TUS_DESCRIPCION 
            FROM TIPO_USUARIO 
            WHERE TUS_ACTIVO = 1";
    $res = mysql_query($sql, $link);
    $datos = array();
    while ($row = mysql_fetch_assoc($res)) {
        $datos[] = $row;
    }
    return $datos;
}

function obtenerUnidades()
{
    global $link;
    $sql = "SELECT UNI_CODIGO, UNI_DESCRIPCION 
            FROM VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS
            ORDER BY UNI_DESCRIPCION ASC";
    $res = mysql_query($sql, $link);
    $datos = array();
    while ($row = mysql_fetch_assoc($res)) {
        $datos[] = $row;
    }
    return $datos;
}
?>