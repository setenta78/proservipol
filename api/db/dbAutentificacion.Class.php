<?php
include("conexion.Class.php");
header('Content-Type: application/json; charset=UTF-8');
Class dbUsuario extends Conexion{
	function autentificacion($user, $pass){		
		if($user==="API"&$pass==="PASS"){
			return array("User"		=> "API",
								   "Token"	=> bin2hex(openssl_random_pseudo_bytes(64)));
		}
		else{
			return array("Error" 			 => "A1",
									 "Descripcion" => "Error de autentificacion");
		}
	}
}
