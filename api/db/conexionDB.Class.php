<?
class ConexionDB{
	var $link_id;
	function conect(){
		$conect = @mysql_connect(HOST_2014_2018, DB_USER_2014_2018, DB_PASS_2014_2018);
		while($conect != 1 and $i < 8){
			$conect = mysql_connect(HOST_2014_2018, DB_USER_2014_2018, DB_PASS_2014_2018);
			$i++;
		}
		if(!$conect){
			exit;
		} else $this->link_id = $conect;
		mysql_select_db (DB_2014_2018);
		$db=@mysql_select_db(DB_2014_2018,$conect);
		if(!$db){
			exit;
		}else return $conect;
	}
	function execute($conn,$query){
		$result = mysql_query($query,$conn);
		if(!$result){
			//echo "El query $query es invalido". mysql_error();
			header("HTTP/1.0 402 Precondition Failed");
			echo json_encode(array("message" => "Error en la consulta.",
								 						 "code"		 => 402));
			exit;
		}
		return $result;
	}
	function myrows($result){
		return mysql_fetch_array($result);
	}
	function desconect(){
		$DESCONN= @mysql_close($this->link_id);
	}
}
?>