<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Parámetros de búsqueda, orden y paginación
$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : 'US_FECHACREACION';
$direccion = isset($_GET['direccion']) && strtolower($_GET['direccion']) === 'asc' ? 'ASC' : 'DESC';
$pagina_actual = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$por_pagina = 20;
$offset = ($pagina_actual - 1) * $por_pagina;
$where = "WHERE 1=1";

if ($search !== '') {
    $s = escape($search);
    $where .= " AND (
        FUNCIONARIO.FUN_CODIGO LIKE '%$s%' OR 
        FUNCIONARIO.FUN_NOMBRE LIKE '%$s%' OR 
        FUNCIONARIO.FUN_APELLIDOPATERNO LIKE '%$s%' OR
        FUNCIONARIO.FUN_APELLIDOMATERNO LIKE '%$s%'
    )";
}
// Consulta total para paginación
$sql_total = "
    SELECT COUNT(*) AS total
    FROM USUARIO
    INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
    INNER JOIN TIPO_USUARIO ON USUARIO.TUS_CODIGO = TIPO_USUARIO.TUS_CODIGO
    INNER JOIN UNIDAD ON USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO
    $where
";
$resultado_total = ejecutar($sql_total);
$fila_total = obtener_array($resultado_total);
$total_registros = $fila_total ? intval($fila_total['total']) : 0;
$total_paginas = ceil($total_registros / $por_pagina);
// Consulta principal con paginación y orden
$sql = "
    SELECT 
        USUARIO.US_LOGIN,
        USUARIO.US_FECHACREACION,
        FUNCIONARIO.FUN_CODIGO,
        FUNCIONARIO.FUN_NOMBRE,
        FUNCIONARIO.FUN_NOMBRE2,
        FUNCIONARIO.FUN_APELLIDOPATERNO,
        FUNCIONARIO.FUN_APELLIDOMATERNO,
        UNIDAD.UNI_DESCRIPCION,
        TIPO_USUARIO.TUS_DESCRIPCION
    FROM USUARIO
    INNER JOIN FUNCIONARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
    INNER JOIN TIPO_USUARIO ON USUARIO.TUS_CODIGO = TIPO_USUARIO.TUS_CODIGO
    INNER JOIN UNIDAD ON USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO
    $where
    ORDER BY $ordenar_por $direccion
    LIMIT $por_pagina OFFSET $offset
";
$usuarios = ejecutar($sql);
// Ahora $usuarios es el resultado, úsalo en el listado como antes
$datos_usuarios = array(
    'usuarios' => $usuarios,
    'pagina_actual' => $pagina_actual,
    'total_paginas' => $total_paginas,
    'search' => $search,
    'ordenar_por' => $ordenar_por,
    'direccion' => $direccion
);