<?php
include_once("../inc/config.inc.php");
include_once("conexionDB.Class.php");
Class dbFuncionarioDB extends ConexionDB{
	function buscarFuncionarioPorNombre($primerApellido, $segundoApellido, $primerNombre){
		$sql = "SELECT 
					FUNCIONARIO.FUN_CODIGO,
					FUNCIONARIO.FUN_RUT,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					UNIDAD.UNI_DESCRIPCION
				FROM
					FUNCIONARIO
					INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)
					AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
					LEFT OUTER JOIN UNIDAD ON (FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
				WHERE 1";
		if ($primerApellido <> "") 	$sql .= " AND FUNCIONARIO.FUN_APELLIDOPATERNO = '{$primerApellido}'";
		if ($segundoApellido <> "") $sql .= " AND FUNCIONARIO.FUN_APELLIDOMATERNO = '{$segundoApellido}'";
		if ($primerNombre <> "") 	$sql .= " AND FUNCIONARIO.FUN_NOMBRE = '{$primerNombre}'";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaFuncionarios, array(
				"FUN_CODIGO" 			=> $myrow["FUN_CODIGO"],
				"FUN_RUT"				=> $myrow["FUN_RUT"],
				"GRA_DESCRIPCION" 		=> $myrow["GRA_DESCRIPCION"],
				"FUN_PRIMERAPELLIDO" 	=> $myrow["FUN_APELLIDOPATERNO"],
				"FUN_SEGUNDOPELLIDO" 	=> $myrow["FUN_APELLIDOMATERNO"],
				"FUN_NOMBRE" 			=> $myrow["FUN_NOMBRE"],
				"FUN_NOMBRE2" 			=> $myrow["FUN_NOMBRE2"],
				"FUN_NOMBRE_COMPLETO"   => $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"UNI_DESCRIPCION" 		=> $myrow["UNI_DESCRIPCION"]
			));
			if($myrow["EXISTE"]<>true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function buscarFuncionarioPorCodigoFuncionario($codigoFuncionario){
		$sql = "SELECT 
					FUNCIONARIO.FUN_CODIGO,
					FUNCIONARIO.FUN_RUT,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					UNIDAD.UNI_DESCRIPCION
				FROM
					FUNCIONARIO
					INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)
					AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
					LEFT OUTER JOIN UNIDAD ON (FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
				WHERE FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaFuncionarios, array(
				"FUN_CODIGO" 			=> $myrow["FUN_CODIGO"],
				"FUN_RUT"				=> $myrow["FUN_RUT"],
				"GRA_DESCRIPCION" 		=> $myrow["GRA_DESCRIPCION"],
				"FUN_PRIMERAPELLIDO" 	=> $myrow["FUN_APELLIDOPATERNO"],
				"FUN_SEGUNDOPELLIDO" 	=> $myrow["FUN_APELLIDOMATERNO"],
				"FUN_NOMBRE" 			=> $myrow["FUN_NOMBRE"],
				"FUN_NOMBRE2" 			=> $myrow["FUN_NOMBRE2"],
				"FUN_NOMBRE_COMPLETO"   => $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"UNI_DESCRIPCION" 		=> $myrow["UNI_DESCRIPCION"]
			));
			if($myrow["EXISTE"]<>true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function serviciosFuncionarioPorCodigo($codigoFuncionario, $fechaDesde, $fechaHasta){
		$sql = "SELECT
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					FUNCIONARIO.FUN_CODIGO,
					GRADO.GRA_DESCRIPCION,
					SERVICIO.FECHA,
					UCASE(TIPO_SERVICIO.TSERV_DESCRIPCION) TIPOSERVICIO,
					SERVICIO.HORA_INICIO,
					SERVICIO.HORA_TERMINO,
					UNIDAD.UNI_DESCRIPCION DESTACAMENTO
				FROM SERVICIO 
				INNER JOIN FUNCIONARIO_SERVICIO ON (FUNCIONARIO_SERVICIO.UNI_CODIGO = SERVICIO.UNI_CODIGO AND FUNCIONARIO_SERVICIO.CORRELATIVO_SERVICIO = SERVICIO.CORRELATIVO_SERVICIO)
				INNER JOIN FUNCIONARIO ON (FUNCIONARIO.FUN_CODIGO = FUNCIONARIO_SERVICIO.FUN_CODIGO)
				INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)	AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
				INNER JOIN TIPO_SERVICIO ON (TIPO_SERVICIO.TSERV_CODIGO = SERVICIO.TSERV_CODIGO)
				INNER JOIN UNIDAD ON (UNIDAD.UNI_CODIGO = SERVICIO.UNI_CODIGO)
				WHERE FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}' AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"],
				"tipoServicio" 				=> $myrow["TIPOSERVICIO"],
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> $myrow["DESTACAMENTO"]
			));
			if($myrow["EXISTE"]<>true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function serviciosFuncionarioPorCodigo_2014_2018($codigoFuncionario, $fechaDesde, $fechaHasta){
		$sql = "SELECT 
					FUNCIONARIO.FUN_CODIGO,
					SERVICIO.FECHA,
					UCASE(TIPO_SERVICIO.TSERV_DESCRIPCION) TIPOSERVICIO,
					SERVICIO.HORA_INICIO,
					SERVICIO.HORA_TERMINO,
					UNIDAD.UNI_DESCRIPCION DESTACAMENTO
				FROM SERVICIO 
				INNER JOIN FUNCIONARIO_SERVICIO ON (FUNCIONARIO_SERVICIO.UNI_CODIGO = SERVICIO.UNI_CODIGO AND FUNCIONARIO_SERVICIO.CORRELATIVO_SERVICIO = SERVICIO.CORRELATIVO_SERVICIO)
				INNER JOIN FUNCIONARIO ON (FUNCIONARIO.FUN_CODIGO = FUNCIONARIO_SERVICIO.FUN_CODIGO)
				INNER JOIN TIPO_SERVICIO ON (TIPO_SERVICIO.TSERV_CODIGO = SERVICIO.TSERV_CODIGO)
				INNER JOIN UNIDAD ON (UNIDAD.UNI_CODIGO = SERVICIO.UNI_CODIGO)
				WHERE FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}' AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA";
		echo "hfhfh" . $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
				"codigoFuncionario"		=> $myrow["FUN_CODIGO"],
				"fecha" 				=> $myrow["FECHA"],
				"tipoServicio" 			=> $myrow["TIPOSERVICIO"],
				"horaInicio" 			=> $myrow["HORA_INICIO"],
				"horaTermino" 			=> $myrow["HORA_TERMINO"],
				"destacamento" 			=> $myrow["DESTACAMENTO"]
			));
			if($myrow["EXISTE"]<>true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
}
?>