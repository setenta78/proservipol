<?php
/**
 * Middleware de Autenticación
 * Verifica que el usuario tenga una sesión válida y perfil autorizado
 */

// Tiempo máximo de inactividad (15 minutos)
$max_inactivity = 15 * 60; // 15 minutos en segundos

// Verificar si existe sesión activa
if (!isset($_SESSION['USUARIO_CODIGOFUNCIONARIO'])) {
    // No hay sesión - redirigir al login
    header("Location: login.php");
    exit;
}

// Verificar tiempo de inactividad
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($inactive_time > $max_inactivity) {
        // Sesión expirada por inactividad
        session_unset();
        session_destroy();
        header("Location: login.php?error=session_expired");
        exit;
    }
}

// Actualizar tiempo de última actividad
$_SESSION['LAST_ACTIVITY'] = time();

// Establecer user_id para compatibilidad con endpoints antiguos
$_SESSION['user_id'] = $_SESSION['USUARIO_CODIGOFUNCIONARIO'];

// Verificar perfil autorizado (solo perfiles 90 y 310)
$perfiles_autorizados = array(90, 310);

if (!isset($_SESSION['USUARIO_CODIGOPERFIL']) || !in_array((int)$_SESSION['USUARIO_CODIGOPERFIL'], $perfiles_autorizados)) {
    // Perfil no autorizado - registrar en log para debug
    error_log("[" . date('Y-m-d H:i:s') . "] Acceso denegado - Perfil: " . (isset($_SESSION['USUARIO_CODIGOPERFIL']) ? $_SESSION['USUARIO_CODIGOPERFIL'] : 'NO DEFINIDO'));
    error_log("[" . date('Y-m-d H:i:s') . "] Código Funcionario: " . (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO']) ? $_SESSION['USUARIO_CODIGOFUNCIONARIO'] : 'NO DEFINIDO'));
    
    session_unset();
    session_destroy();
    header("Location: login.php?error=unauthorized_profile");
    exit;
}

// Obtener nombre del usuario para uso en las páginas
$nombreUsuario = "Usuario";
if (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO'])) {
    require_once "queries/config.php";
    
    if ($link) {
        $codigo = mysql_real_escape_string($_SESSION['USUARIO_CODIGOFUNCIONARIO'], $link);
        $sql = "SELECT CONCAT(FUN_NOMBRE, ' ', FUN_APELLIDOPATERNO) AS NOMBRE_COMPLETO 
                FROM FUNCIONARIO 
                WHERE FUN_CODIGO = '$codigo'";
        $result = mysql_query($sql, $link);
        
        if ($result && $row = mysql_fetch_array($result)) {
            $nombreUsuario = htmlspecialchars($row['NOMBRE_COMPLETO'], ENT_QUOTES, 'UTF-8');
        }
        
        // NO cerrar la conexión - puede ser necesaria en las páginas
    }
}
?>