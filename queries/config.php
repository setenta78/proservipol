<?php
// Mostrar errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración BD para PROSERVIPOL_TEST - CONEXIÓN DIRECTA
$db_server = '172.21.111.67';
$db_username = 'cgonzalez';
$db_password = 'cgonzalez2016';
$db_name = 'proservipol_test';

// 🔴 PHP 5.1.2 - SOLO mysql_* (mysqli no existe)
$link = mysql_connect($db_server, $db_username, $db_password);
if (!$link) {
    die("ERROR: No se pudo conectar al servidor MySQL. " . mysql_error());
}

if (!mysql_select_db($db_name, $link)) {
    die("ERROR: No se pudo seleccionar la base de datos 'proservipol_test'. " . mysql_error());
}

// ⚡ Configurar charset para caracteres especiales (ñ, tildes)
mysql_query("SET NAMES 'utf8'", $link);
mysql_query("SET CHARACTER SET utf8", $link);

// ⚠️ NO definir funciones personalizadas aquí.
// El sistema debe usar mysql_query($sql, $link) directamente.
?>