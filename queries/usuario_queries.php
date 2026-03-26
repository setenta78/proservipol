<?php
/**
 * Funciones de consulta para gestión de usuarios PROSERVIPOL
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

if (!defined('AUTENTIFICATIC_API_URL')) {
    require_once dirname(__FILE__) . '/../inc/config_autentificatic.php';
}

/**
 * Limpia el RUT chileno: elimina puntos y guión
 * Ej: "13.498.948-3" → "134989483"
 */
function limpiarRut($rut)
{
    return str_replace(array('.', '-', ' '), '', trim($rut));
}

/**
 * Registra un usuario en AutentificaTIC API
 * POST /api/nstitutional-app-user-from-external-app
 *
 * @param string $rut RUT del funcionario (cualquier formato, se limpia internamente)
 * @param string $token Bearer token del usuario logueado
 * @return array ['success' => bool, 'connection_error' => bool, 'message' => string]
 */
function registrarEnAutentificatic($rut, $token)
{
    $url      = AUTENTIFICATIC_API_URL . '/api/nstitutional-app-user-from-external-app';
    $rutLimpio = limpiarRut($rut);
    $postData  = 'rut=' . urlencode($rutLimpio);

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ));
        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode === 0) {
            return array(
                'success'          => false,
                'connection_error' => true,
                'message'          => 'En este momento existe un problema de conexión con Autentificatic. Inténtelo más tarde.'
            );
        }
        if ($httpCode >= 200 && $httpCode < 300) {
            return array('success' => true, 'connection_error' => false, 'message' => 'Usuario registrado en AutentificaTIC');
        }
        return array(
            'success'          => false,
            'connection_error' => false,
            'message'          => 'AutentificaTIC rechazó el registro (HTTP ' . $httpCode . '): ' . $response
        );
    }

    // Fallback: file_get_contents
    $context  = stream_context_create(array(
        'http' => array(
            'method'        => 'POST',
            'header'        => "Authorization: Bearer $token\r\nAccept: application/json\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($postData) . "\r\n",
            'content'       => $postData,
            'timeout'       => 10,
            'ignore_errors' => true
        )
    ));
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return array(
            'success'          => false,
            'connection_error' => true,
            'message'          => 'En este momento existe un problema de conexión con Autentificatic. Inténtelo más tarde.'
        );
    }

    $httpCode = 500;
    if (isset($http_response_header) && is_array($http_response_header)) {
        if (preg_match('/HTTP\/[\d\.]+\s+(\d+)/', $http_response_header[0], $m)) {
            $httpCode = (int)$m[1];
        }
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return array('success' => true, 'connection_error' => false, 'message' => 'Usuario registrado en AutentificaTIC');
    }
    return array(
        'success'          => false,
        'connection_error' => false,
        'message'          => 'AutentificaTIC rechazó el registro (HTTP ' . $httpCode . '): ' . $response
    );
}

/**
 * Crea un nuevo usuario en PROSERVIPOL
 *
 * @param string $codFuncionario Código del funcionario
 * @param string $codigoUnidad Código de la unidad
 * @param string $tipoUsuario Tipo de usuario (perfil)
 * @param string $password Contraseña del usuario
 * @param string $usuarioCreador Código del usuario que crea el registro
 * @param string $token Bearer token del usuario logueado (para AutentificaTIC)
 * @return array Resultado de la operación
 */
function crearUsuarioProservipol($codFuncionario, $codigoUnidad, $tipoUsuario, $password, $usuarioCreador, $token = '')
{
    global $link;
    
    // Escapar valores para prevenir SQL injection
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $codigoUnidad = intval($codigoUnidad);
    $tipoUsuario = intval($tipoUsuario);
    $password = mysql_real_escape_string($password, $link);
    $usuarioCreador = mysql_real_escape_string($usuarioCreador, $link);
    
    // ========================================
    // PASO 1: Verificar si el usuario ya existe
    // ========================================
    $sqlCheck = "SELECT 
        u.US_CODIGO,
        u.US_ACTIVO,
        u.FUN_CODIGO,
        f.FUN_NOMBRE,
        f.FUN_APELLIDOPATERNO,
        f.FUN_APELLIDOMATERNO,
        f.FUN_RUT
    FROM USUARIO u
    LEFT JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
    WHERE u.US_LOGIN = '$codFuncionario'
    LIMIT 1";
    
    $resultCheck = mysql_query($sqlCheck, $link);
    
    if (!$resultCheck) {
        return array(
            'success' => false,
            'message' => 'Error al verificar usuario existente: ' . mysql_error($link),
            'code' => 500
        );
    }
    
    $usuarioExistente = mysql_fetch_assoc($resultCheck);
    
    // CASO 1: Usuario existe y está activo
    if ($usuarioExistente && $usuarioExistente['US_ACTIVO'] == '1') {
        $nombreCompleto = trim($usuarioExistente['FUN_NOMBRE'] . ' ' . 
                              $usuarioExistente['FUN_APELLIDOPATERNO'] . ' ' . 
                              $usuarioExistente['FUN_APELLIDOMATERNO']);
        
        return array(
            'success' => false,
            'message' => 'El usuario ' . $nombreCompleto . ' (' . $codFuncionario . ') ya existe y está activo en el sistema',
            'code' => 409
        );
    }
    
    // CASO 2: Usuario existe pero está inactivo - REACTIVAR
    if ($usuarioExistente && $usuarioExistente['US_ACTIVO'] == '0') {
        // Encriptar contraseña (usar el método que use tu sistema)
        $passwordHash = md5($password);
        
        $sqlReactivar = "UPDATE USUARIO 
            SET US_ACTIVO = 1,
                UNI_CODIGO = $codigoUnidad,
                TUS_CODIGO = $tipoUsuario,
                US_CLAVE = '$passwordHash',
                US_FECHAMODIFICACION = NOW()
            WHERE US_LOGIN = '$codFuncionario'";
        
        $resultReactivar = mysql_query($sqlReactivar, $link);
        
        if (!$resultReactivar) {
            return array(
                'success' => false,
                'message' => 'Error al reactivar usuario: ' . mysql_error($link),
                'code' => 500
            );
        }
        
        // Registrar en auditoría
        registrarAuditoria($link, $usuarioCreador, 'REACTIVAR_USUARIO', 
            'Usuario ' . $codFuncionario . ' reactivado');
        
        return array(
            'success' => true,
            'message' => 'Usuario reactivado exitosamente',
            'code' => 200
        );
    }
    
    // ========================================
    // PASO 2: Buscar datos del funcionario en PERSONAL_MOCK o personal_view
    // ========================================
    $checkMock = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $link);
    $useMock = (mysql_num_rows($checkMock) > 0);
    
    if ($useMock) {
        // Desarrollo: Usar PERSONAL_MOCK
        $sqlPersonal = "SELECT 
            PEFBCOD,
            PEFBRUT,
            PEFBNOM1,
            PEFBNOM2,
            PEFBAPEP,
            PEFBAPEM,
            PEFBGRA
        FROM PERSONAL_MOCK
        WHERE PEFBCOD = '$codFuncionario'
        LIMIT 1";
        
        $resultPersonal = mysql_query($sqlPersonal, $link);
        
    } else {
        // Producción: Conectar a BD Personal
        require_once(dirname(__FILE__) . '/../inc/configPersonal.inc.php');
        
        $connPersonal = mysql_connect(HOST_PERSONAL, DB_USER_PERSONAL, DB_PASS_PERSONAL, true);
        if (!$connPersonal) {
            return array(
                'success' => false,
                'message' => 'Error de conexión con BD Personal: ' . mysql_error(),
                'code' => 500
            );
        }
        
        if (!mysql_select_db(DB_PERSONAL, $connPersonal)) {
            mysql_close($connPersonal);
            return array(
                'success' => false,
                'message' => 'Error al seleccionar BD Personal: ' . mysql_error(),
                'code' => 500
            );
        }
        
        mysql_query("SET NAMES 'utf8'", $connPersonal);
        
        $sqlPersonal = "SELECT 
            PEFBCOD,
            PEFBRUT,
            PEFBNOM1,
            PEFBNOM2,
            PEFBAPEP,
            PEFBAPEM,
            PEFBGRA
        FROM personal_view
        WHERE PEFBCOD = '$codFuncionario'
        LIMIT 1";
        
        $resultPersonal = mysql_query($sqlPersonal, $connPersonal);
    }
    
    if (!$resultPersonal || mysql_num_rows($resultPersonal) == 0) {
        if (!$useMock && isset($connPersonal)) {
            mysql_close($connPersonal);
        }
        
        return array(
            'success' => false,
            'message' => 'No se encontraron datos del funcionario ' . $codFuncionario . ' en la base de datos de Personal',
            'code' => 404
        );
    }
    
    $funcionario = mysql_fetch_assoc($resultPersonal);
    
    if (!$useMock && isset($connPersonal)) {
        mysql_close($connPersonal);
    }
    
    // ========================================
    // PASO 3: Verificar si existe en tabla FUNCIONARIO
    // ========================================
    $sqlCheckFuncionario = "SELECT FUN_CODIGO 
        FROM FUNCIONARIO 
        WHERE FUN_CODIGO = '$codFuncionario'
        LIMIT 1";
    
    $resultCheckFuncionario = mysql_query($sqlCheckFuncionario, $link);
    $funcionarioExiste = (mysql_num_rows($resultCheckFuncionario) > 0);
    
    // PASO 3.1: Si no existe, insertar en FUNCIONARIO
    if (!$funcionarioExiste) {
        $rut = mysql_real_escape_string($funcionario['PEFBRUT'], $link);
        $nombre1 = mysql_real_escape_string($funcionario['PEFBNOM1'], $link);
        $nombre2 = mysql_real_escape_string($funcionario['PEFBNOM2'], $link);
        $apellidoP = mysql_real_escape_string($funcionario['PEFBAPEP'], $link);
        $apellidoM = mysql_real_escape_string($funcionario['PEFBAPEM'], $link);
        $gradoCodigo = intval($funcionario['PEFBGRA']);
        
        // Obtener ESC_CODIGO del grado
        $sqlGrado = "SELECT ESC_CODIGO FROM GRADO WHERE GRA_CODIGO = $gradoCodigo LIMIT 1";
        $resultGrado = mysql_query($sqlGrado, $link);
        
        if ($resultGrado && mysql_num_rows($resultGrado) > 0) {
            $grado = mysql_fetch_assoc($resultGrado);
            $escCodigo = intval($grado['ESC_CODIGO']);
        } else {
            $escCodigo = 1; // Valor por defecto
        }
        
        $sqlInsertFuncionario = "INSERT INTO FUNCIONARIO (
            FUN_CODIGO,
            FUN_RUT,
            FUN_NOMBRE,
            FUN_NOMBRE2,
            FUN_APELLIDOPATERNO,
            FUN_APELLIDOMATERNO,
            GRA_CODIGO,
            ESC_CODIGO
        ) VALUES (
            '$codFuncionario',
            '$rut',
            '$nombre1',
            '$nombre2',
            '$apellidoP',
            '$apellidoM',
            $gradoCodigo,
            $escCodigo
        )";
        
        $resultInsertFuncionario = mysql_query($sqlInsertFuncionario, $link);
        
        if (!$resultInsertFuncionario) {
            return array(
                'success' => false,
                'message' => 'Error al insertar funcionario: ' . mysql_error($link),
                'code' => 500
            );
        }
    }
    
    // ========================================
    // PASO 4: Insertar en tabla USUARIO
    // ========================================
    // Encriptar contraseña (usar el método que use tu sistema)
    $passwordHash = md5($password);
    
    $sqlInsertUsuario = "INSERT INTO USUARIO (
        FUN_CODIGO,
        UNI_CODIGO,
        US_LOGIN,
        US_CLAVE,
        TUS_CODIGO,
        US_FECHACREACION,
        US_ACTIVO
    ) VALUES (
        '$codFuncionario',
        $codigoUnidad,
        '$codFuncionario',
        '$passwordHash',
        $tipoUsuario,
        NOW(),
        1
    )";
    
    $resultInsertUsuario = mysql_query($sqlInsertUsuario, $link);
    
    if (!$resultInsertUsuario) {
        return array(
            'success' => false,
            'message' => 'Error al crear usuario: ' . mysql_error($link),
            'code' => 500
        );
    }
    
    // Registrar en auditoría
    registrarAuditoria($link, $usuarioCreador, 'CREAR_USUARIO', 
        'Usuario ' . $codFuncionario . ' creado exitosamente');
    
    // ========================================
    // PASO 5: Registrar en AutentificaTIC API (opcional)
    // ========================================
    // TODO: Implementar llamada a AutentificaTIC API si es necesario
    // $resultAutentificatic = registrarEnAutentificatic($codFuncionario, $password);
    
    // ========================================
    // RESPUESTA EXITOSA
    // ========================================
    return array(
        'success' => true,
        'message' => 'Usuario creado exitosamente',
        'code' => 201,
        'data' => array(
            'codFuncionario' => $codFuncionario,
            'nombre' => trim($nombre1 . ' ' . $nombre2),
            'apellidos' => trim($apellidoP . ' ' . $apellidoM)
        )
    );
}

/**
 * Registra una acción en la tabla de auditoría
 * 
 * @param resource $link Conexión a la base de datos
 * @param string $usuario Usuario que realiza la acción
 * @param string $accion Tipo de acción
 * @param string $descripcion Descripción de la acción
 * @return bool Resultado de la operación
 */
function registrarAuditoria($link, $usuario, $accion, $descripcion)
{
    $usuario = mysql_real_escape_string($usuario, $link);
    $accion = mysql_real_escape_string($accion, $link);
    $descripcion = mysql_real_escape_string($descripcion, $link);
    
    // Verificar si existe la tabla de auditoría
    $checkTable = mysql_query("SHOW TABLES LIKE 'AUDITORIA'", $link);
    
    if (mysql_num_rows($checkTable) == 0) {
        // Si no existe la tabla, no hacer nada (evitar errores)
        return true;
    }
    
    $sqlAuditoria = "INSERT INTO AUDITORIA (
        AUD_USUARIO,
        AUD_ACCION,
        AUD_DESCRIPCION,
        AUD_FECHA,
        AUD_IP
    ) VALUES (
        '$usuario',
        '$accion',
        '$descripcion',
        NOW(),
        '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $link) . "'
    )";
    
    $result = mysql_query($sqlAuditoria, $link);
    
    return ($result !== false);
}

/**
 * Obtiene los datos de un usuario por su código de funcionario
 * 
 * @param string $codFuncionario Código del funcionario
 * @return array|null Datos del usuario o null si no existe
 */
function obtenerUsuarioPorCodigo($codFuncionario)
{
    global $link;
    
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    
    $sql = "SELECT 
        u.US_CODIGO,
        u.FUN_CODIGO,
        u.UNI_CODIGO,
        u.US_LOGIN,
        u.TUS_CODIGO,
        u.US_FECHACREACION,
        u.US_FECHAMODIFICACION,
        u.US_ACTIVO,
        f.FUN_RUT,
        f.FUN_NOMBRE,
        f.FUN_NOMBRE2,
        f.FUN_APELLIDOPATERNO,
        f.FUN_APELLIDOMATERNO,
        g.GRA_DESCRIPCION,
        un.UNI_DESCRIPCION,
        tus.TUS_DESCRIPCION
    FROM USUARIO u
    LEFT JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
    LEFT JOIN GRADO g ON f.GRA_CODIGO = g.GRA_CODIGO AND f.ESC_CODIGO = g.ESC_CODIGO
    LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
    LEFT JOIN TIPO_USUARIO tus ON u.TUS_CODIGO = tus.TUS_CODIGO
    WHERE u.US_LOGIN = '$codFuncionario'
    LIMIT 1";
    
    $result = mysql_query($sql, $link);
    
    if (!$result || mysql_num_rows($result) == 0) {
        return null;
    }
    
    return mysql_fetch_assoc($result);
}

/**
 * Actualiza la contraseña de un usuario
 * 
 * @param string $codFuncionario Código del funcionario
 * @param string $nuevaPassword Nueva contraseña
 * @return array Resultado de la operación
 */
function actualizarPasswordUsuario($codFuncionario, $nuevaPassword)
{
    global $link;
    
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $passwordHash = md5($nuevaPassword);
    
    $sql = "UPDATE USUARIO 
        SET US_CLAVE = '$passwordHash',
            US_FECHAMODIFICACION = NOW()
        WHERE US_LOGIN = '$codFuncionario'";
    
    $result = mysql_query($sql, $link);
    
    if (!$result) {
        return array(
            'success' => false,
            'message' => 'Error al actualizar contraseña: ' . mysql_error($link)
        );
    }
    
    return array(
        'success' => true,
        'message' => 'Contraseña actualizada exitosamente'
    );
}

/**
 * Desactiva un usuario (soft delete)
 * 
 * @param string $codFuncionario Código del funcionario
 * @param string $usuarioModificador Usuario que realiza la modificación
 * @return array Resultado de la operación
 */
function desactivarUsuario($codFuncionario, $usuarioModificador)
{
    global $link;
    
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    
    $sql = "UPDATE USUARIO 
        SET US_ACTIVO = 0,
            US_FECHAMODIFICACION = NOW()
        WHERE US_LOGIN = '$codFuncionario'";
    
    $result = mysql_query($sql, $link);
    
    if (!$result) {
        return array(
            'success' => false,
            'message' => 'Error al desactivar usuario: ' . mysql_error($link)
        );
    }
    
    // Registrar en auditoría
    registrarAuditoria($link, $usuarioModificador, 'DESACTIVAR_USUARIO', 
        'Usuario ' . $codFuncionario . ' desactivado');
    
    return array(
        'success' => true,
        'message' => 'Usuario desactivado exitosamente'
    );
}
?>