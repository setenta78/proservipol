<?php
/**
 * request.php — Validación para buscarFuncionarioAprobado
 * Acepta fun_codigo (código de funcionario)
 * Compatible con PHP 5.1.2
 */
function rules()
{
    $funCodigo = isset($_GET['fun_codigo']) ? strtoupper(trim($_GET['fun_codigo'])) : '';
    $message   = array();

    if (empty($funCodigo)) {
        $message["fun_codigo"] = "Se requiere el código del funcionario";
    } elseif (strlen($funCodigo) < 6) {
        $message["fun_codigo"] = "El código de funcionario debe tener al menos 6 caracteres";
    }

    if (count($message) > 0) {
        return array("success" => false, "error" => $message, "code" => "412");
    }

    return array("funCodigo" => $funCodigo, "code" => "200");
}
?>