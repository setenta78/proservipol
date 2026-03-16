<?php
/**
 * buscarFuncionarioPersonal/index.php
 * Busca un funcionario en la BD de Personal (PERSONAL_MOCK en dev, personal_view en prod)
 * y verifica si ya está registrado como usuario en PROSERVIPOL.
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */

// Evitar cualquier salida antes de headers
ob_start();

// Compatibilidad JSON para PHP < 5.2
if (!function_exists('json_encode')) {
    require_once('../../lib/Services_JSON.php');
    function json_encode($data) {
        $json = new Services_JSON();
        return $json->encode($data);
    }
}

session_start();
include_once("../../inc/config.inc.php");
include_once("../../inc/configPersonal.inc.php");

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

// Verificar sesión
if (!isset($_SESSION['FUN_CODIGO'])) {
    echo json_encode(array("success" => false, "message" => "Sesión no iniciada"));
    exit;
}

// Validar parámetro
$funCodigo = isset($_GET['fun_codigo']) ? strtoupper(trim($_GET['fun_codigo'])) : '';
if (empty($funCodigo)) {
    echo json_encode(array("success" => false, "message" => "Código de funcionario requerido"));
    exit;
}

// ─── Conexión a proservipol_test (siempre necesaria) ───────────────────────
$connPros = mysql_connect(HOST, USER, PASS);
if (!$connPros) {
    echo json_encode(array("success" => false, "message" => "Error al conectar con base de datos principal"));
    exit;
}
if (!mysql_select_db(DB, $connPros)) {
    echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos principal"));
    exit;
}
mysql_query("SET NAMES 'utf8'", $connPros);

// ─── Determinar si usamos PERSONAL_MOCK o base de datos de personal real ───
$useMock = false;
$checkMock = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $connPros);
if ($checkMock && mysql_num_rows($checkMock) > 0) {
    $useMock = true;
}

// ─── Buscar en Personal ────────────────────────────────────────────────────
$datosFuncionario = null;

if ($useMock) {
    // Usar PERSONAL_MOCK en la misma conexión proservipol_test
    $funCodigoEsc = mysql_real_escape_string($funCodigo, $connPros);
    $sqlPersonal = "SELECT
                        PEFBCOD,
                        PEFBRUT,
                        PEFBNOM1,
                        PEFBNOM2,
                        PEFBAPEP,
                        PEFBAPEM,
                        PEFBESC,
                        PEFBGRA,
                        ESC_CODIGO,
                        GRA_CODIGO,
                        GRADO_DESCRIPCION,
                        REPARTICION_DESC,
                        REPARTICION_DEP,
                        ALTA_REPARTICION,
                        PEFBACT
                    FROM PERSONAL_MOCK
                    WHERE PEFBCOD = '{$funCodigoEsc}'";

    $resPersonal = mysql_query($sqlPersonal, $connPros);

    if ($resPersonal && mysql_num_rows($resPersonal) > 0) {
        $row = mysql_fetch_array($resPersonal);
        $datosFuncionario = array(
            "funCodigo"       => $row["PEFBCOD"],
            "funRut"          => $row["PEFBRUT"],
            "funNombre"       => utf8_encode($row["PEFBNOM1"]),
            "funNombre2"      => utf8_encode($row["PEFBNOM2"]),
            "funApellidoP"    => utf8_encode($row["PEFBAPEP"]),
            "funApellidoM"    => utf8_encode($row["PEFBAPEM"]),
            "escCodigo"       => $row["ESC_CODIGO"],
            "graCodigo"       => $row["GRA_CODIGO"],
            "graDescripcion"  => utf8_encode($row["GRADO_DESCRIPCION"]),
            "reparticionDesc" => utf8_encode($row["REPARTICION_DESC"]),
            "reparticionDep"  => utf8_encode($row["REPARTICION_DEP"]),
            "altaReparticion" => utf8_encode($row["ALTA_REPARTICION"]),
            "activo"          => (int)$row["PEFBACT"]
        );
    }
} else {
    // Conexión a base de datos de personal real
    $connPersonal = mysql_connect(HOST_PERSONAL, USER_PERSONAL, PASS_PERSONAL);
    if (!$connPersonal) {
        echo json_encode(array("success" => false, "message" => "Error al conectar con base de datos de personal"));
        exit;
    }
    if (!mysql_select_db(DB_PERSONAL, $connPersonal)) {
        echo json_encode(array("success" => false, "message" => "Error al seleccionar base de datos de personal"));
        exit;
    }
    mysql_query("SET NAMES 'utf8'", $connPersonal);

    $funCodigoEsc = mysql_real_escape_string($funCodigo, $connPersonal);
    $sqlPersonal = "SELECT * FROM personal_view WHERE PEFBCOD = '{$funCodigoEsc}'";
    $resPersonal = mysql_query($sqlPersonal, $connPersonal);

    if ($resPersonal && mysql_num_rows($resPersonal) > 0) {
        $row = mysql_fetch_array($resPersonal);
        $datosFuncionario = array(
            "funCodigo"       => $row["PEFBCOD"],
            "funRut"          => $row["PEFBRUT"],
            "funNombre"       => utf8_encode($row["PEFBNOM1"]),
            "funNombre2"      => utf8_encode($row["PEFBNOM2"]),
            "funApellidoP"    => utf8_encode($row["PEFBAPEP"]),
            "funApellidoM"    => utf8_encode($row["PEFBAPEM"]),
            "escCodigo"       => $row["ESC_CODIGO"],
            "graCodigo"       => $row["GRA_CODIGO"],
            "graDescripcion"  => utf8_encode($row["GRADO_DESCRIPCION"]),
            "reparticionDesc" => utf8_encode($row["REPARTICION_DESC"]),
            "reparticionDep"  => utf8_encode($row["REPARTICION_DEP"]),
            "altaReparticion" => utf8_encode($row["ALTA_REPARTICION"]),
            "activo"          => (int)$row["PEFBACT"]
        );
    }
    mysql_close($connPersonal);
}

// Funcionario no encontrado en personal
if (!$datosFuncionario) {
    mysql_close($connPros);
    echo json_encode(array(
        "success"  => false,
        "message"  => "Funcionario no encontrado en base de datos de personal"
    ));
    exit;
}

// ─── Verificar si ya está registrado como usuario en PROSERVIPOL ───────────
$funCodigoEsc = mysql_real_escape_string($funCodigo, $connPros);
$sqlUsuario = "SELECT FUN_CODIGO, US_LOGIN, US_ACTIVO, TUS_CODIGO
               FROM USUARIO
               WHERE FUN_CODIGO = '{$funCodigoEsc}'";
$resUsuario = mysql_query($sqlUsuario, $connPros);
$yaRegistrado = false;
$datosUsuario = null;

if ($resUsuario && mysql_num_rows($resUsuario) > 0) {
    $rowU = mysql_fetch_array($resUsuario);
    $yaRegistrado = true;
    $datosUsuario = array(
        "usLogin"   => $rowU["US_LOGIN"],
        "usActivo"  => (int)$rowU["US_ACTIVO"],
        "tusCodigo" => (int)$rowU["TUS_CODIGO"]
    );
}

// ─── Verificar capacitación (informativo) ─────────────────────────────────
$tieneCapacitacion = false;
$datosCapacitacion = null;

$sqlCap = "SELECT TIPO_CAPACITACION, VERSION_PROSERVIPOL, NOTA_PROSERVIPOL, FECHA_VALIDEZ
           FROM CAPACITACION
           WHERE FUN_CODIGO = '{$funCodigoEsc}'
           AND ACTIVO = 1
           ORDER BY FECHA_VALIDEZ DESC
           LIMIT 1";
$resCap = mysql_query($sqlCap, $connPros);

if ($resCap && mysql_num_rows($resCap) > 0) {
    $rowC = mysql_fetch_array($resCap);
    $tieneCapacitacion = true;
    $datosCapacitacion = array(
        "tipoCap"    => utf8_encode($rowC["TIPO_CAPACITACION"]),
        "version"    => $rowC["VERSION_PROSERVIPOL"],
        "nota"       => $rowC["NOTA_PROSERVIPOL"],
        "fechaValidez" => $rowC["FECHA_VALIDEZ"]
    );
}

mysql_close($connPros);

// ─── Respuesta final ───────────────────────────────────────────────────────
echo json_encode(array(
    "success"           => true,
    "yaRegistrado"      => $yaRegistrado,
    "datosUsuario"      => $datosUsuario,
    "datosFuncionario"  => $datosFuncionario,
    "tieneCapacitacion" => $tieneCapacitacion,
    "datosCapacitacion" => $datosCapacitacion
));
exit;
?>