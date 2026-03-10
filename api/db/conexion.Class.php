<?php
/**
 * Clase de conexión a base de datos MySQL (compatible con PHP 5.1.2)
 * Conecta a la BD de desarrollo (proservipol_test) donde está PERSONAL_MOCK
 */
class Conexion
{
    /**
     * Establece conexión con la base de datos proservipol_test
     */
    function conect()
    {
        // Credenciales de la BD proservipol_test (donde está PERSONAL_MOCK)
        $host = "172.21.111.67";
        $user = "cgonzalez";
        $pass = "cgonzalez2016";
        $db   = "proservipol_test";
        
        $conn = mysql_connect($host, $user, $pass);
        
        if (!$conn) {
            throw new Exception("Error de conexión a BD proservipol_test: " . mysql_error());
        }
        
        if (!mysql_select_db($db, $conn)) {
            throw new Exception("Error al seleccionar BD proservipol_test: " . mysql_error());
        }
        
        // Establecer charset UTF-8
        mysql_query("SET NAMES 'utf8'", $conn);
        
        return $conn;
    }
    
    /**
     * Ejecuta una consulta SQL
     */
    function execute($conn, $sql)
    {
        $result = mysql_query($sql, $conn);
        
        if (!$result) {
            throw new Exception("Error en consulta SQL: " . mysql_error($conn));
        }
        
        return $result;
    }
    
    /**
     * Cierra la conexión
     */
    function desconect()
    {
        // mysql_close() se llama automáticamente al finalizar el script
        return true;
    }
}
?>