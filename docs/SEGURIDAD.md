# SEGURIDAD: Medidas aplicadas al Sistema de Gestión de Usuarios de PROSERVIPOL

## 1. Contexto de Amenaza

El sistema opera en un entorno legacy:

- **PHP 5.1.2** (sin soporte, múltiples CVE críticos)
- **MySQL 5.0.77** (sin transacciones ACID completas — mezcla MyISAM/InnoDB)
- **RHEL4** (EOL, sin parches de seguridad desde 2017)
- **Sin internet**, pero con acceso a intranet institucional (`autentificaticapi.carabineros.cl`)

Por lo tanto, se aplicaron **controles compensatorios** para mitigar riesgos de:

- Inyección SQL
- Inyección de comandos (al usar `shell_exec`)
- Exposición de credenciales/tokens
- Rollback parcial en fallos transaccionales
- Sesiones no terminadas por inactividad

---

## 2. Controles Implementados

### 2.1. Sanitización de Entradas

Todas las entradas dinámicas (RUT, `FUN_CODIGO`, `UNI_CODIGO`, etc.) se sanitizan con:

- **`mysql_real_escape_string()`** para consultas SQL.
- **`escapeshellarg()`** para parámetros pasados a `shell_exec('curl ...')`.

**Ejemplo** (en `AutentificaticHttpClient.php`):

```php
$rutEsc = escapeshellarg($rut);
$tokenEsc = escapeshellarg($this->accessToken);
$cmd = "curl -X POST -H 'Authorization: Bearer $tokenEsc' -d 'rut=$rutEsc' ...";
```

### 2.2. Integración con Autentificatic API

- **Método**: `shell_exec('curl ...')` (binario `/usr/bin/curl` v7.12.1 disponible).
- **Ventajas**:
  - Soporte completo de métodos (`POST`, `DELETE`).
  - Headers personalizados (`Authorization`, `Content-Type`).
  - Auditoría: se loggea el comando ejecutado en caso de error.
- **Riesgo mitigado**: inyección de comandos → neutralizado por `escapeshellarg()`.

### 2.3. Transaccionalidad Emulada (BD local + API)

Dado que no hay `BEGIN/COMMIT/ROLLBACK` garantizados (tablas MyISAM), se implementó **rollback manual**:

#### Alta:

```
1. INSERT USUARIO (..., US_ACTIVO = 1)
2. curl POST /institutional-app-user...
3. Si falla: UPDATE USUARIO SET US_ACTIVO = 0 WHERE FUN_CODIGO = ?
```

#### Baja:

```
1. UPDATE USUARIO SET US_ACTIVO = 0
2. curl DELETE /institutional-app-user...
3. Si falla: UPDATE USUARIO SET US_ACTIVO = 1 WHERE FUN_CODIGO = ?
```

- Todos los errores se registran en `error_log()` con timestamp y contexto.

### 2.4. Validación de Pertenencia a Unidad

Se evita asignación arbitraria de unidades con:

```sql
SELECT 1 FROM CARGO_FUNCIONARIO
WHERE FUN_CODIGO = ?
  AND (UNI_CODIGO = ? OR UNI_AGREGADO = ?)
  AND FECHA_HASTA IS NULL;
```

→ Si no hay resultados, se rechaza la operación con mensaje claro.

### 2.5. Eliminación de Validación de Curso

- Se eliminó por completo cualquier llamada a:
  - `api/buscarFuncionarioAprobado/`
  - Campo `CAPACITACION` en flujos de UI/API.
- Esto reduce la superficie de ataque y dependencias innecesarias.

### 2.6. Control de Sesión

- `session_monitor.js` corregido:
  - Cuenta 15 minutos desde última interacción.
  - A los 13 min: muestra advertencia.
  - Si no hay confirmación explícita, tras 15:01 min redirige a `logout.php?reason=inactivity`.
- En `logout.php`: se registra la causa en `BITACORA_USUARIO` (si se usa tablas InnoDB), o se loggea vía `error_log()`.

### 2.7. Logging y Auditoría

Todos los errores de integración con Autentificatic se loggean:

```php
error_log("[".date("Y-m-d H:i:s")."] [Autentificatic] Falló alta para $funCodigo: $rut - $response['body']");
```

El script de migración genera un CSV con: `FUN_CODIGO`, `RUT`, `ESTADO`, `ERROR`.

### 2.8. Validación de RUT Chileno

Se implementó validación completa del dígito verificador:

```php
static function normalizarRut($rut) {
    // Remover puntos, guiones y espacios
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    
    // Calcular dígito verificador
    $suma = 0;
    $multiplo = 2;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $suma += $numero[$i] * $multiplo;
        $multiplo = ($multiplo == 7) ? 2 : $multiplo + 1;
    }
    
    $dvCalculado = 11 - ($suma % 11);
    // ... validación
}
```

### 2.9. Protección contra Auto-eliminación

En `eliminarUsuario()`:

```php
if ($funCodigo === $_SESSION['USUARIO_CODIGOFUNCIONARIO']) {
    return array(
        'success' => false,
        'message' => 'No puede eliminar su propio usuario.'
    );
}
```

### 2.10. Control de Permisos por Perfil

| Acción          | Mesa de Ayuda (90) | Administrador (310) |
|-----------------|--------------------|---------------------|
| Crear usuario   | ✅                 | ✅                  |
| Editar usuario  | ✅                 | ✅                  |
| Eliminar usuario| ❌                 | ✅                  |
| Migración masiva| ❌                 | ✅                  |

---

## 3. Recomendaciones DevSecOps (para operación)

| Medida                                          | Prioridad | Descripción                                                                 |
|-------------------------------------------------|-----------|-----------------------------------------------------------------------------|
| 🛡️ Bloquear acceso directo a `/api/`           | Alta      | Solo permitir desde `localhost` mediante referer válido.                    |
| 📄 Respaldar `USUARIO` antes de migración      | Crítica   | `mysqldump proservipol_test USUARIO > backup_usuarios_$(date +%Y%m%d).sql` |
| 📊 Monitorear CSV de migración                 | Media     | Validar que no haya más de 5% de fallos. Si hay muchos, revisar token.     |
| 🔒 Restringir ejecución de script de migración | Alta      | Solo perfil 310 (Administrador) puede ejecutarlo.                          |
| 📝 Revisar logs periódicamente                 | Media     | Buscar patrones de errores HTTP 401, 403, 500 en `error_log`.              |

---

## 4. Limitaciones Conocidas (por entorno legacy)

| Limitación                                      | Riesgo | Mitigación                                                                 |
|-------------------------------------------------|--------|----------------------------------------------------------------------------|
| Sin encriptación en tránsito (HTTP)             | Medio  | Se asume red institucional segura (no internet). Migrar a HTTPS futuro.   |
| Contraseñas en `US_PASSWORD` en texto claro     | Bajo   | Solo se usa para soporte; no se modifica ni se expone en UI (solo lectura).|
| Sesiones PHP con `$_SESSION` (sin rotate)       | Medio  | Se usa `LAST_ACTIVITY` + cierre automático. No se almacena token en cookies.|
| Tablas MyISAM sin transacciones ACID            | Alto   | Rollback manual implementado en `usuario_queries.php`.                     |
| PHP 5.1.2 sin parches de seguridad              | Alto   | Sanitización estricta + validación en cada entrada. Actualizar a PHP 7.4+ en futuro.|

---

## 5. Checklist de Seguridad Pre-Producción

Antes de pasar a producción, verificar:

- [ ] Todos los archivos PHP tienen `session_start()` y `require_once 'middleware_auth.php'` (excepto `login.php`, `save_token.php`).
- [ ] No hay `echo` de variables sin `htmlspecialchars()` en vistas.
- [ ] Todas las consultas SQL usan `mysql_real_escape_string()`.
- [ ] Todos los parámetros de `shell_exec()` usan `escapeshellarg()`.
- [ ] El token de sesión nunca se loggea en texto claro (usar `***TOKEN***`).
- [ ] El script de migración solo es accesible por perfil 310.
- [ ] Se generó backup de tabla `USUARIO` antes de migración.
- [ ] Se probó el cierre automático de sesión tras 15 minutos.
- [ ] Se validó que usuarios inactivos no puedan acceder.
- [ ] Se verificó que el rollback funciona correctamente en caso de fallo de Autentificatic.

---

## 6. Matriz de Riesgos

| Amenaza                          | Probabilidad | Impacto | Riesgo | Control Implementado                          |
|----------------------------------|--------------|---------|--------|-----------------------------------------------|
| Inyección SQL                    | Media        | Alto    | Alto   | `mysql_real_escape_string()` en todas las consultas |
| Inyección de comandos            | Baja         | Alto    | Medio  | `escapeshellarg()` en todos los parámetros de curl |
| Exposición de token              | Baja         | Alto    | Medio  | Token solo en `$_SESSION`, nunca en logs      |
| Sesión no cerrada                | Alta         | Medio   | Medio  | Cierre automático tras 15 minutos             |
| Rollback parcial                 | Media        | Alto    | Alto   | Rollback manual implementado                  |
| Usuario elimina su propia cuenta | Baja         | Medio   | Bajo   | Validación que impide auto-eliminación        |
| Asignación a unidad incorrecta   | Media        | Medio   | Medio  | Validación de `CARGO_FUNCIONARIO`             |

---

## 7. Contacto para Incidentes de Seguridad

- **Responsable**: Ingeniero C.P.R. Denis Quezada Lemus
- **Email**: [Insertar email institucional]
- **Procedimiento**: Reportar inmediatamente cualquier comportamiento anómalo, intentos de acceso no autorizado, o errores en logs relacionados con Autentificatic.

---

**Última actualización**: Noviembre 2025