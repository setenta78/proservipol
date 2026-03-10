<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbServicios extends Conexion{
	function listaServiciosExtraordinarios($codigoUnidad, $fechaDesde, $fechaHasta){
		$sql = "SELECT 
				  V.ZONA_DESCRIPCION ZonaServicio,
				  V.PREFECTURA_DESCRIPCION PrefecturaServicio,
				  V.DEPENDIENTE_DESCRIPCION ComisariaServicio,
				  V.UNI_DESCRIPCION AS DestacamentoServicio,
				  UCASE(T.TSERV_DESCRIPCION) servicioRegistrado,
				  UCASE(TE.TEXT_DESCRIPCION) servicioExtraordinario,
				  UCASE(S.DESCRIPCION_OTRO_EXTRAORDINARIO) otroServicioExtraordinario,
				  S.FECHA fechaServicioRegistrado,
				  S.HORA_INICIO horaInicioServicioRegistrado,
				  S.HORA_TERMINO horaTerminoServicioRegistrado,
				  IF(S.HORA_TERMINO>S.HORA_INICIO,
					SEC_TO_TIME(TIME_TO_SEC(S.HORA_TERMINO)-TIME_TO_SEC(S.HORA_INICIO)),
					SEC_TO_TIME((TIME_TO_SEC(S.HORA_TERMINO)+86400)-TIME_TO_SEC(S.HORA_INICIO))
				  ) duracionServicioRegistrado,
				  F.FUN_CODIGO codigoFuncionarioRegistrado,
				  F.FUN_RUT rutFuncionarioRegistado,
				  UCASE(F.FUN_APELLIDOPATERNO) primerApFuncionarioRegistrado,
				  UCASE(F.FUN_APELLIDOMATERNO) sgdoApFuncionarioRegistrado,
				  UCASE(F.FUN_NOMBRE) primerNombreFuncionarioRegistrado,  
				  UCASE(F.FUN_NOMBRE2) sgdoNombreFuncionarioRegistrado,
				  G.GRA_DESCRIPCION gradoFuncionarioRegistrado,
				  G.ESC_CODIGO codigoEscalafon,
				  G.GRA_CODIGO codigoGrado,
				  UCASE(IF(VE.VEH_CODIGO,VE.VEH_SIGLA_INSTITUCIONAL,'SIN VEHICULO')) siglaVehiculoRegistrado,
				  UCASE(IF(VE.VEH_CODIGO,MVE.MVEH_DESCRIPCION,'SIN VEHICULO')) marcaVehiculoRegistrado,
				  UCASE(IF(VE.VEH_CODIGO,MOVE.MODVEH_DESCRIPCION,'SIN VEHICULO')) modeloVehiculoRegistrado
				FROM SERVICIO S
				JOIN TIPO_SERVICIO T ON (S.TSERV_CODIGO = T.TSERV_CODIGO AND T.TSERV_CODIGO = 1100)
				JOIN TIPO_EXTRAORDINARIO TE ON (TE.TEXT_CODIGO = S.TEXT_CODIGO)
				JOIN FUNCIONARIO_SERVICIO FS ON (S.UNI_CODIGO = FS.UNI_CODIGO AND S.CORRELATIVO_SERVICIO = FS.CORRELATIVO_SERVICIO)
				JOIN FUNCIONARIO F ON (FS.FUN_CODIGO = F.FUN_CODIGO)
				JOIN GRADO G ON (F.ESC_CODIGO = G.ESC_CODIGO AND F.GRA_CODIGO = G.GRA_CODIGO)
				LEFT JOIN VISTA_ARBOL_UNIDADES_NACIONAL V ON (S.UNI_CODIGO = V.UNI_CODIGO)
				LEFT JOIN FUNCIONARIO_VEHICULO FV ON (FV.FUN_UNI_CODIGO = S.UNI_CODIGO AND FV.FUN_CORRELATIVO_SERVICIO = S.CORRELATIVO_SERVICIO AND FV.FUN_CODIGO = F.FUN_CODIGO)
				LEFT JOIN VEHICULO VE ON (VE.VEH_CODIGO = FV.VEH_CODIGO)
				LEFT JOIN MARCA_VEHICULO MVE ON (MVE.MVEH_CODIGO = VE.MVEH_CODIGO)
				LEFT JOIN MODELO_VEHICULO MOVE ON (MOVE.MODVEH_CODIGO = VE.MODVEH_CODIGO AND MOVE.MVEH_CODIGO = VE.MVEH_CODIGO)
				WHERE S.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				AND S.UNI_CODIGO = {$codigoUnidad}	";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
			"ZonaServicio" 							=> $myrow["ZonaServicio"],
			"PrefecturaServicio"					=> $myrow["PrefecturaServicio"],
			"ComisariaServicio" 					=> $myrow["ComisariaServicio"],
			"DestacamentoServicio" 					=> $myrow["DestacamentoServicio"],
			"servicioRegistrado" 					=> $myrow["servicioRegistrado"],
			"servicioExtraordinario" 				=> $myrow["servicioExtraordinario"],
			"otroServicioExtraordinario" 			=> $myrow["otroServicioExtraordinario"],
			"fechaServicioRegistrado" 				=> $myrow["fechaServicioRegistrado"],
			"horaInicioServicioRegistrado" 			=> $myrow["horaInicioServicioRegistrado"],
			"horaTerminoServicioRegistrado" 		=> $myrow["horaTerminoServicioRegistrado"],
			"duracionServicioRegistrado" 			=> $myrow["duracionServicioRegistrado"],
			"codigoFuncionarioRegistrado" 			=> $myrow["codigoFuncionarioRegistrado"],
			"rutFuncionarioRegistado" 				=> $myrow["rutFuncionarioRegistado"],
			"primerApFuncionarioRegistrado" 		=> $myrow["primerApFuncionarioRegistrado"],
			"sgdoApFuncionarioRegistrado" 			=> $myrow["sgdoApFuncionarioRegistrado"],
			"primerNombreFuncionarioRegistrado" 	=> $myrow["primerNombreFuncionarioRegistrado"],
			"sgdoNombreFuncionarioRegistrado" 		=> $myrow["sgdoNombreFuncionarioRegistrado"],
			"codigoEscalafonFuncionarioRegistrado" 	=> $myrow["codigoEscalafon"],
			"codigoGradoFuncionarioRegistrado" 		=> $myrow["codigoGrado"],
			"gradoFuncionarioRegistrado" 			=> $myrow["gradoFuncionarioRegistrado"],
			"siglaVehiculoRegistrado" 				=> $myrow["siglaVehiculoRegistrado"],
			"marcaVehiculoRegistrado" 				=> $myrow["marcaVehiculoRegistrado"],
			"modeloVehiculoRegistrado" 				=> $myrow["modeloVehiculoRegistrado"]
			));
			if($myrow["EXISTE"]<>true) $sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function serviciosFuncionarioPorRut($RutFuncionario, $fechaDesde, $fechaHasta){
		$sql = "SELECT 
					F.FUN_RUT RUT,
					E.ESC_DESCRIPCION ESCALAFON,
					G.GRA_DESCRIPCION GRADO,
					T.TSERV_DESCRIPCION SERVICIO,
					S.FECHA,
					S.HORA_INICIO INICIO,
					S.HORA_TERMINO TERMINO,
					V.UNI_DESCRIPCION UNIDAD
				FROM SERVICIO S
				JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS V ON V.UNI_CODIGO = S.UNI_CODIGO
				JOIN FUNCIONARIO_SERVICIO FS ON FS.UNI_CODIGO = S.UNI_CODIGO AND FS.CORRELATIVO_SERVICIO = S.CORRELATIVO_SERVICIO
				JOIN TIPO_SERVICIO T ON T.TSERV_CODIGO = S.TSERV_CODIGO
				JOIN FUNCIONARIO F ON F.FUN_CODIGO = FS.FUN_CODIGO
				JOIN GRADO G ON G.ESC_CODIGO = F.ESC_CODIGO AND G.GRA_CODIGO = F.GRA_CODIGO
				JOIN ESCALAFON E ON E.ESC_CODIGO = F.ESC_CODIGO
				WHERE F.FUN_RUT = '{$RutFuncionario}'
				AND S.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}'
				ORDER BY
				S.FECHA,S.HORA_INICIO ASC
				LIMIT 1000";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
				"rutFuncionario"			=> $myrow["RUT"],
				"escalafonFuncionario"		=> $myrow["ESCALAFON"],
				"gradoFuncionario"			=> $myrow["GRADO"],
				"tipoServicio"				=> utf8_encode($myrow["SERVICIO"]),
				"fechaServicio"				=> $myrow["FECHA"],
				"horaInicio"				=> $myrow["INICIO"],
				"horaTermino"				=> $myrow["TERMINO"],
				"unidad"					=> utf8_encode($myrow["UNIDAD"])
			));
			$sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
	function serviciosPorRangoFecha($fechaDesde, $fechaHasta){
		$sql = "SELECT 
					 FUNCIONARIO.FUN_RUT
					,VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.ZONA_DESCRIPCION
					,VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.PREFECTURA_DESCRIPCION
					,VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.COMISARIA_DESCRIPCION
					,UNIDAD.UNI_DESCRIPCION
					,FUNCIONARIO_SERVICIO.FUN_CODIGO
					,GRADO.GRA_DESCRIPCION GRADO
					,FUNCIONARIO.FUN_APELLIDOPATERNO
					,FUNCIONARIO.FUN_APELLIDOMATERNO
					,FUNCIONARIO.FUN_NOMBRE
					,FUNCIONARIO.FUN_NOMBRE2
					,SERVICIO.FECHA
					,CARGO.CAR_DESCRIPCION
					,IF(CARGO_FUNCIONARIO.UNI_AGREGADO,UNIDAD_ORIGEN.UNI_DESCRIPCION,'') UNIDAD_ORIGEN
					,UCASE(TIPO_SERVICIO.TSERV_DESCRIPCION) SERVICIO
					,SERVICIO.HORA_INICIO
					,SERVICIO.HORA_TERMINO
				FROM SERVICIO
				INNER JOIN FUNCIONARIO_SERVICIO ON (SERVICIO.UNI_CODIGO = FUNCIONARIO_SERVICIO.UNI_CODIGO AND SERVICIO.CORRELATIVO_SERVICIO = FUNCIONARIO_SERVICIO.CORRELATIVO_SERVICIO)
				INNER JOIN FUNCIONARIO ON (FUNCIONARIO_SERVICIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO)
				INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO AND FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
				INNER JOIN TIPO_SERVICIO ON (SERVICIO.TSERV_CODIGO = TIPO_SERVICIO.TSERV_CODIGO)
				INNER JOIN UNIDAD ON (SERVICIO.UNI_CODIGO = UNIDAD.UNI_CODIGO)
				INNER JOIN CARGO_FUNCIONARIO ON (CARGO_FUNCIONARIO.FUN_CODIGO = FUNCIONARIO.FUN_CODIGO AND (CARGO_FUNCIONARIO.FECHA_DESDE <= SERVICIO.FECHA AND (CARGO_FUNCIONARIO.FECHA_HASTA > SERVICIO.FECHA OR CARGO_FUNCIONARIO.FECHA_HASTA IS NULL)))
				INNER JOIN CARGO ON (CARGO_FUNCIONARIO.CAR_CODIGO = CARGO.CAR_CODIGO)
				LEFT OUTER JOIN UNIDAD UNIDAD_ORIGEN ON (CARGO_FUNCIONARIO.UNI_CODIGO = UNIDAD_ORIGEN.UNI_CODIGO)
				LEFT OUTER JOIN VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS ON (SERVICIO.UNI_CODIGO = VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS.UNI_CODIGO)
				WHERE SERVICIO.FECHA BETWEEN '{$fechaDesde}' AND '{$fechaHasta}' limit 34999, 35000";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaServicios = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaServicios, array(
				"rutFuncionario"				=> $myrow["FUN_RUT"],
				"zona"							=> $myrow["ZONA_DESCRIPCION"],
				"prefectura"					=> $myrow["PREFECTURA_DESCRIPCION"],
				"comisaria"						=> $myrow["COMISARIA_DESCRIPCION"],
				"destacamento"					=> $myrow["UNI_DESCRIPCION"],
				"codigoFuncionario"				=> $myrow["FUN_CODIGO"],
				"primerApellidoFuncionario"		=> $myrow["FUN_APELLIDOPATERNO"],
				"segundoApellidoFuncionario"	=> $myrow["FUN_APELLIDOMATERNO"],
				"primerNombreFuncionario"		=> $myrow["FUN_NOMBRE"],
				"segunodNOmbreFuncionario"		=> $myrow["FUN_NOMBRE2"],
				"gradoFuncionario"				=> $myrow["GRADO"],
				"fechaServicio"					=> $myrow["FECHA"],
				"cargoFuncionario"				=> $myrow["CAR_DESCRIPCION"],
				"unidadOrigenFuncionario"		=> $myrow["UNIDAD_ORIGEN"],
				"servicioRegistrado"			=> utf8_encode($myrow["SERVICIO"]),
				"horaInicio"					=> $myrow["HORA_INICIO"],
				"horaTermino"					=> $myrow["HORA_TERMINO"]
			));
			$sinServicio = false;
		}
		return $sinServicio ? array("data" => false) : array("data" => $listaServicios);
	}
}