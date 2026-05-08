<?php
class ConexionDBProservipolHistorico{
	function conect(){
		$conect = mysqli_connect(HOST, DB_USER, DB_PASS,DB);
		if(mysqli_connect_errno()){
			print_r('conexion fallida: '. mysqli_connect_error());
			exit();
		}
		return $conect;
	}
	function execute($con, $query){
		$result = mysqli_query($con, $query);
		if(!$result){
			echo "El query $query es invalido". mysqli_error();
			exit();
		}
		return $result;
	}
	function myrows($result){
		return mysqli_fetch_row($result);
	}
	function desconect($con){
		mysqli_close($con);
	}
}
?>