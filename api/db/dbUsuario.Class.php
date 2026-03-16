<?php
/**
 * dbUsuario.Class.php
 * Clase de acceso a datos para gestión de usuarios PROSERVIPOL
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 * Tablas reales: USUARIO, FUNCIONARIO, GRADO, UNIDAD, TIPO_USUARIO, CAPACITACION
 */
include_once("../../inc/config.inc.php");
include_once("conexion.Class.php");

class dbUsuario extends Conexion
{
    /**
     * Busca un usuario registrado por FUN_CODIGO
     */
    function buscarUsuarioPorCodigo($funCodigo)
    {
        $conn = $this->conect();
        $funCodigo = mysql_real_escape_string($funCodigo, $conn);

        $sql = "SELECT
                    u.FUN_CODIGO,
                    u.UNI_CODIGO,
                    u.US_LOGIN,
                    u.TUS_CODIGO,
                    u.US_FECHACREACION,
                    u.US_FECHAMODIFICACION,
                    u.US_ACTIVO,
                    f.FUN_RUT,
                    f.FUN_NOMBRE,
                    f.FUN_NOMBRE2,
                    f.FUN_APELLIDOPATERNO,
                    f.FUN_APELLIDOMATERNO,
                    f.ESC_CODIGO,
                    f.GRA_CODIGO,
                    g.GRA_DESCRIPCION,
                    un.UNI_DESCRIPCION,
                    t.TUS_DESCRIPCION,
                    t.VALIDAR,
                    t.VALIDAR_OIC,
                    t.REGISTRAR,
                    t.CONSULTAR_UNIDAD,
                    t.CONSULTAR_PERFIL
                FROM USUARIO u
                INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
                LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
                LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
                LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
                WHERE u.FUN_CODIGO = '{$funCodigo}'";

        $result = $this->execute($conn, $sql);
        $usuario = null;

        if ($myrow = mysql_fetch_array($result)) {
            $usuario = array(
                "funCodigo"           => $myrow["FUN_CODIGO"],
                "uniCodigo"           => $myrow["UNI_CODIGO"],
                "uniDescripcion"      => utf8_encode($myrow["UNI_DESCRIPCION"]),
                "usLogin"             => $myrow["US_LOGIN"],
                "tusCodigo"           => $myrow["TUS_CODIGO"],
                "tusDescripcion"      => utf8_encode($myrow["TUS_DESCRIPCION"]),
                "usFechaCreacion"     => $myrow["US_FECHACREACION"],
                "usFechaModificacion" => $myrow["US_FECHAMODIFICACION"],
                "usActivo"            => $myrow["US_ACTIVO"],
                "funRut"              => $myrow["FUN_RUT"],
                "funNombre"           => utf8_encode($myrow["FUN_NOMBRE"]),
                "funNombre2"          => utf8_encode($myrow["FUN_NOMBRE2"]),
                "funApellidoPaterno"  => utf8_encode($myrow["FUN_APELLIDOPATERNO"]),
                "funApellidoMaterno"  => utf8_encode($myrow["FUN_APELLIDOMATERNO"]),
                "escCodigo"           => $myrow["ESC_CODIGO"],
                "graCodigo"           => $myrow["GRA_CODIGO"],
                "graDescripcion"      => utf8_encode($myrow["GRA_DESCRIPCION"]),
                "permisos"            => array(
                    "validar"          => (int)$myrow["VALIDAR"],
                    "validarOic"       => (int)$myrow["VALIDAR_OIC"],
                    "registrar"        => (int)$myrow["REGISTRAR"],
                    "consultarUnidad"  => (int)$myrow["CONSULTAR_UNIDAD"],
                    "consultarPerfil"  => (int)$myrow["CONSULTAR_PERFIL"]
                )
            );
        }

        $this->desconect();

        return $usuario
            ? array("success" => true, "data" => $usuario)
            : array("success" => false, "data" => false, "message" => "Usuario no encontrado");
    }

    /**
     * Busca un usuario registrado por RUT (desde FUNCIONARIO)
     */
    function buscarUsuarioPorRut($rut)
    {
        $conn = $this->conect();
        $rut = mysql_real_escape_string($rut, $conn);

        $sql = "SELECT
                    u.FUN_CODIGO,
                    u.UNI_CODIGO,
                    u.US_LOGIN,
                    u.TUS_CODIGO,
                    u.US_FECHACREACION,
                    u.US_FECHAMODIFICACION,
                    u.US_ACTIVO,
                    f.FUN_RUT,
                    f.FUN_NOMBRE,
                    f.FUN_NOMBRE2,
                    f.FUN_APELLIDOPATERNO,
                    f.FUN_APELLIDOMATERNO,
                    f.ESC_CODIGO,
                    f.GRA_CODIGO,
                    g.GRA_DESCRIPCION,
                    un.UNI_DESCRIPCION,
                    t.TUS_DESCRIPCION,
                    t.VALIDAR,
                    t.VALIDAR_OIC,
                    t.REGISTRAR,
                    t.CONSULTAR_UNIDAD,
                    t.CONSULTAR_PERFIL
                FROM USUARIO u
                INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
                LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
                LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
                LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
                WHERE f.FUN_RUT = '{$rut}'";

        $result = $this->execute($conn, $sql);
        $usuario = null;

        if ($myrow = mysql_fetch_array($result)) {
            $usuario = array(
                "funCodigo"           => $myrow["FUN_CODIGO"],
                "uniCodigo"           => $myrow["UNI_CODIGO"],
                "uniDescripcion"      => utf8_encode($myrow["UNI_DESCRIPCION"]),
                "usLogin"             => $myrow["US_LOGIN"],
                "tusCodigo"           => $myrow["TUS_CODIGO"],
                "tusDescripcion"      => utf8_encode($myrow["TUS_DESCRIPCION"]),
                "usFechaCreacion"     => $myrow["US_FECHACREACION"],
                "usFechaModificacion" => $myrow["US_FECHAMODIFICACION"],
                "usActivo"            => $myrow["US_ACTIVO"],
                "funRut"              => $myrow["FUN_RUT"],
                "funNombre"           => utf8_encode($myrow["FUN_NOMBRE"]),
                "funNombre2"          => utf8_encode($myrow["FUN_NOMBRE2"]),
                "funApellidoPaterno"  => utf8_encode($myrow["FUN_APELLIDOPATERNO"]),
                "funApellidoMaterno"  => utf8_encode($myrow["FUN_APELLIDOMATERNO"]),
                "escCodigo"           => $myrow["ESC_CODIGO"],
                "graCodigo"           => $myrow["GRA_CODIGO"],
                "graDescripcion"      => utf8_encode($myrow["GRA_DESCRIPCION"]),
                "permisos"            => array(
                    "validar"          => (int)$myrow["VALIDAR"],
                    "validarOic"       => (int)$myrow["VALIDAR_OIC"],
                    "registrar"        => (int)$myrow["REGISTRAR"],
                    "consultarUnidad"  => (int)$myrow["CONSULTAR_UNIDAD"],
                    "consultarPerfil"  => (int)$myrow["CONSULTAR_PERFIL"]
                )
            );
        }

        $this->desconect();

        return $usuario
            ? array("success" => true, "data" => $usuario)
            : array("success" => false, "data" => false, "message" => "Usuario no encontrado");
    }

    /**
     * Lista todos los usuarios con datos completos
     */
    function listarUsuarios()
    {
        $conn = $this->conect();

        $sql = "SELECT
                    u.FUN_CODIGO,
                    u.UNI_CODIGO,
                    u.US_LOGIN,
                    u.TUS_CODIGO,
                    u.US_FECHACREACION,
                    u.US_FECHAMODIFICACION,
                    u.US_ACTIVO,
                    f.FUN_RUT,
                    f.FUN_NOMBRE,
                    f.FUN_NOMBRE2,
                    f.FUN_APELLIDOPATERNO,
                    f.FUN_APELLIDOMATERNO,
                    f.ESC_CODIGO,
                    f.GRA_CODIGO,
                    g.GRA_DESCRIPCION,
                    un.UNI_DESCRIPCION,
                    t.TUS_DESCRIPCION
                FROM USUARIO u
                INNER JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
                LEFT JOIN GRADO g ON f.ESC_CODIGO = g.ESC_CODIGO AND f.GRA_CODIGO = g.GRA_CODIGO
                LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
                LEFT JOIN TIPO_USUARIO t ON u.TUS_CODIGO = t.TUS_CODIGO
                ORDER BY f.FUN_APELLIDOPATERNO, f.FUN_APELLIDOMATERNO, f.FUN_NOMBRE";

        $result = $this->execute($conn, $sql);
        $usuarios = array();

        while ($myrow = mysql_fetch_array($result)) {
            $usuarios[] = array(
                "funCodigo"          => $myrow["FUN_CODIGO"],
                "uniCodigo"          => $myrow["UNI_CODIGO"],
                "uniDescripcion"     => utf8_encode($myrow["UNI_DESCRIPCION"]),
                "usLogin"            => $myrow["US_LOGIN"],
                "tusCodigo"          => $myrow["TUS_CODIGO"],
                "tusDescripcion"     => utf8_encode($myrow["TUS_DESCRIPCION"]),
                "usFechaCreacion"    => $myrow["US_FECHACREACION"],
                "usFechaModificacion"=> $myrow["US_FECHAMODIFICACION"],
                "usActivo"           => $myrow["US_ACTIVO"],
                "funRut"             => $myrow["FUN_RUT"],
                "funNombre"          => utf8_encode($myrow["FUN_NOMBRE"]),
                "funNombre2"         => utf8_encode($myrow["FUN_NOMBRE2"]),
                "funApellidoPaterno" => utf8_encode($myrow["FUN_APELLIDOPATERNO"]),
                "funApellidoMaterno" => utf8_encode($myrow["FUN_APELLIDOMATERNO"]),
                "graDescripcion"     => utf8_encode($myrow["GRA_DESCRIPCION"]),
                "nombreCompleto"     => utf8_encode(
                    trim($myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"]) .
                    " " . $myrow["FUN_APELLIDOPATERNO"] .
                    " " . $myrow["FUN_APELLIDOMATERNO"]
                )
            );
        }

        $this->desconect();

        return array(
            "success" => true,
            "data"    => $usuarios,
            "total"   => count($usuarios)
        );
    }

    /**
     * Crea un nuevo usuario en USUARIO
     * Campos editables: UNI_CODIGO, US_LOGIN, US_PASSWORD, TUS_CODIGO, US_ACTIVO
     */
    function crearUsuario($params)
    {
        $conn = $this->conect();

        $funCodigo  = mysql_real_escape_string($params['funCodigo'], $conn);
        $uniCodigo  = intval($params['uniCodigo']);
        $usLogin    = mysql_real_escape_string($params['usLogin'], $conn);
        $usPassword = mysql_real_escape_string($params['usPassword'], $conn);
        $tusCodigo  = intval($params['tusCodigo']);
        $usActivo   = isset($params['usActivo']) ? intval($params['usActivo']) : 1;

        // Verificar que el funcionario existe
        $sqlFun = "SELECT FUN_CODIGO FROM FUNCIONARIO WHERE FUN_CODIGO = '{$funCodigo}'";
        $resFun = $this->execute($conn, $sqlFun);
        if (mysql_num_rows($resFun) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "Funcionario no encontrado en el sistema");
        }

        // Verificar que no existe ya como usuario
        $sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigo}'";
        $resCheck = $this->execute($conn, $sqlCheck);
        if (mysql_num_rows($resCheck) > 0) {
            $this->desconect();
            return array("success" => false, "message" => "El funcionario ya tiene un usuario registrado");
        }

        // Verificar que US_LOGIN no está en uso
        $sqlLogin = "SELECT FUN_CODIGO FROM USUARIO WHERE US_LOGIN = '{$usLogin}'";
        $resLogin = $this->execute($conn, $sqlLogin);
        if (mysql_num_rows($resLogin) > 0) {
            $this->desconect();
            return array("success" => false, "message" => "El login ya está en uso por otro usuario");
        }

        // Verificar que UNI_CODIGO existe
        $sqlUni = "SELECT UNI_CODIGO FROM UNIDAD WHERE UNI_CODIGO = {$uniCodigo}";
        $resUni = $this->execute($conn, $sqlUni);
        if (mysql_num_rows($resUni) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "La unidad especificada no existe");
        }

        // Verificar que TUS_CODIGO existe y está activo
        $sqlTus = "SELECT TUS_CODIGO FROM TIPO_USUARIO WHERE TUS_CODIGO = {$tusCodigo} AND TUS_ACTIVO = 1";
        $resTus = $this->execute($conn, $sqlTus);
        if (mysql_num_rows($resTus) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "El tipo de usuario especificado no es válido");
        }

        $sql = "INSERT INTO USUARIO (
                    FUN_CODIGO,
                    UNI_CODIGO,
                    US_LOGIN,
                    US_PASSWORD,
                    TUS_CODIGO,
                    US_FECHACREACION,
                    US_ACTIVO
                ) VALUES (
                    '{$funCodigo}',
                    {$uniCodigo},
                    '{$usLogin}',
                    '{$usPassword}',
                    {$tusCodigo},
                    NOW(),
                    {$usActivo}
                )";

        $result = $this->execute($conn, $sql);

        if ($result) {
            $this->desconect();
            return array("success" => true, "message" => "Usuario creado exitosamente", "funCodigo" => $funCodigo);
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            return array("success" => false, "message" => "Error al crear usuario: " . $error);
        }
    }

    /**
     * Edita un usuario existente
     * Campos editables: UNI_CODIGO, US_LOGIN, US_PASSWORD, TUS_CODIGO, US_ACTIVO
     */
    function editarUsuario($params)
    {
        $conn = $this->conect();

        $funCodigo  = mysql_real_escape_string($params['funCodigo'], $conn);
        $uniCodigo  = intval($params['uniCodigo']);
        $usLogin    = mysql_real_escape_string($params['usLogin'], $conn);
        $tusCodigo  = intval($params['tusCodigo']);
        $usActivo   = intval($params['usActivo']);

        // Verificar que el usuario existe
        $sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigo}'";
        $resCheck = $this->execute($conn, $sqlCheck);
        if (mysql_num_rows($resCheck) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "Usuario no encontrado");
        }

        // Verificar que US_LOGIN no está en uso por otro usuario
        $sqlLogin = "SELECT FUN_CODIGO FROM USUARIO
                     WHERE US_LOGIN = '{$usLogin}' AND FUN_CODIGO != '{$funCodigo}'";
        $resLogin = $this->execute($conn, $sqlLogin);
        if (mysql_num_rows($resLogin) > 0) {
            $this->desconect();
            return array("success" => false, "message" => "El login ya está en uso por otro usuario");
        }

        // Verificar que UNI_CODIGO existe
        $sqlUni = "SELECT UNI_CODIGO FROM UNIDAD WHERE UNI_CODIGO = {$uniCodigo}";
        $resUni = $this->execute($conn, $sqlUni);
        if (mysql_num_rows($resUni) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "La unidad especificada no existe");
        }

        // Verificar que TUS_CODIGO existe y está activo
        $sqlTus = "SELECT TUS_CODIGO FROM TIPO_USUARIO WHERE TUS_CODIGO = {$tusCodigo} AND TUS_ACTIVO = 1";
        $resTus = $this->execute($conn, $sqlTus);
        if (mysql_num_rows($resTus) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "El tipo de usuario especificado no es válido");
        }

        // Construir SET dinámico (password es opcional en edición)
        $setParts = array(
            "UNI_CODIGO = {$uniCodigo}",
            "US_LOGIN = '{$usLogin}'",
            "TUS_CODIGO = {$tusCodigo}",
            "US_ACTIVO = {$usActivo}",
            "US_FECHAMODIFICACION = NOW()"
        );

        // Si se envía nueva password, actualizarla
        if (!empty($params['usPassword'])) {
            $usPassword = mysql_real_escape_string($params['usPassword'], $conn);
            $setParts[] = "US_PASSWORD = '{$usPassword}'";
        }

        $setClause = implode(", ", $setParts);
        $sql = "UPDATE USUARIO SET {$setClause} WHERE FUN_CODIGO = '{$funCodigo}'";

        $result = $this->execute($conn, $sql);

        if ($result) {
            $this->desconect();
            return array("success" => true, "message" => "Usuario actualizado exitosamente");
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            return array("success" => false, "message" => "Error al actualizar usuario: " . $error);
        }
    }

    /**
     * Desactiva un usuario (baja lógica: US_ACTIVO = 0)
     * No se elimina físicamente — la tabla no tiene campo de eliminación
     */
    function eliminarUsuario($funCodigo)
    {
        $conn = $this->conect();
        $funCodigo = mysql_real_escape_string($funCodigo, $conn);

        // Verificar que el usuario existe
        $sqlCheck = "SELECT FUN_CODIGO FROM USUARIO WHERE FUN_CODIGO = '{$funCodigo}'";
        $resCheck = $this->execute($conn, $sqlCheck);
        if (mysql_num_rows($resCheck) == 0) {
            $this->desconect();
            return array("success" => false, "message" => "Usuario no encontrado");
        }

        $sql = "UPDATE USUARIO SET
                    US_ACTIVO = 0,
                    US_FECHAMODIFICACION = NOW()
                WHERE FUN_CODIGO = '{$funCodigo}'";

        $result = $this->execute($conn, $sql);

        if ($result) {
            $this->desconect();
            return array("success" => true, "message" => "Usuario desactivado exitosamente");
        } else {
            $error = mysql_error($conn);
            $this->desconect();
            return array("success" => false, "message" => "Error al desactivar usuario: " . $error);
        }
    }
}
?>