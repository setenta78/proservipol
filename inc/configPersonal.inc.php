<?php
// Incluir el archivo de entornos (con protección contra redeclaración)
include_once("config.env.php");

// Crear una instancia de la configuración para la base de datos de Personal
$envPersonal = new personal();

// Definir constantes con un prefijo único para evitar conflictos
define("HOST_PERSONAL", $envPersonal->getHost());
define("DB_USER_PERSONAL", $envPersonal->getUser());
define("DB_PASS_PERSONAL", $envPersonal->getPass());
define("DB_PERSONAL", $envPersonal->getDB());
?>