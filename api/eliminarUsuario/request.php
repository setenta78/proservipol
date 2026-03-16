<?php
/**
 * request.php — Validación para eliminarUsuario
 * Compatible con PHP 5.1.2
 */
function rules($data)
{
    $message = array();

    if (!isset($data['fun_codigo']) || empty(trim($data['fun_codigo']))) {
        $message["fun_codigo"] = "El código de funcionario es obligatorio";
    }

    if (count($message) > 0) {
        return array("success" => false, "error" => $message, "code" => "412");
    }

    return array(
        "funCodigo" => strtoupper(trim($data['fun_codigo'])),
        "code"      => "200"
    );
}
?>