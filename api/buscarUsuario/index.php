<?php
/**
 * buscarUsuario/index.php
 * Busca un usuario registrado en PROSERVIPOL por FUN_CODIGO o FUN_RUT.
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

// Aceptar búsqueda por fun_codigo o por rut
$funCodigo = isset($_GET['fun_codigo']) ? strtoupper(trim($_GET['fun_codigo'])) : '';
$funRut    = isset($_GET['fun_rut'])    ? trim($_GET['fun_rut'])    : '';

if (empty($funCodigo) && empty($funRut)) {
    echo json_encode(array("success" => false, "message" => "Debe indicar fun_codigo o fun_rut"));
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

// Construir condición WHERE
if (!empty($funCodigo)) {
    $funCodigoEsc = mysql_real_escape_string($funCodigo, $conn);
    $whereClause  = "u.FUN_CODIGO = '{$funCodigoEsc}'";
} else {
    $funRutEsc   = mysql_real_escape_string($funRut, $conn);
    $whereClause = "f.FUN_RUT = '{$funRutEsc}'";
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
            t.TUS_DESCRIPCION,
            t.VALIDAR,
            t.VALIDAR_OIC,
            t.REGISTRAR,
            t.CONSULTAR_UNIDAD,
            t.CONSULTAR_PERFIL
        FROM USUARIO u
        INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
        LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
        LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
        LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
        WHERE {$whereClause}";

$result = mysql_query($sql, $conn);

if (!$result) {
    $error = mysql_error($conn);
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Error en consulta: " . $error));
    exit;
}

if (mysql_num_rows($result) == 0) {
    mysql_close($conn);
    echo json_encode(array("success" => false, "message" => "Usuario no encontrado"));
    exit;
}

$row = mysql_fetch_array($result);

// Buscar capacitación (informativo)
$funCodigoEsc2 = mysql_real_escape_string($row["FUN_CODIGO"], $conn);
$sqlCap = "SELECT TIPO_CAPACITACION, VERSION_PROSERVIPOL, NOTA_PROSERVIPOL, FECHA_VALIDEZ
           FROM CAPACITACION
           WHERE FUN_CODIGO = '{$funCodigoEsc2}'
           AND ACTIVO = 1
           ORDER BY FECHA_VALIDEZ DESC
           LIMIT 1";
$resCap = mysql_query($sqlCap, $conn);
$tieneCapacitacion = false;
$datosCapacitacion = null;

if ($resCap && mysql_num_rows($resCap) > 0) {
    $rowC = mysql_fetch_array($resCap);
    $tieneCapacitacion = true;
    $datosCapacitacion = array(
        "tipoCap"     => utf8_encode($rowC["TIPO_CAPACITACION"]),
        "version"     => $rowC["VERSION_PROSERVIPOL"],
        "nota"        => $rowC["NOTA_PROSERVIPOL"],
        "fechaValidez"=> $rowC["FECHA_VALIDEZ"]
    );
}

mysql_close($conn);

echo json_encode(array(
    "success" => true,
    "data"    => array(
        "funCodigo"           => $row["FUN_CODIGO"],
        "uniCodigo"           => (int)$row["UNI_CODIGO"],
        "uniDescripcion"      => utf8_encode($row["UNI_DESCRIPCION"]),
        "usLogin"             => $row["US_LOGIN"],
        "tusCodigo"           => (int)$row["TUS_CODIGO"],
        "tusDescripcion"      => utf8_encode($row["TUS_DESCRIPCION"]),
        "usFechaCreacion"     => $row["US_FECHACREACION"],
        "usFechaModificacion" => $row["US_FECHAMODIFICACION"],
        "usActivo"            => (int)$row["US_ACTIVO"],
        "funRut"              => $row["FUN_RUT"],
        "funNombre"           => utf8_encode($row["FUN_NOMBRE"]),
        "funNombre2"          => utf8_encode($row["FUN_NOMBRE2"]),
        "funApellidoPaterno"  => utf8_encode($row["FUN_APELLIDOPATERNO"]),
        "funApellidoMaterno"  => utf8_encode($row["FUN_APELLIDOMATERNO"]),
        "graDescripcion"      => utf8_encode($row["GRA_DESCRIPCION"]),
        "nombreCompleto"      => utf8_encode(
            trim($row["FUN_NOMBRE"] . " " . $row["FUN_NOMBRE2"]) .
            " " . $row["FUN_APELLIDOPATERNO"] .
            " " . $row["FUN_APELLIDOMATERNO"]
        ),
        "permisos"            => array(
            "validar"         => (int)$row["VALIDAR"],
            "validarOic"      => (int)$row["VALIDAR_OIC"],
            "registrar"       => (int)$row["REGISTRAR"],
            "consultarUnidad" => (int)$row["CONSULTAR_UNIDAD"],
            "consultarPerfil" => (int)$row["CONSULTAR_PERFIL"]
        ),
        "tieneCapacitacion"   => $tieneCapacitacion,
        "datosCapacitacion"   => $datosCapacitacion
    )
));
exit;
?>