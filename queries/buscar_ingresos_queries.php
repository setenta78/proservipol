<?php
/**
 * Queries para GACC-0011 - Buscar Ingresos
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 * Desarrollado por Ingeniero Denis Quezada Lemus
 */
require_once "config.php";

if (!function_exists('json_encode')) {
    require_once(dirname(__FILE__) . '/../inc/Services_JSON.php');
    function json_encode($data) {
        $json = new Services_JSON();
        return $json->encode($data);
    }
}

// Si se llama directamente por POST, ejecutar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');

    $fecha_desde   = isset($_POST['fecha_desde'])   ? trim($_POST['fecha_desde'])   : '';
    $fecha_hasta   = isset($_POST['fecha_hasta'])   ? trim($_POST['fecha_hasta'])   : '';
    $busqueda      = isset($_POST['busqueda'])       ? trim($_POST['busqueda'])       : '';
    $unidad_filter = isset($_POST['unidad_filter']) ? trim($_POST['unidad_filter']) : '';
    $pagina        = isset($_POST['pagina'])         ? (int)$_POST['pagina']         : 1;
    $por_pagina    = 100;

    $resultado = buscarIngresos($fecha_desde, $fecha_hasta, $busqueda, $unidad_filter, $pagina, $por_pagina);
    echo json_encode($resultado);
    exit;
}

function buscarIngresos($fecha_desde, $fecha_hasta, $busqueda, $unidad_filter, $pagina, $por_pagina) {
    global $link;

    $offset = ($pagina - 1) * $por_pagina;

    // Construir condiciones dinámicas
    $condiciones = array("1=1");

    if (!empty($fecha_desde)) {
        $fecha_desde = mysql_real_escape_string($fecha_desde, $link);
        $condiciones[] = "BITACORA_USUARIO.US_FECHAHORA_INICIO >= '$fecha_desde 00:00:00'";
    }

    if (!empty($fecha_hasta)) {
        $fecha_hasta = mysql_real_escape_string($fecha_hasta, $link);
        $condiciones[] = "BITACORA_USUARIO.US_FECHAHORA_INICIO <= '$fecha_hasta 23:59:59'";
    }

    if (!empty($busqueda)) {
        $busqueda_escaped = mysql_real_escape_string($busqueda, $link);

        // Dividir por espacios para búsqueda multi-palabra (ej: "Denis Quezada")
        $palabras = explode(' ', trim($busqueda));
        $cond_nombre = array();
        foreach ($palabras as $palabra) {
            $palabra = trim($palabra);
            if (empty($palabra)) continue;
            $palabra_esc = mysql_real_escape_string($palabra, $link);
            // Cada palabra debe aparecer en algún lugar del nombre completo concatenado
            $cond_nombre[] = "CONCAT_WS(' ',
                FUNCIONARIO.FUN_NOMBRE,
                FUNCIONARIO.FUN_NOMBRE2,
                FUNCIONARIO.FUN_APELLIDOPATERNO,
                FUNCIONARIO.FUN_APELLIDOMATERNO
            ) LIKE '%$palabra_esc%'";
        }

        // Condición final: código OR RUT OR todas las palabras del nombre presentes
        $cond_codigo_rut = "(
            FUNCIONARIO.FUN_CODIGO LIKE '%$busqueda_escaped%'
            OR FUNCIONARIO.FUN_RUT LIKE '%$busqueda_escaped%'
        )";

        if (!empty($cond_nombre)) {
            $cond_nombre_sql = implode(' AND ', $cond_nombre);
            $condiciones[] = "($cond_codigo_rut OR ($cond_nombre_sql))";
        } else {
            $condiciones[] = $cond_codigo_rut;
        }
    }

    if (!empty($unidad_filter)) {
        $unidad_filter = mysql_real_escape_string($unidad_filter, $link);
        $condiciones[] = "BITACORA_USUARIO.UNI_CODIGO = '$unidad_filter'";
    }

    $where = implode(" AND ", $condiciones);

    // Query principal
    $sql = "SELECT
                BITACORA_USUARIO.US_FECHAHORA_INICIO,
                BITACORA_USUARIO.US_FECHAHORA_TERMINO,
                BITACORA_USUARIO.US_DIRECCION_IP,
                BITACORA_USUARIO.US_EVENTO,
                FUNCIONARIO.FUN_CODIGO,
                FUNCIONARIO.FUN_RUT,
                CONCAT_WS(' ',
                    FUNCIONARIO.FUN_NOMBRE,
                    FUNCIONARIO.FUN_NOMBRE2,
                    FUNCIONARIO.FUN_APELLIDOPATERNO,
                    FUNCIONARIO.FUN_APELLIDOMATERNO
                ) AS NOMBRE_COMPLETO,
                GRADO.GRA_DESCRIPCION,
                UNIDAD.UNI_DESCRIPCION,
                TIPO_USUARIO.TUS_DESCRIPCION
            FROM BITACORA_USUARIO
            INNER JOIN FUNCIONARIO   ON FUNCIONARIO.FUN_CODIGO   = BITACORA_USUARIO.FUN_CODIGO
            INNER JOIN GRADO         ON FUNCIONARIO.ESC_CODIGO   = GRADO.ESC_CODIGO
                                    AND FUNCIONARIO.GRA_CODIGO   = GRADO.GRA_CODIGO
            INNER JOIN UNIDAD        ON UNIDAD.UNI_CODIGO        = BITACORA_USUARIO.UNI_CODIGO
            INNER JOIN TIPO_USUARIO  ON TIPO_USUARIO.TUS_CODIGO  = BITACORA_USUARIO.TUS_CODIGO
            WHERE $where
            ORDER BY BITACORA_USUARIO.US_FECHAHORA_INICIO DESC
            LIMIT $por_pagina OFFSET $offset";

    // Query de conteo
    $sql_count = "SELECT COUNT(*) AS total
                  FROM BITACORA_USUARIO
                  INNER JOIN FUNCIONARIO  ON FUNCIONARIO.FUN_CODIGO  = BITACORA_USUARIO.FUN_CODIGO
                  INNER JOIN GRADO        ON FUNCIONARIO.ESC_CODIGO  = GRADO.ESC_CODIGO
                                         AND FUNCIONARIO.GRA_CODIGO  = GRADO.GRA_CODIGO
                  INNER JOIN UNIDAD       ON UNIDAD.UNI_CODIGO       = BITACORA_USUARIO.UNI_CODIGO
                  INNER JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = BITACORA_USUARIO.TUS_CODIGO
                  WHERE $where";

    $res_count = mysql_query($sql_count, $link);
    $total     = 0;
    if ($res_count) {
        $row_count = mysql_fetch_assoc($res_count);
        $total = (int)$row_count['total'];
    }

    $res   = mysql_query($sql, $link);
    $datos = array();

    if ($res) {
        while ($row = mysql_fetch_assoc($res)) {
            $datos[] = $row;
        }
    } else {
        return array(
            'success' => false,
            'message' => 'Error en consulta: ' . mysql_error($link),
            'datos'   => array(),
            'total'   => 0
        );
    }

    return array(
        'success'    => true,
        'datos'      => $datos,
        'total'      => $total,
        'pagina'     => $pagina,
        'por_pagina' => $por_pagina,
        'paginas'    => ceil($total / $por_pagina)
    );
}
?>