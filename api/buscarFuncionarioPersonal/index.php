<?php
/**
 * API: Buscar Funcionario en Personal
 * Busca primero en BD PROSERVIPOL, luego en BD Personal
 * Compatible con PHP 5.1.2 (usando mysql_* functions)
 */

// Iniciar sesión
session_start();

// Incluir librería JSON para PHP 5.1.2
require_once "../../inc/Services_JSON.php";
$json = new Services_JSON();

// Función de respaldo para json_encode (PHP 5.1.2 no lo tiene)
if (!function_exists('json_encode')) {
    function json_encode($data) {
        global $json;
        return $json->encode($data);
    }
}
if (!function_exists('json_decode')) {
    function json_decode($json_str, $assoc = false) {
        global $json;
        return $json->decode($json_str, $assoc);
    }
}

// Incluir middleware de autenticación
require_once(dirname(__FILE__) . '/../../middleware_auth.php');

// Incluir archivos de configuración
require_once(dirname(__FILE__) . '/../../inc/config.inc.php');

// Configurar cabeceras JSON
header('Content-Type: application/json; charset=utf-8');

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(array(
        'success' => false,
        'yaRegistrado' => false,
        'message' => 'Método no permitido'
    ));
    exit;
}

// Obtener código de funcionario
$codFuncionario = isset($_GET['codFuncionario']) ? trim($_GET['codFuncionario']) : '';

// CASO 1: Validar que exista código de funcionario
if (empty($codFuncionario)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(array(
        'success' => false,
        'yaRegistrado' => false,
        'message' => 'SE DEBE INGRESAR EL CODIGO DE FUNCIONARIO PARA PODER REALIZAR LA BUSQUEDA'
    ));
    exit;
}

try {
    // ========================================
    // PASO 1: Buscar en BD PROSERVIPOL
    // ========================================
    $connProservipol = mysql_connect(HOST, DB_USER, DB_PASS);
    
    if (!$connProservipol) {
        throw new Exception('Error de conexión con la base de datos PROSERVIPOL: ' . mysql_error());
    }
    
    if (!mysql_select_db(DB, $connProservipol)) {
        throw new Exception('Error al seleccionar BD PROSERVIPOL: ' . mysql_error());
    }
    
    mysql_query("SET NAMES 'utf8'", $connProservipol);
    
    // Buscar usuario en PROSERVIPOL (escapar para evitar SQL injection)
    $codFuncionarioEscaped = mysql_real_escape_string($codFuncionario, $connProservipol);
    
    // CORRECCIÓN CLAVE: Hacer JOIN con FUNCIONARIO para obtener nombre, apellidos y RUT
    $sqlProservipol = "SELECT 
        u.US_LOGIN,
        u.US_ACTIVO,
        f.FUN_NOMBRE,
        f.FUN_NOMBRE2,
        f.FUN_APELLIDOPATERNO,
        f.FUN_APELLIDOMATERNO,
        f.FUN_RUT,
        g.GRA_DESCRIPCION,
        un.UNI_DESCRIPCION,
        un.UNI_CODIGO,
        tus.TUS_DESCRIPCION,
        tus.TUS_CODIGO
    FROM USUARIO u
    LEFT JOIN FUNCIONARIO f ON u.FUN_CODIGO = f.FUN_CODIGO
    LEFT JOIN GRADO g ON f.GRA_CODIGO = g.GRA_CODIGO AND f.ESC_CODIGO = g.ESC_CODIGO
    LEFT JOIN UNIDAD un ON u.UNI_CODIGO = un.UNI_CODIGO
    LEFT JOIN TIPO_USUARIO tus ON u.TUS_CODIGO = tus.TUS_CODIGO
    WHERE u.US_LOGIN = '$codFuncionarioEscaped'
    LIMIT 1";
    
    $resultProservipol = mysql_query($sqlProservipol, $connProservipol);
    
    if (!$resultProservipol) {
        throw new Exception('Error en consulta PROSERVIPOL: ' . mysql_error($connProservipol));
    }
    
    if (mysql_num_rows($resultProservipol) > 0) {
        // Usuario encontrado en PROSERVIPOL
        $usuario = mysql_fetch_assoc($resultProservipol);
        
        // CASO 2: Usuario está ACTIVO
        if ($usuario['US_ACTIVO'] == '1') {
            mysql_close($connProservipol);
            
            header('HTTP/1.1 200 OK');
            echo json_encode(array(
                'success' => false,
                'yaRegistrado' => true,
                'usuarioInactivo' => false,
                'message' => 'EL FUNCIONARIO YA ESTÁ REGISTRADO COMO USUARIO DEL SISTEMA PROSERVIPOL',
                'data' => array(
                    'codigo' => $usuario['US_LOGIN'],
                    'rut' => $usuario['FUN_RUT'],
                    'nombre' => trim($usuario['FUN_NOMBRE'] . ' ' . $usuario['FUN_NOMBRE2']),
                    'apellidoPaterno' => $usuario['FUN_APELLIDOPATERNO'],
                    'apellidoMaterno' => $usuario['FUN_APELLIDOMATERNO'],
                    'grado' => $usuario['GRA_DESCRIPCION'],
                    'unidad' => $usuario['UNI_DESCRIPCION'],
                    'perfil' => $usuario['TUS_DESCRIPCION']
                )
            ));
            exit;
        }
        
        // CASO 3: Usuario existe pero está INACTIVO
        if ($usuario['US_ACTIVO'] == '0') {
            mysql_close($connProservipol);
            
            header('HTTP/1.1 200 OK');
            echo json_encode(array(
                'success' => false,
                'yaRegistrado' => false,
                'usuarioInactivo' => true,
                'message' => 'EL FUNCIONARIO YA EXISTE PERO ESTÁ INACTIVO. ¿DESEA REACTIVARLO?',
                'data' => array(
                    'codigo' => $usuario['US_LOGIN'],
                    'rut' => $usuario['FUN_RUT'],
                    'nombre' => trim($usuario['FUN_NOMBRE'] . ' ' . $usuario['FUN_NOMBRE2']),
                    'apellidoPaterno' => $usuario['FUN_APELLIDOPATERNO'],
                    'apellidoMaterno' => $usuario['FUN_APELLIDOMATERNO'],
                    'grado' => $usuario['GRA_DESCRIPCION'],
                    'unidad' => $usuario['UNI_DESCRIPCION'],
                    'perfil' => $usuario['TUS_DESCRIPCION']
                )
            ));
            exit;
        }
    }
    
    // ========================================
    // PASO 2: Buscar en BD PERSONAL
    // ========================================
    // Determinar si estamos en desarrollo (usa PERSONAL_MOCK) o producción (usa personal_view)
    $checkMock = mysql_query("SHOW TABLES LIKE 'PERSONAL_MOCK'", $connProservipol);
    $useMock = (mysql_num_rows($checkMock) > 0);

    if ($useMock) {
        // === DESARROLLO: Usar la misma conexión a proservipol_test y la tabla PERSONAL_MOCK ===
        $sqlPersonal = "SELECT 
            PEFBCOD,
            PEFBRUT,
            PEFBNOM1,
            PEFBNOM2,
            PEFBAPEP,
            PEFBAPEM,
            PEFBGRA,
            GRADO_DESCRIPCION,
            REPARTICION_DESC,
            REPARTICION_DEP
        FROM PERSONAL_MOCK
        WHERE PEFBCOD = '$codFuncionarioEscaped'
        LIMIT 1";

        $resultPersonal = mysql_query($sqlPersonal, $connProservipol);

        if (!$resultPersonal) {
            throw new Exception('Error en consulta PERSONAL_MOCK: ' . mysql_error($connProservipol));
        }

    } else {
        // === PRODUCCIÓN: Conectarse a la base de datos de Personal real ===
        require_once(dirname(__FILE__) . '/../../inc/configPersonal.inc.php');
        
        $connPersonal = mysql_connect(HOST_PERSONAL, DB_USER_PERSONAL, DB_PASS_PERSONAL, true);
        if (!$connPersonal) {
            throw new Exception('Error de conexión con la base de datos de Personal: ' . mysql_error());
        }
        
        if (!mysql_select_db(DB_PERSONAL, $connPersonal)) {
            throw new Exception('Error al seleccionar BD Personal: ' . mysql_error());
        }
        
        mysql_query("SET NAMES 'utf8'", $connPersonal);
        
        $sqlPersonal = "SELECT 
            PEFBCOD,
            PEFBRUT,
            PEFBNOM1,
            PEFBNOM2,
            PEFBAPEP,
            PEFBAPEM,
            PEFBGRA,
            GRADO_DESCRIPCION,
            REPARTICION_DESC,
            REPARTICION_DEP
        FROM personal_view
        WHERE PEFBCOD = '$codFuncionarioEscaped'
        LIMIT 1";
        
        $resultPersonal = mysql_query($sqlPersonal, $connPersonal);
        
        if (!$resultPersonal) {
            throw new Exception('Error en consulta Personal: ' . mysql_error($connPersonal));
        }
    }

    if (mysql_num_rows($resultPersonal) > 0) {
        // CASO 4: Funcionario encontrado en BD Personal
        $funcionario = mysql_fetch_assoc($resultPersonal);
        
        $funcionarioData = array(
            'codigo' => $funcionario['PEFBCOD'],
            'rut' => $funcionario['PEFBRUT'],
            'nombre' => trim($funcionario['PEFBNOM1'] . ' ' . $funcionario['PEFBNOM2']),
            'apellidoPaterno' => $funcionario['PEFBAPEP'],
            'apellidoMaterno' => $funcionario['PEFBAPEM'],
            'grado' => $funcionario['GRADO_DESCRIPCION'],
            'unidad' => $funcionario['REPARTICION_DESC'],
            'departamento' => $funcionario['REPARTICION_DEP']
        );
        
        $encontrado = true;
        $origen = $useMock ? 'PERSONAL_MOCK' : 'PERSONAL';
        
    } else {
        // CASO 5: NO encontrado en ninguna base de datos
        if (!$useMock && isset($connPersonal)) {
            mysql_close($connPersonal);
        }
        mysql_close($connProservipol);
        
        header('HTTP/1.1 404 Not Found');
        echo json_encode(array(
            'success' => false,
            'yaRegistrado' => false,
            'usuarioInactivo' => false,
            'message' => 'NO EXISTE FUNCIONARIO PARA EL CODIGO INDICADO'
        ));
        exit;
    }

    // Cerrar conexión de Personal solo si fue abierta (producción)
    if (!$useMock && isset($connPersonal)) {
        mysql_close($connPersonal);
    }
    
    // ========================================
    // PASO 3: Verificar curso PROSERVIPOL
    // ========================================
    $cursoInfo = array(
        'tieneCurso' => false,
        'mensaje' => 'EL USUARIO NO TIENE EL CURSO PROSERVIPOL APROBADO'
    );
    
    // Verificar si existe la tabla de cursos
    $checkTableCursos = mysql_query("SHOW TABLES LIKE 'CAPACITACION'", $connProservipol);
    
    if (mysql_num_rows($checkTableCursos) > 0) {
        // Buscar en tabla de cursos
        $sqlCurso = "SELECT 
            FECHA_CAPACITACION,
            NOTA_PROSERVIPOL
        FROM CAPACITACION
        WHERE FUN_CODIGO = '$codFuncionarioEscaped'
        AND ACTIVO = 1
        AND TIPO_CAPACITACION = 'CURSO PROSERVIPOL'
        ORDER BY FECHA_CAPACITACION DESC
        LIMIT 1";
        
        $resultCurso = mysql_query($sqlCurso, $connProservipol);
        
        if ($resultCurso && mysql_num_rows($resultCurso) > 0) {
            $curso = mysql_fetch_assoc($resultCurso);
            if (!empty($curso['FECHA_CAPACITACION'])) {
                $fechaAprobacion = date('d/m/Y', strtotime($curso['FECHA_CAPACITACION']));
                $cursoInfo = array(
                    'tieneCurso' => true,
                    'fechaAprobacion' => $fechaAprobacion,
                    'nota' => $curso['NOTA_PROSERVIPOL'],
                    'mensaje' => 'EL FUNCIONARIO TIENE EL CURSO APROBADO CON FECHA ' . $fechaAprobacion
                );
            }
        }
    }
    
    mysql_close($connProservipol);
    
    // ========================================
    // RESPUESTA EXITOSA
    // ========================================
    header('HTTP/1.1 200 OK');
    echo json_encode(array(
        'success' => true,
        'yaRegistrado' => false,
        'usuarioInactivo' => false,
        'message' => 'Funcionario encontrado',
        'origen' => $origen,
        'data' => $funcionarioData,
        'curso' => $cursoInfo
    ));
    
} catch (Exception $e) {
    // CASO 6: Error de conexión o excepción
    error_log("Error en buscarFuncionarioPersonal: " . $e->getMessage());
    
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(array(
        'success' => false,
        'yaRegistrado' => false,
        'usuarioInactivo' => false,
        'message' => 'Error al buscar funcionario: ' . $e->getMessage()
    ));
}
?>