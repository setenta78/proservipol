<?php
/**
 * Clase para consumir la API AutentificaTIC
 * Compatible con PHP 5.1.2
 * 
 * Endpoints:
 * - POST /api/institutional-app-user-from-external-app (Registrar usuario)
 * - DELETE /api/institutional-app-user-from-external-app (Eliminar usuario)
 */

class AutentificaTicAPI {
    
    private $baseUrl;
    private $accessToken;
    
    /**
     * Constructor de la clase
     * 
     * @param string $accessToken Token de acceso del usuario autenticado
     */
    function AutentificaTicAPI($accessToken) {
        // URL base de la API AutentificaTIC
        $this->baseUrl = 'http://autentificaticapi.carabineros.cl';
        $this->accessToken = $accessToken;
    }
    
    /**
     * Registrar usuario en AutentificaTIC API
     * 
     * @param string $rut RUT del usuario sin puntos ni guión
     * @return array Resultado de la operación
     */
    function registrarUsuario($rut) {
        $endpoint = '/api/institutional-app-user-from-external-app';
        $url = $this->baseUrl . $endpoint;
        
        $params = array(
            'rut' => $rut
        );
        
        return $this->_request('POST', $url, $params);
    }
    
    /**
     * Eliminar usuario de AutentificaTIC API
     * 
     * @param string $rut RUT del usuario sin puntos ni guión
     * @return array Resultado de la operación
     */
    function eliminarUsuario($rut) {
        $endpoint = '/api/institutional-app-user-from-external-app';
        $url = $this->baseUrl . $endpoint;
        
        $params = array(
            'rut' => $rut
        );
        
        return $this->_request('DELETE', $url, $params);
    }
    
    /**
     * Método interno para realizar peticiones HTTP
     * Compatible con PHP 5.1.2 (sin cURL, usando file_get_contents)
     * 
     * @param string $method Método HTTP (POST, DELETE)
     * @param string $url URL completa del endpoint
     * @param array $params Parámetros de la petición
     * @return array Resultado de la operación
     */
    function _request($method, $url, $params) {
        $result = array(
            'success' => false,
            'message' => '',
            'data' => null,
            'http_code' => 0
        );
        
        // Preparar headers
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->accessToken
        );
        
        // Preparar datos
        $dataString = http_build_query($params);
        
        // Configurar contexto para file_get_contents
        $options = array(
            'http' => array(
                'method'  => $method,
                'header'  => implode("\r\n", $headers),
                'content' => $dataString,
                'timeout' => 30
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false
            )
        );
        
        $context = stream_context_create($options);
        
        try {
            // Realizar petición
            $response = @file_get_contents($url, false, $context);
            
            // Obtener código HTTP desde $http_response_header
            $httpCode = 200;
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                        $httpCode = intval($matches[1]);
                        break;
                    }
                }
            }
            
            $result['http_code'] = $httpCode;
            
            // Parsear respuesta JSON
            $responseData = json_decode($response, true);
            
            // Verificar si fue exitoso
            if ($httpCode >= 200 && $httpCode < 300) {
                $result['success'] = true;
                $result['message'] = isset($responseData['message']) ? $responseData['message'] : 'Operación exitosa';
                $result['data'] = $responseData;
            } else {
                // Error desde la API
                $errorMessage = 'Error en la petición';
                
                if (isset($responseData['errors'])) {
                    if (is_array($responseData['errors'])) {
                        $errorMessage = implode(', ', $responseData['errors']);
                    } else {
                        $errorMessage = $responseData['errors'];
                    }
                } elseif (isset($responseData['error'])) {
                    $errorMessage = $responseData['error'];
                }
                
                $result['message'] = $errorMessage;
                $result['data'] = $responseData;
            }
            
        } catch (Exception $e) {
            $result['message'] = 'Error de conexión con AutentificaTIC API: ' . $e->getMessage();
            $result['http_code'] = 503;
        }
        
        return $result;
    }
    
    /**
     * Validar si el token es válido
     * 
     * @return bool True si el token es válido
     */
    function validarToken() {
        $endpoint = '/api/auth/validate-token';
        $url = $this->baseUrl . $endpoint;
        
        $headers = array(
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken
        );
        
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header'  => implode("\r\n", $headers),
                'timeout' => 10
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false
            )
        );
        
        $context = stream_context_create($options);
        
        try {
            $response = @file_get_contents($url, false, $context);
            $responseData = json_decode($response, true);
            
            return isset($responseData['success']) && $responseData['success'] === true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
