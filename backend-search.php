<?php
session_start();
require_once 'middleware_auth.php';
require_once "queries/config.php";

$param_term = "'%" . $_REQUEST["term"] . "%'";

if (isset($param_term)) {
	// Prepare a select statement
	$sqlUnidad = "SELECT 
			  `UNIDAD`.`UNI_CODIGO`,
			  `UNIDAD`.`UNI_DESCRIPCION`,
			  `UNIDAD`.`UNI_MOSTRAR`
			FROM
			  `UNIDAD`
			WHERE
			  `UNIDAD`.`UNI_MOSTRAR` = 1 AND 
			  `UNIDAD`.`UNI_ACTIVO` = 1 AND 
			  `UNIDAD`.`UNI_DESCRIPCION` LIKE $param_term";
	// echo $sqlUnidad;
	$result = mysqli_query($link, $sqlUnidad);

	// Check number of rows in the result set
	if (mysqli_num_rows($result) > 0) {
		// Fetch result rows as an associative array
		while ($row = mysqli_fetch_array($result)) {

			echo "<p>" . $row["UNI_DESCRIPCION"] . " - COD: " . $row["UNI_CODIGO"] . "</p>";
		}
	} else {
		echo "<p>No se encontraron coincidencias</p>";
	}
}
