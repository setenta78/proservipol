<?php
require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!$link) {
    die("Error al conectar a la base de datos.");
} else {
    //echo "todo bien conectado";
}
// Verificar archivo
$archivotmp = $_FILES['dataCamara']['tmp_name'];
$tipo       = $_FILES['dataCamara']['type'];
$tamanio    = $_FILES['dataCamara']['size'];

if (empty($archivotmp) || $tipo !== 'text/csv') {
    die("No se ha subido ning&uacute;n archivo v&aacute;lido");
} else {
    //echo "buenardo";
}
// Inicialización de variables
$updateData = array();
$insertData = array();
$mvcMap = array();
$modvcMap = array();
$contadorInsertCamaras = 0;
$contadorUpdateCamaras = 0;
$contadorInsertMarcas = 0;
$contadorInsertModelos = 0;
$contadorNoInsertados = 0;
$errorMessages = array();
$filasOmitidas = array();
$contadorStatusOmitidos = 0;
$filasStatusOmitidas = array();
// Cargar datos existentes
function cargarDatosExistentes($link, &$mvcMap, &$modvcMap)
{
    $resultMarca = mysql_query("SELECT MVC_CODIGO, MVC_DESCRIPCION FROM MARCA_VIDEOCAMARA", $link);
    while ($row = mysql_fetch_assoc($resultMarca)) {
        $mvcMap[$row['MVC_DESCRIPCION']] = $row['MVC_CODIGO'];
    }
    $resultModelo = mysql_query("SELECT MODVC_CODIGO, MODVC_DESCRIPCION FROM MODELO_VIDEOCAMARA", $link);
    while ($row = mysql_fetch_assoc($resultModelo)) {
        $modvcMap[$row['MODVC_DESCRIPCION']] = $row['MODVC_CODIGO'];
    }
}
function insertarMarcaModelo($link, $descripcion, &$map, $tabla, $campoCodigo, $campoDescripcion)
{
    $nuevoCodigo = !empty($map) ? max(array_values($map)) + 10 : 10;
    $sqlInsert = sprintf(
        "INSERT INTO %s (%s, %s) VALUES ('%d', '%s')",
        $tabla,
        $campoCodigo,
        $campoDescripcion,
        $nuevoCodigo,
        mysql_real_escape_string($descripcion)
    );
    if (mysql_query($sqlInsert, $link)) {
        $map[$descripcion] = $nuevoCodigo;
        return $nuevoCodigo;
    } else {
        global $errorMessages;
        $errorMessages[] = "Error al insertar en $tabla: " . mysql_error($link);
        return false;
    }
}
function insertarModeloConMarca($link, $descripcionModelo, $marcaCod, &$modvcMap)
{
    $nuevoCodigo = max(array_values($modvcMap)) + 10;
    $sqlInsert = sprintf(
        "INSERT INTO MODELO_VIDEOCAMARA (MODVC_CODIGO, MODVC_DESCRIPCION, MVC_CODIGO) VALUES ('%d', '%s', '%d')",
        $nuevoCodigo,
        mysql_real_escape_string($descripcionModelo),
        $marcaCod
    );
    if (mysql_query($sqlInsert, $link)) {
        $modvcMap[$descripcionModelo] = $nuevoCodigo;
        return $nuevoCodigo;
    } else {
        global $errorMessages;
        $errorMessages[] = "Error al insertar el modelo: " . mysql_error($link);
        return false;
    }
}
// Cargar datos de marca y modelo
cargarDatosExistentes($link, $mvcMap, $modvcMap);
// Procesar el archivo CSV
if (($handle = fopen($archivotmp, "r")) === FALSE) {
    die("Error al abrir el archivo CSV.");
}
fgetcsv($handle, 1000, ";"); // Saltar cabecera
while (($datos = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $procedencia = ($datos[0] == "C" || $datos[0] == "J") ? 10 : (($datos[0] == "N") ? 70 : (($datos[0] == "S") ? 20 : (($datos[0] == "E") ? 80 : 80)));
    $codEquipoSap = isset($datos[1]) ? $datos[1] : '';
    $codActivoSap = isset($datos[2]) ? $datos[2] : 'NULL';
    $nroSerie = isset($datos[3]) ? $datos[3] : '';
    $marca = isset($datos[7]) ? $datos[7] : '';
    $modelo = isset($datos[8]) ? $datos[8] : '';
    $status = isset($datos[13]) ? $datos[13] : '';
    if ($status !== "ENCA" && $status !== "OPER") {
        $contadorStatusOmitidos++;
        $filasStatusOmitidas[] = array($marca, $modelo, $procedencia, $codActivoSap, $codEquipoSap, $nroSerie, $status);
        continue;
    }
    if (!isset($mvcMap[$marca])) {
        $marcaCod = insertarMarcaModelo($link, $marca, $mvcMap, 'MARCA_VIDEOCAMARA', 'MVC_CODIGO', 'MVC_DESCRIPCION');
        $contadorInsertMarcas++;
    } else {
        $marcaCod = $mvcMap[$marca];
    }
    if (!isset($modvcMap[$modelo])) {
        $modeloCod = insertarModeloConMarca($link, $modelo, $marcaCod, $modvcMap);
        $contadorInsertModelos++;
    } else {
        $modeloCod = $modvcMap[$modelo];
    }
    $sqlDuplicidad = sprintf("SELECT VC_NRO_SERIE FROM VIDEOCAMARA WHERE VC_NRO_SERIE = '%s'", mysql_real_escape_string($nroSerie));
    $resultDuplicidad = mysql_query($sqlDuplicidad, $link);
    if (mysql_num_rows($resultDuplicidad) == 0) { //VALIDA SI NO EXISTEN DUPLICADOS
        if (!empty($nroSerie)) { // SOLO INSERTA SI EL NÚMERO DE SERIE NO ESTÁ VACÍO
            $insertData[] = "('$marcaCod', '$modeloCod', '$procedencia', '$codActivoSap', '$codEquipoSap', '$nroSerie')";
            $contadorInsertCamaras++;
        } else {
            $contadorNoInsertados++;
            $filasOmitidas[] = array($marca, $modelo, $procedencia, $codActivoSap, $codEquipoSap, $nroSerie);
        }
    } else {
        $updateData[] = array(
            'marca' => $marcaCod,
            'modelo' => $modeloCod,
            'procedencia' => $procedencia,
            'codActivoSap' => $codActivoSap,
            'nroSerie' => $nroSerie,
            'codEquipoSap' => $codEquipoSap
        );
        $contadorUpdateCamaras++;
    }
}
fclose($handle);
if (!empty($insertData)) { // SI INSERT TRAE ALGO 
    $insertQuery = "INSERT INTO VIDEOCAMARA (MVC_CODIGO, MODVC_CODIGO, PREC_CODIGO, VC_COD_ACTIVO_SAP, VC_COD_EQUIPO_SAP, VC_NRO_SERIE) VALUES " . implode(", ", $insertData);
    if (!mysql_query($insertQuery, $link)) {
        $errorMessages[] = "Error en la inserción de cámaras: " . mysql_error($link);
    }
}
foreach ($updateData as $data) {
    // Escapa cada valor antes de pasarlo a sprintf
    $marca = mysql_real_escape_string($data['marca']);
    $modelo = mysql_real_escape_string($data['modelo']);
    $procedencia = mysql_real_escape_string($data['procedencia']);
    $codActivoSap = mysql_real_escape_string($data['codActivoSap']);
    $nroSerie = mysql_real_escape_string($data['nroSerie']);
    $codEquipoSap = mysql_real_escape_string($data['codEquipoSap']);
    // Construye la consulta segura
    $updateQuery = sprintf(
        "UPDATE VIDEOCAMARA 
        SET MVC_CODIGO = %d, MODVC_CODIGO = %d, PREC_CODIGO = %d, VC_COD_ACTIVO_SAP = '%s', VC_COD_EQUIPO_SAP = '%s' 
        WHERE VC_NRO_SERIE = '%s'",
        $marca,
        $modelo,
        $procedencia,
        $codActivoSap,
        $codEquipoSap,
        $nroSerie
    );
    if (!mysql_query($updateQuery, $link)) {
        $errorMessage = "Error en la actualización del equipo SAP '{$data['codEquipoSap']}': " . mysql_error($link);
        $errorMessages[] = $errorMessage;
    }
}
// Mensaje de confirmación final
echo "<strong>OPERACI&Oacute;N COMPLETADA CORRECTAMENTE.</strong><br>";
echo "<strong>Resumen de la operaci&oacute;n:</strong><br>";
echo "C&aacute;maras insertadas: $contadorInsertCamaras<br>";
echo "C&aacute;maras actualizadas: $contadorUpdateCamaras<br>";
echo "Marcas insertadas: $contadorInsertMarcas<br>";
echo "Modelos insertados: $contadorInsertModelos<br>";
echo "Filas omitidas por serie vac&iacute;a: $contadorNoInsertados<br>";
echo "Filas omitidas por status no v&aacute;lido: $contadorStatusOmitidos<br>";
if (!empty($errorMessages)) {
    echo "Errores encontrados:<br>";
    foreach ($errorMessages as $error) {
        echo $error . "<br>";
    }
} else {
    echo "Errores encontrados: No se encontraron errores durante la operaci&oacute;n.<br>";
}
// Mostrar tabla con filas omitidas por número de serie vacío
if ($contadorNoInsertados > 0) {
    echo "<br><strong>Detalles de filas omitidas por Nro. de Serie vacio:</strong><br>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Procedencia</th>
            <th>Cod. Activo SAP</th>
            <th>Cod. Equipo SAP</th>
            <th>Nro. Serie</th>
          </tr>";

    foreach ($filasOmitidas as $fila) {
        echo "<tr>
                    <td>{$fila[0]}</td>
                    <td>{$fila[1]}</td>
                    <td>{$fila[2]}</td>
                    <td>{$fila[3]}</td>
                    <td>{$fila[4]}</td>
                    <td>{$fila[5]}</td>
                  </tr>";
    }

    echo "</table>";
}