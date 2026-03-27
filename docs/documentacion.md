# Documentación Técnica - Sistema de Gestión de Usuarios PROSERVIPOL

**Fecha de actualización:** Marzo 2026  
**Versión del Sistema:** 3.9 (Legacy PHP 5.1.2 / MySQL 5.0.77)  
**Estado del Desarrollo:** En curso (Iteración GACC-0010)

---

## 1. Resumen Ejecutivo

El sistema **PROSERVIPOL** es una aplicación web institucional para la gestión de usuarios, cargos, unidades y bitácoras de acceso de Carabineros de Chile. Actualmente se encuentra en una fase de modernización funcional sobre una base tecnológica legacy.

### Stack Tecnológico
- **Backend:** PHP 5.1.2 (Sin soporte oficial desde 2011).
- **Base de Datos:** MySQL 5.0.77.
- **Servidor Web:** Apache 2.2.0.
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla + jQuery), TailwindCSS (vía CDN).
- **API Externa:** AutentificaticAPI (OAuth2 Bearer Token).
- **Compatibilidad:** Uso de librerías shim (`Services_JSON.php`) para suplir falta de funciones nativas `json_encode/decode`.

### Objetivo Principal
Implementar la gestión completa del ciclo de vida del usuario (Alta, Baja, Modificación, Consulta) con integración atómica a la API institucional **AutentificaticAPI**, asegurando que cualquier registro en la base de datos local tenga su contraparte válida en la plataforma central de autenticación.

---

## 2. Estructura de Directorios y Archivos

A continuación se detalla la arquitectura del proyecto, analizando la función de cada componente relevante.

### 📂 Raíz del Proyecto (`/`)
Archivos principales de interfaz y lógica de navegación.

| Archivo | Función |
| :--- | :--- |
| `index.php` | Redirecciona al login o al gestor si hay sesión activa. |
| `login.php` | Interfaz gráfica del formulario de inicio de sesión. |
| `logout.php` | Destruye la sesión y redirige al login. |
| `gestor_usuarios.php` | **Vista Principal.** Muestra la grilla de usuarios, busca libre y abre modales de gestión. |
| `nuevo_usuario.php` | **Vista de Alta.** Formulario para buscar funcionario y crear nuevo usuario. |
| `editar_usuario.php` | **Vista de Edición/Detalle.** Muestra datos completos, historial de accesos y permite modificar perfil/unidad. |
| `buscar_usuario.php` | Lógica backend para el modal de búsqueda paramétrica (por nombre, unidad, perfil). |
| `buscar_ingresos_usuario.php` | Endpoint interno para buscar bitácoras de acceso con filtros de fecha. |
| `middleware_auth.php` | **Seguridad.** Verifica sesión activa, inactividad y perfiles autorizados (90, 310) en cada carga de página. |
| `valida.php` | Script legacy de validación de sesiones (en desuso progresivo en favor de `middleware_auth.php`). |
| `header.php`, `nav.php` | Componentes de interfaz reutilizables (cabecera y menú de navegación). |

### 📂 `/api/` (Capa de Servicios Web)
Endpoints RESTful que consumen el frontend o sistemas externos. Todos retornan JSON.

#### Subdirectorios de Endpoints
| Ruta | Función |
| :--- | :--- |
| `api/autentificaTic/` | Proxy o utilitarios relacionados directamente con la conexión a Autentificatic. |
| `api/buscarFuncionarioPersonal/` | Busca datos del funcionario en `PERSONAL_MOCK` o BD Personal real. Retorna estado de curso Proservipol. |
| `api/buscarFuncionarioAprobado/` | Verifica si un funcionario tiene el curso aprobado. |
| `api/buscarUsuario/` | Busca un usuario específico por código para validar existencia. |
| `api/crearUsuario/` | **Crítico.** Recibe POST con datos de nuevo usuario y ejecuta la transacción de alta. |
| `api/editarUsuario/` | Procesa actualizaciones de perfil, unidad o contraseña. |
| `api/eliminarUsuario/` | Ejecuta la baja lógica (soft delete) y sincroniza con Autentificatic. |
| `api/listarPerfiles/` | Retorna lista de tipos de usuario disponibles. |
| `api/listarUsuarios/` | Retorna la grilla paginada para `gestor_usuarios.php`. |
| `api/cargosPorCodigoFuncionario/` | Obtiene cargos históricos o actuales de un funcionario. |

#### Archivos de Soporte en `/api/`
| Archivo | Función |
| :--- | :--- |
| `api/check_session.php` | Valida sesión AJAX para llamadas asíncronas. |
| `api/tools.php` | Funciones utilitarias globales para los endpoints (limpieza de datos, logs). |

### 📂 `/api/db/` (Clases de Acceso a Datos y APIs)
Clases PHP orientadas a objetos (estilo legacy) para conexiones específicas.

| Clase | Función |
| :--- | :--- |
| `dbAutentificaTic.Class.php` | **Crítico.** Cliente HTTP para consumir AutentificaticAPI (registro, eliminación, validación de token). Usa `file_get_contents` por compatibilidad PHP 5.1.2. |
| `dbUsuario.Class.php` | Operaciones CRUD directas sobre la tabla `USUARIO`. |
| `dbFuncionario.Class.php` | Operaciones sobre tabla `FUNCIONARIO`. |
| `dbPersonal.Class.php` | Conexión y consultas a la base de datos externa `DB_Personal`. |
| `dbPerfil.Class.php` | Gestión de tipos de usuario (`TIPO_USUARIO`). |
| `dbUnidad.Class.php` | Gestión de unidades organizacionales. |
| `dbBitacora.Class.php` | (Implícita) Lectura de `BITACORA_USUARIO` para auditoría. |
| `conexion*.Class.php` | Gestores de conexión a distintas bases de datos (Proservipol, Histórico, Cámaras, etc.). |

### 📂 `/queries/` (Funciones Procedurales de Negocio)
Lógica de negocio encapsulada en funciones PHP que utilizan las clases de `/api/db/` y conexiones globales.

| Archivo | Función |
| :--- | :--- |
| `usuario_queries.php` | **Crítico.** Contiene `crearUsuarioProservipol()`. Implementa la transacción atómica: BD Local + API Externa. |
| `usuarios_listado.php` | Funciones `obtenerUsuarios()` y `contarUsuarios()` con filtros de búsqueda libre y paramétrica. |
| `editar_queries.php` | Funciones `obtenerFuncionario()`, `actualizarUsuario()`, `obtenerUltimosAccesos()`. |
| `eliminar_queries.php` | Función `eliminarUsuario()` (soft delete). |
| `general_queries.php` | Consultas transversales (listar perfiles, unidades simples). |
| `buscar_unidad.php` | Endpoint AJAX para autocomplete de unidades en formularios. |
| `config.php` | Inicializa la conexión global `$link` a la BD `proservipol_test`. |

### 📂 `/js/` (Lógica del Frontend)
Scripts JavaScript que manejan la interacción del usuario y llamadas AJAX.

| Archivo | Función |
| :--- | :--- |
| `nuevo_usuario.js` | **Crítico.** Maneja la búsqueda de funcionarios, validación de curso, llenado de formulario y envío de creación de usuario. |
| `modal_nuevo.js` | Controla la apertura y cierre del modal de nuevo usuario. |
| `modal_editar.js` | Carga datos en el modal de edición y guarda cambios. |
| `modal_eliminar.js` | Muestra confirmación y ejecuta la eliminación. |
| `modal_busquedaParametrica.js` | Lógica para el buscador avanzado (filtros combinados). |
| `modal_ingresos.js` | Maneja la búsqueda de bitácoras con rangos de fecha. |
| `login.js` | Manejo del formulario de login y errores. |
| `session_monitor.js` | Monitorea la expiración de la sesión y alerta al usuario. |
| `main.js` | Funciones utilitarias globales del frontend. |

### 📂 `/inc/` (Configuración y Librerías)
| Archivo | Función |
| :--- | :--- |
| `config.inc.php` | Configuración general (rutas, constantes). |
| `config_autentificatic.php` | Define la URL base de la API externa. |
| `configPersonal.inc.php` | Credenciales de acceso a la BD de Personal (Producción). |
| `Services_JSON.php` | **Shim de compatibilidad.** Implementa `json_encode` y `json_decode` para PHP 5.1.2. |

### 📂 `/auth/`
| Archivo | Función |
| :--- | :--- |
| `authenticate.php` | Endpoint de login. Valida credenciales contra AutentificaticAPI, verifica perfil local e inicia sesión PHP. |

### 📂 `/docs/`
Documentación del proyecto, manuales de usuario y especificaciones de API.

---

## 3. Implementaciones Realizadas (Iteración GACC-0010)

Se ha completado la implementación de la historia de usuario **GACC-0010: Registrar Nuevo Usuario**, con un enfoque de **transacción atómica**.

### Flujo Implementado
1.  **Búsqueda:** El usuario ingresa código en `nuevo_usuario.php`. JS llama a `api/buscarFuncionarioPersonal/`.
2.  **Validación:** Se verifica existencia en `PERSONAL_MOCK` (o BD Personal) y estado del curso Proservipol.
3.  **Confirmación:** Si no tiene curso, se alerta pero se permite continuar bajo responsabilidad. Si está inactivo, se ofrece reactivar.
4.  **Transacción Atómica (Backend):**
    *   Se inicia `START TRANSACTION` en MySQL.
    *   **Paso A:** Inserta/Actualiza en tablas `FUNCIONARIO` y `USUARIO`.
    *   **Paso B:** Obtiene el `access_token` de la sesión PHP.
    *   **Paso C:** Instancia `AutentificaTicAPI` y llama al endpoint `POST /api/institutional-app-user-from-external-app` enviando el RUT.
    *   **Éxito:** Si la API responde 200/201, se hace `COMMIT`.
    *   **Fallo:** Si la API responde error (4xx, 5xx) o falla la red, se ejecuta `ROLLBACK` inmediatamente, dejando la BD local sin cambios.

### Archivos Modificados
1.  **`api/db/dbAutentificaTic.Class.php`**:
    *   Reescritura del método `_request()` para usar `file_get_contents` con contexto stream (compatible PHP 5.1.2).
    *   Agregado `'ignore_errors' => true` para capturar cuerpos de respuesta en errores HTTP.
    *   Forzado de protocolo HTTP 1.1 para asegurar envío correcto de headers `Host`.
    *   Mejora en el parseo de errores específicos de la API (Token inválido, Plataforma no encontrada).

2.  **`queries/usuario_queries.php`**:
    *   Función `crearUsuarioProservipol()` refactorizada completamente.
    *   Implementación de bloques `try/catch` lógicos y manejo explícito de transacciones MySQL.
    *   Diferenciación clara entre casos: Usuario Nuevo vs. Reactivación.
    *   Mensajes de error específicos según la respuesta de la API externa.

3.  **`js/nuevo_usuario.js`**:
    *   Corrección en `asignarValores()` para poblar automáticamente el campo "Usuario PROSERVIPOL" con el código del funcionario.
    *   Manejo robusto de respuestas JSON y errores de red.
    *   Validación visual del estado del curso (colores rojo/verde).

4.  **`api/crearUsuario/index.php`**:
    *   Validación estricta de método POST y campos obligatorios.
    *   Manejo de códigos HTTP correctos (201 Created, 400 Bad Request, 500 Error).
    *   Inclusión de `Services_JSON.php` para compatibilidad.

---

## 4. Diagnóstico Actual y Bloqueo Técnico

### Situación
Al intentar crear un usuario real, el sistema realiza correctamente los pasos locales (INSERT en BD), pero falla al contactar a la API externa, provocando el **Rollback** de la transacción.

### Evidencia Técnica
1.  **Prueba de Token (`test_token.php`):**
    *   El endpoint `GET /api/auth/validate-token` responde **HTTP 200 OK**.
    *   Respuesta: `{"success": {"domain": "http://aplicativos.des-proservipol.carabineros.cl", "rut": "..."}}`.
    *   **Conclusión:** El token es válido, la conexión de red funciona y el dominio está registrado para **autenticación**.

2.  **Prueba de Registro (`crearUsuario`):**
    *   El endpoint `POST /api/institutional-app-user-from-external-app` responde **HTTP 404 Not Found**.
    *   Cuerpo de error (según documentación API): `{"errors": {"rut": "Plataforma web no encontrada en nuestros registros."}}`.

### Causa Raíz
El dominio `aplicativos.des-proservipol.carabineros.cl` tiene permisos de **lectura/autenticación** en AutentificaticAPI, pero **NO tiene habilitado el permiso de escritura/gestión** para el endpoint de registro de usuarios institucionales. La API rechaza la petición asumiendo que la plataforma solicitante no existe para ese propósito específico.

### Solución Requerida (Externa)
Se debe gestionar con el equipo administrador de **AutentificaticAPI** lo siguiente:
*   **Solicitud:** Habilitar el dominio `http://aplicativos.des-proservipol.carabineros.cl` para consumir el endpoint `POST /api/institutional-app-user-from-external-app`.
*   **Justificación:** El sistema Proservipol requiere registrar a sus usuarios locales en la plataforma central para permitirles el SSO (Single Sign-On).

---

## 5. Plan de Trabajo Pendiente

Una vez resuelto el bloqueo externo, quedan las siguientes tareas para completar el módulo de gestión:

### Historias Críticas (Prioridad Alta)
| ID | Historia | Estado | Tarea Pendiente |
| :--- | :--- | :--- | :--- |
| **GACC-0008** | Eliminar Usuario | ⚠️ Parcial | Implementar llamada a API Autentificatic (DELETE) con transacción atómica (igual que GACC-0010). Corregir bug de IDs duplicados en botones de la grilla. |
| **GACC-0009** | Modificar Acceso | ⚠️ Parcial | Agregar confirmación "¿Está seguro?" antes de guardar. Revisar lógica de cambio de password (opcional vs obligatorio). |
| **GACC-0011** | Buscar Ingresos | ❌ Pendiente | Crear vista independiente con filtros de fecha (desde/hasta) y columna IP. Implementar botón "Limpiar filtros". |

### Historias Secundarias (Prioridad Media/Baja)
| ID | Historia | Estado | Tarea Pendiente |
| :--- | :--- | :--- | :--- |
| **GACC-0002 a 0005** | Modales Específicos | ⚠️ Parcial | Actualmente integrados en búsqueda paramétrica. Evaluar si se requieren modales separados físicamente o si basta con mejorar los mensajes de error específicos ("No existe usuario con código X"). |
| **GACC-0007** | Doble Clic | ⚠️ Parcial | Implementar evento `dblclick` en la fila de la tabla para abrir el modal de edición (adicional al botón lápiz). |
| **GACC-0001/0006** | Mensajes Exactos | ✅ Casi Listo | Ajustar textos de "No existen registros" a "No existen usuarios registrados..." según especificación literal. |

---

## 6. Guía de Despliegue (Manual)

Dado que el servidor objetivo no permite Git directo y usa PHP 5.1.2, el despliegue se realiza manualmente:

1.  **Preparación Local:**
    *   Mantener los archivos corregidos en una carpeta local espejo (`/systema/web/aplicativos-proservipol/`).
    *   Verificar que no haya caracteres UTF-8 BOM en los archivos PHP (usar Notepad++: *Codificación > Convertir a UTF-8 sin BOM*).

2.  **Subida (WinSCP):**
    *   Conectar a `aplicativos.des-proservipol.carabineros.cl`.
    *   Navegar a `/systema/web/aplicativos-proservipol/`.
    *   Sobrescribir únicamente los archivos modificados:
        *   `api/db/dbAutentificaTic.Class.php`
        *   `queries/usuario_queries.php`
        *   `api/crearUsuario/index.php`
        *   `js/nuevo_usuario.js`
        *   `nuevo_usuario.php` (si hubo cambios de UI)

3.  **Verificación:**
    *   Limpiar caché del navegador (Ctrl+F5).
    *   Ejecutar prueba de creación de usuario.
    *   Revisar logs de Apache (`error_log`) si persisten errores 500.

---

## 7. Anexos: Comandos de Prueba (Postman/cURL)

Para validar la conectividad con AutentificaticAPI manualmente:

### 1. Obtener Token
```bash
curl -X POST "http://autentificaticapi.carabineros.cl/api/auth/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=RUT_SIN_PUNTOS&password=TU_PASSWORD"
```

### 2. Validar Token
```bash
curl -X GET "http://autentificaticapi.carabineros.cl/api/auth/validate-token" \
  -H "Authorization: Bearer TU_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

### 3. Registrar Usuario (Prueba de Fuego)
```bash
curl -X POST "http://autentificaticapi.carabineros.cl/api/institutional-app-user-from-external-app" \
  -H "Authorization: Bearer TU_ACCESS_TOKEN" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Accept: application/json" \
  -d "rut=RUT_FUNCIONARIO_A_CREAR"
```
*Nota: Si el paso 3 retorna 404, confirmar que el dominio está habilitado.*
