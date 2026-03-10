<?php
// Archivo: debug_usuario.php
session_start();
require_once 'queries/config.php';

$codigo = '010944V';

echo "<h3>DIAGNÓSTICO PARA USUARIO: $codigo</h3>";

// 1. Verificar en FUNCIONARIO
$sql1 = "SELECT * FROM FUNCIONARIO WHERE FUN_CODIGO = '$codigo'";
$res1 = mysql_query($sql1, $link);
echo "<h4>1. Existe en FUNCIONARIO:</h4>";
if ($res1 && mysql_num_rows($res1) > 0) {
    echo "✅ SÍ - " . mysql_num_rows($res1) . " registro(s)<br>";
    $row = mysql_fetch_assoc($res1);
    echo "<​pre>" . print_r($row, true) . "<​/pre>";
} else {
    echo "❌ NO<br>";
}

// 2. Verificar en USUARIO
$sql2 = "SELECT * FROM USUARIO WHERE FUN_CODIGO = '$codigo'";
$res2 = mysql_query($sql2, $link);
echo "<h4>2. Existe en USUARIO:</h4>";
if ($res2 && mysql_num_rows($res2) > 0) {
    echo "✅ SÍ - " . mysql_num_rows($res2) . " registro(s)<br>";
    $row = mysql_fetch_assoc($res2);
    echo "<​pre>" . print_r($row, true) . "<​/pre>";
} else {
    echo "❌ NO<br>";
}

// 3. Verificar en CARGO_FUNCIONARIO
$sql3 = "SELECT * FROM CARGO_FUNCIONARIO WHERE FUN_CODIGO = '$codigo' AND FECHA_HASTA IS NULL";
$res3 = mysql_query($sql3, $link);
echo "<h4>3. Existe en CARGO_FUNCIONARIO (activo):</h4>";
if ($res3 && mysql_num_rows($res3) > 0) {
    echo "✅ SÍ - " . mysql_num_rows($res3) . " registro(s)<br>";
    $row = mysql_fetch_assoc($res3);
    echo "<​pre>" . print_r($row, true) . "<​/pre>";
} else {
    echo "❌ NO<br>";
}

// 4. Ejecutar la consulta completa de obtenerFuncionario
echo "<h4>4. Consulta completa de obtenerFuncionario():</h4>";
$sql = "SELECT 
    FUNCIONARIO.FUN_CODIGO,
    FUNCIONARIO.FUN_RUT,
    FUNCIONARIO.FUN_NOMBRE,
    IFNULL(FUNCIONARIO.FUN_NOMBRE2, '') AS FUN_NOMBRE2,
    FUNCIONARIO.FUN_APELLIDOPATERNO,
    FUNCIONARIO.FUN_APELLIDOMATERNO,
    GRADO.GRA_DESCRIPCION,
    IF(CARGO_FUNCIONARIO.UNI_AGREGADO, CONCAT(CARGO.CAR_DESCRIPCION, ' A ', UNIDAD.UNI_DESCRIPCION), CARGO.CAR_DESCRIPCION) AS CARGO,
    IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.ZONA_DESCRIPCION, '') AS ZONA_DESCRIPCION,
    IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.PREFECTURA_DESCRIPCION, '') AS PREFECTURA_DESCRIPCION,
    IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.COMISARIA_DESCRIPCION, '') AS COMISARIA_DESCRIPCION,
    IFNULL(VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION, '') AS UNI_DESCRIPCION,
    IFNULL(TIPO_USUARIO.TUS_DESCRIPCION, '') AS TUS_DESCRIPCION,
    IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
    CAPACITACION.NOTA_PROSERVIPOL,
    CAPACITACION.FECHA_CAPACITACION,
    IFNULL(USUARIO.US_LOGIN, '') AS US_LOGIN,
    IFNULL(USUARIO.US_PASSWORD, '') AS US_PASSWORD,
    IFNULL(USUARIO.UNI_CODIGO, 0) AS UNI_CODIGO,
    IFNULL(USUARIO.TUS_CODIGO, 0) AS TUS_CODIGO,
    CASE WHEN USUARIO.FUN_CODIGO IS NULL THEN 0 ELSE 1 END AS TIENE_USUARIO
    FROM CARGO_FUNCIONARIO
    JOIN FUNCIONARIO ON CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
    JOIN GRADO ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO 
        AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
    JOIN CARGO ON CARGO.CAR_CODIGO = CARGO_FUNCIONARIO.CAR_CODIGO 
        AND CARGO.CAR_CODIGO != 3500
    LEFT JOIN UNIDAD ON UNIDAD.UNI_CODIGO = CARGO_FUNCIONARIO.UNI_AGREGADO
    LEFT JOIN USUARIO ON USUARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO
    LEFT JOIN TIPO_USUARIO ON TIPO_USUARIO.TUS_CODIGO = USUARIO.TUS_CODIGO
    LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
        AND CAPACITACION.ACTIVO = 1
    LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
        ON USUARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
    WHERE CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
        AND FUNCIONARIO.FUN_CODIGO = '$codigo'";

echo "<pre>SQL:\n" . $sql . "<​/pre>";

$res = mysql_query($sql, $link);
if (!$res) {
    echo "❌ ERROR EN LA CONSULTA: " . mysql_error($link) . "<​br>";
} else {
    $num_rows = mysql_num_rows($res);
    echo "Resultados: $num_rows registro(s)<br>";
    if ($num_rows > 0) {
        echo "✅ DATOS ENCONTRADOS:<br>";
        $row = mysql_fetch_assoc($res);
        echo "<​pre>" . print_r($row, true) . "<​/pre>";
    } else {
        echo "❌ NO SE ENCONTRARON DATOS<br>";
    }
}

mysql_close($link);
?>