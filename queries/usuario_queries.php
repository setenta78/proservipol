<?php
/**
 * Funciones de consulta para gestión de usuarios PROSERVIPOL
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

if (!class_exists('AutentificaTicAPI')) {
    include_once(dirname(__FILE__) . '/../api/db/dbAutentificaTic.Class.php');
}

function _obtenerTokenServicio() {
    $apiLogin    = new AutentificaTicAPI('');
    $loginResult = $apiLogin->loginServicio(
        SERVICIO_RUT_DESPROSERVIPOL,
        SERVICIO_PASS_DESPROSERVIPOL
    );
    return $loginResult;
}

function crearUsuarioProservipol($codFuncionario, $codigoUnidad, $tipoUsuario, $password, $usuarioCreador)
{
    global $link;

    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $codigoUnidad   = intval($codigoUnidad);
    $tipoUsuario    = intval($tipoUsuario);
    $password       = mysql_real_escape_string($password, $link);
    $usuarioCreador = mysql_real_escape_string($usuarioCreador, $link);

    // ================================================
    // PASO 1: Verificar si el usuario ya existe en USUARIO
    // ================================================
    $sqlCheck = "SELECT 
        u.FUN_CODIGO,
        u.US_ACTIVO,
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
            'code'    => 500
        );
    }

    $usuarioExistente = mysql_fetch_assoc($resultCheck);

    // CASO 1: Usuario activo — no permitir duplicado
    if ($usuarioExistente && $usuarioExistente['US_ACTIVO'] == '1') {
        $nombreCompleto = trim(
            $usuarioExistente['FUN_NOMBRE'] . ' ' .
            $usuarioExistente['FUN_APELLIDOPATERNO'] . ' ' .
            $usuarioExistente['FUN_APELLIDOMATERNO']
        );
        return array(
            'success' => false,
            'message' => 'El usuario ' . $nombreCompleto . ' (' . $codFuncionario . ') ya existe y está activo en el sistema.',
            'code'    => 409
        );
    }

    // ================================================
    // PASO 2: Validar sesión activa del administrador
    // ================================================
    $sessionToken = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;

    if (empty($sessionToken)) {
        return array(
            'success' => false,
            'message' => 'Error de sesión: No se pudo obtener el token de autenticación. Por favor, cierre sesión e inicie nuevamente.',
            'code'    => 401
        );
    }

    // ================================================
    // PASO 2B: Obtener token de servicio para AutentificaTIC
    // (siempre con Origin: des-proservipol, independiente del admin logueado)
    // ================================================
    $loginResult = _obtenerTokenServicio();

    if (!$loginResult['success']) {
        return array(
            'success' => false,
            'message' => 'Error al conectar con AutentificaTIC como servicio: ' . $loginResult['message'],
            'code'    => 500
        );
    }

    $tokenServicio = $loginResult['access_token'];

    // ================================================
    // CASO 2: Usuario inactivo — REACTIVAR
    // ================================================
    if ($usuarioExistente && $usuarioExistente['US_ACTIVO'] == '0') {

        // Obtener RUT del funcionario
        $sqlRut = "SELECT FUN_RUT FROM FUNCIONARIO 
                   WHERE FUN_CODIGO = '$codFuncionario' LIMIT 1";
        $resultRut = mysql_query($sqlRut, $link);
        $rut = '';
        if ($resultRut && mysql_num_rows($resultRut) > 0) {
            $funcData = mysql_fetch_assoc($resultRut);
            $rut = preg_replace('/[^0-9kK]/', '', $funcData['FUN_RUT']);
        }

        if (empty($rut)) {
            return array(
                'success' => false,
                'message' => 'No se pudo obtener el RUT del funcionario para reactivar en AutentificaTIC.',
                'code'    => 500
            );
        }

        // Llamar a AutentificaTIC con token de servicio ANTES de modificar la BD
        $api       = new AutentificaTicAPI($tokenServicio);
        $resultApi = $api->registrarUsuario($rut);

        if (!$resultApi['success']) {
            return array(
                'success'   => false,
                'message'   => _mensajeErrorApi($resultApi),
                'code'      => 500,
                'api_error' => $resultApi['message']
            );
        }

        // API exitosa — ahora actualizar BD
        mysql_query("START TRANSACTION", $link);

        $passwordHash = md5($password);

        $sqlReactivar = "UPDATE USUARIO 
            SET US_ACTIVO            = 1,
                UNI_CODIGO           = $codigoUnidad,
                TUS_CODIGO           = $tipoUsuario,
                US_PASSWORD          = '$passwordHash',
                US_FECHAMODIFICACION = NOW()
            WHERE US_LOGIN = '$codFuncionario'";

        $resultReactivar = mysql_query($sqlReactivar, $link);

        if (!$resultReactivar) {
            mysql_query("ROLLBACK", $link);
            // Intentar revertir en AutentificaTIC
            $api->eliminarUsuario($rut);
            return array(
                'success' => false,
                'message' => 'Error al reactivar usuario en BD: ' . mysql_error($link),
                'code'    => 500
            );
        }

        mysql_query("COMMIT", $link);
        registrarAuditoria($link, $usuarioCreador, 'REACTIVAR_USUARIO',
            'Usuario ' . $codFuncionario . ' reactivado');

        return array(
            'success' => true,
            'message' => 'Usuario reactivado exitosamente.',
            'code'    => 200
        );
    }

    // ================================================
    // CASO 3: Usuario nuevo — CREACIÓN COMPLETA
    // ================================================

    // PASO 3: Buscar datos en PERSONAL_MOCK o personal_view
    $checkMock = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $link);
    $useMock   = (mysql_num_rows($checkMock) > 0);

    if ($useMock) {
        $sqlPersonal = "SELECT 
            PEFBCOD, PEFBRUT, PEFBNOM1, PEFBNOM2,
            PEFBAPEP, PEFBAPEM, PEFBGRA
        FROM PERSONAL_MOCK
        WHERE PEFBCOD = '$codFuncionario'
        LIMIT 1";
        $resultPersonal = mysql_query($sqlPersonal, $link);
    } else {
        if (!file_exists(dirname(__FILE__) . '/../inc/configPersonal.inc.php')) {
            return array(
                'success' => false,
                'message' => 'Archivo de configuración de Personal no encontrado.',
                'code'    => 500
            );
        }
        require_once(dirname(__FILE__) . '/../inc/configPersonal.inc.php');
        $connPersonal = mysql_connect(HOST_PERSONAL, DB_USER_PERSONAL, DB_PASS_PERSONAL, true);
        if (!$connPersonal) {
            return array(
                'success' => false,
                'message' => 'Error de conexión con BD Personal: ' . mysql_error(),
                'code'    => 500
            );
        }
        if (!mysql_select_db(DB_PERSONAL, $connPersonal)) {
            mysql_close($connPersonal);
            return array(
                'success' => false,
                'message' => 'Error al seleccionar BD Personal.',
                'code'    => 500
            );
        }
        $sqlPersonal = "SELECT 
            PEFBCOD, PEFBRUT, PEFBNOM1, PEFBNOM2,
            PEFBAPEP, PEFBAPEM, PEFBGRA
        FROM personal_view
        WHERE PEFBCOD = '$codFuncionario'
        LIMIT 1";
        $resultPersonal = mysql_query($sqlPersonal, $connPersonal);
    }

    if (!$resultPersonal || mysql_num_rows($resultPersonal) == 0) {
        if (!$useMock && isset($connPersonal)) mysql_close($connPersonal);
        return array(
            'success' => false,
            'message' => 'No se encontraron datos del funcionario ' . $codFuncionario . ' en la base de datos de Personal.',
            'code'    => 404
        );
    }

    $funcionario = mysql_fetch_assoc($resultPersonal);
    if (!$useMock && isset($connPersonal)) mysql_close($connPersonal);

    // Limpiar RUT
    $rut = preg_replace('/[^0-9kK]/', '', $funcionario['PEFBRUT']);

    if (empty($rut)) {
        return array(
            'success' => false,
            'message' => 'El RUT del funcionario es inválido o está vacío.',
            'code'    => 500
        );
    }

    // Llamar a AutentificaTIC con token de servicio ANTES de tocar la BD
    $api       = new AutentificaTicAPI($tokenServicio);
    $resultApi = $api->registrarUsuario($rut);

    if (!$resultApi['success']) {
        return array(
            'success'   => false,
            'message'   => _mensajeErrorApi($resultApi),
            'code'      => 500,
            'api_error' => $resultApi['message']
        );
    }

    // AutentificaTIC OK — ahora iniciar transacción en BD
    mysql_query("START TRANSACTION", $link);

    // PASO 4: Insertar en FUNCIONARIO si no existe
    $sqlCheckFun    = "SELECT FUN_CODIGO FROM FUNCIONARIO 
                       WHERE FUN_CODIGO = '$codFuncionario' LIMIT 1";
    $resultCheckFun    = mysql_query($sqlCheckFun, $link);
    $funcionarioExiste = (mysql_num_rows($resultCheckFun) > 0);

    if (!$funcionarioExiste) {
        $rutEsc    = mysql_real_escape_string($funcionario['PEFBRUT'], $link);
        $nombre1   = mysql_real_escape_string($funcionario['PEFBNOM1'], $link);
        $nombre2   = mysql_real_escape_string($funcionario['PEFBNOM2'], $link);
        $apellidoP = mysql_real_escape_string($funcionario['PEFBAPEP'], $link);
        $apellidoM = mysql_real_escape_string($funcionario['PEFBAPEM'], $link);
        $gradoCodigo = intval($funcionario['PEFBGRA']);

        $sqlGrado    = "SELECT ESC_CODIGO FROM GRADO WHERE GRA_CODIGO = $gradoCodigo LIMIT 1";
        $resultGrado = mysql_query($sqlGrado, $link);
        $escCodigo   = 1;
        if ($resultGrado && mysql_num_rows($resultGrado) > 0) {
            $rowGrado  = mysql_fetch_assoc($resultGrado);
            $escCodigo = intval($rowGrado['ESC_CODIGO']);
        }

        $sqlInsertFun = "INSERT INTO FUNCIONARIO (
            FUN_CODIGO, FUN_RUT, FUN_NOMBRE, FUN_NOMBRE2,
            FUN_APELLIDOPATERNO, FUN_APELLIDOMATERNO,
            GRA_CODIGO, ESC_CODIGO
        ) VALUES (
            '$codFuncionario', '$rutEsc', '$nombre1', '$nombre2',
            '$apellidoP', '$apellidoM',
            $gradoCodigo, $escCodigo
        )";

        if (!mysql_query($sqlInsertFun, $link)) {
            mysql_query("ROLLBACK", $link);
            $api->eliminarUsuario($rut);
            return array(
                'success' => false,
                'message' => 'Error al insertar funcionario: ' . mysql_error($link),
                'code'    => 500
            );
        }
    }

    // PASO 5: Insertar en USUARIO
    $passwordHash = md5($password);

    $sqlInsertUsuario = "INSERT INTO USUARIO (
        FUN_CODIGO,
        UNI_CODIGO,
        US_LOGIN,
        US_PASSWORD,
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

    if (!mysql_query($sqlInsertUsuario, $link)) {
        mysql_query("ROLLBACK", $link);
        $api->eliminarUsuario($rut);
        return array(
            'success' => false,
            'message' => 'Error al crear usuario: ' . mysql_error($link),
            'code'    => 500
        );
    }

    // Todo OK — confirmar transacción
    mysql_query("COMMIT", $link);

    registrarAuditoria($link, $usuarioCreador, 'CREAR_USUARIO',
        'Usuario ' . $codFuncionario . ' creado exitosamente');

    return array(
        'success' => true,
        'message' => 'Usuario creado exitosamente.',
        'code'    => 201,
        'data'    => array(
            'codFuncionario' => $codFuncionario,
            'nombre'         => trim($funcionario['PEFBNOM1'] . ' ' . $funcionario['PEFBNOM2']),
            'apellidos'      => trim($funcionario['PEFBAPEP'] . ' ' . $funcionario['PEFBAPEM'])
        )
    );
}

/**
 * Función auxiliar para generar mensajes de error de la API
 */
function _mensajeErrorApi($resultApi)
{
    $msgLower = strtolower($resultApi['message']);

    if (strpos($msgLower, 'no encontrado') !== false ||
        strpos($msgLower, 'not found') !== false) {
        return 'El funcionario no se encuentra registrado en AutentificaTIC.';
    }
    if (strpos($msgLower, 'plataforma') !== false) {
        return 'La plataforma no se encuentra registrada en AutentificaTIC.';
    }
    if ($resultApi['http_code'] == 400) {
        return 'Datos inválidos enviados a AutentificaTIC: ' . $resultApi['message'];
    }
    if ($resultApi['http_code'] == 401) {
        return 'Token de sesión inválido o expirado. Por favor, cierre sesión e inicie nuevamente.';
    }
    return 'En este momento existe un problema de conexión con AutentificaTIC. Por favor intente nuevamente.';
}

function registrarAuditoria($link, $usuario, $accion, $descripcion)
{
    $usuario     = mysql_real_escape_string($usuario, $link);
    $accion      = mysql_real_escape_string($accion, $link);
    $descripcion = mysql_real_escape_string($descripcion, $link);
    $checkTable  = mysql_query("SHOW TABLES LIKE 'AUDITORIA'", $link);
    if (mysql_num_rows($checkTable) == 0) return true;
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $sql = "INSERT INTO AUDITORIA (
        AUD_USUARIO, AUD_ACCION, AUD_DESCRIPCION, AUD_FECHA, AUD_IP
    ) VALUES (
        '$usuario', '$accion', '$descripcion', NOW(),
        '" . mysql_real_escape_string($ip, $link) . "'
    )";
    return (mysql_query($sql, $link) !== false);
}

function obtenerUsuarioPorCodigo($codFuncionario)
{
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $sql = "SELECT 
        u.FUN_CODIGO, u.UNI_CODIGO, u.US_LOGIN, u.TUS_CODIGO,
        u.US_FECHACREACION, u.US_FECHAMODIFICACION, u.US_ACTIVO,
        f.FUN_RUT, f.FUN_NOMBRE, f.FUN_NOMBRE2,
        f.FUN_APELLIDOPATERNO, f.FUN_APELLIDOMATERNO,
        g.GRA_DESCRIPCION, un.UNI_DESCRIPCION, tus.TUS_DESCRIPCION
    FROM USUARIO u
    LEFT JOIN FUNCIONARIO f    ON u.FUN_CODIGO  = f.FUN_CODIGO
    LEFT JOIN GRADO g          ON f.GRA_CODIGO  = g.GRA_CODIGO AND f.ESC_CODIGO = g.ESC_CODIGO
    LEFT JOIN UNIDAD un        ON u.UNI_CODIGO  = un.UNI_CODIGO
    LEFT JOIN TIPO_USUARIO tus ON u.TUS_CODIGO  = tus.TUS_CODIGO
    WHERE u.US_LOGIN = '$codFuncionario'
    LIMIT 1";
    $result = mysql_query($sql, $link);
    if (!$result || mysql_num_rows($result) == 0) return null;
    return mysql_fetch_assoc($result);
}

function actualizarPasswordUsuario($codFuncionario, $nuevaPassword)
{
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $passwordHash   = md5($nuevaPassword);
    $sql = "UPDATE USUARIO 
        SET US_PASSWORD = '$passwordHash', US_FECHAMODIFICACION = NOW()
        WHERE US_LOGIN = '$codFuncionario'";
    $result = mysql_query($sql, $link);
    if (!$result) {
        return array('success' => false, 'message' => 'Error al actualizar contraseña: ' . mysql_error($link));
    }
    return array('success' => true, 'message' => 'Contraseña actualizada exitosamente.');
}

function desactivarUsuario($codFuncionario, $usuarioModificador)
{
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $sql = "UPDATE USUARIO 
        SET US_ACTIVO = 0, US_FECHAMODIFICACION = NOW()
        WHERE US_LOGIN = '$codFuncionario'";
    $result = mysql_query($sql, $link);
    if (!$result) {
        return array('success' => false, 'message' => 'Error al desactivar usuario: ' . mysql_error($link));
    }
    registrarAuditoria($link, $usuarioModificador, 'DESACTIVAR_USUARIO',
        'Usuario ' . $codFuncionario . ' desactivado');
    return array('success' => true, 'message' => 'Usuario desactivado exitosamente.');
}
?>