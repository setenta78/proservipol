<?php
/**
 * buscarFuncionarioAprobado/index.php
 * Verifica si un funcionario tiene capacitación PROSERVIPOL activa y aprobada.
 * Usa tabla CAPACITACION de proservipol_test.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
ob_start();
include_once("../tools.php");
session_start();
include_once("../../inc/config.inc.php");
include_once("request.php");
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['FUN_CODIGO'])) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada", "code" => 401));
    exit;
}

$params = rules();
if ($params["code"] == "412") {
    http_response_code(412);
    echo json_encode($params);
    exit;
}

$conn = mysql_connect(HOST, USER, PASS);
if (!$conn) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error de conexión", "code" => 500));
    exit;
}
if (!mysql_select_db(DB, $conn)) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos", "code" => 500));
    exit;
}
mysql_query("SET NAMES 'utf8'", $conn);

$funCodigo = mysql_real_escape_string($params['funCodigo'], $conn);

// Buscar capacitación activa más reciente
$sql = "SELECT
            c.FUN_CODIGO,
            c.TIPO_CAPACITACION,
            c.VERSION_PROSERVIPOL,
            c.NOTA_PROSERVIPOL,
            c.FECHA_CAPACITACION,
            c.FECHA_VALIDEZ,
            c.CODIGO_VERIFICACION,
            f.FUN_NOMBRE,
            f.FUN_NOMBRE2,
            f.FUN_APELLIDOPATERNO,
            f.FUN_APELLIDOMATERNO,
            f.FUN_RUT,
            g.GRA_DESCRIPCION
        FROM CAPACITACION c
        INNER JOIN FUNCIONARIO f ON c.FUN_CODIGO = f.FUN_CODIGO
        LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
        WHERE c.FUN_CODIGO = '{$funCodigo}'
        AND c.ACTIVO = 1
        ORDER BY c.FECHA_VALIDEZ DESC
        LIMIT 1";

$result = mysql_query($sql, $conn);

if (!$result) {
    $error = mysql_error($conn);
    mysql_close($conn);
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Error en consulta: " . $error));
    exit;
}

if (mysql_num_rows($result) == 0) {
    mysql_close($conn);
    http_response_code(404);
    echo json_encode(array(
        "success"          => false,
        "tieneCapacitacion" => false,
        "message"          => "El funcionario no tiene capacitación PROSERVIPOL activa",
        "data"             => false
    ));
    exit;
}

$row = mysql_fetch_array($result);
mysql_close($conn);

echo json_encode(array(
    "success"           => true,
    "tieneCapacitacion" => true,
    "data"              => array(
        "funCodigo"          => $row["FUN_CODIGO"],
        "funRut"             => $row["FUN_RUT"],
        "funNombre"          => utf8_encode($row["FUN_NOMBRE"]),
        "funNombre2"         => utf8_encode($row["FUN_NOMBRE2"]),
        "funApellidoPaterno" => utf8_encode($row["FUN_APELLIDOPATERNO"]),
        "funApellidoMaterno" => utf8_encode($row["FUN_APELLIDOMATERNO"]),
        "graDescripcion"     => utf8_encode($row["GRA_DESCRIPCION"]),
        "tipoCap"            => utf8_encode($row["TIPO_CAPACITACION"]),
        "version"            => $row["VERSION_PROSERVIPOL"],
        "nota"               => $row["NOTA_PROSERVIPOL"],
        "fechaCapacitacion"  => $row["FECHA_CAPACITACION"],
        "fechaValidez"       => $row["FECHA_VALIDEZ"],
        "codigoVerificacion" => $row["CODIGO_VERIFICACION"]
    )
));
exit;
?>