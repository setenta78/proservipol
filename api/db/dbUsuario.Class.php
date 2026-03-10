<?php
include_once("../../inc/config.inc.php");
include_once("conexion.Class.php");
class dbUsuario extends Conexion
{
    /**
     * Busca un usuario por RUT
     */
    function buscarUsuario($params)
    {
        $conn = $this->conect();
        $rut = mysql_real_escape_string($params['rut'], $conn);
        $sql = "SELECT 
                    u.id_usuario,
                    u.rut,
                    u.cod_funcionario,
                    u.primer_nombre,
                    u.segundo_nombre,
                    u.apellido_paterno,
                    u.apellido_materno,
                    u.email,
                    u.id_perfil,
                    p.nombre_perfil,
                    u.estado,
                    u.fecha_creacion,
                    u.usuario_creacion,
                    u.fecha_modificacion,
                    u.usuario_modificacion
                FROM usuarios u
                LEFT JOIN perfiles p ON u.id_perfil = p.id_perfil
                WHERE u.rut = '{$rut}'
                AND u.fecha_eliminacion IS NULL";
        $result = $this->execute($conn, $sql);
        // Leer datos ANTES de cerrar conexión
        $usuario = null;
        if ($myrow = mysql_fetch_array($result)) {
            $usuario = array(
                "idUsuario"         => $myrow["id_usuario"],
                "rut"               => $myrow["rut"],
                "codFuncionario"    => $myrow["cod_funcionario"],
                "primerNombre"      => utf8_encode($myrow["primer_nombre"]),
                "segundoNombre"     => utf8_encode($myrow["segundo_nombre"]),
                "apellidoPaterno"   => utf8_encode($myrow["apellido_paterno"]),
                "apellidoMaterno"   => utf8_encode($myrow["apellido_materno"]),
                "email"             => $myrow["email"],
                "idPerfil"          => $myrow["id_perfil"],
                "nombrePerfil"      => utf8_encode($myrow["nombre_perfil"]),
                "estado"            => $myrow["estado"],
                "fechaCreacion"     => $myrow["fecha_creacion"],
                "usuarioCreacion"   => $myrow["usuario_creacion"],
                "fechaModificacion" => $myrow["fecha_modificacion"],
                "usuarioModificacion" => $myrow["usuario_modificacion"]
            );
        }
        $this->desconect();
        return $usuario ? 
            array("success" => true, "data" => $usuario) : 
            array("success" => false, "data" => false, "message" => "Usuario no encontrado");
    }
    /**
     * Busca un usuario por código de funcionario
     */
    function buscarUsuarioPorCodigo($codFuncionario)
    {
        $conn = $this->conect();
        $codFuncionario = mysql_real_escape_string($codFuncionario, $conn);
        $sql = "SELECT 
                    u.id_usuario,
                    u.rut,
                    u.cod_funcionario,
                    u.primer_nombre,
                    u.segundo_nombre,
                    u.apellido_paterno,
                    u.apellido_materno,
                    u.email,
                    u.id_perfil,
                    p.nombre_perfil,
                    u.estado,
                    u.fecha_creacion,
                    u.usuario_creacion,
                    u.fecha_modificacion,
                    u.usuario_modificacion
                FROM usuarios u
                LEFT JOIN perfiles p ON u.id_perfil = p.id_perfil
                WHERE u.cod_funcionario = '{$codFuncionario}'
                AND u.fecha_eliminacion IS NULL";
        $result = $this->execute($conn, $sql);
        // Leer datos ANTES de cerrar conexión
        $usuario = null;
        if ($myrow = mysql_fetch_array($result)) {
            $usuario = array(
                "idUsuario"         => $myrow["id_usuario"],
                "rut"               => $myrow["rut"],
                "codFuncionario"    => $myrow["cod_funcionario"],
                "primerNombre"      => utf8_encode($myrow["primer_nombre"]),
                "segundoNombre"     => utf8_encode($myrow["segundo_nombre"]),
                "apellidoPaterno"   => utf8_encode($myrow["apellido_paterno"]),
                "apellidoMaterno"   => utf8_encode($myrow["apellido_materno"]),
                "email"             => $myrow["email"],
                "idPerfil"          => $myrow["id_perfil"],
                "nombrePerfil"      => utf8_encode($myrow["nombre_perfil"]),
                "estado"            => $myrow["estado"],
                "fechaCreacion"     => $myrow["fecha_creacion"],
                "usuarioCreacion"   => $myrow["usuario_creacion"],
                "fechaModificacion" => $myrow["fecha_modificacion"],
                "usuarioModificacion" => $myrow["usuario_modificacion"]
            );
        }
        $this->desconect();
        return $usuario ? 
            array("success" => true, "data" => $usuario) : 
            array("success" => false, "data" => false, "message" => "Usuario no encontrado");
    }
    /**
     * Crea un nuevo usuario
     */
    function crearUsuario($params)
    {
        $conn = $this->conect();
        // Escapar datos
        $rut = mysql_real_escape_string($params['rut'], $conn);
        $codFuncionario = mysql_real_escape_string($params['codFuncionario'], $conn);
        $primerNombre = mysql_real_escape_string($params['primerNombre'], $conn);
        $segundoNombre = mysql_real_escape_string($params['segundoNombre'], $conn);
        $apellidoPaterno = mysql_real_escape_string($params['apellidoPaterno'], $conn);
        $apellidoMaterno = mysql_real_escape_string($params['apellidoMaterno'], $conn);
        $email = mysql_real_escape_string($params['email'], $conn);
        $idPerfil = intval($params['idPerfil']);
        $estado = intval($params['estado']);
        $usuarioCreacion = intval($params['usuarioCreacion']);
        
        // Verificar si el usuario ya existe
        $sqlCheck = "SELECT id_usuario FROM usuarios 
                     WHERE (rut = '{$rut}' OR cod_funcionario = '{$codFuncionario}' OR email = '{$email}')
                     AND fecha_eliminacion IS NULL";
        
        $resultCheck = $this->execute($conn, $sqlCheck);
        if (mysql_num_rows($resultCheck) > 0) {
            $this->desconect();
            return array(
                "success" => false,
                "message" => "Ya existe un usuario con ese RUT, Código de Funcionario o Email"
            );
        }
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (
                    rut,
                    cod_funcionario,
                    primer_nombre,
                    segundo_nombre,
                    apellido_paterno,
                    apellido_materno,
                    email,
                    id_perfil,
                    estado,
                    fecha_creacion,
                    usuario_creacion
                ) VALUES (
                    '{$rut}',
                    '{$codFuncionario}',
                    '{$primerNombre}',
                    '{$segundoNombre}',
                    '{$apellidoPaterno}',
                    '{$apellidoMaterno}',
                    '{$email}',
                    {$idPerfil},
                    {$estado},
                    NOW(),
                    {$usuarioCreacion}
                )";
        $result = $this->execute($conn, $sql);
        if ($result) {
            $idUsuario = mysql_insert_id($conn);
            $this->desconect();
            return array(
                "success" => true,
                "message" => "Usuario creado exitosamente",
                "data" => array("idUsuario" => $idUsuario)
            );
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            
            return array(
                "success" => false,
                "message" => "Error al crear usuario: " . $error
            );
        }
    }
     /**
     * Edita un usuario existente
     */
    function editarUsuario($params)
    {
        $conn = $this->conect();
        // Escapar datos
        $idUsuario = intval($params['idUsuario']);
        $email = mysql_real_escape_string($params['email'], $conn);
        $idPerfil = intval($params['idPerfil']);
        $estado = intval($params['estado']);
        $usuarioModificacion = intval($params['usuarioModificacion']);
        // Verificar si el usuario existe
        $sqlCheck = "SELECT id_usuario FROM usuarios 
                     WHERE id_usuario = {$idUsuario}
                     AND fecha_eliminacion IS NULL";
        
        $resultCheck = $this->execute($conn, $sqlCheck);
        
        if (mysql_num_rows($resultCheck) == 0) {
            $this->desconect();
            return array(
                "success" => false,
                "message" => "Usuario no encontrado"
            );
        }
        
        // Verificar si el email ya está en uso por otro usuario
        $sqlCheckEmail = "SELECT id_usuario FROM usuarios 
                          WHERE email = '{$email}'
                          AND id_usuario != {$idUsuario}
                          AND fecha_eliminacion IS NULL";
        
        $resultCheckEmail = $this->execute($conn, $sqlCheckEmail);
        
        if (mysql_num_rows($resultCheckEmail) > 0) {
            $this->desconect();
            return array(
                "success" => false,
                "message" => "El email ya está en uso por otro usuario"
            );
        }
        
        // Actualizar usuario
        $sql = "UPDATE usuarios SET
                    email = '{$email}',
                    id_perfil = {$idPerfil},
                    estado = {$estado},
                    fecha_modificacion = NOW(),
                    usuario_modificacion = {$usuarioModificacion}
                WHERE id_usuario = {$idUsuario}";
        
        $result = $this->execute($conn, $sql);
        
        if ($result) {
            $this->desconect();
            
            return array(
                "success" => true,
                "message" => "Usuario actualizado exitosamente"
            );
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            
            return array(
                "success" => false,
                "message" => "Error al actualizar usuario: " . $error
            );
        }
    }
    
    /**
     * Elimina un usuario (eliminación lógica)
     */
    function eliminarUsuario($params)
    {
        $conn = $this->conect();
        
        // Escapar datos
        $idUsuario = intval($params['idUsuario']);
        $usuarioEliminacion = intval($params['usuarioEliminacion']);
        
        // Verificar si el usuario existe
        $sqlCheck = "SELECT id_usuario FROM usuarios 
                     WHERE id_usuario = {$idUsuario}
                     AND fecha_eliminacion IS NULL";
        
        $resultCheck = $this->execute($conn, $sqlCheck);
        
        if (mysql_num_rows($resultCheck) == 0) {
            $this->desconect();
            return array(
                "success" => false,
                "message" => "Usuario no encontrado"
            );
        }
        
        // Eliminar usuario (lógicamente)
        $sql = "UPDATE usuarios SET
                    fecha_eliminacion = NOW(),
                    usuario_eliminacion = {$usuarioEliminacion}
                WHERE id_usuario = {$idUsuario}";
        
        $result = $this->execute($conn, $sql);
        
        if ($result) {
            $this->desconect();
            
            return array(
                "success" => true,
                "message" => "Usuario eliminado exitosamente"
            );
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            
            return array(
                "success" => false,
                "message" => "Error al eliminar usuario: " . $error
            );
        }
    }
    
    /**
     * Lista todos los usuarios activos
     */
    function listarUsuarios()
    {
        $conn = $this->conect();
        
        $sql = "SELECT 
                    u.id_usuario,
                    u.rut,
                    u.cod_funcionario,
                    u.primer_nombre,
                    u.segundo_nombre,
                    u.apellido_paterno,
                    u.apellido_materno,
                    u.email,
                    u.id_perfil,
                    p.nombre_perfil,
                    u.estado,
                    u.fecha_creacion
                FROM usuarios u
                LEFT JOIN perfiles p ON u.id_perfil = p.id_perfil
                WHERE u.fecha_eliminacion IS NULL
                ORDER BY u.apellido_paterno, u.apellido_materno, u.primer_nombre";
        
        $result = $this->execute($conn, $sql);
        
        // Leer datos ANTES de cerrar conexión
        $usuarios = array();
        
        while ($myrow = mysql_fetch_array($result)) {
            $usuarios[] = array(
                "idUsuario"         => $myrow["id_usuario"],
                "rut"               => $myrow["rut"],
                "codFuncionario"    => $myrow["cod_funcionario"],
                "primerNombre"      => utf8_encode($myrow["primer_nombre"]),
                "segundoNombre"     => utf8_encode($myrow["segundo_nombre"]),
                "apellidoPaterno"   => utf8_encode($myrow["apellido_paterno"]),
                "apellidoMaterno"   => utf8_encode($myrow["apellido_materno"]),
                "nombreCompleto"    => utf8_encode($myrow["primer_nombre"] . " " . $myrow["segundo_nombre"] . " " . $myrow["apellido_paterno"] . " " . $myrow["apellido_materno"]),
                "email"             => $myrow["email"],
                "idPerfil"          => $myrow["id_perfil"],
                "nombrePerfil"      => utf8_encode($myrow["nombre_perfil"]),
                "estado"            => $myrow["estado"],
                "fechaCreacion"     => $myrow["fecha_creacion"]
            );
        }
        
        $this->desconect();
        
        return array(
            "success" => true,
            "data" => $usuarios,
            "total" => count($usuarios)
        );
    }
}
?>