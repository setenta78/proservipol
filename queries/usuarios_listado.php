<?php
// queries/usuarios_listado.php

/**
 * Obtener usuarios con búsqueda general (con soporte multi-término)
 */
function obtenerUsuarios($link, $search, $sort, $order, $offset, $records_per_page)
{
    $whereClause = "USUARIO.US_ACTIVO = 1";
    
    // Si hay término de búsqueda, dividirlo en palabras
    if (!empty($search)) {
        // Dividir el término de búsqueda en palabras individuales
        $searchWords = preg_split('/\s+/', trim($search));
        $whereConditions = array();
        
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $word_safe = mysql_real_escape_string($word);
                
                // Cada palabra debe coincidir con al menos uno de los campos
                $wordCondition = "(
                    USUARIO.FUN_CODIGO LIKE '%$word_safe%' 
                    OR USUARIO.US_LOGIN LIKE '%$word_safe%'
                    OR FUNCIONARIO.FUN_APELLIDOPATERNO LIKE '%$word_safe%' 
                    OR FUNCIONARIO.FUN_APELLIDOMATERNO LIKE '%$word_safe%' 
                    OR FUNCIONARIO.FUN_NOMBRE LIKE '%$word_safe%'
                    OR FUNCIONARIO.FUN_NOMBRE2 LIKE '%$word_safe%'
                    OR VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION LIKE '%$word_safe%' 
                    OR TIPO_USUARIO.TUS_DESCRIPCION LIKE '%$word_safe%'
                )";
                
                $whereConditions[] = $wordCondition;
            }
        }
        
        // Unir todas las condiciones con AND (todas las palabras deben coincidir)
        if (!empty($whereConditions)) {
            $whereClause .= " AND " . implode(" AND ", $whereConditions);
        }
    }
    
    $sql = "SELECT 
                FUNCIONARIO.FUN_CODIGO,
                FUNCIONARIO.FUN_NOMBRE,
                IFNULL(FUNCIONARIO.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
                FUNCIONARIO.FUN_APELLIDOPATERNO,
                FUNCIONARIO.FUN_APELLIDOMATERNO,
                GRADO.GRA_DESCRIPCION,
                IFNULL(CARGO.CAR_DESCRIPCION, 'SIN CARGO') AS CARGO,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.ZONA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.PREFECTURA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.COMISARIA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION,
                TIPO_USUARIO.TUS_DESCRIPCION,
                IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
                CAPACITACION.NOTA_PROSERVIPOL,
                CAPACITACION.FECHA_CAPACITACION
            FROM USUARIO
            INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
            INNER JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO 
                AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
            INNER JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
            LEFT JOIN CARGO_FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
                AND CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
            LEFT JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO
            LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
                AND CAPACITACION.ACTIVO = 1
            LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
                ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
            WHERE $whereClause
            ORDER BY $sort $order
            LIMIT $records_per_page OFFSET $offset";

    $result = mysql_query($sql, $link);
    
    if (!$result) {
        die("Error en obtenerUsuarios: " . mysql_error($link) . "<br>SQL: $sql");
    }
    
    return $result;
}

/**
 * Contar total de usuarios (con soporte multi-término)
 */
function contarUsuarios($link, $search = '')
{
    $whereClause = "USUARIO.US_ACTIVO = 1";
    
    // Si hay término de búsqueda, dividirlo en palabras
    if (!empty($search)) {
        // Dividir el término de búsqueda en palabras individuales
        $searchWords = preg_split('/\s+/', trim($search));
        $whereConditions = array();
        
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $word_safe = mysql_real_escape_string($word);
                
                // Cada palabra debe coincidir con al menos uno de los campos
                $wordCondition = "(
                    USUARIO.FUN_CODIGO LIKE '%$word_safe%' 
                    OR USUARIO.US_LOGIN LIKE '%$word_safe%'
                    OR FUNCIONARIO.FUN_APELLIDOPATERNO LIKE '%$word_safe%' 
                    OR FUNCIONARIO.FUN_APELLIDOMATERNO LIKE '%$word_safe%' 
                    OR FUNCIONARIO.FUN_NOMBRE LIKE '%$word_safe%'
                    OR FUNCIONARIO.FUN_NOMBRE2 LIKE '%$word_safe%'
                    OR VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION LIKE '%$word_safe%' 
                    OR TIPO_USUARIO.TUS_DESCRIPCION LIKE '%$word_safe%'
                )";
                
                $whereConditions[] = $wordCondition;
            }
        }
        
        // Unir todas las condiciones con AND
        if (!empty($whereConditions)) {
            $whereClause .= " AND " . implode(" AND ", $whereConditions);
        }
    }
    
    $count_sql = "SELECT COUNT(DISTINCT FUNCIONARIO.FUN_CODIGO) AS total
                    FROM USUARIO
                    INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
                    INNER JOIN TIPO_USUARIO ON USUARIO.TUS_CODIGO = TIPO_USUARIO.TUS_CODIGO
                    LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
                        ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
                    WHERE $whereClause";

    $count_result = mysql_query($count_sql, $link);
    
    if (!$count_result) {
        die("Error en contarUsuarios: " . mysql_error($link));
    }
    
    $row = mysql_fetch_assoc($count_result);
    mysql_free_result($count_result);
    
    return isset($row['total']) ? $row['total'] : 0;
}

/**
 * Obtener usuarios con búsqueda paramétrica
 */
function obtenerUsuariosParametricos($link, $apellido1, $apellido2, $nombre1, $nombre2, $unidades, $perfiles, $sort, $order, $offset, $records_per_page)
{
    $where = "USUARIO.US_ACTIVO = 1";
    
    // Filtros de nombres
    if ($apellido1 != '') {
        $apellido1 = mysql_real_escape_string($apellido1);
        $where .= " AND FUNCIONARIO.FUN_APELLIDOPATERNO LIKE '%$apellido1%'";
    }
    if ($apellido2 != '') {
        $apellido2 = mysql_real_escape_string($apellido2);
        $where .= " AND FUNCIONARIO.FUN_APELLIDOMATERNO LIKE '%$apellido2%'";
    }
    if ($nombre1 != '') {
        $nombre1 = mysql_real_escape_string($nombre1);
        $where .= " AND FUNCIONARIO.FUN_NOMBRE LIKE '%$nombre1%'";
    }
    if ($nombre2 != '') {
        $nombre2 = mysql_real_escape_string($nombre2);
        $where .= " AND FUNCIONARIO.FUN_NOMBRE2 LIKE '%$nombre2%'";
    }
    
    // Filtro de unidades
    if ($unidades != '') {
        $ids = explode(',', $unidades);
        $ids = array_map('intval', $ids);
        if (count($ids) > 0) {
            $where .= " AND USUARIO.UNI_CODIGO IN (" . implode(',', $ids) . ")";
        }
    }
    
    // Filtro de perfiles
    if (!empty($perfiles) && is_array($perfiles)) {
        $ids = array_map('intval', $perfiles);
        if (count($ids) > 0) {
            $where .= " AND USUARIO.TUS_CODIGO IN (" . implode(',', $ids) . ")";
        }
    }
    
    // Validación de orden
    $allowed_sort_columns = array(
        'FUN_CODIGO',
        'FUN_APELLIDOPATERNO',
        'FUN_APELLIDOMATERNO',
        'FUN_NOMBRE',
        'FUN_NOMBRE2',
        'GRA_DESCRIPCION',
        'CARGO',
        'UNI_DESCRIPCION',
        'TUS_DESCRIPCION',
        'CAPACITACION'
    );
    $sort  = in_array($sort, $allowed_sort_columns) ? $sort : 'FUN_CODIGO';
    $order = ($order === 'desc') ? 'desc' : 'asc';
    
    // Armar SQL
    $sql = "SELECT 
                FUNCIONARIO.FUN_CODIGO,
                FUNCIONARIO.FUN_NOMBRE,
                IFNULL(FUNCIONARIO.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
                FUNCIONARIO.FUN_APELLIDOPATERNO,
                FUNCIONARIO.FUN_APELLIDOMATERNO,
                GRADO.GRA_DESCRIPCION,
                IFNULL(CARGO.CAR_DESCRIPCION, 'SIN CARGO') AS CARGO,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.ZONA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.PREFECTURA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.COMISARIA_DESCRIPCION,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION,
                TIPO_USUARIO.TUS_DESCRIPCION,
                IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
                CAPACITACION.NOTA_PROSERVIPOL,
                CAPACITACION.FECHA_CAPACITACION
            FROM USUARIO
            INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
            INNER JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO 
                AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
            INNER JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
            LEFT JOIN CARGO_FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
                AND CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
            LEFT JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO
            LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
                AND CAPACITACION.ACTIVO = 1
            LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
                ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
            WHERE $where
            ORDER BY $sort $order
            LIMIT $records_per_page OFFSET $offset";
    
    $result = mysql_query($sql, $link);
    
    if (!$result) {
        die("Error en obtenerUsuariosParametricos: " . mysql_error($link) . "<br>SQL: $sql");
    }
    
    return $result;
}

/**
 * Contar usuarios con búsqueda paramétrica
 */
function contarUsuariosParametricos($link, $apellido1, $apellido2, $nombre1, $nombre2, $unidades, $perfiles)
{
    $where = "USUARIO.US_ACTIVO = 1";
    
    // Filtros de nombres
    if ($apellido1 != '') {
        $apellido1 = mysql_real_escape_string($apellido1);
        $where .= " AND FUNCIONARIO.FUN_APELLIDOPATERNO LIKE '%$apellido1%'";
    }
    if ($apellido2 != '') {
        $apellido2 = mysql_real_escape_string($apellido2);
        $where .= " AND FUNCIONARIO.FUN_APELLIDOMATERNO LIKE '%$apellido2%'";
    }
    if ($nombre1 != '') {
        $nombre1 = mysql_real_escape_string($nombre1);
        $where .= " AND FUNCIONARIO.FUN_NOMBRE LIKE '%$nombre1%'";
    }
    if ($nombre2 != '') {
        $nombre2 = mysql_real_escape_string($nombre2);
        $where .= " AND FUNCIONARIO.FUN_NOMBRE2 LIKE '%$nombre2%'";
    }
    
    // Filtro de unidades
    if ($unidades != '') {
        $ids = explode(',', $unidades);
        $ids = array_map('intval', $ids);
        if (count($ids) > 0) {
            $where .= " AND USUARIO.UNI_CODIGO IN (" . implode(',', $ids) . ")";
        }
    }
    
    // Filtro de perfiles
    if (!empty($perfiles) && is_array($perfiles)) {
        $ids = array_map('intval', $perfiles);
        if (count($ids) > 0) {
            $where .= " AND USUARIO.TUS_CODIGO IN (" . implode(',', $ids) . ")";
        }
    }
    
    $sql = "SELECT COUNT(DISTINCT FUNCIONARIO.FUN_CODIGO) AS total
            FROM USUARIO
            INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
            INNER JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO 
                AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
            INNER JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
            LEFT JOIN CARGO_FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
                AND CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
            LEFT JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO
            LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
                AND CAPACITACION.ACTIVO = 1
            LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
                ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
            WHERE $where";

    $result = mysql_query($sql, $link);
    
    if (!$result) {
        die("Error en contarUsuariosParametricos: " . mysql_error($link));
    }
    
    $row = mysql_fetch_assoc($result);
    mysql_free_result($result);
    
    return isset($row['total']) ? (int)$row['total'] : 0;
}
?>