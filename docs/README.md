# AUTENTIFICATIC
#### 29 de mayo del año 2019

[![Autentificatic](http://autentificaticapi.carabineros.cl/assets/images/autentificatic.png)](http://autentificaticapi.carabineros.cl)

### Carabineros de Chile


> Desarrollado por el Departamento Tecnologías de la Información y las Comunicaciones (TIC) © 2019


# Visión general
El presente proyecto está desarrollado para unificar la autenticación de usuarios en las diversas plataformas Institucionales de Carabineros de Chile mediante el protocolo de autorización OAuth2 a través de una API RESTful.

# Objetivo general
Apoyar la seguridad de las plataformas institucionales con la gestión de contraseñas por medio de una herramienta tecnológica, para unificar los procesos de autenticación de usuario, permitiendo tener un control del acceso a los sistemas y resguardo de contraseñas.

# Tecnologías utilizadas!

  - PHP 7.2
  - MySql 5.7
  - Laravel 5.8
  - Authentication with Laravel Passport
  - Vuejs 3.0

###### API RESTful

# Orden General
> Orden General N°. 002701, de fecha 30 de octubre del año 2019, publicada en el B/O N° 4825, que aprobó la Politica de Seguridad de la Información en Carabineros de Chile.

# OAuth2
Este sistema se encuentra adaptado a las necesidades de Carabineros de Chile, por consiguiente, el primer paso de autenticación de OAuth2 con respecto a obtención de credenciales y access token para cada cliente (servidor) fue omitido, permitiendo a cada plataforma consultar de manera directa a la API mediante el access token del usuario autenticado.

Se encuentra habilitado el CORS (Cross-origin resource sharing), para poder hacer uso de la API desde el Frontend utilizando javascript, permitiendo métodos de control de acceso 'GET, POST, PUT, PATCH, DELETE, OPTIONS', que serán necesarios para realizar las peticiones.


# Pasos a seguir antes de integrar el sistema

## Solicitud de registro de la plataforma

Para hacer uso del sistema Autentificatic API, primero debe solicitar el registro de su aplicación web a través de Documentación Electrónica dirigido a la oficina de Aplicaciones y Data Center del Departamento TIC, debiendo informar el nombre del sistema y el dominio con el cual accederá. Ej. Nombre del sistema: Autentificatic, Url: http://autentificatic.carabineros.cl. Asimismo, deberá informar el rut de un usuario con credenciales de Autentificatic con la finalidad de ser vinculado a la plataforma y le permita realizar pruebas.

## Descarga de los recursos del sistema

Autentificatic cuenta con los recursos necesarios, para integrar en su aplicación web el sistema de autenticación, el cual puede descargar desde http://autentificaticapi.carabineros.cl

[![Autentificaticapi](http://autentificaticapi.carabineros.cl/assets/images/autentificaticapi.JPG)](http://autentificaticapi.carabineros.cl)

### Pantalla de login

Proporciona una plantilla de inicio de sesión que se encuentra desarrollada en HTML5, CSS y Javascript, que permitirá integrar una interfaz gráfica de inicio de sesión estándar para todas las plataformas institucionales que se encuentren bajo el sistema Autentificatic.

[![Autentificatic](http://autentificaticapi.carabineros.cl/assets/images/template.JPG)](http://autentificaticapi.carabineros.cl)

### Documentación de la API

Proporciona información con respecto a la integración de la API Autentificatic, los recursos que se deben utilizar para la autenticación y scripts de ejemplos en Axios con Vue js y Guzzle con PHP, para consumir la API.

### Procedimiento de seguridad de contraseña

Proporciona la información de procedimiento de seguridad de contraseñas que se encuentran integradas en el sistema de Autentificatic.



# Empezar
Antes de que pueda integrar el servicio de autenticación en su plataforma Institucional, debe tener una libreria para consumir los recursos de una API, los descritos como ejemplo en este documento serán:
  - Axios js
  - Guzzle PHP

#### Instalación de axios
Usando npm:
```sh
$ npm install axios
```
Usando bower:
```sh
$ bower install axios
```
Usando yarn:
```sh
$ yarn add axios
```
Usando cdn:
```sh
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
```

#### Instalación de Guzzle
##### Requisitos
- PHP 5.5.0 o superior
- Para usar el controlador de flujo de PHP, allow_url_fopen debe estar habilitado en php.ini de su sistema.
- Para usar el controlador cURL, debe tener una versión reciente de cURL> = 7.19.4 compilada con OpenSSL y zlib.

La forma recomendada de instalar Guzzle es con Composer. Composer es una herramienta de administración de dependencias para PHP que le permite declarar las dependencias que su proyecto necesita y las instala en su proyecto.
```sh
# Instalar Composer 
$ curl -sS https://getcomposer.org/installer | php
```
Puede agregar Guzzle como una dependencia usando la CLI composer.phar:
```sh
{ 
  "require" :  { 
    "guzzlehttp / guzzle" :  "~ 6.0" 
  } 
}
```
Después de la instalación, debe requerir el autocargador Composer:
```sh
require  'vendor/autoload.php' ;
```
Asimismo, puede instalar Guzzle directamente con el comando 
```sh
$ composer require guzzlehttp/guzzle
```

# Hacer llamadas a la API REST

En las llamadas a la API, incluya la URL de cada uno de los recursos que se describen a continuación:

- **Inicio de sesión:**
http://autentificaticapi.carabineros.cl/api/auth/login [POST]
- **Obtener datos del usuario autenticado:**
http://autentificaticapi.carabineros.cl/api/auth/user [GET]
- **Obtener datos completos del usuario autenticado:**
http://autentificaticapi.carabineros.cl/api/auth/user-full [GET]
- **Consultar si el token de autenticación aún es válido:**
http://autentificaticapi.carabineros.cl/api/auth/validate-token [GET]
- **Cerrar sesión:**
http://autentificaticapi.carabineros.cl/api/auth/logout [GET]
- **Obtener datos del usuario mediante su rut:**
http://autentificaticapi.carabineros.cl/api/user-data/{rut} [GET]
- **Registrar usuario, para la validación en Autentificatic API** [POST]
http://autentificaticapi.carabineros.cl/api/nstitutional-app-user-from-external-app [POST]
- **Eliminar usuario, para la validación en Autentificatic API**
http://autentificaticapi.carabineros.cl/api/nstitutional-app-user-from-external-app [DELETE]

Además, incluya el access token (sólo si es requerido) para probar su identidad y acceder a los recursos protegidos.

# Encabezados de solicitud HTTP

#### Aceptar
El formato de respuesta, que se requiere para las operaciones con un cuerpo de respuesta. La sintaxis es:
```sh
#Accept: application/<format>
'Accept': 'application/json'
```
#### Autorización
Al hacer una petición incluya el token de acceso:
```sh
'Authorization': 'Bearer <Access Token>'
```
#### Tipo de contenido
El formato de solicitud, que se requiere para las operaciones con un cuerpo de solicitud. La sintaxis es:
```sh
#Content-Type: application/<format>
'Content-Type': 'application/x-www-form-urlencoded'
```
# Autenticación
El usuario al iniciar sesión se le proporcionará un JSON WEB TOKEN (JWT), que será su identificación por plataforma y host, para acceder a los recursos de la API.

# Respuestas API

Las llamadas a la API de Autentificatic API devuelven códigos de estado HTTP. Algunas llamadas a la API también devuelven cuerpos de respuesta JSON que incluyen información sobre el recurso. Cada solicitud de API REST devuelve un código de estado HTTP.

#### Códigos de estados HTTP
Para solicitudes exitosas, Autentificatic API devuelve 2xx códigos de estado HTTP.

Para solicitudes fallidas, Autentificatic API devuelve 4xx o 5xx códigos de estado HTTP.

Autentificatic API devuelve estos códigos de estado HTTP:

| Código de estado | Descripción |
| ------ | ------ |
| 200 OK | La solicitud tuvo éxito. |
| 201 Created | Un POSTmétodo creó con éxito un recurso. Si el recurso ya fue creado por una ejecución anterior del mismo método, por ejemplo, el servidor devuelve el 200 OKcódigo de estado HTTP. |
| 202 Accepted | El servidor aceptó la solicitud y la ejecutará más tarde. |
| 204 No Content | El servidor ejecutó correctamente el método pero no devuelve ningún cuerpo de respuesta. |
| 400 Bad Request | INVALID_REQUEST. La solicitud no está bien formada, es sintácticamente incorrecta o viola el esquema. |
| 401 Unauthorized | AUTHENTICATION_FAILURE. La autenticación falló debido a credenciales de autenticación no válidas. |
| 403 Forbidden | NOT_AUTHORIZED. La autorización falló debido a permisos insuficientes. |
| 404 Not Found | RESOURCE_NOT_FOUND. El recurso especificado no existe. |
| 405 Method Not Allowed | METHOD_NOT_SUPPORTED. El servidor no implementa el método HTTP solicitado. |
| 406 Not Acceptable | MEDIA_TYPE_NOT_ACCEPTABLE. El servidor no implementa el tipo de medio que sería aceptable para el cliente. |
| 415 Unsupported Media Type | UNSUPPORTED_MEDIA_TYPE. El servidor no admite el tipo de medios de la carga útil de solicitud. |
| 422 Unprocessable Entity | UNPROCCESSABLE_ENTITY. La API no puede completar la acción solicitada, o la acción de solicitud es semánticamente incorrecta o falla la validación comercial. |
| 429 Unprocessable Entity  | RATE_LIMIT_REACHED. Demasiadas peticiones. Bloqueado debido a la limitación de velocidad. |
| 500 Internal Server Error | INTERNAL_SERVER_ERROR. Se ha producido un error interno del servidor. |
| 503 Service Unavailable | SERVICE_UNAVAILABLE. Servicio no disponible. |

Para todos los errores, excepto los errores de identidad, Autentificatic API devuelve un cuerpo de respuesta de error que incluye detalles de error adicionales en este formato.

# Tipo de plataformas

Autentificatic identifica dos tipos de plataformas, las plataformas generales y las específicas.

- **Las plataformas generales** son aquellas que todos los usuarios de Autentificatic podrán acceder, por lo que no es necesario utilizar todos los recursos del sistema, como por ejemplo, no es necesario utilizar los recursos de registrar y eliminar usuarios.

- **Las plataformas específicas** son aquellas que sólo algunos usuarios tienen acceso, por consiguiente, se deberán utilizar todos los recursos que proporciona Autentificatic.


# Recursos proporcionados por la API RESTful

A continuación, se detallarán cada uno de los recursos de Autentificatic, un ejemplo de cada solicitud y las respuestas que generan cada uno.

## Inicio de sesión

Para iniciar sesión el usuario deberá acceder a través de sus credenciales de Autentificatic, ingresando su cédula de identidad y su contraseña. Se validará que las credenciales sean correctas, que el usuairo esté asociado a la plataforma que está accediendo y que su contraseña no se encuentre caducada.

Ejemplo en axios utilizando Vue js Framework

```sh
<script>
    import axios from 'axios'
    export default {
        methods: {
            login: () => {
                let urlLogin = 'http://autentificaticapi.carabineros.cl/api/auth/login'
                axios.post(urlLogin, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    rut: <rut del usuario>,
                    password: <contraseña del usuario>     
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function login () {
        $validator = $validator = Validator::make(request()->input(), [
            'rut' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = null;
        $http = new Client;
        try {
            $response = $http->request('POST', env('API_URL_FROM_ENV') . '/api/auth/login', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'rut' => request()->input('rut'),
                    'password' => request()->input('password'),
                ],
            ]);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
```

#### Petición realizada satisfactoriamente

Se proporciona un access token el cual tiene un tiempo de caducidad de 24 horas.

```sh
{
    "success": {
        "access_token": <Token de acceso del usuario autenticado>,
        "token_type": <Tipo de token>,
        "expires_at": <Fecha y hora de expiración del token (24 horas)> 
    }
}
```
#### Errores de validación
Para evitar errores de validación, asegúrese de que los parámetros sean del tipo correcto y cumplan con las restricciones:

| Tipo de parámetro | Descripción |
| ------ | ------ |
| rut | Rut del usuario, campo requerido, debe ser de tipo String y rut chileno válido sin puntos ni guión |
| password | Contraseña del usuario, campo requerido y debe ser de tipo String |

#### Errores con información adicional

##### Credenciales no válidas

En caso que el usuario esté desactivado o las credenciales proporcionadas no sean válidas.
```sh
{
    "errors": {
        "rut": "Las credenciales no son validas"
    }
}
```
##### Usuario sin acceso a la plataforma

En caso que el usuario no haya sido vinculado a la plataforma mediante el recurso **Registrar usuario, para la validación en Autentificatic API**
```sh
{
    "errors": {
        "rut": "No tiene acceso a esta plataforma"
    }
}
```

##### Contraseña caducada

En caso que el usuario no haya cambiado su contraseña dentro de un periodo de 90 días.
```sh
{
    "errors": {
        "rut": "Su clave ha caducado, debe actualizarla"
    }
}
```
## Obtener datos del usuario autenticado mediante su access token

Una vez que el usuario inicia sesión y se obtiene el access token, se podrá obtener los datos de él mediante el token, permitiendo acceder a una gran cantidad de datos que pueden ser utilizados en la sesión del usuario.

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            fetchUser: () => {
                var urlUser = 'http://autentificaticapi.carabineros.cl/api/auth/user'
                axios.get(urlUser, {
                    headers: {                         
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json'
                    }   
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function fetchUser () {
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('GET', env('API_URL_FROM_ENV') . '/api/auth/user', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "success": {
        "photo": <Fotografía del usuario en base64>,
        "user": {            
            "codigo_dotacion": <Código de dotación>,
            "dotacion": <Descripción de la dotación>,
            "codigo_escalafon": <Código de escalafon>
            "escalafon": <Descripción del escalafon>
            "codigo_grado": <Código de grado>,
            "grado": <Código de grado>,
            "apellido_materno": <Apellido materno>,
            "apellido_paterno": <Apellido paterno>,
            "primer_nombre": <Primer nombre>,
            "segundo_nombre": <Segundo nombre>,          
            "codigo_funcionario": <Código de funcionario>,
            "correo_particular": <Correo electrónico particular>,
            "correo_institucional": <Correo electrónico institucional>,
            "rut": <Rut>,            
            "password_changed_at": <Fecha de última modificación de contraseña>,
            "password_expiration": <Días de expiración de la contraseña>
        }
    }
}
```
#### Errores con información adicional

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```

## Obtener datos completos del usuario

Si usted necesita más datos de lo que proporciona el recurso anterior, puede utilizar este recurso para obtener mayores datos.

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            fetchUser: () => {
                let urlUser = 'http://autentificaticapi.carabineros.cl/api/auth/user-full'
                axios.get(urlUser, {
                    headers: {
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json'
                    }   
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function fetchUser () {
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('GET', env('API_URL_FROM_ENV') . '/api/auth/user-full', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "success": {
        "photo": <Fotografía del usuario en base64>,
        "user": {            
            "codigo_dotacion": <Código de dotación>,
            "dotacion": <Descripción de la dotación>,
            "codigo_escalafon": <Código de escalafon>
            "escalafon": <Descripción del escalafon>
            "codigo_grado": <Código de grado>,
            "grado": <Código de grado>,
            "apellido_materno": <Apellido materno>,
            "apellido_paterno": <Apellido paterno>,
            "primer_nombre": <Primer nombre>,
            "segundo_nombre": <Segundo nombre>,          
            "codigo_funcionario": <Código de funcionario>,
            "correo_particular": <Correo electrónico particular>,
            "correo_institucional": <Correo electrónico institucional>,
            "rut": <Rut>,            
            "password_changed_at": <Fecha de última modificación de contraseña>,
            "password_expiration": <Días de expiración de la contraseña>,
            "codigo_alta_reparticion": <Código de alta repartición>,
            "descripcion_alta_reparticion": <Descripción de alta repartición>,
            "codigo_reparticion": <Código de repartición>,
            "descripcion_reparticion": <Descripción de repartición>,
            "codigo_unidad": <Código de unidad>,
            "descripcion_unidad": <Descripción de unidad>,
            "codigo_destacamento": <Código de destacamento>,
            "descripcion_destacamento": <Descripción de destacamento>
        }
    }
}
```
#### Errores con información adicional

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```

## Validar Token de Acceso

Debido a que el access token tiene un tiempo de caducidad de 24 horas, se debe validar si el Access Token aún está activo. Autentificatic API proporciona este recurso, por lo que se recomienda que cree un middleware para que en las peticiones a utilizar en su plataforma Institucional, valide si el token es válido antes de continuar.

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            validateToken: () => {
                let urlValidateToken = 'http://autentificaticapi.carabineros.cl/api/auth/validate-token'
                axios.get(urlValidateToken, {
                    headers: {
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json'
                    }   
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function validateToken () {
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('GET', env('API_URL_FROM_ENV') . '/api/auth/validate-token', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "success": {
        "domain": <Dominio registrado en la creación del Token>,
        "rut": <Rut del propietario del Token>
    }
}
```
#### Errores con información adicional

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```

## Cerrar sesión

Ya sea que el usuario cierre sesión por acción propia, por inactividad de uso en su plataforma o el access token haya caducado, Autentificatic API proporciona el cierre de sesión para eliminar el Token de Acceso.

> NOTA: La API Rest no maneja sesiones, sólo estados, motivo por el cual usted como porgramador deberá desarrollar un temporizador por inactividad y validar el token en cada petición interna de su plataforma.

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            logout: () => {
                let urlLogout = 'http://autentificaticapi.carabineros.cl/api/auth/logout'
                axios.get(urlLogout, {
                    headers: {                         
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json'
                    }   
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function logout () {
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('GET', env('API_URL_FROM_ENV') . '/api/auth/logout', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "message": "Cierre de sesión realizado satisfactoriamente"
}
```
#### Errores con información adicional

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```

## Obtener datos de otros funcionarios

Usted podrá buscar en Autentificatic API a un usuario por su rut, obteniendo como resultado el rut normalizado (sin puntos ni guión), código de funcionario, nombre completo, entre otros datos que serán de utilidad para registrar a los usuarios en su plataforma Institucional y utilizar el recurso de **Registrar usuario, para la validación en Autentificatic API**.

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            getUserData: (rut) => {               
                let urlUserData = 'http://autentificaticapi.carabineros.cl/api/user-data/'+rut;
                axios.get(urlUserData, {
                    headers: {
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json'
                    }   
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function getUserData ($rut) {
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('GET', env('API_URL_FROM_ENV') . '/api/user-data/'.$rut, [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "user": {
        "rut": <Rut del usuario>,
        "codigo_funcionario": <Código de funcionario>,
        "apellido_paterno": <Apellido paterno>,
        "apellido_materno": <Apellido materno>,            
        "primer_nombre": <Primer nombre>,
        "segundo_nombre": <Segundo nombre>,
        "codigo_escalafon": <Código de escalafon>
        "escalafon": <Descripción del escalafon>
        "codigo_grado": <Código de grado>,
        "grado": <Código de grado>
    }
}
```                                                             
#### Errores con información adicional

##### Rut no encontrado
```sh
{
    "errors": {
        "rut": "No se encuentra registro asociado al rut"
    }
}
```

##### Rut no válido
```sh
{
    "errors": {
        "rut": "Rut no válido"
    }
}
```

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```

## Registrar usuario, para la validación en Autentificatic API

Cuando usted registre a un usuario en su plataforma, debe ser registrado de igual manera en Autentificatic API, debido a que lleva el control de cada acceso por usuario/plataforma (No aplica para **plataformas generales**).

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            registerAppsToUser: () => {
                let urlRegisterUser = 'http://autentificaticapi.carabineros.cl/api/institutional-app-user-from-external-app';
                axios.post(urlRegisterUser, {
                    headers: {                         
                        'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },                    
                    rut: <rut del usuario>
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function registerAppsToUser () {
    
        $validator = $validator = Validator::make(request()->input(), [            
            'rut' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('POST', env('API_URL_FROM_ENV') . '/api/institutional-app-user-from-external-app', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                ],
                'form_params' => [                    
                    'rut' => request()->input('rut'),
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "message": "Usuario registrado satisfactoriamente en la plataforma."
}
```
#### Errores de validación
Para evitar errores de validación, asegúrese de que los parámetros sean del tipo correcto y cumplan con las restricciones:

| Tipo de parámetro | Descripción |
| ------ | ------ |
| rut | Rut del usuario, campo requerido, debe ser de tipo String y rut chileno válido sin puntos ni guión |

#### Errores con información adicional

##### Funcionario no encontrado en los registros de Autentificatic API

En caso que el rut no sea válido o no se encuentre registrado.

> NOTA: Este error no debería ser gatillado si usted utiliza el recurso **Obtener datos de otros funcionarios**.

```sh
{
    "errors": {
        "rut": "Funcionario no encontrado en nuestros registros."
    }
}
```

##### Plataforma Institucional no encontrada en los registros de Autentificatic API

En caso que el dominio no se encuentre registrado.

> NOTA: Este error no debería ser gatillado si el dominio que se está utilizando, se encuentra registrado en Autentificatic.
```sh
{
    "errors": {
        "rut": "Plataforma web no encontrada en nuestros registros."
    }
}
```

##### Token no autorizado
```sh
{
    "errors": "No autorizado"
}
```


## Eliminar usuario, para la validación en Autentificatic API

Cuando usted elimine un usuario en su aplicación, debe ser eliminado de igual manera en Autentificatic API, debido a que lleva el control de cada acceso por usuario/plataforma. (No aplica para **plataformas generales**).

Ejemplo en axios utilizando Vue js Framework
```sh
<script>
    import axios from 'axios'
    import { mapGetters } from 'vuex' 
    export default {
        computed: mapGetters({
            /* Obtención del token con vuex */
            token: 'auth/token'
        }),
        methods: {
            deleteAppsToUser: () => {                
                let urlDeleteUser = 'http://autentificaticapi.carabineros.cl/api/institutional-app-user-from-external-app'
                axios.delete(urlDeleteUser, {
                    headers: {
                         'Authorization': 'Bearer '+this.token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },                    
                    rut: <rut del usuario>
                }).then(response => {
                    /* Respuesta satisfactoria */
                    console.log(response.data)
                }).catch(error => {
                    /* Respuesta de error */
                    console.log(error.response.data)
                })
            } 
        }
    }
<script>
```

Ejemplo en guzzle utilizando Laravel Framework
```sh
<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function deleteAppsToUser () {
    
        $validator = $validator = Validator::make(request()->input(), [            
            'rut' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $response = null;
        try {
            $http = new Client;

            $response = $http->request('DELETE', env('API_URL_FROM_ENV') . '/api/institutional-app-user-from-external-app', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    /* Token de acceso extraído desde las Cookies */
                    'Authorization' => 'Bearer ' . $_COOKIE['token'],
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                ],
                'form_params' => [                    
                    'rut' => request()->input('rut'),
                ],
            ]);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            return response()->json(json_decode($response->getBody()->getContents(),true), $exception->getCode());
        }
        return json_decode((string) $response->getBody(), true);
    }
}
```

#### Petición realizada satisfactoriamente

```sh
{
    "message": "Usuario eliminado satisfactoriamente en la plataforma."
}
```
#### Errores de validación
Para evitar errores de validación, asegúrese de que los parámetros sean del tipo correcto y cumplan con las restricciones:

| Tipo de parámetro | Descripción |
| ------ | ------ |
| rut | Rut del usuario, campo requerido, debe ser de tipo String y rut chileno válido sin puntos ni guión |

#### Errores con información adicional

##### Funcionario no encontrado en los registros de Autentificatic API
```sh
{
    "errors": {
        "rut": "Funcionario no encontrado en nuestros registros."
    }
}
```

##### Plataforma Institucional no encontrada en los registros de Autentificatic API
```sh
{
    "errors": {
        "rut": "Plataforma web no encontrada en nuestros registros."
    }
}
```


License
----
**Departamento Tecnologías de la Información y las Comunicaciones (TIC)  © 2019**