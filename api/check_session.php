<?php
/**
 * check_session.php
 * Verifica el estado de la sesión activa.
 * Compatible con PHP 5.1.2
 */
ob_start();
include_once("../tools.php");
session_start();
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$max_inactivity = 15 * 60; // 15 minutos
$session_active = false;
$message        = '';
$userData       = null;

if (isset($_SESSION['FUN_CODIGO']) && isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];

    if ($inactive_time < $max_inactivity) {
        $_SESSION['LAST_ACTIVITY'] = time();
        $session_active = true;
        $message        = 'Sesión activa';
        $userData       = array(
            "funCodigo"      => $_SESSION['FUN_CODIGO'],
            "tusCodigo"      => isset($_SESSION['TUS_CODIGO'])      ? $_SESSION['TUS_CODIGO']      : null,
            "tusDescripcion" => isset($_SESSION['TUS_DESCRIPCION']) ? $_SESSION['TUS_DESCRIPCION'] : null,
            "uniCodigo"      => isset($_SESSION['UNI_CODIGO'])      ? $_SESSION['UNI_CODIGO']      : null,
            "permisos"       => array(
                "validar"         => isset($_SESSION['VALIDAR'])          ? (int)$_SESSION['VALIDAR']          : 0,
                "validarOic"      => isset($_SESSION['VALIDAR_OIC'])      ? (int)$_SESSION['VALIDAR_OIC']      : 0,
                "registrar"       => isset($_SESSION['REGISTRAR'])        ? (int)$_SESSION['REGISTRAR']        : 0,
                "consultarUnidad" => isset($_SESSION['CONSULTAR_UNIDAD']) ? (int)$_SESSION['CONSULTAR_UNIDAD'] : 0,
                "consultarPerfil" => isset($_SESSION['CONSULTAR_PERFIL']) ? (int)$_SESSION['CONSULTAR_PERFIL'] : 0
            )
        );
    } else {
        $message = 'Sesión expirada por inactividad';
        session_unset();
        session_destroy();
    }
} else {
    $message = 'No hay sesión activa';
}

echo json_encode(array(
    "active"    => $session_active,
    "message"   => $message,
    "timestamp" => time(),
    "userData"  => $userData
));
exit;
?>