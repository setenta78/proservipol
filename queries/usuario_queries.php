<?php
/**
 * Funciones de consulta para gestión de usuarios PROSERVIPOL
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

include_once(dirname(__FILE__) . '/../api/db/dbAutentificaTic.Class.php');

/**
 * Crea un nuevo usuario en PROSERVIPOL
 * 
 * @param string $codFuncionario Código del funcionario
 * @param string $codigoUnidad Código de la unidad
 * @param string $tipoUsuario Tipo de usuario (perfil)
 * @param string $password Contraseña del usuario
 * @param string $usuarioCreador Código del usuario que crea el registro
 * @return array Resultado de la operación
 */
function crearUsuarioProservipol($codFuncionario, $codigoUnidad, $tipoUsuario, $password, $usuarioCreador)
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
        f.FUN_APELLIDOMATERNO
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
        // ========================================
        // TRANSACCIÓN ATÓMICA PARA REACTIVACIÓN
        // ========================================
        mysql_query("START TRANSACTION", $link);
        
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
            mysql_query("ROLLBACK", $link);
            return array(
                'success' => false,
                'message' => 'Error al reactivar usuario: ' . mysql_error($link),
                'code' => 500
            );
        }
        
        // ========================================
        // PASO ADICIONAL: Llamar a AutentificaTIC API para reactivar
        // ========================================
        // Obtener RUT del funcionario para llamar a la API
        $sqlRut = "SELECT f.FUN_RUT 
                   FROM FUNCIONARIO f 
                   WHERE f.FUN_CODIGO = '$codFuncionario' 
                   LIMIT 1";
        $resultRut = mysql_query($sqlRut, $link);
        $rut = '';
        if ($resultRut && mysql_num_rows($resultRut) > 0) {
            $funcData = mysql_fetch_assoc($resultRut);
            $rut = $funcData['FUN_RUT'];
        }
        
        // Si tenemos el RUT, intentar registrar en AutentificaTIC
        if (!empty($rut)) {
            // El token ya está disponible en $_SESSION (sesión iniciada en el endpoint)
            $accessToken = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;
            
            if ($accessToken) {
                $api = new AutentificaTicAPI($accessToken);
                $resultApi = $api->registrarUsuario($rut);
                
                if (!$resultApi['success']) {
                    // Rollback: revertir la reactivación en BD
                    mysql_query("ROLLBACK", $link);
                    
                    // Mensaje específico según el error
                    $mensajeError = 'En este momento existe un problema de conexión con Autentificatic. Por favor intente nuevamente.';
                    if (strpos($resultApi['message'], 'no encontrado') !== false) {
                        $mensajeError = 'El funcionario no se encuentra registrado en Autentificatic.';
                    } elseif (strpos($resultApi['message'], 'Plataforma') !== false) {
                        $mensajeError = 'La plataforma no se encuentra registrada en Autentificatic.';
                    }
                    
                    return array(
                        'success' => false,
                        'message' => $mensajeError,
                        'code' => 500,
                        'api_error' => $resultApi['message']
                    );
                }
            } else {
                // No hay token de acceso - rollback
                mysql_query("ROLLBACK", $link);
                return array(
                    'success' => false,
                    'message' => 'No se pudo obtener el token de autenticación. Por favor inicie sesión nuevamente.',
                    'code' => 500
                );
            }
        }
        
        // Confirmar transacción
        mysql_query("COMMIT", $link);
        
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
    
    // ========================================
    // INICIAR TRANSACCIÓN PARA CREACIÓN
    // ========================================
    mysql_query("START TRANSACTION", $link);

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
    // PASO 4: Insertar en tabla USUARIO (dentro de transacción)
    // ========================================
    // Encriptar contraseña (usar el método que use tu sistema)
    $passwordHash = md5($password);
    
    // Obtener RUT del funcionario
    $rut = mysql_real_escape_string($funcionario['PEFBRUT'], $link);

    $sqlInsertUsuario = "INSERT INTO USUARIO (
        FUN_CODIGO,
        UNI_CODIGO,
        US_LOGIN,
        US_RUT,
        US_PASSWORD,
        TUS_CODIGO,
        US_FECHACREACION,
        US_ACTIVO
    ) VALUES (
        '$codFuncionario',
        $codigoUnidad,
        '$codFuncionario',
        '$rut',
        '$passwordHash',
        $tipoUsuario,
        NOW(),
        1
    )";

    $resultInsertUsuario = mysql_query($sqlInsertUsuario, $link);

    if (!$resultInsertUsuario) {
        mysql_query("ROLLBACK", $link);
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
    // PASO 5: Registrar en AutentificaTIC API
    // ========================================
    // El RUT ya fue obtenido arriba
    
    // El token ya está disponible en $_SESSION (sesión iniciada en el endpoint)
    $accessToken = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;
    
    if ($accessToken && !empty($rut)) {
        $api = new AutentificaTicAPI($accessToken);
        $resultApi = $api->registrarUsuario($rut);
        
        if (!$resultApi['success']) {
            // Rollback: revertir toda la creación en BD
            mysql_query("ROLLBACK", $link);
            
            // Mensaje específico según el error
            $mensajeError = 'En este momento existe un problema de conexión con Autentificatic. Por favor intente nuevamente.';
            if (strpos($resultApi['message'], 'no encontrado') !== false) {
                $mensajeError = 'El funcionario no se encuentra registrado en Autentificatic.';
            } elseif (strpos($resultApi['message'], 'Plataforma') !== false) {
                $mensajeError = 'La plataforma no se encuentra registrada en Autentificatic.';
            }
            
            return array(
                'success' => false,
                'message' => $mensajeError,
                'code' => 500,
                'api_error' => $resultApi['message']
            );
        }
    } elseif (!empty($rut) && !$accessToken) {
        // No hay token de acceso - rollback
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'No se pudo obtener el token de autenticación. Por favor inicie sesión nuevamente.',
            'code' => 500
        );
    }
    
    // Confirmar transacción
    mysql_query("COMMIT", $link);

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