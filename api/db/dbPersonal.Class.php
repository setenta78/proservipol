<?php
include_once("../../inc/configPersonal.inc.php");
include_once("conexion.Class.php");

class dbPersonal extends Conexion
{
    /**
     * Busca un funcionario en la tabla PERSONAL_MOCK
     */
    function buscarFuncionario($codFuncionario)
    {
        $conn = $this->conect();
        $codFuncionario = mysql_real_escape_string($codFuncionario, $conn);
        
        // Consulta adaptada a la tabla PERSONAL_MOCK
        $sql = "SELECT 
                  P.PEFBCOD,
                  P.PEFBRUT,
                  P.PEFBNOM1,
                  P.PEFBNOM2,
                  P.PEFBAPEP,
                  P.PEFBAPEM,
                  P.PEFBESC,
                  P.PEFBGRA,
                  P.GRADO_DESCRIPCION,
                  P.REPARTICION_DESC,
                  P.REPARTICION_DEP,
                  P.ALTA_REPARTICION,
                  P.PEFBACT
                FROM PERSONAL_MOCK P
                WHERE P.PEFBCOD = '{$codFuncionario}'
                AND P.PEFBACT IN (0, 29, 32)";
        
        $result = $this->execute($conn, $sql);
        
        // Leer datos ANTES de cerrar conexión
        $listaServicios = array();
        while ($myrow = mysql_fetch_array($result)) {
            $listaServicios[] = array(
                "rut"                       => utf8_encode($myrow["PEFBRUT"]),
                "primerNombre"              => utf8_encode($myrow["PEFBNOM1"]),
                "segundoNombre"             => utf8_encode($myrow["PEFBNOM2"]),
                "apellidoPaterno"           => utf8_encode($myrow["PEFBAPEP"]),
                "apellidoMaterno"           => utf8_encode($myrow["PEFBAPEM"]),
                "codEscalafon"              => utf8_encode($myrow["PEFBESC"]),
                "codGrado"                  => utf8_encode($myrow["PEFBGRA"]),
                "grado"                     => utf8_encode($myrow["GRADO_DESCRIPCION"]),
                "dotacion"                  => utf8_encode($myrow["REPARTICION_DESC"]),
                "reparticionDependiente"    => utf8_encode($myrow["REPARTICION_DEP"]),
                "altaReparticion"           => utf8_encode($myrow["ALTA_REPARTICION"])
            );
        }
        
        $this->desconect();
        
        return count($listaServicios) > 0 ? 
            array("success" => true, "data" => $listaServicios) : 
            array("success" => false, "data" => false, "message" => "Funcionario no encontrado en BD Personal");
    }
    
    /**
     * Busca escalafones faltantes
     */
    function buscarEscalafonesFaltantes($Jdata)
    {
        $data = json_decode($Jdata, true);
        if (!$data || !is_array($data)) {
            return array("success" => false, "data" => false, "message" => "Datos inválidos");
        }
        
        $conn = $this->conect();
        
        // Escapar cada elemento del array
        $escapedData = array();
        foreach ($data as $item) {
            $escapedData[] = "'" . mysql_real_escape_string($item, $conn) . "'";
        }
        $lista = implode(",", $escapedData);
        
        $sql = "SELECT DISTINCT P.PEFBESC AS ESCALAFON_CODIGO
                FROM PERSONAL_MOCK P
                WHERE P.PEFBESC IN ({$lista})";
        
        $result = $this->execute($conn, $sql);
        
        // Leer datos ANTES de cerrar conexión
        $listaServicios = array();
        while ($myrow = mysql_fetch_array($result)) {
            $listaServicios[] = array(
                "ESCALAFON_CODIGO" => utf8_encode($myrow["ESCALAFON_CODIGO"])
            );
        }
        
        $this->desconect();
        
        return count($listaServicios) > 0 ? 
            array("success" => true, "data" => $listaServicios) : 
            array("success" => false, "data" => false, "message" => "No se encontraron escalafones");
    }
    
    /**
     * Busca datos personales de un funcionario
     */
    function buscarDatosPersonal($codFuncionario)
    {
        $conn = $this->conect();
        $codFuncionario = mysql_real_escape_string($codFuncionario, $conn);
        
        // Consulta simplificada para PERSONAL_MOCK
        $sql = "SELECT
                    P.PEFBCOD AS CODFUN,
                    '1' AS CODGENERO,
                    '1980-01-01' AS FECHANAC,
                    P.CREATED_AT AS FECHAING
                FROM PERSONAL_MOCK P
                WHERE P.PEFBCOD = '{$codFuncionario}'";
        
        $result = $this->execute($conn, $sql);
        
        // Leer datos ANTES de cerrar conexión
        $listaDatos = array();
        while ($myrow = mysql_fetch_array($result)) {
            $listaDatos[] = array(
                "codigoFuncionario"     => utf8_encode($myrow["CODFUN"]),
                "codigoGenero"          => utf8_encode($myrow["CODGENERO"]),
                "fechaNacimiento"       => utf8_encode($myrow["FECHANAC"]),
                "fechaIngreso"          => utf8_encode($myrow["FECHAING"])
            );
        }
        
        $this->desconect();
        
        return count($listaDatos) > 0 ? 
            array("success" => true, "data" => $listaDatos[0]) : 
            array("success" => false, "data" => false, "message" => "Datos personales no encontrados");
    }
}
?>