<?php
function rules($data)
{
    $message = array();
    // Validar ID Usuario (obligatorio para editar)
    if (!isset($data['idUsuario']) || empty($data['idUsuario'])) {
        $message["idUsuario"] = "El ID de Usuario es obligatorio";
    }
    // Validar Email
    if (!isset($data['email']) || empty(trim($data['email']))) {
        $message["email"] = "El Email es obligatorio";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $message["email"] = "El Email no tiene un formato válido";
    }
    // Validar Perfil
    if (!isset($data['idPerfil']) || empty($data['idPerfil'])) {
        $message["idPerfil"] = "El Perfil es obligatorio";
    }
    // Validar Estado
    if (!isset($data['estado'])) {
        $message["estado"] = "El Estado es obligatorio";
    }
    // Si hay errores, retornar código 412
    if (count($message) > 0) {
        return array(
            "success" => false,
            "error" => $message,
            "code" => "412"
        );
    }
    // Si todo está correcto, retornar los datos validados y sanitizados
    return array(
        "idUsuario" => intval($data['idUsuario']),
        "email" => strtolower(trim($data['email'])),
        "idPerfil" => intval($data['idPerfil']),
        "estado" => intval($data['estado']),
        "code" => "200"
    );
}
?>