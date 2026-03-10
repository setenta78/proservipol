<?php
function rules()
{
	$user		= $_POST['rut'];
	$password	= $_POST['password'];
	$message = array();
	(isset($user) === false || $user === "") ? $message = $message + array("rut" => "Se requiere el usuario") : null;
	(isset($password) === false || $password === "") ? $message = $message + array("password" => "Se requiere la contraseña") : null;
	if (count($message) > 0) {
		header("HTTP/1.0 412 Precondition Failed");
		return array("error" => $message, "code" => "412");
	} else {
		header("HTTP/1.1 200 OK");
		return array(
			"user" 	=> $user,
			"password" 	=> $password
		);
	}
}
