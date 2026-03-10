<?php
include_once("../inc/configProservipolHistorico_2019_2021.inc.php");
include_once("conexionDBProservipolHistorico.Class.php");
Class dbFuncionarioProservipolHistorico_2019_2021 extends ConexionDBProservipolHistorico{
	function serviciosFuncionarioPorCodigo_2019_2021($codigoFuncionario, $fechaDesde, $fechaHasta){
		$sql = "SELECT 
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_CODIGO,
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
		mysqli_close($this->conect());
		$fecha_actual = date("d-m-Y H:i:s"); 
		$listaServicios = array();
		$sinServicio 	= true;
		while($myrow = mysqli_fetch_array($result)){
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"],
				"tipoServicio" 				=> $myrow["TIPOSERVICIO"],
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> $myrow["DESTACAMENTO"],
				"fechaHoraConsulta"			=> $fecha_actual
			));
		}
		return array("data" => $listaServicios);
	}
	/*funcion que busca servicios de uno o mas funcionarios*/
	function serviciosFuncionarioPorCodigo_2019_2021_2($codigoFuncionario, $fechaDesde, $fechaHasta){
		$codigos = preg_replace('([^A-Za-z0-9 \' \,])', '', $codigoFuncionario);
		$sql = "SELECT 
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_CODIGO,
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
				WHERE FUNCIONARIO.FUN_CODIGO IN ({$codigos}) AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA";
		echo "fhcvh" .$sql;
		$result = $this->execute($this->conect(),$sql);
		mysqli_close($this->conect());
		$fecha_actual = date("d-m-Y H:i:s"); 
		$listaServicios = array();
		$sinServicio 	= true;
		while($myrow = mysqli_fetch_array($result)){
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"],
				"tipoServicio" 				=> $myrow["TIPOSERVICIO"],
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> $myrow["DESTACAMENTO"],
				"fechaHoraConsulta"			=> $fecha_actual
			));
		}
		return array("data" => $listaServicios);
	}
}
?>