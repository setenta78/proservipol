<?php
include_once("../../inc/config.inc.php");
include_once("conexion.Class.php");
class dbPerfil extends Conexion
{
    /**
     * Lista todos los perfiles activos
     */
    function listarPerfiles()
    {
        $conn = $this->conect();
        $sql = "SELECT 
                    id_perfil,
                    nombre_perfil,
                    descripcion,
                    estado
                FROM perfiles
                WHERE estado = 1
                ORDER BY nombre_perfil";
        $result = $this->execute($conn, $sql);
        // Leer datos ANTES de cerrar conexión
        $perfiles = array();
        while ($myrow = mysql_fetch_array($result)) {
            $perfiles[] = array(
                "idPerfil"      => $myrow["id_perfil"],
                "nombrePerfil"  => utf8_encode($myrow["nombre_perfil"]),
                "descripcion"   => utf8_encode($myrow["descripcion"]),
                "estado"        => $myrow["estado"]
            );
        }
        $this->desconect();
        return array(
            "success" => true,
            "data" => $perfiles,
            "total" => count($perfiles)
        );
    }
    /**
     * Busca un perfil por ID
     */
    function buscarPerfil($idPerfil)
    {
        $conn = $this->conect();
        $idPerfil = intval($idPerfil);
        $sql = "SELECT 
                    id_perfil,
                    nombre_perfil,
                    descripcion,
                    estado
                FROM perfiles
                WHERE id_perfil = {$idPerfil}";
        $result = $this->execute($conn, $sql);
        // Leer datos ANTES de cerrar conexión
        $perfil = null;
        if ($myrow = mysql_fetch_array($result)) {
            $perfil = array(
                "idPerfil"      => $myrow["id_perfil"],
                "nombrePerfil"  => utf8_encode($myrow["nombre_perfil"]),
                "descripcion"   => utf8_encode($myrow["descripcion"]),
                "estado"        => $myrow["estado"]
            );
        }
        $this->desconect();
        return $perfil ? 
            array("success" => true, "data" => $perfil) : 
            array("success" => false, "data" => false, "message" => "Perfil no encontrado");
    }
}
?>