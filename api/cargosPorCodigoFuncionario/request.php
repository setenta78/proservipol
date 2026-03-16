<?php
/**
 * request.php — Validación para cargosPorCodigoFuncionario
 * Compatible con PHP 5.1.2
 */
function rules()
{
    $codigoFuncionario = isset($_GET['codigoFuncionario']) ? strtoupper(trim($_GET['codigoFuncionario'])) : '';
    $fechaDesde        = isset($_GET['fechaDesde'])        ? trim($_GET['fechaDesde'])        : '';
    $fechaHasta        = isset($_GET['fechaHasta'])        ? trim($_GET['fechaHasta'])        : '';
    $message           = array();

    if (empty($codigoFuncionario)) {
        $message["codigoFuncionario"] = "El código de funcionario es obligatorio";
    }
    if (empty($fechaDesde)) {
        $message["fechaDesde"] = "La fecha desde es obligatoria (YYYY-MM-DD)";
    }
    if (empty($fechaHasta)) {
        $message["fechaHasta"] = "La fecha hasta es obligatoria (YYYY-MM-DD)";
    }

    if (count($message) > 0) {
        return array("success" => false, "error" => $message, "code" => "412");
    }

    return array(
        "codigoFuncionario" => $codigoFuncionario,
        "fechaDesde"        => $fechaDesde,
        "fechaHasta"        => $fechaHasta,
        "code"              => "200"
    );
}
?>