<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbUsuario extends Conexion{
	function autentificacion($data){
		$sql = "SELECT
					F.FUN_RUT FUN_RUT
				FROM FUNCIONARIO F
				JOIN USUARIO U ON U.FUN_CODIGO = F.FUN_CODIGO
				WHERE F.FUN_RUT = '{$data['user']}'";
		//echo $sql;
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$datosUsuario	= array();
		$sinUsuario = true;
		while($myrow = mysql_fetch_array($result)){
			if($myrow["EXISTE"]<>true) $sinUsuario = false;
		}
		return $sinUsuario ? array("data" => false) : array("success" => array(
																		"access_token" => bin2hex(rand(0,1048)),
																		"token_type" => "auth",
																		"expires_at" => date("Y-m-d H:i:s")));
	}
}
