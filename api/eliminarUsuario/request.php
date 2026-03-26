<?php
function rules($data)
{
    $message = array();
    // Validar ID Usuario (obligatorio para eliminar)
    if (!isset($data['idUsuario']) || empty($data['idUsuario'])) {
        $message["idUsuario"] = "El ID de Usuario es obligatorio";
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
        "idUsuario" => intval($data['idUsuario']),
        "code" => "200"
    );
}
?>