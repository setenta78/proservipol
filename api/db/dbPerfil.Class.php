<?php
/**
 * dbPerfil.Class.php
 * Acceso a TIPO_USUARIO (perfiles/roles del sistema)
 * Compatible con PHP 5.1.2 + MySQL 5.0.77
 */
include_once("../../inc/config.inc.php");
include_once("conexion.Class.php");

class dbPerfil extends Conexion
{
    function listarPerfiles()
    {
        $conn = $this->conect();
        $sql = "SELECT
                    TUS_CODIGO,
                    TUS_DESCRIPCION,
                    VALIDAR,
                    VALIDAR_OIC,
                    REGISTRAR,
                    CONSULTAR_UNIDAD,
                    CONSULTAR_PERFIL
                FROM TIPO_USUARIO
                WHERE TUS_ACTIVO = 1
                ORDER BY TUS_CODIGO";

        $result = $this->execute($conn, $sql);
        $perfiles = array();

        while ($myrow = mysql_fetch_array($result)) {
            $perfiles[] = array(
                "tusCodigo"       => (int)$myrow["TUS_CODIGO"],
                "tusDescripcion"  => utf8_encode($myrow["TUS_DESCRIPCION"]),
                "validar"         => (int)$myrow["VALIDAR"],
                "validarOic"      => (int)$myrow["VALIDAR_OIC"],
                "registrar"       => (int)$myrow["REGISTRAR"],
                "consultarUnidad" => (int)$myrow["CONSULTAR_UNIDAD"],
                "consultarPerfil" => (int)$myrow["CONSULTAR_PERFIL"]
            );
        }

        $this->desconect();
        return array("success" => true, "data" => $perfiles, "total" => count($perfiles));
    }

    function buscarPerfil($tusCodigo)
    {
        $conn = $this->conect();
        $tusCodigo = intval($tusCodigo);

        $sql = "SELECT
                    TUS_CODIGO,
                    TUS_DESCRIPCION,
                    VALIDAR,
                    VALIDAR_OIC,
                    REGISTRAR,
                    CONSULTAR_UNIDAD,
                    CONSULTAR_PERFIL
                FROM TIPO_USUARIO
                WHERE TUS_CODIGO = {$tusCodigo}
                AND TUS_ACTIVO = 1";

        $result = $this->execute($conn, $sql);
        $perfil = null;

        if ($myrow = mysql_fetch_array($result)) {
            $perfil = array(
                "tusCodigo"       => (int)$myrow["TUS_CODIGO"],
                "tusDescripcion"  => utf8_encode($myrow["TUS_DESCRIPCION"]),
                "validar"         => (int)$myrow["VALIDAR"],
                "validarOic"      => (int)$myrow["VALIDAR_OIC"],
                "registrar"       => (int)$myrow["REGISTRAR"],
                "consultarUnidad" => (int)$myrow["CONSULTAR_UNIDAD"],
                "consultarPerfil" => (int)$myrow["CONSULTAR_PERFIL"]
            );
        }

        $this->desconect();
        return $perfil
            ? array("success" => true, "data" => $perfil)
            : array("success" => false, "data" => false, "message" => "Perfil no encontrado");
    }
}
?>