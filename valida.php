<?php
session_start();
require_once 'middleware_auth.php';
include("./inc/configV4.inc.php");
include("./baseDatos/Conexion.class.php");
require("./baseDatos/dbUsuarios.class.php");
$userName = $_POST['textUsuario'];
$clave = 'dummy';
$aplicacion = 10;
$ip = $_SERVER['REMOTE_ADDR'];
$fecha_hra_inicio = date("Y/m/d H:i:s");
$objDBUsuarios = new dbUsuarios;
$usuario = new usuario();
$usuario->setUserName($userName);
$usuario->setClave($clave);
$objDBUsuarios->validaUsuario($userName, $usuario);
if (is_object($usuario)) {
    $userNameDB = $usuario->getUserName();
    if ($userNameDB != $_POST['textUsuario']) {
        error_log("Usuario no coincide: Esperado " . $_POST['textUsuario'] . ", Obtenido " . $userNameDB);
        header("Location: index.php?ctrl=1");
        exit;
    }
    // Compatible con PHP 5.1.2
    $token = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : '';
    $codigoFuncionario = isset($_SESSION['USUARIO_CODIGOFUNCIONARIO']) ? $_SESSION['USUARIO_CODIGOFUNCIONARIO'] : '';
    if (!$token || !$codigoFuncionario) {
        session_destroy();
        header("Location: login.php?error=1");
        exit;
    }
    // Instanciar conexión (ajustar según implementación real de Conexion.class.php)
    $con = new Conexion();
    $link = $con->Conecta(); // o Conexion::Conecta(), según corresponda
    if (!$link) {
        error_log("Error al conectar a la base de datos");
        session_destroy();
        header("Location: login.php?error=2");
        exit;
    }
    // Escapar entrada
    $codigoEsc = mysql_real_escape_string($codigoFuncionario, $link);
    $sql = "SELECT US_ACTIVO, TUS_CODIGO FROM USUARIO WHERE FUN_CODIGO = '$codigoEsc'";
    $result = mysql_query($sql, $link);
    if (!$result) {
        error_log("Error en consulta SQL: " . mysql_error($link));
        session_destroy();
        header("Location: login.php?error=2");
        exit;
    }
    $row = mysql_fetch_array($result);
    if (!$row || $row['US_ACTIVO'] != 1 || !in_array($row['TUS_CODIGO'], array(90, 310))) {
        session_destroy();
        header("Location: login.php?error=3");
        exit;
    }
    // Ya debería estar definido por middleware_auth.php, pero se reafirma
    $_SESSION['USUARIO_CODIGOPERFIL'] = $row['TUS_CODIGO'];
    header("Location: gestor_usuarios.php");
    exit;
} else {
    // Evitar JS en redirecciones backend cuando es posible
    header("Location: login.php?error=4");
    exit;
}
?>