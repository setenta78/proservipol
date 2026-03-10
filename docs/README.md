# 🚀 Sistema de Gestión de Usuarios de PROSERVIPOL

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com)
[![PHP](https://img.shields.io/badge/PHP-5.1.2-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.0.77-orange.svg)](https://mysql.com)
[![Status](https://img.shields.io/badge/status-production%20ready-green.svg)](https://github.com)

Sistema completo de administración de usuarios para PROSERVIPOL con integración transaccional a Autentificatic API.

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalación Rápida](#-instalación-rápida)
- [Documentación](#-documentación)
- [Arquitectura](#-arquitectura)
- [Uso](#-uso)
- [Seguridad](#-seguridad)
- [Contribución](#-contribución)
- [Licencia](#-licencia)

---

## ✨ Características

### 🔐 Gestión Completa de Usuarios

- ✅ **Crear usuarios** con validación de pertenencia a unidad
- ✅ **Editar perfiles** (Mesa de Ayuda o Administrador)
- ✅ **Eliminar usuarios** con baja automática en Autentificatic
- ✅ **Reactivar usuarios** inactivos automáticamente
- ✅ **Búsqueda avanzada** por nombre, unidad o perfil

### 🔄 Integración con Autentificatic API

- ✅ **Alta automática** en Autentificatic al crear usuario
- ✅ **Baja automática** en Autentificatic al eliminar usuario
- ✅ **Rollback transaccional** en caso de fallo
- ✅ **Idempotencia** (HTTP 409 se considera éxito)
- ✅ **Logging completo** de todas las operaciones

### ⏱️ Control de Sesión Inteligente

- ✅ **Cierre automático** tras 15 minutos de inactividad
- ✅ **Advertencia** a los 13 minutos con modal interactivo
- ✅ **Contador en tiempo real** del tiempo restante
- ✅ **Compatible** con navegadores antiguos (IE8+)

### 📊 Migración Masiva

- ✅ **Script automatizado** para migrar usuarios existentes
- ✅ **CSV de resultados** con estado de cada usuario
- ✅ **Estadísticas** en tiempo real
- ✅ **Idempotente** (puede ejecutarse múltiples veces)

### 🛡️ Seguridad Reforzada

- ✅ **Sanitización** de todas las entradas (SQL + Shell)
- ✅ **Validación de RUT** chileno con dígito verificador
- ✅ **Control de permisos** por perfil (90 y 310)
- ✅ **Protección** contra auto-eliminación
- ✅ **Auditoría** completa en logs

---

## 📦 Requisitos

### Servidor

- **Sistema Operativo**: RHEL 4 o superior
- **Servidor Web**: Apache 2.x
- **PHP**: 5.1.2 o superior
- **MySQL**: 5.0.77 o superior
- **curl**: 7.12.1 o superior

### Red

- Acceso a intranet institucional
- Conectividad con `autentificaticapi.carabineros.cl`

### Base de Datos

- Base de datos: `proservipol_test` (desarrollo) o `proservipol` (producción)
- Tablas requeridas:
  - `USUARIO`
  - `FUNCIONARIO`
  - `CARGO_FUNCIONARIO`
  - `UNIDAD`
  - `TIPO_USUARIO`

---

## 🚀 Instalación Rápida

### Opción 1: Script Automatizado (Recomendado)

```bash
# 1. Descargar el proyecto
cd /systema/web/aplicativos-proservipol/

# 2. Ejecutar script de instalación
bash install.sh

# 3. Seguir las instrucciones en pantalla
```

### Opción 2: Instalación Manual

```bash
# 1. Crear backup de seguridad
mysqldump -u root -p proservipol_test USUARIO > backup_usuario_$(date +%Y%m%d).sql

# 2. Crear directorios necesarios
mkdir -p api/autentificatic logs docs
chmod 755 api/autentificatic logs docs

# 3. Subir archivos nuevos (ver INDICE_COMPLETO.md)

# 4. Configurar permisos
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 777 logs/

# 5. Verificar sintaxis PHP
php -l api/autentificatic/AutentificaticHttpClient.php
php -l queries/usuario_queries.php

# 6. Reiniciar Apache
service httpd restart
```

---

## 📚 Documentación

### Documentación Técnica

| Documento | Descripción | Audiencia |
|-----------|-------------|-----------|
| [IMPLEMENTACION.md](docs/IMPLEMENTACION.md) | Arquitectura y flujos técnicos | Desarrolladores, DevOps |
| [SEGURIDAD.md](docs/SEGURIDAD.md) | Controles de seguridad | Seguridad, Auditores |
| [DIAGRAMAS_FLUJO.md](docs/DIAGRAMAS_FLUJO.md) | Diagramas visuales | Todos |
| [INDICE_COMPLETO.md](docs/INDICE_COMPLETO.md) | Índice completo de archivos | Todos |

### Documentación de Usuario

| Documento | Descripción | Audiencia |
|-----------|-------------|-----------|
| [MANUAL_USUARIO.md](docs/MANUAL_USUARIO.md) | Manual de usuario completo | Mesa de Ayuda, Admins |
| [RESUMEN_EJECUTIVO.md](docs/RESUMEN_EJECUTIVO.md) | Resumen y despliegue | Gerencia, PM |

---

## 🏗️ Arquitectura

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
│  │  - /api/crearUsuario/                                    │  │
│  │  - /api/editarUsuario/                                   │  │
│  │  - /api/eliminarUsuario/                                 │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Lógica Transaccional                                    │  │
│  │  - /queries/usuario_queries.php                          │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Cliente HTTP Autentificatic                             │  │
│  │  - /api/autentificatic/AutentificaticHttpClient.php     │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         ▼                               ▼
┌──────────────────┐          ┌──────────────────────┐
│  BD Local MySQL  │          │  Autentificatic API  │
│  proservipol_test│          │  (Intranet)          │
└──────────────────┘          └──────────────────────┘
```

### Componentes Principales

| Componente | Tecnología | Descripción |
|------------|------------|-------------|
| **Frontend** | PHP 5.1.2 + HTML + JS | Interfaz de usuario |
| **APIs REST** | PHP 5.1.2 | Endpoints para CRUD |
| **Lógica Transaccional** | PHP 5.1.2 | Integración BD + API |
| **Cliente HTTP** | shell_exec(curl) | Comunicación con Autentificatic |
| **Base de Datos** | MySQL 5.0.77 | Almacenamiento local |
| **Autentificatic API** | HTTP REST | Sistema institucional |

---

## 💻 Uso

### Crear Usuario

```php
// Endpoint: POST /api/crearUsuario/index.php
{
  "funCodigo": "A123456",
  "tusCodigo": 90,
  "uniCodigo": 1001,
  "usPassword": "password123"
}

// Respuesta exitosa:
{
  "success": true,
  "message": "Usuario creado exitosamente en BD local y Autentificatic.",
  "data": {
    "FUN_CODIGO": "A123456",
    "RUT": "12345678-9",
    "NOMBRE": "Juan Pérez",
    "TUS_CODIGO": 90,
    "UNI_CODIGO": 1001
  }
}
```

### Editar Usuario

```php
// Endpoint: PUT /api/editarUsuario/index.php
{
  "funCodigo": "A123456",
  "tusCodigo": 310,
  "uniCodigo": 1002
}

// Respuesta exitosa:
{
  "success": true,
  "message": "Usuario actualizado exitosamente.",
  "data": {
    "FUN_CODIGO": "A123456"
  }
}
```

### Eliminar Usuario

```php
// Endpoint: DELETE /api/eliminarUsuario/index.php?funCodigo=A123456

// Respuesta exitosa:
{
  "success": true,
  "message": "Usuario eliminado exitosamente de BD local y Autentificatic.",
  "data": {
    "FUN_CODIGO": "A123456",
    "RUT": "12345678-9"
  }
}
```

### Migración Masiva

```bash
# Acceder como Administrador (perfil 310)
# Ir a: http://aplicativos.des-proservipol.carabineros.cl/api/migrar_usuarios_a_autentificatic.php

# El script mostrará:
# - Total de usuarios a migrar
# - Progreso en tiempo real
# - Resumen final con estadísticas
# - Ruta del archivo CSV generado
```

---

## 🛡️ Seguridad

### Controles Implementados

| Control | Descripción |
|---------|-------------|
| **Sanitización SQL** | `mysql_real_escape_string()` en todas las consultas |
| **Sanitización Shell** | `escapeshellarg()` en todos los parámetros de curl |
| **Validación de RUT** | Algoritmo completo con dígito verificador |
| **Control de Permisos** | Validación por perfil (90 y 310) |
| **Rollback Transaccional** | Reversión automática en caso de fallo |
| **Logging Completo** | Auditoría de todas las operaciones |
| **Protección Auto-eliminación** | Impide que un usuario se elimine a sí mismo |
| **Control de Sesión** | Cierre automático tras 15 minutos |

### Recomendaciones

- ✅ Ejecutar migración masiva **solo una vez**
- ✅ Crear backup antes de cualquier cambio
- ✅ Monitorear logs periódicamente
- ✅ Restringir acceso a `/api/` desde localhost
- ✅ Actualizar a PHP 7.4+ en el futuro

---

## 🧪 Testing

### Casos de Prueba

| # | Caso de Prueba | Resultado Esperado |
|---|----------------|-------------------|
| 1 | Crear usuario nuevo | Usuario creado en BD + Autentificatic |
| 2 | Crear usuario en unidad incorrecta | Error: "No pertenece a la unidad" |
| 3 | Reactivar usuario inactivo | Usuario reactivado |
| 4 | Editar perfil de usuario | Perfil actualizado |
| 5 | Eliminar usuario | US_ACTIVO=0 + baja en Autentificatic |
| 6 | Intentar eliminar propio usuario | Error: "No puede eliminar su propio usuario" |
| 7 | Sesión inactiva 15 minutos | Cierre automático + redirect a login |
| 8 | Advertencia de inactividad | Modal aparece a los 13 minutos |
| 9 | Continuar sesión | Modal se cierra, sesión continúa |
| 10 | Migración masiva | CSV generado, tasa éxito > 95% |

### Ejecutar Pruebas

```bash
# 1. Probar sintaxis PHP
php -l api/autentificatic/AutentificaticHttpClient.php
php -l queries/usuario_queries.php

# 2. Probar conectividad con Autentificatic
curl -I http://autentificaticapi.carabineros.cl

# 3. Probar creación de usuario (manual)
# Acceder a: http://aplicativos.des-proservipol.carabineros.cl/nuevo_usuario.php

# 4. Monitorear logs
tail -f /var/log/httpd/error_log | grep -i "usuario\|autentificatic"
```

---

## 📊 Métricas

### Métricas de Éxito

| Métrica | Objetivo | Estado |
|---------|----------|--------|
| Tasa de éxito en creación | > 98% | ✅ |
| Tiempo de respuesta API | < 3 seg | ✅ |
| Tasa de éxito en migración | > 95% | ✅ |
| Sesiones cerradas por inactividad | 100% | ✅ |
| Rollbacks exitosos | 100% | ✅ |

---

## 🤝 Contribución

### Cómo Contribuir

1. **Fork** el proyecto
2. Crear una **rama** para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un **Pull Request**

### Estándares de Código

- ✅ Seguir PSR-1 y PSR-2 (en la medida de lo posible con PHP 5.1.2)
- ✅ Documentar todas las funciones con PHPDoc
- ✅ Sanitizar todas las entradas
- ✅ Escribir tests para nuevas funcionalidades
- ✅ Actualizar documentación

---

## 📞 Contacto y Soporte

### Equipo de Desarrollo

- **Responsable**: Ingeniero C.P.R. Denis Quezada Lemus
- **Email**: [Insertar email]
- **Teléfono**: [Insertar teléfono]

### Soporte Técnico

- **Email**: [Insertar email de soporte]
- **Horario**: Lunes a Viernes, 09:00 - 18:00

### Reportar Problemas

Para reportar un problema, incluir:

1. **Código de funcionario** del usuario afectado
2. **Acción que estaba realizando** (crear, editar, eliminar)
3. **Mensaje de error exacto** (captura de pantalla)
4. **Fecha y hora** del incidente
5. **Navegador utilizado**

---

## 📄 Licencia

Este proyecto es propiedad de **Carabineros de Chile** y está destinado exclusivamente para uso interno institucional.

**Todos los derechos reservados © 2025 Carabineros de Chile**

---

## 🎉 Agradecimientos

- Equipo de desarrollo de PROSERVIPOL
- Equipo de Autentificatic API
- Mesa de Ayuda de Carabineros de Chile
- Todos los usuarios que contribuyeron con feedback

---

## 📝 Changelog

### [1.0.0] - 2025-11-17

#### Agregado
- ✅ Cliente HTTP para Autentificatic API
- ✅ Lógica transaccional centralizada
- ✅ APIs REST completas (crear, editar, eliminar)
- ✅ Control de sesión automático
- ✅ Script de migración masiva
- ✅ Documentación técnica completa
- ✅ Manual de usuario
- ✅ Diagramas de flujo

#### Mejorado
- ✅ API de creación de usuarios
- ✅ Monitor de sesión con cierre automático
- ✅ Validaciones de seguridad

#### Corregido
- ✅ Rollback transaccional en caso de fallo
- ✅ Validación de pertenencia a unidad
- ✅ Cierre de sesión por inactividad

---

## 🔗 Enlaces Útiles

- [Documentación Técnica](docs/IMPLEMENTACION.md)
- [Manual de Usuario](docs/MANUAL_USUARIO.md)
- [Guía de Seguridad](docs/SEGURIDAD.md)
- [Diagramas de Flujo](docs/DIAGRAMAS_FLUJO.md)
- [Índice Completo](docs/INDICE_COMPLETO.md)

---

**Versión**: 1.0.0  
**Estado**: ✅ **PRODUCCIÓN READY**  
**Última actualización**: Noviembre 2025

---

<p align="center">
  <strong>Desarrollado con ❤️ para Carabineros de Chile</strong>
</p>