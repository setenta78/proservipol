<?php
/**
 * Función personalizada para json_encode (PHP 5.1.2 no tiene json_encode)
 */
if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch (gettype($data)) {
            case 'boolean':
                return $data ? 'true' : 'false';
            case 'integer':
            case 'double':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            case 'object':
                $vars = get_object_vars($data);
                $output = array();
                foreach ($vars as $key => $value) {
                    $output[] = json_encode($key) . ':' . json_encode($value);
                }
                return '{' . implode(',', $output) . '}';
            default:
                return 'null';
        }
    }
}

/**
 * Función personalizada para json_decode (PHP 5.1.2 no tiene json_decode)
 */
if (!function_exists('json_decode')) {
    function json_decode($json, $assoc = false) {
        // Remover espacios en blanco
        $json = trim($json);
        
        // Verificar si es null
        if ($json == 'null') return null;
        
        // Verificar si es boolean
        if ($json == 'true') return true;
        if ($json == 'false') return false;
        
        // Verificar si es número
        if (is_numeric($json)) {
            return (strpos($json, '.') !== false) ? floatval($json) : intval($json);
        }
        
        // Verificar si es string
        if ($json[0] == '"') {
            return stripslashes(substr($json, 1, -1));
        }
        
        // Verificar si es array u objeto
        if ($json[0] == '[' || $json[0] == '{') {
            $result = array();
            $level = 0;
            $in_string = false;
            $current = '';
            $key = '';
            $is_key = true;
            
            for ($i = 1; $i < strlen($json) - 1; $i++) {
                $char = $json[$i];
                
                if ($char == '"' && $json[$i-1] != '\\') {
                    $in_string = !$in_string;
                }
                
                if (!$in_string) {
                    if ($char == '{' || $char == '[') {
                        $level++;
                    } else if ($char == '}' || $char == ']') {
                        $level--;
                    }
                    
                    if ($level == 0 && ($char == ',' || $i == strlen($json) - 2)) {
                        if ($i == strlen($json) - 2) {
                            $current .= $char;
                        }
                        
                        if ($json[0] == '{') {
                            if ($is_key) {
                                $key = json_decode($current);
                                $is_key = false;
                                $current = '';
                                continue;
                            } else {
                                $result[$key] = json_decode($current);
                                $is_key = true;
                            }
                        } else {
                            $result[] = json_decode($current);
                        }
                        
                        $current = '';
                        continue;
                    }
                    
                    if ($level == 0 && $char == ':') {
                        $key = json_decode($current);
                        $current = '';
                        $is_key = false;
                        continue;
                    }
                }
                
                $current .= $char;
            }
            
            if (!$assoc && $json[0] == '{') {
                return (object) $result;
            }
            
            return $result;
        }
        
        return null;
    }
}

/**
 * Función personalizada para establecer código de respuesta HTTP
 * Compatible con PHP 5.1.2
 */
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {
        if ($code !== NULL) {
            switch ($code) {
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 204: $text = 'No Content'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 503: $text = 'Service Unavailable'; break;
                default: $text = 'Unknown'; break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }
}

/**
 * Función para codificar JSON con manejo de errores
 */
function _json_encode($data) {
    return json_encode($data);
}

/**
 * Función para decodificar JSON con manejo de errores
 */
function _json_decode($json, $assoc = true) {
    return json_decode($json, $assoc);
}

/**
 * Función para sanitizar entrada de datos
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>