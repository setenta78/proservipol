<?php
function rules($data)
{
    $message = array();
    
    // Validar ID Usuario (obligatorio para editar)
    if (!isset($data['idUsuario']) || empty($data['idUsuario'])) {
        $message["idUsuario"] = "El ID de Usuario es obligatorio";
    }
    
    // Validar Código de Funcionario
    if (!isset($data['codFuncionario']) || empty($data['codFuncionario'])) {
        $message["codFuncionario"] = "El Código de Funcionario es obligatorio";
    }
    
    // Validar Perfil
    if (!isset($data['idPerfil']) || empty($data['idPerfil'])) {
        $message["idPerfil"] = "El Perfil es obligatorio";
    }
    
    // Validar Unidad
    if (!isset($data['uniCodigo'])) {
        $message["uniCodigo"] = "La Unidad es obligatoria";
    }
    
    // Validar password (opcional, solo formato si se proporciona)
    if (isset($data['password']) && !empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            $message["password"] = "La contraseña debe tener al menos 6 caracteres";
        }
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
        "codFuncionario" => mysql_real_escape_string($data['codFuncionario']),
        "idPerfil" => intval($data['idPerfil']),
        "uniCodigo" => intval($data['uniCodigo']),
        "password" => isset($data['password']) ? $data['password'] : '',
        "code" => "200"
    );
}
?>