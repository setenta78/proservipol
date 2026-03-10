<?php
include_once("../../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbCamarasCorporales extends Conexion{
	function listaCamarasCorporales($data){
		$sql = "SELECT 
					V.VC_CODIGO CODIGO_CAMARA,
					MC.MVC_DESCRIPCION MARCA,
					MOV.MODVC_DESCRIPCION MODELO,
					V.VC_NRO_SERIE NRO_SERIE,
					V.VC_COD_EQUIPO_SAP COD_EQUIPO,
					E.EST_DESCRIPCION ESTADO,
					U.UNI_DESCRIPCION UNIDAD
				FROM VIDEOCAMARA V
				JOIN ESTADO_VIDEOCAMARA EV ON EV.VC_CODIGO = V.VC_CODIGO
				JOIN ESTADO E ON E.EST_CODIGO = EV.EST_CODIGO
				LEFT JOIN MARCA_VIDEOCAMARA MC ON MC.MVC_CODIGO = V.MVC_CODIGO
				LEFT JOIN MODELO_VIDEOCAMARA MOV ON MOV.MVC_CODIGO = V.MVC_CODIGO AND MOV.MODVC_CODIGO = V.MODVC_CODIGO
				LEFT JOIN UNIDAD U ON U.UNI_CODIGO = EV.UNI_AGREGADO
				WHERE EV.UNI_CODIGO = {$data['codigoUnidad']} AND EV.FECHA_HASTA IS NULL
				AND EV.EST_CODIGO NOT IN (3500,3600)
				ORDER BY {$data['columna']} {$data['ordenar']}";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaCamarasCorporales = array();
		$sinCamarasCorporales = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaCamarasCorporales, array(
    			"codigoCamara"	=> $myrow["CODIGO_CAMARA"],
    			"marca"			=> utf8_encode($myrow["MARCA"]),
   				"modelo"		=> utf8_encode($myrow["MODELO"]),
   				"nroSerie"		=> utf8_encode($myrow["NRO_SERIE"]),
				"codEquipo"		=> utf8_encode($myrow["COD_EQUIPO"]),
				"estado"		=> utf8_encode($myrow["ESTADO"]),
				"unidad"		=> utf8_encode($myrow["UNIDAD"]))
				);
			if($myrow["EXISTE"]<>true) $sinCamarasCorporales = false;
		}
		return $sinCamarasCorporales ? array("data" => false) : array("data" => $listaCamarasCorporales);
	}
		function listaCamarasCorporalesAgregadas($data){
		$sql = "SELECT 
					V.VC_CODIGO CODIGO_CAMARA,
					MC.MVC_DESCRIPCION MARCA,
					MOV.MODVC_DESCRIPCION MODELO,
					V.VC_NRO_SERIE NRO_SERIE,
					V.VC_COD_EQUIPO_SAP COD_EQUIPO,
					E.EST_DESCRIPCION ESTADO,
					U.UNI_DESCRIPCION UNIDAD
				FROM VIDEOCAMARA V
				JOIN ESTADO_VIDEOCAMARA EV ON EV.VC_CODIGO = V.VC_CODIGO
				JOIN ESTADO E ON E.EST_CODIGO = EV.EST_CODIGO
				LEFT JOIN MARCA_VIDEOCAMARA MC ON MC.MVC_CODIGO = V.MVC_CODIGO
				LEFT JOIN MODELO_VIDEOCAMARA MOV ON MOV.MVC_CODIGO = V.MVC_CODIGO AND MOV.MODVC_CODIGO = V.MODVC_CODIGO
				JOIN UNIDAD U ON U.UNI_CODIGO = EV.UNI_CODIGO
				WHERE EV.UNI_AGREGADO = {$data['codigoUnidad']} AND EV.FECHA_HASTA IS NULL
				ORDER BY {$data['columna']} {$data['ordenar']}";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaCamarasCorporales = array();
		$sinCamarasCorporales = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaCamarasCorporales, array(
				"codigoCamara"	=> $myrow["CODIGO_CAMARA"],
				"marca"			=> utf8_encode($myrow["MARCA"]),
				"modelo"		=> utf8_encode($myrow["MODELO"]),
				"nroSerie"		=> utf8_encode($myrow["NRO_SERIE"]),
				"codEquipo"		=> utf8_encode($myrow["COD_EQUIPO"]),
				"estado"		=> utf8_encode($myrow["ESTADO"]),
				"unidad"		=> utf8_encode($myrow["UNIDAD"]))
				);
			if($myrow["EXISTE"]<>true) $sinCamarasCorporales = false;
		}
		return $sinCamarasCorporales ? array("data" => false) : array("data" => $listaCamarasCorporales);
	}
	function buscarCamara($codigoCamara, $codigoEquipo){
		$buscarPor = ($codigoCamara) ? "V.VC_CODIGO = {$codigoCamara}" : "V.VC_COD_EQUIPO_SAP = {$codigoEquipo}";
		$sql = "SELECT 
					V.VC_CODIGO CODIGO_CAMARA,
					V.VC_COD_EQUIPO_SAP CODIGO_EQUIPO,
					V.VC_NRO_SERIE NRO_SERIE,
					V.MVC_CODIGO CODIGO_MARCA,
					M.MVC_DESCRIPCION DESC_MARCA,
					V.MODVC_CODIGO CODIGO_MODELO,
					MO.MODVC_DESCRIPCION DESC_MODELO,
					V.PREC_CODIGO CODIGO_ORIGEN,
					V.VC_COD_ACTIVO_SAP CODIGO_SAP,
					IFNULL(EV.EST_CODIGO,3500) CODIGO_ESTADO,
					IFNULL(E.EST_DESCRIPCION,'') DESC_ESTADO,
					EV.FECHA_DESDE FECHA,
					V.UNI_CODIGO CODIGO_UNIDAD,
					U.UNI_DESCRIPCION DESC_UNIDAD,
					EV.UNI_AGREGADO CODIGO_AGREGADO,
					A.UNI_DESCRIPCION DESC_AGREGADO
				FROM VIDEOCAMARA V
				LEFT JOIN MARCA_VIDEOCAMARA M ON M.MVC_CODIGO = V.MVC_CODIGO
				LEFT JOIN MODELO_VIDEOCAMARA MO ON MO.MVC_CODIGO = V.MVC_CODIGO AND MO.MODVC_CODIGO = V.MODVC_CODIGO
				LEFT JOIN ESTADO_VIDEOCAMARA EV ON EV.VC_CODIGO = V.VC_CODIGO AND EV.FECHA_HASTA IS NULL
				LEFT JOIN ESTADO E ON E.EST_CODIGO = EV.EST_CODIGO
				LEFT JOIN UNIDAD U ON U.UNI_CODIGO = V.UNI_CODIGO
				LEFT JOIN UNIDAD A ON A.UNI_CODIGO = EV.UNI_AGREGADO
				WHERE {$buscarPor}";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaFuncionarios = array();
		$sinFuncionario = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaFuncionarios, array(
				"CODIGO_CAMARA"		=> $myrow["CODIGO_CAMARA"],
				"CODIGO_EQUIPO"		=> $myrow["CODIGO_EQUIPO"],
				"NRO_SERIE"			=> $myrow["NRO_SERIE"],
				"CODIGO_MARCA"		=> $myrow["CODIGO_MARCA"],
				"DESC_MARCA"		=> $myrow["DESC_MARCA"],
				"CODIGO_MODELO"		=> $myrow["CODIGO_MODELO"],
				"DESC_MODELO"		=> $myrow["DESC_MODELO"],
				"CODIGO_ORIGEN"		=> $myrow["CODIGO_ORIGEN"],
				"CODIGO_SAP"		=> $myrow["CODIGO_SAP"],
				"CODIGO_ESTADO"		=> $myrow["CODIGO_ESTADO"],
				"DESC_ESTADO"		=> $myrow["DESC_ESTADO"],
				"FECHA"				=> $myrow["FECHA"],
				"CODIGO_UNIDAD"		=> $myrow["CODIGO_UNIDAD"],
				"DESC_UNIDAD"		=> $myrow["DESC_UNIDAD"],
				"CODIGO_AGREGADO"	=> $myrow["CODIGO_AGREGADO"],
				"DESC_AGREGADO"		=> $myrow["DESC_AGREGADO"]
			));
			if($myrow["EXISTE"]<>true) $sinFuncionario = false;
		}
		return $sinFuncionario ? array("data" => false) : array("data" => $listaFuncionarios);
	}
}
