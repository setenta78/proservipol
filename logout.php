<?php
/**
 * Cierre de Sesión
 * Destruye la sesión del usuario y redirige al login
 */
session_start();
// Obtener razón del cierre de sesión (si existe)
$reason = isset($_GET['reason']) ? $_GET['reason'] : 'manual';
// Destruir todas las variables de sesión
$_SESSION = array();
// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
// Destruir la sesión
session_destroy();
// Redirigir al login con mensaje apropiado
switch ($reason) {
    case 'inactivity':
        header("Location: login.php?message=session_expired_inactivity");
        break;
    case 'expired':
        header("Location: login.php?message=session_expired");
        break;
    default:
        header("Location: login.php?message=logout_success");
        break;
}
exit;
?>