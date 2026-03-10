<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
class dbFuncionario extends Conexion
{
	function listaServiciosFuncionario($rutFuncionario, $fechaDesde, $fechaHasta)
	{
		$sql = "SELECT MF.FECHA,
				SE.RUT,
				SE.TIPOSERVICIO,
				SE.DESTACAMENTO,
				ISNULL(SE.RUT) EXISTE
			FROM MARCELO_FECHA MF
			LEFT JOIN (SELECT F.FUN_RUT RUT,
							S.FECHA,
							T.TSERV_DESCRIPCION TIPOSERVICIO,
							U.UNI_DESCRIPCION DESTACAMENTO
						FROM SERVICIO S
						JOIN FUNCIONARIO_SERVICIO FS ON FS.UNI_CODIGO = S.UNI_CODIGO AND FS.CORRELATIVO_SERVICIO = S.CORRELATIVO_SERVICIO
						JOIN FUNCIONARIO F ON F.FUN_CODIGO = FS.FUN_CODIGO
						JOIN TIPO_SERVICIO T ON T.TSERV_CODIGO = S.TSERV_CODIGO
						JOIN UNIDAD U ON U.UNI_CODIGO = S.UNI_CODIGO
						WHERE F.FUN_RUT = '{$rutFuncionario}' AND S.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}') SE ON SE.FECHA = MF.FECHA
			WHERE MF.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'";
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while ($myrow = mysql_fetch_array($result)) {
			array_push($listaServicios, array(
				"fecha" 				=> $myrow["FECHA"],
				"rutFuncionario"		=> $myrow["RUT"],
				"tipoServicio" 			=> $myrow["PRIMER_NOMBRE"],
				"destacamento" 			=> $myrow["SEGUNDO_NOMBRE"],
				"destacamento" 			=> $myrow["APELLIDO_PATERNO"],
				"destacamento" 			=> $myrow["APELLIDO_MATERNO"],
				"destacamento" 			=> $myrow["ESC_CODIGO"],
				"destacamento" 			=> $myrow["GRA_CODIGO"],
				"destacamento" 			=> $myrow["GRADO"],
				"destacamento" 			=> $myrow["DOTACION"],
				"destacamento" 			=> $myrow["REPARTICION_DEPENDIENTE"],
				"destacamento" 			=> $myrow["ALTA_REPARTICION"],
				"destacamento" 			=> $myrow["NUMERO_CELULAR"],
				"destacamento" 			=> $myrow["NUMERO_IP"],
				"destacamento" 			=> $myrow["EMAIL"],
				"destacamento" 			=> $myrow["IP"],
				"destacamento" 			=> $myrow["FECHA"],
				"destacamento" 			=> $myrow["TIPO_CURSO"]
			));
			if ($myrow["EXISTE"] <> true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function buscarFuncionarioPorUnidad($codigoUnidad, $fecha)
	{
		$sql = "SELECT 
					DATA.UNI_DESCRIPCION,
					DATA.PRIMER_APELLIDO,
					DATA.SEGUNDO_APELLIDO,
					DATA.FUN_NOMBRE,
					DATA.FUN_NOMBRE2,
					DATA.FUN_CODIGO,
					DATA.GRA_DESCRIPCION,
					DATA.FECHA,
					DATA.CARGO
				FROM
				(
						SELECT 
							UNIDAD.UNI_DESCRIPCION,
							FUNCIONARIO.FUN_APELLIDOPATERNO PRIMER_APELLIDO,
							FUNCIONARIO.FUN_APELLIDOMATERNO SEGUNDO_APELLIDO,
							FUNCIONARIO.FUN_NOMBRE,
							FUNCIONARIO.FUN_NOMBRE2,
							FUNCIONARIO.FUN_CODIGO,
							GRADO.GRA_DESCRIPCION,
							MARCELO_FECHA.FECHA,
							IF(CARGO_FUNCIONARIO.UNI_AGREGADO,CONCAT(CARGO.CAR_DESCRIPCION,' A ', UNIDAD1.UNI_DESCRIPCION),CARGO.CAR_DESCRIPCION) CARGO
						FROM
						CARGO_FUNCIONARIO
						INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
						INNER JOIN FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
						INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO) AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
						INNER JOIN MARCELO_FECHA ON (CARGO_FUNCIONARIO.FECHA_DESDE <= MARCELO_FECHA.FECHA AND (CARGO_FUNCIONARIO.FECHA_HASTA > MARCELO_FECHA.FECHA OR CARGO_FUNCIONARIO.FECHA_HASTA IS NULL))
						INNER JOIN UNIDAD ON (CARGO_FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
						LEFT OUTER JOIN UNIDAD UNIDAD1 ON (CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD1.UNI_CODIGO)
						WHERE 
							MARCELO_FECHA.FECHA =  '{$fecha}'
							AND CARGO_FUNCIONARIO.UNI_CODIGO = {$codigoUnidad}
							AND CARGO_FUNCIONARIO.CAR_CODIGO NOT IN (3500)
						
						UNION
						
						SELECT 
							UNIDAD1.UNI_DESCRIPCION,
							FUNCIONARIO.FUN_APELLIDOPATERNO PRIMER_APELLIDO,
							FUNCIONARIO.FUN_APELLIDOMATERNO SEGUNDO_APELLIDO,
							FUNCIONARIO.FUN_NOMBRE,
							FUNCIONARIO.FUN_NOMBRE2,
							FUNCIONARIO.FUN_CODIGO,
							GRADO.GRA_DESCRIPCION,
							MARCELO_FECHA.FECHA,
							IF(CARGO_FUNCIONARIO.UNI_AGREGADO,CONCAT(CARGO.CAR_DESCRIPCION,' DESDE ',UNIDAD.UNI_DESCRIPCION),CARGO.CAR_DESCRIPCION) CARGO
						FROM
						CARGO_FUNCIONARIO
						INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
						INNER JOIN FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
						INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO) AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
						INNER JOIN MARCELO_FECHA ON (CARGO_FUNCIONARIO.FECHA_DESDE <= MARCELO_FECHA.FECHA AND (CARGO_FUNCIONARIO.FECHA_HASTA > MARCELO_FECHA.FECHA OR CARGO_FUNCIONARIO.FECHA_HASTA IS NULL))
						INNER JOIN UNIDAD ON (CARGO_FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
						LEFT OUTER JOIN UNIDAD UNIDAD1 ON (CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD1.UNI_CODIGO)
						WHERE 
							MARCELO_FECHA.FECHA =  '{$fecha}'
							AND CARGO_FUNCIONARIO.UNI_AGREGADO = {$codigoUnidad}
				) DATA
				ORDER BY DATA.UNI_DESCRIPCION, DATA.PRIMER_APELLIDO, DATA.SEGUNDO_APELLIDO, DATA.FUN_NOMBRE, DATA.FUN_NOMBRE2 ";
		//echo "sgfserterter" . $sql;
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while ($myrow = mysql_fetch_array($result)) {
			array_push($listaFuncionarios, array(
				"FUN_CODIGO" 			=> $myrow["FUN_CODIGO"],
				"GRA_DESCRIPCION" 		=> $myrow["GRA_DESCRIPCION"],
				"FUN_PRIMERAPELLIDO" 	=> $myrow["PRIMER_APELLIDO"],
				"FUN_SEGUNDOPELLIDO" 	=> $myrow["SEGUNDO_APELLIDO"],
				"FUN_NOMBRE" 			=> $myrow["FUN_NOMBRE"],
				"FUN_NOMBRE2" 			=> $myrow["FUN_NOMBRE2"],
				"FUN_NOMBRE_COMPLETO"   => $myrow["PRIMER_APELLIDO"] . " " . $myrow["SEGUNDO_APELLIDO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"UNI_DESCRIPCION" 		=> $myrow["UNI_DESCRIPCION"],
				"CARGO" 				=> $myrow["CARGO"],
				"FECHA" 				=> $myrow["FECHA"]
			));
			if ($myrow["EXISTE"] <> true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function buscarFuncionarioPorNombre($primerApellido, $segundoApellido, $primerNombre)
	{
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
		//AND FUNCIONARIO.FUN_APELLIDOMATERNO = '{$segundoApellido}'";
		if ($primerApellido <> "") 	$sql .= " AND FUNCIONARIO.FUN_APELLIDOPATERNO = '{$primerApellido}'";
		if ($segundoApellido <> "") $sql .= " AND FUNCIONARIO.FUN_APELLIDOMATERNO = '{$segundoApellido}'";
		if ($primerNombre <> "") 	$sql .= " AND FUNCIONARIO.FUN_NOMBRE = '{$primerNombre}'";
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while ($myrow = mysql_fetch_array($result)) {
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
			if ($myrow["EXISTE"] <> true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function buscarFuncionarioPorCodigoFuncionario($codigoFuncionario)
	{
		// ---- 1) Consulta principal (funcionario)
		$sql = "SELECT 
                FUNCIONARIO.FUN_CODIGO,
                FUNCIONARIO.FUN_RUT,
                GRADO.GRA_CODIGO,
                GRADO.GRA_DESCRIPCION,
                FUNCIONARIO.FUN_APELLIDOPATERNO,
                FUNCIONARIO.FUN_APELLIDOMATERNO,
                FUNCIONARIO.FUN_NOMBRE,
                FUNCIONARIO.FUN_NOMBRE2,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO,
                VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_DESCRIPCION
            FROM
                FUNCIONARIO
                INNER JOIN GRADO 
                    ON FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO
                   AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO
                LEFT OUTER JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS 
                    ON FUNCIONARIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO
            WHERE FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'";

		$result = $this->execute($this->conect(), $sql);
		$funcionario = mysql_fetch_array($result);
		if (!$funcionario) {
			mysql_close();
			return array("data" => false);
		}
		// ---- 2) Consulta secundaria (cargos del funcionario)
		$sqlCargos = "SELECT 
                    CARGO_FUNCIONARIO.CAR_CODIGO,
                    CARGO.CAR_DESCRIPCION AS CARGO,
                    CARGO_FUNCIONARIO.FECHA_DESDE AS FECHA_CARGO,
                    UNIDAD1.UNI_CODIGO,
                    UNIDAD1.UNI_DESCRIPCION AS UNIDAD_AGREGADO
                FROM
                    CARGO_FUNCIONARIO
                    INNER JOIN CARGO 
                        ON CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO
                    LEFT OUTER JOIN UNIDAD UNIDAD1 
                        ON CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD1.UNI_CODIGO
                WHERE
                    CARGO_FUNCIONARIO.FECHA_HASTA IS NULL
                    AND CARGO.CAR_CODIGO NOT IN (3500)
                    AND CARGO_FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'";

		$resultCargos = $this->execute($this->conect(), $sqlCargos);
		$cargos = array();
		while ($row = mysql_fetch_array($resultCargos)) {
			$cargos[] = array(
				"CAR_CODIGO"      => $row["CAR_CODIGO"],
				"CARGO"           => $row["CARGO"],
				"FECHA_CARGO"     => $row["FECHA_CARGO"],
				"UNI_CODIGO"      => $row["UNI_CODIGO"],
				"UNIDAD_AGREGADO" => $row["UNIDAD_AGREGADO"]
			);
		}
		mysql_close();
		// ---- 3) Consulta Terciaria (curso)
		$sqlCapacitacion = "SELECT 
						FUNCIONARIO.FUN_CODIGO,
						IFNULL(CAPACITACION.TIPO_CAPACITACION, 'SIN CURSO') AS CAPACITACION,
						CAPACITACION.NOTA_PROSERVIPOL,
						CAPACITACION.FECHA_CAPACITACION
						FROM FUNCIONARIO
						LEFT JOIN CAPACITACION ON CAPACITACION.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO 
						WHERE CAPACITACION.ACTIVO = 1
						AND FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'";

		$resultCapacitacion = $this->execute($this->conect(), $sqlCapacitacion);
		$capacitacion = array();
		while ($row = mysql_fetch_array($resultCapacitacion)) {
			$capacitacion[] = array(
				"CAPACITACION"      => $row["CAPACITACION"],
				"NOTA_PROSERVIPOL"  => $row["NOTA_PROSERVIPOL"],
				"FECHA_CAPACITACION"     => $row["FECHA_CAPACITACION"]
			);
		}
		mysql_close();
		// ---- 4) Retorno combinado
		$data = array(
			"FUN_CODIGO"         => $funcionario["FUN_CODIGO"],
			"FUN_RUT"            => $funcionario["FUN_RUT"],
			"GRA_CODIGO"         => $funcionario["GRA_CODIGO"],
			"GRA_DESCRIPCION"    => $funcionario["GRA_DESCRIPCION"],
			"FUN_PRIMERAPELLIDO" => $funcionario["FUN_APELLIDOPATERNO"],
			"FUN_SEGUNDOPELLIDO" => $funcionario["FUN_APELLIDOMATERNO"],
			"FUN_NOMBRE"         => $funcionario["FUN_NOMBRE"],
			"FUN_NOMBRE2"        => $funcionario["FUN_NOMBRE2"],
			"FUN_NOMBRE_COMPLETO" => $funcionario["FUN_APELLIDOPATERNO"] . " " . $funcionario["FUN_APELLIDOMATERNO"] . ", " .  $funcionario["FUN_NOMBRE"] . " " . $funcionario["FUN_NOMBRE2"],
			"UNI_CODIGO"         => $funcionario["UNI_CODIGO"],
			"UNI_DESCRIPCION"    => $funcionario["UNI_DESCRIPCION"],
			"CARGOS"              => $cargos, // <- array con  el cargo activo
			"CAPACITACION"       => $capacitacion // <- array datos capacitacion
		);
		return array("data" => $data);
	}
	//BUSCAR CARGO ACTUAL	
	function buscarCargoActualFuncionario($codigoFuncionario)
	{
		$sql = "SELECT 
				CARGO_FUNCIONARIO.FUN_CODIGO,
				CARGO_FUNCIONARIO.CAR_CODIGO,
				CARGO.CAR_DESCRIPCION AS CARGO,
				CARGO_FUNCIONARIO.FECHA_DESDE AS FECHA_CARGO,
				UNIDAD1.UNI_CODIGO,
				UNIDAD1.UNI_DESCRIPCION AS UNIDAD_AGREGADO
				FROM
				CARGO_FUNCIONARIO
				INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
				INNER JOIN FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
				LEFT OUTER JOIN UNIDAD UNIDAD1 ON (CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD1.UNI_CODIGO)
				WHERE
				CARGO_FUNCIONARIO.FECHA_HASTA IS NULL AND 
				CARGO.CAR_CODIGO NOT IN (3500) AND 
				CARGO_FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'";

		//echo $sql;
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while ($myrow = mysql_fetch_array($result)) {
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
			if ($myrow["EXISTE"] <> true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
	function serviciosFuncionarioPorCodigo($codigoFuncionario, $fechaDesde, $fechaHasta)
	{
		$sql = "SELECT 
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_CODIGO,
					SERVICIO.FECHA,
					UCASE(TIPO_SERVICIO.TSERV_DESCRIPCION) TIPOSERVICIO,
					UCASE(TIPO_EXTRAORDINARIO.TEXT_DESCRIPCION) TIPOEXTRAORDINARIO,
					SERVICIO.HORA_INICIO,
					SERVICIO.HORA_TERMINO,
					UNIDAD.UNI_DESCRIPCION DESTACAMENTO
				FROM SERVICIO 
				INNER JOIN FUNCIONARIO_SERVICIO ON (FUNCIONARIO_SERVICIO.UNI_CODIGO = SERVICIO.UNI_CODIGO AND FUNCIONARIO_SERVICIO.CORRELATIVO_SERVICIO = SERVICIO.CORRELATIVO_SERVICIO)
				INNER JOIN FUNCIONARIO ON (FUNCIONARIO.FUN_CODIGO = FUNCIONARIO_SERVICIO.FUN_CODIGO)
				INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)	AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
				INNER JOIN TIPO_SERVICIO ON (TIPO_SERVICIO.TSERV_CODIGO = SERVICIO.TSERV_CODIGO)
				LEFT OUTER JOIN TIPO_EXTRAORDINARIO ON (SERVICIO.TEXT_CODIGO = TIPO_EXTRAORDINARIO.TEXT_CODIGO)
				INNER JOIN UNIDAD ON (UNIDAD.UNI_CODIGO = SERVICIO.UNI_CODIGO)
				WHERE FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}' AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA";
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		$servicioRealizado = "";
		$fecha_actual = date("d-m-Y H:i:s");
		while ($myrow = mysql_fetch_array($result)) {
			if ($myrow["TIPOEXTRAORDINARIO"] == "") $servicioRealizado = utf8_encode($myrow["TIPOSERVICIO"]);
			else $servicioRealizado = utf8_encode($myrow["TIPOSERVICIO"]) . " (" . utf8_encode($myrow["TIPOEXTRAORDINARIO"]) . ")";
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"], //date('d-m-Y', strtotime($myrow["FECHA"])), 
				"tipoServicio" 				=> $servicioRealizado, //$myrow["TIPOSERVICIO"] . " (" . $myrow["TIPOEXTRAORDINARIO"] .")",
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> utf8_encode($myrow["DESTACAMENTO"]),
				"fechaHoraConsulta"			=> $fecha_actual
			));
			if ($myrow["EXISTE"] <> true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	/*funcion que busca servicios de uno o mas funcionarios*/
	function serviciosFuncionarioPorCodigo2($codigoFuncionario, $fechaDesde, $fechaHasta)
	{
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
					UCASE(TIPO_EXTRAORDINARIO.TEXT_DESCRIPCION) TIPOEXTRAORDINARIO,
					SERVICIO.HORA_INICIO,
					SERVICIO.HORA_TERMINO,
					UNIDAD.UNI_DESCRIPCION DESTACAMENTO
				FROM SERVICIO 
				INNER JOIN FUNCIONARIO_SERVICIO ON (FUNCIONARIO_SERVICIO.UNI_CODIGO = SERVICIO.UNI_CODIGO AND FUNCIONARIO_SERVICIO.CORRELATIVO_SERVICIO = SERVICIO.CORRELATIVO_SERVICIO)
				INNER JOIN FUNCIONARIO ON (FUNCIONARIO.FUN_CODIGO = FUNCIONARIO_SERVICIO.FUN_CODIGO)
				INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)	AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
				INNER JOIN TIPO_SERVICIO ON (TIPO_SERVICIO.TSERV_CODIGO = SERVICIO.TSERV_CODIGO)
				LEFT OUTER JOIN TIPO_EXTRAORDINARIO ON (SERVICIO.TEXT_CODIGO = TIPO_EXTRAORDINARIO.TEXT_CODIGO)
				INNER JOIN UNIDAD ON (UNIDAD.UNI_CODIGO = SERVICIO.UNI_CODIGO)
				WHERE FUNCIONARIO.FUN_CODIGO IN ({$codigos}) AND SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY SERVICIO.FECHA";
		//echo $sql;
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		$servicioRealizado = "";
		$fecha_actual = date("d-m-Y H:i:s");
		while ($myrow = mysql_fetch_array($result)) {
			if ($myrow["TIPOEXTRAORDINARIO"] == "") $servicioRealizado = utf8_encode($myrow["TIPOSERVICIO"]);
			else $servicioRealizado = utf8_encode($myrow["TIPOSERVICIO"]) . " (" . utf8_encode($myrow["TIPOEXTRAORDINARIO"]) . ")";
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fecha" 					=> $myrow["FECHA"], //date('d-m-Y', strtotime($myrow["FECHA"])), 
				"tipoServicio" 				=> $servicioRealizado, //$myrow["TIPOSERVICIO"] . " (" . $myrow["TIPOEXTRAORDINARIO"] .")",
				"horaInicio" 				=> $myrow["HORA_INICIO"],
				"horaTermino" 				=> $myrow["HORA_TERMINO"],
				"destacamento" 				=> utf8_encode($myrow["DESTACAMENTO"]),
				"fechaHoraConsulta"			=> $fecha_actual
			));
			if ($myrow["EXISTE"] <> true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function cargosFuncionarioPorCodigo($codigoFuncionario, $fechaDesde, $fechaHasta)
	{
		$sql = "SELECT 
					FUNCIONARIO.FUN_APELLIDOPATERNO,
					FUNCIONARIO.FUN_APELLIDOMATERNO,
					FUNCIONARIO.FUN_NOMBRE,
					FUNCIONARIO.FUN_NOMBRE2,
					GRADO.GRA_DESCRIPCION,
					FUNCIONARIO.FUN_CODIGO,
					MARCELO_FECHA.FECHA,
					IF(CARGO_FUNCIONARIO.UNI_AGREGADO,CONCAT(CARGO.CAR_DESCRIPCION,' A ', UNIDAD1.UNI_DESCRIPCION),CARGO.CAR_DESCRIPCION) CARGO,
					UNIDAD.UNI_DESCRIPCION
				FROM
					CARGO_FUNCIONARIO
					INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
					INNER JOIN FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
					INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO) AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
					INNER JOIN MARCELO_FECHA ON (CARGO_FUNCIONARIO.FECHA_DESDE <= MARCELO_FECHA.FECHA AND (CARGO_FUNCIONARIO.FECHA_HASTA > MARCELO_FECHA.FECHA OR CARGO_FUNCIONARIO.FECHA_HASTA IS NULL))
					INNER JOIN UNIDAD ON (CARGO_FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
					LEFT OUTER JOIN UNIDAD UNIDAD1 ON (CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD1.UNI_CODIGO)
				WHERE CARGO_FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}' AND MARCELO_FECHA.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY MARCELO_FECHA.FECHA";
		$sql = "SELECT
					 FUNCIONARIO.FUN_APELLIDOPATERNO
					,FUNCIONARIO.FUN_APELLIDOMATERNO
					,FUNCIONARIO.FUN_NOMBRE
					,FUNCIONARIO.FUN_NOMBRE2
					,GRADO.GRA_DESCRIPCION 
					,CARGO_FUNCIONARIO.FUN_CODIGO	
					,UNIDAD.UNI_DESCRIPCION
					,IF(CARGO_FUNCIONARIO.UNI_AGREGADO,CONCAT(CARGO.CAR_DESCRIPCION,' A ', UNIDAD_AGREGADO.UNI_DESCRIPCION),CARGO.CAR_DESCRIPCION) CARGO
					,CARGO_FUNCIONARIO.FECHA_DESDE
					,CARGO_FUNCIONARIO.FECHA_HASTA 
				FROM CARGO_FUNCIONARIO
				INNER JOIN FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
				INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO) AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
				INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
				INNER JOIN UNIDAD ON (CARGO_FUNCIONARIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
				LEFT OUTER JOIN UNIDAD UNIDAD_AGREGADO ON (CARGO_FUNCIONARIO.UNI_AGREGADO = UNIDAD_AGREGADO.UNI_CODIGO)
				WHERE 
					CARGO_FUNCIONARIO.FUN_CODIGO = '{$codigoFuncionario}'
				ORDER BY CARGO_FUNCIONARIO.CORRELATIVO_CARGOFUNCIONARIO";
		$result = $this->execute($this->conect(), $sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		$fecha_actual = date("d-m-Y H:i:s");
		while ($myrow = mysql_fetch_array($result)) {
			$cargo = preg_replace("[\n|\r|\n\r]", "", utf8_encode($myrow["CARGO"]));
			array_push($listaServicios, array(
				"nombreCompletoFuncionario"	=> $myrow["FUN_APELLIDOPATERNO"] . " " . $myrow["FUN_APELLIDOMATERNO"] . ", " .  $myrow["FUN_NOMBRE"] . " " . $myrow["FUN_NOMBRE2"],
				"gradoFuncionario" 			=> $myrow["GRA_DESCRIPCION"],
				"codigoFuncionario"			=> $myrow["FUN_CODIGO"],
				"fechaDesde" 				=> $myrow["FECHA_DESDE"],
				"fechaHasta" 				=> $myrow["FECHA_HASTA"],
				"cargo" 					=> $cargo,
				"destacamento" 				=> utf8_encode($myrow["UNI_DESCRIPCION"]),
				"fechaHoraConsulta"			=> $fecha_actual
			));
			if ($myrow["EXISTE"] <> true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
}
