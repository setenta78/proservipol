<?php

function rules()
{
    // Validar que el parámetro existe y no está vacío
    $rut = isset($_GET['rut']) ? trim($_GET['rut']) : '';

    $message = array();
    
    // Validación del RUT
    if (empty($rut)) {
        $message["rut"] = "Se requiere que indique el RUT del Funcionario";
    } elseif (strlen($rut) < 7) {
        $message["rut"] = "El RUT debe tener al menos 7 caracteres";
    }

    // Si hay errores, retornar código 412
    if (count($message) > 0) {
        return array(
            "success" => false,
            "error" => $message,
            "code" => "412"
        );
    }

    // Si todo está correcto, retornar los datos validados
    return array(
        "rut" => $rut,
        "code" => "200"
    );
}
?>