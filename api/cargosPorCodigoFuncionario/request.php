<?php
function rules()
{
    // Validar que los parámetros existen
    $codigoFuncionario = isset($_GET['codigoFuncionario']) ? trim($_GET['codigoFuncionario']) : '';
    $fechaDesde = isset($_GET['fechaDesde']) ? trim($_GET['fechaDesde']) : '';
    $fechaHasta = isset($_GET['fechaHasta']) ? trim($_GET['fechaHasta']) : '';
    $message = array();
     // Validación del código de funcionario
    if (empty($codigoFuncionario)) {
        $message["codigoFuncionario"] = "Se requiere que indique el Código del Funcionario";
    }
     // Validación de fechas (opcional según tu lógica de negocio)
    if (!empty($fechaDesde) && !empty($fechaHasta)) {
        // Validar formato de fecha si están presentes
        if (!preg_match('/\d{2}-\d{2}-\d{4}/', $fechaDesde)) {
            $message["fechaDesde"] = "La fecha Desde debe tener formato DD-MM-YYYY";
        }
         if (!preg_match('/\d{2}-\d{2}-\d{4}/', $fechaHasta)) {
            $message["fechaHasta"] = "La fecha Hasta debe tener formato DD-MM-YYYY";
        }
        // Si las fechas son válidas, verificar que fechaDesde <= fechaHasta
        if (empty($message)) {
            $fechasFormatDesde = explode('-', $fechaDesde);
            $fechaDesdeTimestamp = strtotime($fechasFormatDesde[2] . '-' . $fechasFormatDesde[1] . '-' . $fechasFormatDesde[0]);
            $fechasFormatHasta = explode('-', $fechaHasta);
            $fechaHastaTimestamp = strtotime($fechasFormatHasta[2] . '-' . $fechasFormatHasta[1] . '-' . $fechasFormatHasta[0]);
            if ($fechaDesdeTimestamp > $fechaHastaTimestamp) {
                $message["fecha"] = "La fecha Desde no puede ser mayor a la fecha Hasta";
            }
            // Convertir fechas al formato YYYYMMDD
            $fechaDesde = date('Ymd', $fechaDesdeTimestamp);
            $fechaHasta = date('Ymd', $fechaHastaTimestamp);
        }
    }
    // Si hay errores, retornar código 412
    if (count($message) > 0) {
        return array(
            "error" => $message,
            "code" => "412"
        );
    }
    // Si todo está correcto, retornar los datos validados
    return array(
        "codigoFuncionario" => $codigoFuncionario,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
        "code" => "200"
    );
}
?>