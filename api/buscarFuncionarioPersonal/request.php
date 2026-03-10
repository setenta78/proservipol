<?php

function rules()
{
    // Validar que el parámetro existe y no está vacío
    $codFuncionario = isset($_GET['codFuncionario']) ? strtoupper(trim($_GET['codFuncionario'])) : '';

    $message = array();
    
    // Validación del código de funcionario
    if (empty($codFuncionario)) {
        $message["codFuncionario"] = "Se requiere que indique el Código del Funcionario";
    } elseif (strlen($codFuncionario) < 3) {
        $message["codFuncionario"] = "El Código del Funcionario debe tener al menos 3 caracteres";
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
        "codFuncionario" => $codFuncionario,
        "code" => "200"
    );
}
?>