<?php
include("configProservipolHistorico.env.php");
$enviorment = new proservipol_2010_2013;
define("HOST" , $enviorment->getHost());
define("DB_USER" , $enviorment->getUser());
define("DB_PASS" , $enviorment->getPass());
define("DB" , $enviorment->getDB());