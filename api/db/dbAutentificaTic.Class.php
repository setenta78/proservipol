<?php
/**
 * Clase para consumir la API AutentificaTIC
 * Compatible con PHP 5.1.2
 */

class AutentificaTicAPI {
    
    private $baseUrl;
    private $accessToken;
    
    function AutentificaTicAPI($accessToken) {
        $this->baseUrl    = 'http://autentificaticapi.carabineros.cl';
        $this->accessToken = $accessToken;
    }

    // ================================================
    // LOGIN SERVER-TO-SERVER para obtener token propio
    // de la plataforma des-proservipol.carabineros.cl
    // ================================================
    function loginServicio($rut, $password) {
        $endpoint = '/api/auth/login';
        $url      = $this->baseUrl . $endpoint;

        $dataString = 'rut=' . urlencode($rut) . '&password=' . urlencode($password);

        $headers = array(
            'Host: autentificaticapi.carabineros.cl',
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($dataString),
            'Connection: close',
            'Origin: http://des-proservipol.carabineros.cl'
        );

        $options = array(
            'http' => array(
                'method'           => 'POST',
                'header'           => implode("\r\n", $headers),
                'content'          => $dataString,
                'timeout'          => 30,
                'ignore_errors'    => true,
                'protocol_version' => 1.0
            ),
            'ssl' => array(
                'verify_peer'       => false,
                'verify_host'       => false,
                'allow_self_signed' => true
            )
        );

        $context  = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        $httpCode = 0;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/[\d\.]+\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                    break;
                }
            }
        }

        if ($httpCode === 200 && $response !== false) {
            if (function_exists('json_decode')) {
                $data = json_decode($response, true);
            } else {
                require_once(dirname(__FILE__) . '/../../inc/Services_JSON.php');
                $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
                $data = $json->decode($response);
            }

            // La respuesta tiene: success.access_token
            if (isset($data['success']['access_token'])) {
                return array(
                    'success'      => true,
                    'access_token' => $data['success']['access_token']
                );
            }
        }

        return array(
            'success' => false,
            'message' => 'No se pudo obtener token de servicio. HTTP ' . $httpCode
        );
    }
    
    function registrarUsuario($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (empty($rut)) {
            return array(
                'success'   => false,
                'message'   => 'El RUT proporcionado es inválido.',
                'http_code' => 400
            );
        }

        $endpoint = '/api/institutional-app-user-from-external-app';
        $url      = $this->baseUrl . $endpoint;
        $params   = array('rut' => $rut);
        
        return $this->_request('POST', $url, $params);
    }
    
    function eliminarUsuario($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (empty($rut)) {
            return array(
                'success'   => false,
                'message'   => 'El RUT proporcionado es inválido.',
                'http_code' => 400
            );
        }
        
        $endpoint = '/api/institutional-app-user-from-external-app';
        $url      = $this->baseUrl . $endpoint;
        $params   = array('rut' => $rut);
        
        return $this->_request('DELETE', $url, $params);
    }
    
    function _request($method, $url, $params) {
        $result = array(
            'success'   => false,
            'message'   => '',
            'data'      => null,
            'http_code' => 0
        );

        $dataString = 'rut=' . urlencode($params['rut']);

        $headers = array(
            'Host: autentificaticapi.carabineros.cl',
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($dataString),
            'Connection: close',
            'Origin: http://des-proservipol.carabineros.cl',
            'Authorization: Bearer ' . $this->accessToken
        );

        $options = array(
            'http' => array(
                'method'           => $method,
                'header'           => implode("\r\n", $headers),
                'content'          => $dataString,
                'timeout'          => 30,
                'ignore_errors'    => true,
                'protocol_version' => 1.0
            ),
            'ssl' => array(
                'verify_peer'       => false,
                'verify_host'       => false,
                'allow_self_signed' => true
            )
        );

        $context  = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        $httpCode = 0;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/[\d\.]+\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                    break;
                }
            }
        }

        $result['http_code'] = $httpCode;

        if ($httpCode >= 200 && $httpCode < 300) {
            $result['success'] = true;
            $result['message'] = 'Operación exitosa';
            $result['data']    = null;

            if ($response !== false && trim($response) !== '') {
                if (function_exists('json_decode')) {
                    $responseData = json_decode($response, true);
                } else {
                    require_once(dirname(__FILE__) . '/../../inc/Services_JSON.php');
                    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
                    $responseData = $json->decode($response);
                }
                if (isset($responseData['message'])) {
                    $result['message'] = $responseData['message'];
                }
                $result['data'] = $responseData;
            }

            return $result;
        }

        if ($httpCode === 0) {
            $result['message'] = 'Error de red: No se pudo conectar con AutentificaTIC.';
            return $result;
        }

        $errorMessage = 'Error en la petición (HTTP ' . $httpCode . ')';

        if ($response !== false && trim($response) !== '') {
            if (function_exists('json_decode')) {
                $responseData = json_decode($response, true);
            } else {
                require_once(dirname(__FILE__) . '/../../inc/Services_JSON.php');
                $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
                $responseData = $json->decode($response);
            }
            if (is_array($responseData)) {
                if (isset($responseData['errors']) && is_array($responseData['errors'])) {
                    $errorMessage = implode(', ', $responseData['errors']);
                } elseif (isset($responseData['errors'])) {
                    $errorMessage = $responseData['errors'];
                } elseif (isset($responseData['error'])) {
                    $errorMessage = $responseData['error'];
                } elseif (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                }
            }
        } else {
            $errorMessage = 'La API respondió con error HTTP ' . $httpCode . ' sin cuerpo.';
        }

        $result['message'] = $errorMessage;
        return $result;
    }
}
?>