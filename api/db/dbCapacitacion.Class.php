<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbCapacitacion extends Conexion{
	function funcionariosPorCodFuncionario($codFuncionario, $limite){
		$sql = "SELECT 
					F.FUN_CODIGO,
					G.GRA_DESCRIPCION,
					F.FUN_APELLIDOPATERNO,
					IFNULL(F.FUN_APELLIDOMATERNO,'') FUN_APELLIDOMATERNO,
					F.FUN_NOMBRE,
					IFNULL(F.FUN_NOMBRE2,'') FUN_NOMBRE2,
					IFNULL(T.TUS_DESCRIPCION,'SIN PERFIL') PERFIL
				FROM FUNCIONARIO F
				JOIN GRADO G ON G.ESC_CODIGO = F.ESC_CODIGO AND G.GRA_CODIGO = F.GRA_CODIGO
				LEFT JOIN USUARIO U ON U.FUN_CODIGO = F.FUN_CODIGO
				LEFT JOIN TIPO_USUARIO T ON T.TUS_CODIGO = U.TUS_CODIGO
				WHERE F.FUN_CODIGO LIKE '{$codFuncionario}%'
				LIMIT {$limite}";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinDatos = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaFuncionarios, array(
				"codFuncionario"	=> $myrow["FUN_CODIGO"],
				"grado"				=> $myrow["GRA_DESCRIPCION"],
				"primerApellido"	=> utf8_encode($myrow["FUN_APELLIDOPATERNO"]),
				"segundoApellido"	=> utf8_encode($myrow["FUN_APELLIDOMATERNO"]),
				"nombre"			=> utf8_encode($myrow["FUN_NOMBRE"]),
				"nombre2"			=> utf8_encode($myrow["FUN_NOMBRE2"]),
				"perfil"			=> $myrow["PERFIL"]
			));
			if($myrow["EXISTE"]<>true) $sinDatos = false;
		}
		return $sinDatos ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function capacitacionesPorCodFuncionario($codFuncionario){
		$sql = "SELECT 
					IF(C.TIPO_CAPACITACION='','CURSO PROSERVIPOL',C.TIPO_CAPACITACION) CURSO,
					C.FECHA_CAPACITACION,
					C.NOTA_PROSERVIPOL,
					IFNULL(C.CODIGO_VERIFICACION,'') CODVERIFICACION,
					C.ACTIVO
				FROM CAPACITACION C
				LEFT JOIN MATRICULA_FUNCIONARIO M ON M.FUN_CODIGO = C.FUN_CODIGO
				WHERE C.FUN_CODIGO = '{$codFuncionario}'";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaCapacitaciones = array();
		$sinDatos = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaCapacitaciones, array(
				"curso"					=> $myrow["CURSO"],
				"fechaCapacitacion"		=> $myrow["FECHA_CAPACITACION"],
				"notaProservipol"		=> $myrow["NOTA_PROSERVIPOL"],
				"codigoVerificacion"	=> $myrow["CODVERIFICACION"],
				"activo"				=> $myrow["ACTIVO"]
			));
			if($myrow["EXISTE"]<>true) $sinDatos = false;
		}
		return $sinDatos ? array("data" => false) : array("data" => $listaCapacitaciones);
	}
}