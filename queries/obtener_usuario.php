<?php
// Este archivo obtiene los datos del usuario logueado
$datosUsuario = array(
    'nombreCompleto' => 'Usuario',
    'rol' => 'Sin rol'
);
if (isset($_SESSION['USUARIO_CODIGOFUNCIONARIO'])) {
    $codigo = $_SESSION['USUARIO_CODIGOFUNCIONARIO'];
    $perfil = $_SESSION['USUARIO_CODIGOPERFIL'] ?? 0;
    // Conectar a la base de datos
    require_once '../queries/config.php';
    // Obtener nombre completo
    $sql = "SELECT CONCAT(FUN_NOMBRE, ' ', FUN_APELLIDOPATERNO) AS NOMBRE_COMPLETO 
            FROM FUNCIONARIO 
            WHERE FUN_CODIGO = '$codigo'";
    $result = mysql_query($sql, $link);
    if ($row = mysql_fetch_array($result)) {
        $datosUsuario['nombreCompleto'] = htmlspecialchars($row['NOMBRE_COMPLETO'], ENT_QUOTES, 'UTF-8');
    }
    // Determinar rol
    if ($perfil == 310) {
        $datosUsuario['rol'] = 'Administrador';
    } elseif ($perfil == 90) {
        $datosUsuario['rol'] = 'Mesa de Ayuda';
    }
}
?>