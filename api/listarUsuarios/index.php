<?php
/**
 * listarUsuarios/index.php
 * Lista todos los usuarios registrados con datos completos.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */

ob_start();

if (!function_exists('json_encode')) {
    require_once('../../lib/Services_JSON.php');
    function json_encode($data) {
        $json = new Services_JSON();
        return $json->encode($data);
    }
}

session_start();
include_once("../../inc/config.inc.php");

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

// Verificar sesión
if (!isset($_SESSION['FUN_CODIGO'])) {
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada"));
    exit;
}

// Verificar permiso CONSULTAR_PERFIL
if (!isset($_SESSION['CONSULTAR_PERFIL']) || $_SESSION['CONSULTAR_PERFIL'] != 1) {
    echo json_encode(array("success" => false, "message" => "No tiene permisos para listar usuarios"));
    exit;
}

// Conexión
$conn = mysql_connect(HOST, USER, PASS);
if (!$conn) {
    echo json_encode(array("success" => false, "message" => "Error de conexión"));
    exit;
}
if (!mysql_select_db(DB, $conn)) {
    echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos"));
    exit;
}
mysql_query("SET NAMES 'utf8'", $conn);

// Filtros opcionales
$whereExtra = "1=1";

if (!empty($_GET['us_activo'])) {
    $activo = intval($_GET['us_activo']);
    $whereExtra .= " AND u.US_ACTIVO = {$activo}";
}

if (!empty($_GET['tus_codigo'])) {
    $tusCodigo = intval($_GET['tus_codigo']);
    $whereExtra .= " AND u.TUS_CODIGO = {$tusCodigo}";
}

if (!empty($_GET['uni_codigo'])) {
    $uniCodigo = intval($_GET['uni_codigo']);
    $whereExtra .= " AND u.UNI_CODIGO = {$uniCodigo}";
}

$sql = "SELECT
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
            f.ESC_CODIGO,
            f.GRA_CODIGO,
            g.GRA_DESCRIPCION,
            un.UNI_DESCRIPCION,
            t.TUS_DESCRIPCION
        FROM USUARIO u
        INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
        LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
        LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
        LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
        WHERE {$whereExtra}
        ORDER BY f.FUN_APELLIDOPATERNO, f.FUN_APELLIDOMATERNO, f.FUN_NOMBRE";

$result = mysql_query($sql, $conn);

if (!$result) {
    $error = mysql_error($conn);
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Error en consulta: " . $error));
    exit;
}

$usuarios = array();
while ($row = mysql_fetch_array($result)) {
    $usuarios[] = array(
        "funCodigo"          => $row["FUN_CODIGO"],
        "uniCodigo"          => (int)$row["UNI_CODIGO"],
        "uniDescripcion"     => utf8_encode($row["UNI_DESCRIPCION"]),
        "usLogin"            => $row["US_LOGIN"],
        "tusCodigo"          => (int)$row["TUS_CODIGO"],
        "tusDescripcion"     => utf8_encode($row["TUS_DESCRIPCION"]),
        "usFechaCreacion"    => $row["US_FECHACREACION"],
        "usFechaModificacion"=> $row["US_FECHAMODIFICACION"],
        "usActivo"           => (int)$row["US_ACTIVO"],
        "funRut"             => $row["FUN_RUT"],
        "funNombre"          => utf8_encode($row["FUN_NOMBRE"]),
        "funNombre2"         => utf8_encode($row["FUN_NOMBRE2"]),
        "funApellidoPaterno" => utf8_encode($row["FUN_APELLIDOPATERNO"]),
        "funApellidoMaterno" => utf8_encode($row["FUN_APELLIDOMATERNO"]),
        "graDescripcion"     => utf8_encode($row["GRA_DESCRIPCION"]),
        "nombreCompleto"     => utf8_encode(
            trim($row["FUN_NOMBRE"] . " " . $row["FUN_NOMBRE2"]) .
            " " . $row["FUN_APELLIDOPATERNO"] .
            " " . $row["FUN_APELLIDOMATERNO"]
        )
    );
}

mysql_close($conn);

echo json_encode(array(
    "success" => true,
    "data"    => $usuarios,
    "total"   => count($usuarios)
));
exit;
?>