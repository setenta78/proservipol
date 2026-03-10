# IMPLEMENTACIÓN: Sistema de Gestión de Usuarios de PROSERVIPOL

## 1. Objetivo

Implementar un módulo independiente de administración de usuarios para PROSERVIPOL que:

- Integre transaccionalmente la base de datos local (`USUARIO`) con **Autentificatic API**.
- Valide localmente:
  - `US_ACTIVO = 1`
  - `TUS_CODIGO IN (90, 310)`
- Cumpla con las restricciones del entorno: PHP 5.1.2, MySQL 5.0.77, RHEL4, sin internet (solo intranet).
- Proporcione interfaz clara, con manejo de errores específico según la documentación de Autentificatic.

---

## 2. Arquitectura General

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND                                 │
│  (nuevo_usuario.php, editar_usuario.php, gestor_usuarios.php)  │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      BACKEND PHP                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  APIs REST                                                │  │
│  │  - /api/crearUsuario/index.php                           │  │
│  │  - /api/editarUsuario/index.php                          │  │
│  │  - /api/eliminarUsuario/index.php                        │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Lógica Transaccional                                    │  │
│  │  - /queries/usuario_queries.php                          │  │
│  │    · crearUsuario()                                      │  │
│  │    · editarUsuario()                                     │  │
│  │    · eliminarUsuario()                                   │  │
│  │    · reactivarUsuario()                                  │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Cliente HTTP Autentificatic                             │  │
│  │  - /api/autentificatic/AutentificaticHttpClient.php     │  │
│  │    · registrarUsuario() → POST                           │  │
│  │    · eliminarUsuario() → DELETE                          │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         ▼                               ▼
┌──────────────────┐          ┌──────────────────────┐
│  BD Local MySQL  │          │  Autentificatic API  │
│  proservipol_test│          │  (Intranet)          │
│  - USUARIO       │          │  - POST /api/...     │
│  - FUNCIONARIO   │          │  - DELETE /api/...   │
│  - CARGO_...     │          └──────────────────────┘
└──────────────────┘
```

---

## 3. Estrategia de Integración con Autentificatic API

### 3.1. Método HTTP

- **`shell_exec('curl ...')`** (binario `/usr/bin/curl` v7.12.1 disponible en CLI).
- **Ventajas:**
  - Total compatibilidad con PHP 5.1.2.
  - Soporte completo: `POST`, `DELETE`, headers, parámetros.
  - Auditable y trazable.
- **Seguridad:**
  - Todos los parámetros dinámicos sanitizados con `escapeshellarg()`.
  - Token de sesión proviene de `$_SESSION['access_token']` (ya validado por `middleware_auth.php`).

### 3.2. Endpoints utilizados

| Acción | Método   | Endpoint                                          | Parámetros                |
|--------|----------|---------------------------------------------------|---------------------------|
| Alta   | `POST`   | `/api/institutional-app-user-from-external-app`  | `rut` (sin puntos/guion)  |
| Baja   | `DELETE` | `/api/institutional-app-user-from-external-app`  | `rut` (sin puntos/guion)  |

### 3.3. Manejo de Errores

- **Éxito**: `HTTP 200`, `201`, o `409` (idempotente: ya existe en Autentificatic se considera éxito).
- **Fallo**: cualquier otro código → rollback local (`US_ACTIVO = 0`) + log en `error_log()` + mensaje claro al usuario.

---

## 4. Flujos Transaccionales

### 4.1. Alta de Usuario Nuevo

```
1. Validar FUN_CODIGO existe en FUNCIONARIO
2. Validar pertenencia a unidad (ver 5.1)
3. Insertar en USUARIO con US_ACTIVO = 1
4. Llamar a POST /institutional-app-user... con rut
5. Si éxito → confirmar
6. Si fallo → rollback: UPDATE USUARIO SET US_ACTIVO = 0 WHERE FUN_CODIGO = ?
```

### 4.2. Reactivación de Usuario Inactivo

```
1. Verificar US_ACTIVO = 0
2. Validar pertenencia a unidad
3. Llamar a POST /institutional-app-user...
4. Si éxito → UPDATE USUARIO SET US_ACTIVO = 1
5. Si fallo → mensaje, sin cambios
```

### 4.3. Baja/Inactivación

```
1. Validar US_ACTIVO = 1
2. Llamar a DELETE /institutional-app-user...
3. Si éxito → UPDATE USUARIO SET US_ACTIVO = 0
4. Si fallo → mensaje, sin cambios
```

---

## 5. Validaciones Críticas

### 5.1. Pertenencia a Unidad

Un usuario solo puede asignarse a una unidad si **el funcionario está vigente en esa unidad**:

```sql
SELECT 1
FROM CARGO_FUNCIONARIO
WHERE FUN_CODIGO = ?
  AND (UNI_CODIGO = ? OR UNI_AGREGADO = ?)
  AND FECHA_HASTA IS NULL;
```

→ Si no hay resultados: rechazar operación.

### 5.2. Eliminación de Validación de Curso

- Se elimina por completo la llamada a `api/buscarFuncionarioAprobado/`.
- El campo `CAPACITACION` ya no se valida ni se muestra en flujos de alta/edición.

---

## 6. Cierre Automático por Inactividad

- `session_monitor.js` corregido:
  - Cuenta 15 minutos desde última interacción.
  - A los 13 min: muestra advertencia con ventana modal.
  - Si el usuario no confirma explícitamente, tras 15:01 min se redirige a `logout.php?reason=inactivity`.
  - Registro en bitácora no se hace en BD (por MyISAM), pero el parámetro permite auditoría externa.

---

## 7. Migración Masiva Inicial

- Script `migrar_usuarios_a_autentificatic.php`:
  - Recorre `USUARIO WHERE US_ACTIVO = 1`.
  - Para cada `FUN_CODIGO`, obtiene `FUN_RUT`.
  - Ejecuta `POST /institutional-app-user...`.
  - Genera `migracion_resultado_YYYYMMDD.csv` con:
    - `FUN_CODIGO`, `RUT`, `ESTADO` (EXITO/FALLA), `ERROR` (si aplica).
  - Idempotente: `409` se considera éxito.

---

## 8. Estructura de Archivos

```
/aplicativos-proservipol/
├── api/
│   ├── autentificatic/
│   │   └── AutentificaticHttpClient.php [NUEVO]
│   ├── crearUsuario/
│   │   └── index.php [MEJORADO]
│   ├── editarUsuario/
│   │   └── index.php [NUEVO]
│   ├── eliminarUsuario/
│   │   └── index.php [NUEVO]
│   ├── migrar_usuarios_a_autentificatic.php [NUEVO]
│   └── tools.php [OK]
├── queries/
│   ├── config.php [OK]
│   ├── usuario_queries.php [NUEVO]
│   └── general_queries.php [OK]
├── js/
│   └── session_monitor.js [CORREGIDO]
├── docs/
│   ├── IMPLEMENTACION.md [ESTE ARCHIVO]
│   ├── SEGURIDAD.md
│   └── MANUAL_USUARIO.md
├── logs/ [NUEVO - para CSV de migración]
├── middleware_auth.php [OK]
├── nuevo_usuario.php [MEJORAR]
├── editar_usuario.php [MEJORAR]
└── gestor_usuarios.php [MEJORAR]
```

---

## 9. Pruebas Recomendadas

| Caso de Prueba                | Entrada                                      | Resultado Esperado                                                |
|-------------------------------|----------------------------------------------|-------------------------------------------------------------------|
| Alta usuario nuevo            | Ingresar FUN_CODIGO válido, unidad correcta | Usuario creado local + en Autentificatic                          |
| Alta en unidad incorrecta     | Asignar unidad no vigente                    | Mensaje: "El funcionario no pertenece a la unidad asignada."      |
| Reactivación                  | Usuario inactivo → activar                   | US_ACTIVO = 1, sin error                                          |
| Baja                          | Usuario activo → eliminar                    | US_ACTIVO = 0, baja en Autentificatic                             |
| Inactividad                   | No interactuar 15:01 min                     | Redirección automática a login                                    |
| Migración masiva              | Ejecutar script con 100 usuarios             | CSV generado con resultados, tasa éxito > 95%                     |

---

## 10. Orden de Despliegue (Seguro y Atómico)

1. **Documentación** (`/docs/`)
2. **Cliente HTTP seguro** (`/api/autentificatic/AutentificaticHttpClient.php`)
3. **Lógica transaccional central** (`/queries/usuario_queries.php`)
4. **APIs actualizadas** (`/api/crearUsuario/`, `/editarUsuario/`, `/eliminarUsuario/`)
5. **Frontend corregido** (`nuevo_usuario.php`, `editar_usuario.php`, `js/session_monitor.js`)
6. **Script de migración** (`/api/migrar_usuarios_a_autentificatic.php`)
7. **Archivos obsoletos a eliminar**: `queries/nuevo_queries.php` (100% comentado, sin uso)

---

## 11. Contacto y Soporte

- **Responsable**: Ingeniero C.P.R. Denis Quezada Lemus
- **Fecha**: Noviembre 2025
- **Entorno**: PHP 5.1.2 + MySQL 5.0.77 + RHEL4
- **Dominio**: http://aplicativos.des-proservipol.carabineros.cl