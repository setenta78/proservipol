<?php
include_once("../inc/config.inc.php");
include_once("conexion.Class.php");
Class dbEscalafonGrado extends Conexion{
	function listaEscalafones(){
		$sql = "SELECT E.ESC_CODIGO
				FROM ESCALAFON E";
		$result = $this->execute($this->conect(),$sql);
		mysql_close();
		$listaEscalafones = array();
		$sinServicio = true;
		while($myrow = mysql_fetch_array($result)){
			array_push($listaEscalafones, array(
				"codEscalafon" => utf8_encode($myrow["ESC_CODIGO"])
			));
		}
		return array("data" => $listaEscalafones);
	}
}
