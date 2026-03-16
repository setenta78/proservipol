<?php
/**
 * editar_queries.php
 * Funciones para editar y consultar usuarios en el frontend.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo   = isset($_POST['codigo'])   ? $_POST['codigo']   : '';
    $perfil   = isset($_POST['perfil'])   ? $_POST['perfil']   : '';
    $unidad   = isset($_POST['unidad'])   ? $_POST['unidad']   : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    echo actualizarUsuario($codigo, $perfil, $unidad, $password);
}

function obtenerFuncionario($codigo)
{
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);

    $sql = "SELECT
                f.FUN_CODIGO,
                f.FUN_RUT,
                f.FUN_NOMBRE,
                IFNULL(f.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
                f.FUN_APELLIDOPATERNO,
                f.FUN_APELLIDOMATERNO,
                g.GRA_DESCRIPCION,
                un.UNI_DESCRIPCION,
                t.TUS_DESCRIPCION,
                IFNULL(c.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
                c.NOTA_PROSERVIPOL,
                c.FECHA_CAPACITACION,
                u.US_LOGIN,
                u.US_PASSWORD,
                u.UNI_CODIGO,
                u.TUS_CODIGO,
                u.US_ACTIVO
            FROM USUARIO u
            INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
            LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
            LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
            LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
            LEFT JOIN CAPACITACION c ON c.FUN_CODIGO = f.FUN_CODIGO AND c.ACTIVO = 1
            WHERE u.FUN_CODIGO LIKE '%{$codigo}%'
            LIMIT 1";

    $res = mysql_query($sql, $link);
    return $res ? mysql_fetch_assoc($res) : null;
}

function obtenerUltimosAccesos($codigo)
{
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);

    // BITACORA_USUARIO no existe aún — retornar array vacío
    // Cuando se implemente la tabla de bitácora, descomentar la query real
    /*
    $sql = "SELECT
                b.US_FECHAHORA_INICIO,
                b.US_DIRECCION_IP,
                f.FUN_CODIGO,
                CONCAT_WS(' ', f.FUN_APELLIDOPATERNO, f.FUN_APELLIDOMATERNO, f.FUN_NOMBRE, f.FUN_NOMBRE2) AS NOMBRE_COMPLETO,
                g.GRA_DESCRIPCION,
                un.UNI_DESCRIPCION,
                t.TUS_DESCRIPCION
            FROM BITACORA_USUARIO b
            INNER JOIN USUARIO u ON b.FUN_CODIGO = u.FUN_CODIGO
            INNER JOIN FUNCIONARIO f ON f.FUN_CODIGO = u.FUN_CODIGO
            LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
            LEFT JOIN UNIDAD un ON b.UNI_CODIGO = un.UNI_CODIGO
            LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
            WHERE b.FUN_CODIGO = '{$codigo}'
            ORDER BY b.US_FECHAHORA_INICIO DESC
            LIMIT 10";
    $result = mysql_query($sql, $link);
    $accesos = array();
    while ($row = mysql_fetch_assoc($result)) {
        $accesos[] = $row;
    }
    return $accesos;
    */

    return array(); // Sin bitácora por ahora
}

function actualizarUsuario($codigo, $perfil, $unidad, $password)
{
    global $link;

    if ($codigo === '' || $perfil === '' || $unidad === '') {
        return json_encode(array("success" => false, "message" => "Faltan datos obligatorios"));
    }

    $codigo   = mysql_real_escape_string($codigo,   $link);
    $perfil   = mysql_real_escape_string($perfil,   $link);
    $unidad   = mysql_real_escape_string($unidad,   $link);
    $password = mysql_real_escape_string($password, $link);

    // Verificar que el usuario existe
    $sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$codigo}'";
    $resCheck = mysql_query($sqlCheck, $link);
    if (!$resCheck || mysql_num_rows($resCheck) == 0) {
        return json_encode(array("success" => false, "message" => "Usuario no encontrado"));
    }

    // Verificar que TUS_CODIGO es válido
    $sqlTus = "SELECT TUS_CODIGO FROM TIPO_USUARIO WHERE TUS_CODIGO = '{$perfil}' AND TUS_ACTIVO = 1";
    $resTus = mysql_query($sqlTus, $link);
    if (!$resTus || mysql_num_rows($resTus) == 0) {
        return json_encode(array("success" => false, "message" => "Tipo de usuario no válido"));
    }

    // Verificar que UNI_CODIGO existe
    $sqlUni = "SELECT UNI_CODIGO FROM UNIDAD WHERE UNI_CODIGO = '{$unidad}'";
    $resUni = mysql_query($sqlUni, $link);
    if (!$resUni || mysql_num_rows($resUni) == 0) {
        return json_encode(array("success" => false, "message" => "Unidad no válida"));
    }

    $sql = "UPDATE USUARIO SET
                TUS_CODIGO = '{$perfil}',
                UNI_CODIGO = '{$unidad}',
                US_FECHAMODIFICACION = NOW()";

    if ($password !== '') {
        $sql .= ", US_PASSWORD = MD5('{$password}')";
    }

    $sql .= " WHERE FUN_CODIGO = '{$codigo}'";

    $res = mysql_query($sql, $link);

    if (!$res) {
        return json_encode(array("success" => false, "message" => "Error al actualizar: " . mysql_error($link)));
    }

    return json_encode(array(
        "success" => true,
        "message" => mysql_affected_rows($link) > 0
            ? "Datos actualizados correctamente"
            : "No se realizaron cambios"
    ));
}
?>