<?php
/**
 * Funciones de consulta para gestión de usuarios PROSERVIPOL
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

// Asegurar que la clase de API esté disponible
if (!class_exists('AutentificaTicAPI')) {
    include_once(dirname(__FILE__) . '/../api/db/dbAutentificaTic.Class.php');
}

/**
 * Crea un nuevo usuario en PROSERVIPOL
 * Transacción atómica: BD + AutentificaTIC API
 */
function crearUsuarioProservipol($codFuncionario, $codigoUnidad, $tipoUsuario, $password, $usuarioCreador)
{
    global $link;

    // Escapar valores
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $codigoUnidad   = intval($codigoUnidad);
    $tipoUsuario    = intval($tipoUsuario);
    $password       = mysql_real_escape_string($password, $link);
    $usuarioCreador = mysql_real_escape_string($usuarioCreador, $link);

    // ================================================
    // PASO 1: Verificar si el usuario ya existe
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
            'message' => 'El usuario ' . $nombreCompleto . ' (' . $codFuncionario . ') ya existe y está activo.',
            'code'    => 409
        );
    }

    // ================================================
    // PASO 2: Validar sesión activa del administrador
    // ================================================
    if (!isset($_SESSION)) {
        @session_start();
    }
    
    $sessionToken = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;

    if (empty($sessionToken)) {
        return array(
            'success' => false,
            'message' => 'Error de sesión: No hay token de autenticación. Inicie sesión nuevamente.',
            'code'    => 401
        );
    }

    // NOTA: Usamos el token de sesión del admin logueado directamente.
    // NO se requiere token de servicio ni constantes externas.
    $api = new AutentificaTicAPI($sessionToken);

    // ================================================
    // CASO 2: Usuario inactivo — REACTIVAR
    // ================================================
    if ($usuarioExistente && $usuarioExistente['US_ACTIVO'] == '0') {
        
        // Obtener RUT limpio
        $sqlRut = "SELECT FUN_RUT FROM FUNCIONARIO 
                   WHERE FUN_CODIGO = '$codFuncionario' LIMIT 1";
        $resultRut = mysql_query($sqlRut, $link);
        $rut = '';
        if ($resultRut && mysql_num_rows($resultRut) > 0) {
            $funcData = mysql_fetch_assoc($resultRut);
            // Limpiar RUT: solo números y K
            $rut = strtoupper(trim($funcData['FUN_RUT']));
            $rut = str_replace('.', '', $rut);
            $rut = str_replace('-', '', $rut);
        }

        if (empty($rut)) {
            return array(
                'success' => false,
                'message' => 'No se pudo obtener el RUT del funcionario.',
                'code'    => 500
            );
        }

        // 1. Llamar a AutentificaTIC PRIMERO (Registro/Reactivación)
        $resultApi = $api->registrarUsuario($rut);

        if (!$resultApi['success']) {
            return array(
                'success'   => false,
                'message'   => _mensajeErrorApi($resultApi),
                'code'      => 500,
                'api_error' => $resultApi['message']
            );
        }

        // 2. Si API OK, actualizar BD con transacción
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
            // Opcional: intentar revertir en API si falla BD
            return array(
                'success' => false,
                'message' => 'Error al reactivar usuario en BD: ' . mysql_error($link),
                'code'    => 500
            );
        }

        mysql_query("COMMIT", $link);
        registrarAuditoria($link, $usuarioCreador, 'REACTIVAR_USUARIO', 'Usuario reactivado');

        return array(
            'success' => true,
            'message' => 'Usuario reactivado exitosamente.',
            'code'    => 200
        );
    }

    // ================================================
    // CASO 3: Usuario NUEVO — CREACIÓN COMPLETA
    // ================================================

    // PASO 3: Buscar datos en PERSONAL_MOCK o personal_view
    $checkMock = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $link);
    $useMock   = (mysql_num_rows($checkMock) > 0);
    $resultPersonal = null;

    if ($useMock) {
        $sqlPersonal = "SELECT 
            PEFBCOD, PEFBRUT, PEFBNOM1, PEFBNOM2,
            PEFBAPEP, PEFBAPEM, PEFBGRA
        FROM PERSONAL_MOCK
        WHERE PEFBCOD = '$codFuncionario'
        LIMIT 1";
        $resultPersonal = mysql_query($sqlPersonal, $link);
    } else {
        // Producción: Conectar a BD Personal
        if (!file_exists(dirname(__FILE__) . '/../inc/configPersonal.inc.php')) {
            return array('success' => false, 'message' => 'Configuración Personal no encontrada.', 'code' => 500);
        }
        require_once(dirname(__FILE__) . '/../inc/configPersonal.inc.php');
        
        $connPersonal = mysql_connect(HOST_PERSONAL, DB_USER_PERSONAL, DB_PASS_PERSONAL, true);
        if (!$connPersonal) {
            return array('success' => false, 'message' => 'Error conexión BD Personal.', 'code' => 500);
        }
        if (!mysql_select_db(DB_PERSONAL, $connPersonal)) {
            mysql_close($connPersonal);
            return array('success' => false, 'message' => 'Error selecting BD Personal.', 'code' => 500);
        }
        mysql_query("SET NAMES 'utf8'", $connPersonal);

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
            'message' => 'Funcionario no encontrado en base de datos de Personal.',
            'code'    => 404
        );
    }

    $funcionario = mysql_fetch_assoc($resultPersonal);
    if (!$useMock && isset($connPersonal)) mysql_close($connPersonal);

    // Limpiar RUT
    $rut = strtoupper(trim($funcionario['PEFBRUT']));
    $rut = str_replace('.', '', $rut);
    $rut = str_replace('-', '', $rut);

    if (empty($rut)) {
        return array('success' => false, 'message' => 'RUT inválido en datos del funcionario.', 'code' => 500);
    }

    // 1. Llamar a AutentificaTIC PRIMERO (Registro)
    $resultApi = $api->registrarUsuario($rut);

    if (!$resultApi['success']) {
        return array(
            'success'   => false,
            'message'   => _mensajeErrorApi($resultApi),
            'code'      => 500,
            'api_error' => $resultApi['message']
        );
    }

    // 2. Si API OK, insertar en BD con transacción
    mysql_query("START TRANSACTION", $link);

    // Insertar en FUNCIONARIO si no existe
    $sqlCheckFun = "SELECT FUN_CODIGO FROM FUNCIONARIO WHERE FUN_CODIGO = '$codFuncionario' LIMIT 1";
    $resultCheckFun = mysql_query($sqlCheckFun, $link);
    
    if (mysql_num_rows($resultCheckFun) == 0) {
        $rutEsc    = mysql_real_escape_string($funcionario['PEFBRUT'], $link);
        $nombre1   = mysql_real_escape_string($funcionario['PEFBNOM1'], $link);
        $nombre2   = mysql_real_escape_string($funcionario['PEFBNOM2'], $link);
        $apellidoP = mysql_real_escape_string($funcionario['PEFBAPEP'], $link);
        $apellidoM = mysql_real_escape_string($funcionario['PEFBAPEM'], $link);
        $gradoCod  = intval($funcionario['PEFBGRA']);

        // Obtener ESC_CODIGO
        $sqlGrado = "SELECT ESC_CODIGO FROM GRADO WHERE GRA_CODIGO = $gradoCod LIMIT 1";
        $resGrado = mysql_query($sqlGrado, $link);
        $escCod = 1;
        if ($resGrado && mysql_num_rows($resGrado) > 0) {
            $rowG = mysql_fetch_assoc($resGrado);
            $escCod = intval($rowG['ESC_CODIGO']);
        }

        $sqlInsFun = "INSERT INTO FUNCIONARIO (
            FUN_CODIGO, FUN_RUT, FUN_NOMBRE, FUN_NOMBRE2,
            FUN_APELLIDOPATERNO, FUN_APELLIDOMATERNO, GRA_CODIGO, ESC_CODIGO
        ) VALUES (
            '$codFuncionario', '$rutEsc', '$nombre1', '$nombre2',
            '$apellidoP', '$apellidoM', $gradoCod, $escCod
        )";

        if (!mysql_query($sqlInsFun, $link)) {
            mysql_query("ROLLBACK", $link);
            return array('success' => false, 'message' => 'Error al insertar funcionario: ' . mysql_error($link), 'code' => 500);
        }
    }

    // Insertar en USUARIO
    $passwordHash = md5($password);
    $sqlInsUsu = "INSERT INTO USUARIO (
        FUN_CODIGO, UNI_CODIGO, US_LOGIN, US_PASSWORD, TUS_CODIGO, US_FECHACREACION, US_ACTIVO
    ) VALUES (
        '$codFuncionario', $codigoUnidad, '$codFuncionario', '$passwordHash', $tipoUsuario, NOW(), 1
    )";

    if (!mysql_query($sqlInsUsu, $link)) {
        mysql_query("ROLLBACK", $link);
        return array('success' => false, 'message' => 'Error al crear usuario: ' . mysql_error($link), 'code' => 500);
    }

    mysql_query("COMMIT", $link);
    registrarAuditoria($link, $usuarioCreador, 'CREAR_USUARIO', 'Usuario creado exitosamente');

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
 * Auxiliar: Mensajes de error de API
 */
function _mensajeErrorApi($resultApi)
{
    $msg = strtolower($resultApi['message']);
    
    if (strpos($msg, 'no encontrado') !== false || strpos($msg, 'not found') !== false) {
        return 'El funcionario no existe en registros de AutentificaTIC.';
    }
    if (strpos($msg, 'plataforma') !== false) {
        return 'La plataforma no está registrada en AutentificaTIC para esta operación.';
    }
    if ($resultApi['http_code'] == 400) {
        return 'Datos inválidos: ' . $resultApi['message'];
    }
    if ($resultApi['http_code'] == 401) {
        return 'Sesión expirada o inválida. Reinicie sesión.';
    }
    return 'Error de conexión con AutentificaTIC. Intente nuevamente.';
}

/**
 * Auxiliar: Auditoría
 */
function registrarAuditoria($link, $usuario, $accion, $descripcion)
{
    $usuario     = mysql_real_escape_string($usuario, $link);
    $accion      = mysql_real_escape_string($accion, $link);
    $descripcion = mysql_real_escape_string($descripcion, $link);
    
    $check = mysql_query("SHOW TABLES LIKE 'AUDITORIA'", $link);
    if (mysql_num_rows($check) == 0) return true;

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    
    $sql = "INSERT INTO AUDITORIA (AUD_USUARIO, AUD_ACCION, AUD_DESCRIPCION, AUD_FECHA, AUD_IP) 
            VALUES ('$usuario', '$accion', '$descripcion', NOW(), '" . mysql_real_escape_string($ip, $link) . "')";
    
    return mysql_query($sql, $link);
}

// ... (Resto de funciones auxiliares sin cambios: obtenerUsuarioPorCodigo, actualizarPasswordUsuario, desactivarUsuario)

function obtenerUsuarioPorCodigo($codFuncionario) {
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $sql = "SELECT u.*, f.FUN_RUT, f.FUN_NOMBRE, f.FUN_NOMBRE2, f.FUN_APELLIDOPATERNO, f.FUN_APELLIDOMATERNO, g.GRA_DESCRIPCION, un.UNI_DESCRIPCION, tus.TUS_DESCRIPCION
            FROM USUARIO u
            LEFT JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
            LEFT JOIN GRADO g ON f.GRA_CODIGO = g.GRA_CODIGO AND f.ESC_CODIGO = g.ESC_CODIGO
            LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
            LEFT JOIN TIPO_USUARIO tus ON u.TUS_CODIGO = tus.TUS_CODIGO
            WHERE u.US_LOGIN = '$codFuncionario' LIMIT 1";
    $res = mysql_query($sql, $link);
    if (!$res || mysql_num_rows($res) == 0) return null;
    return mysql_fetch_assoc($res);
}

function actualizarPasswordUsuario($codFuncionario, $nuevaPassword) {
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $hash = md5($nuevaPassword);
    $sql = "UPDATE USUARIO SET US_PASSWORD = '$hash', US_FECHAMODIFICACION = NOW() WHERE US_LOGIN = '$codFuncionario'";
    if (!mysql_query($sql, $link)) return array('success' => false, 'message' => mysql_error($link));
    return array('success' => true, 'message' => 'Contraseña actualizada.');
}

function desactivarUsuario($codFuncionario, $usuarioModificador) {
    global $link;
    $codFuncionario = mysql_real_escape_string($codFuncionario, $link);
    $sql = "UPDATE USUARIO SET US_ACTIVO = 0, US_FECHAMODIFICACION = NOW() WHERE US_LOGIN = '$codFuncionario'";
    if (!mysql_query($sql, $link)) return array('success' => false, 'message' => mysql_error($link));
    registrarAuditoria($link, $usuarioModificador, 'DESACTIVAR_USUARIO', 'Usuario desactivado');
    return array('success' => true, 'message' => 'Usuario desactivado.');
}
?>