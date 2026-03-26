<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
    $perfil = $_POST['perfil'];
    $unidad = $_POST['unidad'];
    $password = $_POST['password'];
    echo actualizarUsuario($codigo, $perfil, $unidad, $password);
}

// Función para obtener datos del funcionario
function obtenerFuncionario($codigo)
{
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);
    
    $sql = "SELECT 
        FUNCIONARIO.FUN_CODIGO,
        FUNCIONARIO.FUN_RUT,
        FUNCIONARIO.FUN_NOMBRE,
        IFNULL(FUNCIONARIO.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
        FUNCIONARIO.FUN_APELLIDOPATERNO,
        FUNCIONARIO.FUN_APELLIDOMATERNO,
        GRADO.GRA_DESCRIPCION,
        IFNULL(
            IF(CARGO_FUNCIONARIO.UNI_AGREGADO, 
               CONCAT(CARGO.CAR_DESCRIPCION, ' A ', UNIDAD.UNI_DESCRIPCION), 
               CARGO.CAR_DESCRIPCION),
            'SIN CARGO'
        ) AS CARGO,
        IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.ZONA_DESCRIPCION, '') AS ZONA_DESCRIPCION,
        IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.PREFECTURA_DESCRIPCION, '') AS PREFECTURA_DESCRIPCION,
        IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.COMISARIA_DESCRIPCION, '') AS COMISARIA_DESCRIPCION,
        IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION, 'MESA DE AYUDA') AS UNI_DESCRIPCION,
        IFNULL(TIPO_USUARIO.TUS_DESCRIPCION, '') AS TUS_DESCRIPCION,
        IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
        CAPACITACION.NOTA_PROSERVIPOL,
        CAPACITACION.FECHA_CAPACITACION,
        IFNULL(USUARIO.US_LOGIN, '') AS US_LOGIN,
        IFNULL(USUARIO.US_PASSWORD, '') AS US_PASSWORD,
        IFNULL(USUARIO.UNI_CODIGO, 0) AS UNI_CODIGO,
        IFNULL(USUARIO.TUS_CODIGO, 0) AS TUS_CODIGO,
        CASE WHEN USUARIO.FUN_CODIGO IS NULL THEN 0 ELSE 1 END AS TIENE_USUARIO
        FROM FUNCIONARIO
        JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO 
            AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
        LEFT JOIN CARGO_FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
            AND CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
        LEFT JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO
        LEFT JOIN UNIDAD ON UNIDAD.UNI_CODIGO = CARGO_FUNCIONARIO.UNI_AGREGADO
        LEFT JOIN USUARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
        LEFT JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
        LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
            AND CAPACITACION.ACTIVO = 1
        LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
            ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
        WHERE FUNCIONARIO.FUN_CODIGO = '$codigo'";
    
    $res = mysql_query($sql, $link);
    return $res ? mysql_fetch_assoc($res) : null;
}

// Función para obtener últimos accesos
function obtenerUltimosAccesos($codigo)
{
    global $link;
    $codigo = mysql_real_escape_string($codigo, $link);
    
    $sql = "SELECT 
            BITACORA_USUARIO.US_FECHAHORA_INICIO,
            GRADO.GRA_DESCRIPCION,
            CONCAT_WS(' ', FUNCIONARIO.FUN_APELLIDOPATERNO, FUNCIONARIO.FUN_APELLIDOMATERNO, FUNCIONARIO.FUN_NOMBRE, FUNCIONARIO.FUN_NOMBRE2) AS NOMBRE_COMPLETO,
            FUNCIONARIO.FUN_CODIGO,
            UNIDAD.UNI_DESCRIPCION,
            TIPO_USUARIO.TUS_DESCRIPCION,
            BITACORA_USUARIO.US_DIRECCION_IP
            FROM
            BITACORA_USUARIO
            INNER JOIN UNIDAD ON (BITACORA_USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
            INNER JOIN USUARIO ON (USUARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
            AND (BITACORA_USUARIO.FUN_CODIGO = USUARIO.FUN_CODIGO)
            INNER JOIN TIPO_USUARIO ON (USUARIO.TUS_CODIGO = TIPO_USUARIO.TUS_CODIGO)
            INNER JOIN FUNCIONARIO ON (FUNCIONARIO.FUN_CODIGO = USUARIO.FUN_CODIGO)
            INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)
            AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
            WHERE
            BITACORA_USUARIO.FUN_CODIGO = '$codigo'
            ORDER BY
            BITACORA_USUARIO.US_FECHAHORA_INICIO DESC
            LIMIT 10";

    $result = mysql_query($sql, $link);
    $accesos = array();
    while ($row = mysql_fetch_assoc($result)) {
        $accesos[] = $row;
    }
    return $accesos;
}

function actualizarUsuario($codigo, $perfil, $unidad, $password)
{
    global $link;
    if ($codigo === '' || $perfil === '' || $unidad === '') {
        return "Faltan datos";
    }
    // Escapar datos
    $codigo = mysql_real_escape_string($codigo, $link);
    $perfil = mysql_real_escape_string($perfil, $link);
    $unidad = mysql_real_escape_string($unidad, $link);
    $password = mysql_real_escape_string($password, $link);
    
    // Verificar si el usuario existe en la tabla USUARIO
    $check_sql = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '$codigo'";
    $check_res = mysql_query($check_sql, $link);
    
    if (mysql_num_rows($check_res) > 0) {
        // El usuario existe, hacer UPDATE
        $sql = "UPDATE USUARIO
                SET TUS_CODIGO = '$perfil',
                    UNI_CODIGO = '$unidad'";
        
        // Si el password viene vacío, no se actualiza
        if ($password !== '') {
            $sql .= ", US_PASSWORD = MD5('$password')";
        }
        
        $sql .= " WHERE FUN_CODIGO = '$codigo'";
        $res = mysql_query($sql, $link);
        
        if (!$res) {
            return "Error al actualizar: " . mysql_error($link);
        }
        if (mysql_affected_rows($link) > 0) {
            return "Datos actualizados correctamente";
        } else {
            return "No se realizaron cambios";
        }
    } else {
        // El usuario NO existe, hacer INSERT
        // Obtener el RUT del funcionario
        $rut_sql = "SELECT FUN_RUT FROM FUNCIONARIO WHERE FUN_CODIGO = '$codigo'";
        $rut_res = mysql_query($rut_sql, $link);
        
        if (!$rut_res || mysql_num_rows($rut_res) == 0) {
            return "Error: Funcionario no encontrado";
        }
        
        $rut_row = mysql_fetch_assoc($rut_res);
        $rut = $rut_row['FUN_RUT'];
        
        // Crear el login (usar el código del funcionario)
        $login = $codigo;
        
        // Si no se proporciona password, usar el RUT como password por defecto
        $pass_value = ($password !== '') ? "MD5('$password')" : "MD5('$rut')";
        
        $insert_sql = "INSERT INTO USUARIO (FUN_CODIGO, US_LOGIN, US_PASSWORD, TUS_CODIGO, UNI_CODIGO)
                       VALUES ('$codigo', '$login', $pass_value, '$perfil', '$unidad')";
        
        $res = mysql_query($insert_sql, $link);
        
        if (!$res) {
            return "Error al crear usuario: " . mysql_error($link);
        }
        
        return "Usuario creado correctamente";
    }
}
?>