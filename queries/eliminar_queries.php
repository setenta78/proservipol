<?php
/**
 * Eliminación de usuarios PROSERVIPOL con integración atómica a AutentificaTIC API
 * Compatible con PHP 5.1.2 y MySQL 5.0.77
 */

// No incluir config.php aquí directamente si se llama desde index.php que ya lo hizo
// Pero para seguridad de ejecución directa, verificamos $link
if (!isset($link)) {
    require_once "config.php";
}

if (!class_exists('AutentificaTicAPI')) {
    require_once(dirname(__FILE__) . '/../api/db/dbAutentificaTic.Class.php');
}

/**
 * Elimina un usuario (desactiva en BD y elimina de AutentificaTIC API)
 * 
 * @param string $codigo Código del funcionario a eliminar
 * @return array Resultado de la operación
 */
function eliminarUsuario($codigo)
{
    global $link;
    
    // Validación: solo verificar que no esté vacío
    $codigo = trim($codigo);
    if (empty($codigo)) {
        return array(
            'success' => false,
            'message' => 'Código de funcionario inválido',
            'code' => 400
        );
    }
    
    // Escapar datos
    $codigo = mysql_real_escape_string($codigo, $link);
    
    // ========================================
    // PASO 1: Verificar que el usuario existe y obtener su RUT
    // ========================================
    $sqlCheck = "SELECT 
        u.FUN_CODIGO,
        u.US_ACTIVO,
        f.FUN_RUT,
        f.FUN_NOMBRE,
        f.FUN_APELLIDOPATERNO,
        f.FUN_APELLIDOMATERNO
    FROM USUARIO u
    LEFT JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
    WHERE u.FUN_CODIGO = '$codigo'
    LIMIT 1";
    
    $resultCheck = mysql_query($sqlCheck, $link);
    
    if (!$resultCheck) {
        return array(
            'success' => false,
            'message' => 'Error al verificar usuario: ' . mysql_error($link),
            'code' => 500
        );
    }
    
    if (mysql_num_rows($resultCheck) == 0) {
        return array(
            'success' => false,
            'message' => 'No existe un usuario registrado con el código de funcionario indicado',
            'code' => 404
        );
    }
    
    $usuario = mysql_fetch_assoc($resultCheck);
    
    // Verificar si ya está eliminado
    if ($usuario['US_ACTIVO'] == '0') {
        return array(
            'success' => false,
            'message' => 'El usuario ya se encuentra eliminado',
            'code' => 409
        );
    }
    
    $rut = $usuario['FUN_RUT'];
    $nombreCompleto = trim($usuario['FUN_NOMBRE'] . ' ' . 
                          $usuario['FUN_APELLIDOPATERNO'] . ' ' . 
                          $usuario['FUN_APELLIDOMATERNO']);
    
    // ========================================
    // PASO 2: Iniciar transacción atómica
    // ========================================
    mysql_query("START TRANSACTION", $link);
    
    // ========================================
    // PASO 3: Desactivar usuario en BD (soft delete)
    // ========================================
    $sqlUpdate = "UPDATE USUARIO 
                  SET US_ACTIVO = '0',
                      US_FECHAMODIFICACION = NOW()
                  WHERE FUN_CODIGO = '$codigo'";
    
    $resultUpdate = mysql_query($sqlUpdate, $link);
    
    if (!$resultUpdate) {
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'Error al desactivar usuario en BD: ' . mysql_error($link),
            'code' => 500
        );
    }
    
    if (mysql_affected_rows($link) == 0) {
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'No se pudo desactivar el usuario',
            'code' => 500
        );
    }
    
    // ========================================
    // PASO 4: Llamar a AutentificaTIC API para eliminar usuario
    // ========================================
    if (empty($rut)) {
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'No se encontró el RUT del funcionario para eliminar de AutentificaTIC',
            'code' => 500
        );
    }
    
    // Obtener token de sesión del usuario que elimina
    if (!isset($_SESSION)) {
        @session_start();
    }
    
    $accessToken = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;
    
    if (!$accessToken) {
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'En este momento existe un problema de conexión con Autentificatic. Por favor intente nuevamente.',
            'code' => 500,
            'detail' => 'Token de sesión no disponible'
        );
    }
    
    try {
        $api = new AutentificaTicAPI($accessToken);
        $resultApi = $api->eliminarUsuario($rut);
        
        if (!$resultApi['success']) {
            $http_code = isset($resultApi['http_code']) ? $resultApi['http_code'] : 0;
            
            // Si el usuario no existe en Autentificatic (404), igual confirmar en BD
            if ($http_code == 404 || 
                strpos(strtolower($resultApi['message']), 'no encontrado') !== false || 
                strpos(strtolower($resultApi['message']), 'not found') !== false) {
                
                mysql_query("COMMIT", $link);
                registrarAuditoriaEliminar($link, $codigo, $nombreCompleto);
                return array(
                    'success' => true,
                    'message' => 'Usuario eliminado de PROSERVIPOL. (No estaba registrado en AutentificaTIC).',
                    'code' => 200,
                    'data' => array(
                        'codFuncionario' => $codigo,
                        'nombre' => $nombreCompleto,
                        'rut' => $rut
                    )
                );
            }
            
            // Otro error de API — hacer rollback
            mysql_query("ROLLBACK", $link);
            
            $mensajeError = 'En este momento existe un problema de conexión con Autentificatic. Por favor intente nuevamente.';
            if ($http_code == 401 || $http_code == 403) {
                $mensajeError = 'Error de autenticación con Autentificatic. Por favor inicie sesión nuevamente.';
            } elseif ($http_code >= 500) {
                $mensajeError = 'La plataforma Autentificatic presenta problemas temporales. Intente más tarde.';
            }
            
            return array(
                'success' => false,
                'message' => $mensajeError,
                'code' => 500,
                'api_error' => $resultApi['message'],
                'api_http_code' => $http_code
            );
        }
        
    } catch (Exception $e) {
        mysql_query("ROLLBACK", $link);
        return array(
            'success' => false,
            'message' => 'En este momento existe un problema de conexión con Autentificatic. Por favor intente nuevamente.',
            'code' => 500,
            'detail' => 'Excepción: ' . $e->getMessage()
        );
    }
    
    // ========================================
    // PASO 5: Confirmar transacción
    // ========================================
    mysql_query("COMMIT", $link);
    
    registrarAuditoriaEliminar($link, $codigo, $nombreCompleto);
    
    return array(
        'success' => true,
        'message' => 'Usuario eliminado exitosamente. Se ha desactivado en PROSERVIPOL y eliminado de AutentificaTIC.',
        'code' => 200,
        'data' => array(
            'codFuncionario' => $codigo,
            'nombre' => $nombreCompleto,
            'rut' => $rut
        )
    );
}

/**
 * Registra la eliminación en la tabla de auditoría
 */
function registrarAuditoriaEliminar($link, $codigo, $nombre)
{
    $usuarioEliminador = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO']) ? $_SESSION['USUARIO_CODIGOFUNCIONARIO'] : 0);
    
    $codigo = mysql_real_escape_string($codigo, $link);
    $nombre = mysql_real_escape_string($nombre, $link);
    $usuarioEliminador = mysql_real_escape_string($usuarioEliminador, $link);
    
    // Verificar si existe la tabla BITACORA_AUDITORIA o usar BITACORA_USUARIO si no
    $checkTable = mysql_query("SHOW TABLES LIKE 'BITACORA_AUDITORIA'", $link);
    
    if (mysql_num_rows($checkTable) > 0) {
        $sqlAudit = "INSERT INTO BITACORA_AUDITORIA (
            ACCION,
            DESCRIPCION,
            USUARIO_ID,
            FECHA_ACCION
        ) VALUES (
            'ELIMINAR_USUARIO',
            'Usuario $nombre ($codigo) eliminado del sistema',
            '$usuarioEliminador',
            NOW()
        )";
        mysql_query($sqlAudit, $link);
    } else {
        // Fallback a log de error si no hay tabla específica
        error_log("AUDITORIA: Usuario $nombre ($codigo) eliminado por $usuarioEliminador");
    }
}
?>