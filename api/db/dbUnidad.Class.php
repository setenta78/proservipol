<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbUnidad extends Conexion{
	function listaUnidades($codigoFuncionario){
		$sql = "SELECT 
                    UNIDAD.UNI_CODIGO, 
                    UNIDAD.UNI_DESCRIPCION 
                FROM UNIDAD
                WHERE UNIDAD.UNI_CAPTURA = 1";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaUnidades = array();
		$sinUnidad = true;
		$fecha_actual = date("d-m-Y H:i:s"); 
		while($myrow = mysql_fetch_array($result)){
			array_push($listaUnidades, array(
				"data"	            => $myrow["UNI_CODIGO"],
				"value"			=> $myrow["UNI_DESCRIPCION"]
			));
			if($myrow["EXISTE"]<>true) $sinUnidad = false;
		}
		return $sinUnidad ? array("data" => false) : array("data" => $listaUnidades);
	}
	function serviciosPorCodigoUnidad($codigoUnidad, $fechaDesde, $fechaHasta){
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
				WHERE SERVICIO.UNI_CODIGO = '{$codigoUnidad}' AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA, TIPO_SERVICIO.TSERV_DESCRIPCION, FUNCIONARIO.FUN_APELLIDOPATERNO, FUNCIONARIO.FUN_APELLIDOMATERNO, FUNCIONARIO.FUN_NOMBRE, FUNCIONARIO.FUN_NOMBRE2";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		$fecha_actual = date("d-m-Y H:i:s"); 
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"nombreCompletoFuncionarioGradoCodigo"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"] . " (" .$myrow["GRA_DESCRIPCION"]. " - ".  $myrow["FUN_CODIGO"] . ")",
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"],
				"tipoServicio" 				=> $myrow["TIPOSERVICIO"],
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> $myrow["DESTACAMENTO"],
				"fechaHoraConsulta"			=> $fecha_actual
			));
			if($myrow["EXISTE"]<>true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function buscarUnidadCodigo($codigoUnidad){
		$sql = "SELECT 
					U.UNI_CODIGO,
					U.UNI_PADRE,
					U.UNI_DESCRIPCION,
					U1.UNI_PADRE UNI_ABUELO,
					IF(ISNULL(U2.UNI_CODIGO),0,1) CONHIJOS,
					U.UNI_CAPTURA
				FROM UNIDAD U
				LEFT JOIN UNIDAD U1 ON U.UNI_PADRE = U1.UNI_CODIGO
				LEFT JOIN UNIDAD U2 ON U.UNI_CODIGO = U2.UNI_PADRE
				WHERE U.UNI_MOSTRAR = 1 AND U.UNI_CODIGO <> 1 AND U.UNI_PADRE = {$codigoUnidad}
				GROUP BY U.UNI_CODIGO
				ORDER BY U.UNI_ORDEN, U.UNI_CODIGO";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaUnidades = array();
		$sinUnidad = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaUnidades, array(
				"codUnidad"		=> $myrow["UNI_CODIGO"],
				"descUnidad"	=> utf8_encode($myrow["UNI_DESCRIPCION"]),
				"codPadre"		=> $myrow["UNI_PADRE"],
				"codAbuelo"		=> $myrow["UNI_ABUELO"],
				"conHijos"		=> $myrow["CONHIJOS"],
				"captura"		=> $myrow["UNI_CAPTURA"]
			));
			if($myrow["EXISTE"]<>true) $sinUnidad = false;
		}
		return $sinUnidad ? array("data" => false) : array("data" => $listaUnidades);
	}
	function buscarUnidadNombre($descUnidad, $captura){
		$sql = "SELECT 
					U.UNI_CODIGO,
					U.UNI_PADRE,
					U.UNI_DESCRIPCION,
					U1.UNI_PADRE UNI_ABUELO
				FROM UNIDAD U
				LEFT JOIN UNIDAD U1 ON U.UNI_PADRE = U1.UNI_CODIGO
				WHERE U.UNI_MOSTRAR = 1 AND U.UNI_DESCRIPCION LIKE '%{$descUnidad}%'
				AND U.UNI_CODIGO NOT IN (0,1,20)";
		if($captura) $sql .= " AND U.UNI_CAPTURA = 1";
		$sql .= " ORDER BY U.UNI_ORDEN, U.UNI_CODIGO";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaUnidades = array();
		$sinUnidad = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaUnidades, array(
				"codUnidad"		=> $myrow["UNI_CODIGO"],
				"descUnidad"	=> utf8_encode($myrow["UNI_DESCRIPCION"])
			));
			if($myrow["EXISTE"]<>true) $sinUnidad = false;
		}
		return $sinUnidad ? array("data" => false) : array("data" => $listaUnidades);
	}
}