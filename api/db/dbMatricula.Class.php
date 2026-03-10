<?php
include_once("../../inc/configMatricula.inc.php");
include_once("conexion.Class.php");
class dbMatricula extends Conexion
{
    /**
     * Busca un funcionario aprobado en la BD Matrícula
     */
    function buscarFuncionarioAprobado($params)
    {
        $conn = $this->conect();
        $rut = mysql_real_escape_string($params['rut'], $conn);
        
        $sql = "SELECT 
                    m.rut,
                    m.cod_funcionario,
                    m.primer_nombre,
                    m.segundo_nombre,
                    m.apellido_paterno,
                    m.apellido_materno,
                    m.curso_aprobado,
                    m.fecha_aprobacion,
                    m.nota_final
                FROM matricula m
                WHERE m.rut = '{$rut}'
                AND m.estado_aprobacion = 'APROBADO'
                ORDER BY m.fecha_aprobacion DESC
                LIMIT 1";
        
        $result = $this->execute($conn, $sql);
        // Leer datos ANTES de cerrar conexión
        $funcionario = null;
        if ($myrow = mysql_fetch_array($result)) {
            $funcionario = array(
                "rut"               => $myrow["rut"],
                "codFuncionario"    => $myrow["cod_funcionario"],
                "primerNombre"      => utf8_encode($myrow["primer_nombre"]),
                "segundoNombre"     => utf8_encode($myrow["segundo_nombre"]),
                "apellidoPaterno"   => utf8_encode($myrow["apellido_paterno"]),
                "apellidoMaterno"   => utf8_encode($myrow["apellido_materno"]),
                "cursoAprobado"     => utf8_encode($myrow["curso_aprobado"]),
                "fechaAprobacion"   => $myrow["fecha_aprobacion"],
                "notaFinal"         => $myrow["nota_final"]
            );
        }
        $this->desconect();
        return $funcionario ? 
            array("success" => true, "data" => $funcionario) : 
            array("success" => false, "data" => false, "message" => "Funcionario no encontrado o no tiene curso aprobado");
    }
}
?>