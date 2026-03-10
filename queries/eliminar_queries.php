<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
    echo eliminarUsuario($codigo);
}

function eliminarUsuario($codigo)
{
    global $link;
    // Validación estricta: que no estén vacíos pero sí acepten 0 como válido
    if ($codigo === '') {
        return "Faltan datos";
    }
    // Escapar datos usando la conexión activa
    $codigo = mysql_real_escape_string($codigo, $link);
    // Construir SQL
    $sql = "UPDATE USUARIO SET US_ACTIVO = '0' WHERE FUN_CODIGO = '$codigo'";
    $res = mysql_query($sql, $link);
    if (!$res) {
        return "Error al actualizar: " . mysql_error($link);
    }
    // Verificar si realmente se actualizó algo
    if (mysql_affected_rows($link) > 0) {
        return "Usuario eliminado correctamente";
    } else {
        return "No se eliminó un usuario";
    }
}
?>