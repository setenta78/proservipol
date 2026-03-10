<?php
// listar_estructura.php
// Compatible con PHP 5.1.2 y RHEL 4
// Solo texto plano, sin HTML, sin clases, sin funciones modernas

$root = dirname(__FILE__);
$excluir_ext = array('.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.bmp');

function listar($dir, $nivel = 0) {
    global $excluir_ext;
    
    $salida = '';
    $indent = str_repeat('  ', $nivel);
    
    if (!is_dir($dir)) {
        return $indent . "[ERROR] No es directorio: $dir\n";
    }

    $dh = opendir($dir);
    if (!$dh) {
        return $indent . "[NO ACCESO] $dir/\n";
    }

    // Leer todo primero para ordenar
    $entries = array();
    while (false !== ($entry = readdir($dh))) {
        if ($entry == '.' || $entry == '..') continue;
        $entries[] = $entry;
    }
    closedir($dh);

    sort($entries);

    // Separar directorios y archivos
    $dirs = array();
    $files = array();
    foreach ($entries as $e) {
        $full = $dir . '/' . $e;
        if (is_dir($full)) {
            $dirs[] = $e;
        } else {
            $files[] = $e;
        }
    }

    // Mostrar subdirectorios primero
    foreach ($dirs as $d) {
        $salida .= $indent . "📁 " . $d . "/\n";
        $salida .= listar($dir . '/' . $d, $nivel + 1);
    }

    // Mostrar archivos (filtrando imágenes)
    foreach ($files as $f) {
        $ext = strtolower(substr($f, strrpos($f, '.')));
        if (in_array($ext, $excluir_ext)) continue;
        $salida .= $indent . "📄 " . $f . "\n";
    }

    return $salida;
}

echo "🔍 Estructura del proyecto `/aplicativos-proservipol/` (imágenes excluidas)\n";
echo "============================================================\n";
echo listar($root);
echo "\n✅ Generado el " . date('Y-m-d H:i:s') . " en servidor (PHP " . phpversion() . ")\n";
?>