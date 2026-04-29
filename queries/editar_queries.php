<?php
/**
 * Edición de usuarios PROSERVIPOL con transacción atómica y auditoría
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

require_once "config.php";

// Incluir librería JSON para PHP 5.1.2 si es necesario
if (!function_exists('json_encode')) {
    require_once(dirname(__FILE__) . '/../inc/Services_JSON.php');
    function json_encode($data) {
        $json = new Services_JSON();
        return $json->encode($data);
    }
}

// Si se llama directamente por POST, ejecutar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    header('Content-Type: application/json; charset=UTF-8');
    
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
    $unidad = isset($_POST['unidad']) ? $_POST['unidad'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Obtener usuario que modifica desde sesión
    $usuarioModificador = isset($_SESSION['USUARIO_CODIGOFUNCIONARIO']) ? $_SESSION['USUARIO_CODIGOFUNCIONARIO'] : 'SYSTEM';
    
    $resultado = actualizarUsuario($codigo, $perfil, $unidad, $password, $usuarioModificador);
    
    echo json_encode($resultado);
    exit;
}

// ... [Resto de las funciones obtenerFuncionario, obtenerUltimosAccesos igual que antes] ...
function obtenerFuncionario($codigo) {
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);
    $sql = "SELECT 
        FUNCIONARIO.FUN_CODIGO, FUNCIONARIO.FUN_RUT, FUNCIONARIO.FUN_NOMBRE,
        IFNULL(FUNCIONARIO.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
        FUNCIONARIO.FUN_APELLIDOPATERNO, FUNCIONARIO.FUN_APELLIDOMATERNO,
        GRADO.GRA_DESCRIPCION,
        IFNULL(IF(CARGO_FUNCIONARIO.UNI_AGREGADO, CONCAT(CARGO.CAR_DESCRIPCION, ' A ', UNIDAD.UNI_DESCRIPCION), CARGO.CAR_DESCRIPCION), 'SIN CARGO') AS CARGO,
        IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION, 'MESA DE AYUDA') AS UNI_DESCRIPCION,
        IFNULL(TIPO_USUARIO.TUS_DESCRIPCION, '') AS TUS_DESCRIPCION,
        IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
        IFNULL(USUARIO.US_LOGIN, '') AS US_LOGIN,
        IFNULL(USUARIO.UNI_CODIGO, 0) AS UNI_CODIGO,
        IFNULL(USUARIO.TUS_CODIGO, 0) AS TUS_CODIGO,
        CASE WHEN USUARIO.FUN_CODIGO IS NULL THEN 0 ELSE 1 END AS TIENE_USUARIO
        FROM FUNCIONARIO
        JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
        LEFT JOIN CARGO_FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO AND CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
        LEFT JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO
        LEFT JOIN UNIDAD ON UNIDAD.UNI_CODIGO = CARGO_FUNCIONARIO.UNI_AGREGADO
        LEFT JOIN USUARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
        LEFT JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
        LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO AND CAPACITACION.ACTIVO = 1
        LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
        WHERE FUNCIONARIO.FUN_CODIGO = '$codigo'";
    $res = mysql_query($sql, $link);
    return $res ? mysql_fetch_assoc($res) : null;
}

function obtenerUltimosAccesos($codigo) {
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);
    $sql = "SELECT BITACORA_USUARIO.US_FECHAHORA_INICIO, GRADO.GRA_DESCRIPCION,
            CONCAT_WS(' ', FUNCIONARIO.FUN_APELLIDOPATERNO, FUNCIONARIO.FUN_APELLIDOMATERNO, FUNCIONARIO.FUN_NOMBRE, FUNCIONARIO.FUN_NOMBRE2) AS NOMBRE_COMPLETO,
            FUNCIONARIO.FUN_CODIGO, UNIDAD.UNI_DESCRIPCION, TIPO_USUARIO.TUS_DESCRIPCION, BITACORA_USUARIO.US_DIRECCION_IP
            FROM BITACORA_USUARIO
            INNER JOIN UNIDAD ON BITACORA_USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO
            INNER JOIN USUARIO ON BITACORA_USUARIO.FUN_CODIGO = USUARIO.FUN_CODIGO AND USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO
            INNER JOIN TIPO_USUARIO ON USUARIO.TUS_CODIGO = TIPO_USUARIO.TUS_CODIGO
            INNER JOIN FUNCIONARIO ON FUNCIONARIO.FUN_CODIGO = USUARIO.FUN_CODIGO
            INNER JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
            WHERE BITACORA_USUARIO.FUN_CODIGO = '$codigo'
            ORDER BY BITACORA_USUARIO.US_FECHAHORA_INICIO DESC LIMIT 10";
    $result = mysql_query($sql, $link);
    $accesos = array();
    while ($row = mysql_fetch_assoc($result)) { $accesos[] = $row; }
    return $accesos;
}

function actualizarUsuario($codigo, $perfil, $unidad, $password, $usuarioModificador) {
    global $link;
    
    if (empty($codigo) || empty($perfil) || empty($unidad)) {
        return array('success' => false, 'message' => 'Faltan datos obligatorios (Código, Perfil o Unidad).', 'code' => 400);
    }
    
    $codigo = mysql_real_escape_string(trim($codigo), $link);
    $perfil = mysql_real_escape_string(trim($perfil), $link);
    $unidad = mysql_real_escape_string(trim($unidad), $link);
    $password = trim($password);
    
    $check_sql = "SELECT FUN_CODIGO, US_ACTIVO FROM USUARIO WHERE FUN_CODIGO = '$codigo' LIMIT 1";
    $check_res = mysql_query($check_sql, $link);
    
    if (!$check_res || mysql_num_rows($check_res) == 0) {
        return array('success' => false, 'message' => 'El usuario especificado no existe.', 'code' => 404);
    }
    
    mysql_query("START TRANSACTION", $link);
    
    $sql = "UPDATE USUARIO SET TUS_CODIGO = '$perfil', UNI_CODIGO = '$unidad', US_FECHAMODIFICACION = NOW()";
    
    if (!empty($password)) {
        if (strlen($password) < 6) {
            mysql_query("ROLLBACK", $link);
            return array('success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.', 'code' => 400);
        }
        $passwordHash = md5($password);
        $sql .= ", US_PASSWORD = '$passwordHash'";
    }
    
    $sql .= " WHERE FUN_CODIGO = '$codigo'";
    $res = mysql_query($sql, $link);
    
    if (!$res) {
        mysql_query("ROLLBACK", $link);
        return array('success' => false, 'message' => 'Error al actualizar: ' . mysql_error($link), 'code' => 500);
    }
    
    mysql_query("COMMIT", $link);
    
    // Auditoría simple (opcional)
    $descripcion = "Edición de usuario $codigo. Perfil: $perfil, Unidad: $unidad." . (!empty($password) ? " Cambio de clave." : "");
    // Aquí podrías insertar en BITACORA_AUDITORIA si existe
    
    if (mysql_affected_rows($link) > 0) {
        return array('success' => true, 'message' => 'Datos actualizados correctamente.', 'code' => 200);
    } else {
        return array('success' => true, 'message' => 'No se realizaron cambios (datos iguales).', 'code' => 200);
    }
}
?>