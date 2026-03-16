<?php
/**
 * general_queries.php
 * Funciones de consulta general reutilizables para el frontend.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
require_once "config.php";

function obtenerPerfiles()
{
    global $link;
    $sql = "SELECT TUS_CODIGO, TUS_DESCRIPCION
            FROM TIPO_USUARIO
            WHERE TUS_ACTIVO = 1
            ORDER BY TUS_CODIGO";
    $res   = mysql_query($sql, $link);
    $datos = array();
    while ($row = mysql_fetch_assoc($res)) {
        $datos[] = array(
            "TUS_CODIGO"      => $row["TUS_CODIGO"],
            "TUS_DESCRIPCION" => utf8_encode($row["TUS_DESCRIPCION"])
        );
    }
    return $datos;
}

function obtenerUnidades()
{
    global $link;
    // Usar UNIDAD directamente (VISTA_ARBOL no existe en proservipol_test)
    $sql = "SELECT UNI_CODIGO, UNI_DESCRIPCION
            FROM UNIDAD
            WHERE UNI_ACTIVO = 1
            AND UNI_SELECCIONABLE = 1
            ORDER BY UNI_DESCRIPCION ASC";
    $res   = mysql_query($sql, $link);
    $datos = array();
    while ($row = mysql_fetch_assoc($res)) {
        $datos[] = array(
            "UNI_CODIGO"      => $row["UNI_CODIGO"],
            "UNI_DESCRIPCION" => utf8_encode($row["UNI_DESCRIPCION"])
        );
    }
    return $datos;
}

function obtenerUsuarioPorCodigo($funCodigo)
{
    global $link;
    $funCodigo = mysql_real_escape_string($funCodigo, $link);
    $sql = "SELECT
                u.FUN_CODIGO,
                u.UNI_CODIGO,
                u.US_LOGIN,
                u.TUS_CODIGO,
                u.US_ACTIVO,
                u.US_FECHACREACION,
                u.US_FECHAMODIFICACION,
                f.FUN_RUT,
                f.FUN_NOMBRE,
                f.FUN_NOMBRE2,
                f.FUN_APELLIDOPATERNO,
                f.FUN_APELLIDOMATERNO,
                g.GRA_DESCRIPCION,
                un.UNI_DESCRIPCION,
                t.TUS_DESCRIPCION
            FROM USUARIO u
            INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
            LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
            LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
            LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
            WHERE u.FUN_CODIGO = '{$funCodigo}'";
    $res = mysql_query($sql, $link);
    return $res ? mysql_fetch_assoc($res) : null;
}
?>