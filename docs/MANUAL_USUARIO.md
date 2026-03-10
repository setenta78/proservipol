# 📖 MANUAL DE USUARIO - SISTEMA DE GESTIÓN DE USUARIOS PROSERVIPOL

**Versión**: 1.0.0  
**Fecha**: Noviembre 2025  
**Dirigido a**: Mesa de Ayuda (Perfil 90) y Administradores (Perfil 310)

---

## 📑 ÍNDICE

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Gestión de Usuarios](#3-gestión-de-usuarios)
   - [Crear Usuario](#31-crear-usuario)
   - [Buscar Usuario](#32-buscar-usuario)
   - [Editar Usuario](#33-editar-usuario)
   - [Eliminar Usuario](#34-eliminar-usuario)
4. [Control de Sesión](#4-control-de-sesión)
5. [Preguntas Frecuentes](#5-preguntas-frecuentes)
6. [Solución de Problemas](#6-solución-de-problemas)

---

## 1. INTRODUCCIÓN

El **Sistema de Gestión de Usuarios de PROSERVIPOL** permite administrar los usuarios que tienen acceso al sistema de programación de servicios policiales.

### ¿Qué puedo hacer con este sistema?

- ✅ **Crear nuevos usuarios** para funcionarios de Carabineros
- ✅ **Editar perfiles y unidades** de usuarios existentes
- ✅ **Eliminar usuarios** (solo Administradores)
- ✅ **Buscar usuarios** por nombre, unidad o perfil
- ✅ **Consultar historial de ingresos** al sistema

### Perfiles de Usuario

| Perfil | Código | Permisos |
|--------|--------|----------|
| **Mesa de Ayuda** | 90 | Crear y editar usuarios |
| **Administrador** | 310 | Crear, editar y eliminar usuarios |

---

## 2. ACCESO AL SISTEMA

### 2.1. Iniciar Sesión

1. Abrir navegador web (Chrome, Firefox, Internet Explorer 8+)
2. Ir a: **http://aplicativos.des-proservipol.carabineros.cl**
3. Ingresar credenciales:
   - **RUT**: Sin puntos, con guión (ej: 12345678-9)
   - **Contraseña**: Contraseña de Autentificatic

![Pantalla de Login](imagenes/login.png)

4. Clic en **"Iniciar Sesión"**

### 2.2. Pantalla Principal

Tras iniciar sesión, verá el **Gestor de Usuarios** con:

- **Menú lateral izquierdo**: Navegación del sistema
- **Barra superior**: Información del usuario logueado
- **Área central**: Listado de usuarios

![Pantalla Principal](imagenes/gestor_usuarios.png)

---

## 3. GESTIÓN DE USUARIOS

### 3.1. CREAR USUARIO

#### Paso 1: Acceder al Formulario

1. En el Gestor de Usuarios, clic en **"Nuevo Usuario"**
2. Se abrirá un formulario modal

![Formulario Nuevo Usuario](imagenes/nuevo_usuario.png)

#### Paso 2: Completar Datos

| Campo | Descripción | Ejemplo | Obligatorio |
|-------|-------------|---------|-------------|
| **Código Funcionario** | Código de 7 caracteres del funcionario | A123456 | ✅ Sí |
| **Perfil** | Tipo de usuario (Mesa de Ayuda o Administrador) | Mesa de Ayuda (90) | ✅ Sí |
| **Unidad** | Unidad a la que pertenece | 1ª COMISARÍA SANTIAGO | ✅ Sí |
| **Contraseña** | Contraseña local (diferente a Autentificatic) | ******** | ✅ Sí |

#### Paso 3: Buscar Unidad

1. En el campo **"Unidad"**, escribir parte del nombre
2. Aparecerán sugerencias automáticamente
3. Seleccionar la unidad correcta de la lista

![Búsqueda de Unidad](imagenes/buscar_unidad.png)

#### Paso 4: Crear Usuario

1. Verificar que todos los datos son correctos
2. Clic en **"CREAR USUARIO"**
3. Esperar mensaje de confirmación

**Mensajes posibles**:

| Mensaje | Significado | Acción |
|---------|-------------|--------|
| ✅ "Usuario creado exitosamente" | Usuario creado en BD local y Autentificatic | Ninguna |
| ⚠️ "El funcionario no pertenece a la unidad asignada" | El funcionario no está vigente en esa unidad | Verificar unidad en CARGO_FUNCIONARIO |
| ❌ "Error al registrar en Autentificatic" | Fallo en la integración con Autentificatic | Contactar soporte técnico |

#### Validaciones Automáticas

El sistema valida automáticamente:

- ✅ Que el código de funcionario exista en el sistema
- ✅ Que el funcionario esté vigente en la unidad seleccionada
- ✅ Que el RUT del funcionario sea válido
- ✅ Que el usuario no exista ya (si existe inactivo, lo reactiva)

---

### 3.2. BUSCAR USUARIO

#### Búsqueda Simple

1. En el Gestor de Usuarios, usar el campo de búsqueda superior
2. Escribir:
   - Código de funcionario
   - Nombre o apellido
   - RUT
3. Los resultados se filtran automáticamente

![Búsqueda Simple](imagenes/buscar_simple.png)

#### Búsqueda Paramétrica

1. Clic en **"Búsqueda Avanzada"**
2. Completar filtros:
   - **Por Nombre**: Primer nombre, segundo nombre, apellidos
   - **Por Unidad**: Seleccionar una o más unidades
   - **Por Perfil**: Marcar uno o más perfiles
3. Clic en **"BUSCAR"**

![Búsqueda Paramétrica](imagenes/buscar_parametrica.png)

#### Agregar Múltiples Unidades

1. En el campo "Unidad", buscar y seleccionar una unidad
2. Clic en **"AGREGAR >>"**
3. La unidad se agrega a la lista de la derecha
4. Repetir para agregar más unidades
5. Para quitar una unidad, clic en la **X** junto a su nombre

---

### 3.3. EDITAR USUARIO

#### Paso 1: Seleccionar Usuario

1. En el listado de usuarios, ubicar el usuario a editar
2. Clic en el botón **"Editar"** (ícono de lápiz)

![Botón Editar](imagenes/boton_editar.png)

#### Paso 2: Modificar Datos

Se abrirá un formulario con los datos actuales del usuario.

**Campos editables**:
- ✅ Perfil (Mesa de Ayuda o Administrador)
- ✅ Unidad

**Campos NO editables**:
- ❌ Código de funcionario
- ❌ RUT
- ❌ Nombre

#### Paso 3: Guardar Cambios

1. Modificar los campos deseados
2. Clic en **"GUARDAR CAMBIOS"**
3. Esperar mensaje de confirmación

**Nota**: Los cambios se aplican solo en la base de datos local. Autentificatic mantiene el registro del usuario.

---

### 3.4. ELIMINAR USUARIO

⚠️ **IMPORTANTE**: Solo usuarios con perfil **Administrador (310)** pueden eliminar usuarios.

#### Paso 1: Seleccionar Usuario

1. En el listado de usuarios, ubicar el usuario a eliminar
2. Clic en el botón **"Eliminar"** (ícono de papelera)

![Botón Eliminar](imagenes/boton_eliminar.png)

#### Paso 2: Confirmar Eliminación

1. Aparecerá un mensaje de confirmación:
   ```
   ¿Está seguro que desea eliminar al usuario [NOMBRE]?
   Esta acción inactivará al usuario en el sistema local
   y lo eliminará de Autentificatic.
   ```
2. Clic en **"SÍ, ELIMINAR"** para confirmar
3. Clic en **"CANCELAR"** para abortar

#### Paso 3: Verificar Eliminación

- El usuario aparecerá como **INACTIVO** en el listado
- El usuario **NO podrá iniciar sesión** en el sistema
- El usuario será **eliminado de Autentificatic**

**Restricciones**:
- ❌ No puede eliminar su propio usuario
- ❌ No puede eliminar usuarios ya inactivos

---

## 4. CONTROL DE SESIÓN

### 4.1. Tiempo de Inactividad

El sistema cierra automáticamente la sesión tras **15 minutos de inactividad**.

**¿Qué cuenta como actividad?**
- Mover el mouse
- Hacer clic en cualquier parte
- Escribir en el teclado
- Desplazarse (scroll)

### 4.2. Advertencia de Inactividad

A los **13 minutos** de inactividad, aparecerá una advertencia:

![Advertencia de Inactividad](imagenes/advertencia_inactividad.png)

**Opciones**:

1. **Continuar Sesión**: Clic en el botón verde → La sesión se extiende por 15 minutos más
2. **Cerrar Sesión**: Clic en el botón rojo → Cierra sesión inmediatamente
3. **No hacer nada**: Tras 15:01 minutos, la sesión se cierra automáticamente

### 4.3. Contador de Tiempo

El modal de advertencia muestra un **contador en tiempo real** del tiempo restante:

```
Tiempo restante: 2:00
```

Cuando llegue a `0:00`, la sesión se cerrará automáticamente.

### 4.4. Cerrar Sesión Manualmente

Para cerrar sesión en cualquier momento:

1. Clic en el menú de usuario (esquina superior derecha)
2. Clic en **"Cerrar Sesión"**

---

## 5. PREGUNTAS FRECUENTES

### ❓ ¿Puedo crear un usuario para un funcionario que no está en mi unidad?

**Respuesta**: Sí, siempre y cuando el funcionario esté **vigente** en la unidad que desea asignarle. El sistema valida automáticamente la pertenencia a la unidad consultando la tabla `CARGO_FUNCIONARIO`.

---

### ❓ ¿Qué pasa si intento crear un usuario que ya existe?

**Respuesta**: 
- Si el usuario está **activo**: El sistema mostrará un error indicando que el usuario ya existe.
- Si el usuario está **inactivo**: El sistema lo **reactivará automáticamente** con los nuevos datos (perfil y unidad).

---

### ❓ ¿Puedo cambiar la contraseña de un usuario?

**Respuesta**: No. La contraseña se establece solo al crear el usuario. Para cambiarla, debe:
1. Eliminar el usuario
2. Crearlo nuevamente con la nueva contraseña

**Nota**: La contraseña local es diferente a la de Autentificatic.

---

### ❓ ¿Qué diferencia hay entre Mesa de Ayuda y Administrador?

**Respuesta**:

| Acción | Mesa de Ayuda (90) | Administrador (310) |
|--------|-------------------|---------------------|
| Crear usuario | ✅ Sí | ✅ Sí |
| Editar usuario | ✅ Sí | ✅ Sí |
| Eliminar usuario | ❌ No | ✅ Sí |
| Ejecutar migración masiva | ❌ No | ✅ Sí |

---

### ❓ ¿Puedo eliminar mi propio usuario?

**Respuesta**: **No**. El sistema impide que un usuario se elimine a sí mismo para evitar pérdida de acceso accidental.

---

### ❓ ¿Qué pasa si mi sesión se cierra por inactividad?

**Respuesta**: Deberá iniciar sesión nuevamente con sus credenciales de Autentificatic. No se pierden datos, ya que todas las operaciones se guardan inmediatamente.

---

### ❓ ¿Puedo ver el historial de ingresos de un usuario?

**Respuesta**: Sí. En el Gestor de Usuarios:
1. Clic en el botón **"Ingresos"** junto al usuario
2. Se abrirá un modal con el historial de ingresos (fecha, hora, IP)

---

### ❓ ¿Qué significa "Error al registrar en Autentificatic"?

**Respuesta**: Significa que el usuario se creó en la base de datos local, pero falló la integración con Autentificatic. Posibles causas:
- Red caída
- Token de sesión expirado
- Servicio de Autentificatic no disponible

**Solución**: El sistema hace **rollback automático** (inactiva al usuario). Contacte a soporte técnico.

---

## 6. SOLUCIÓN DE PROBLEMAS

### 🔴 Problema: "El funcionario no pertenece a la unidad asignada"

**Causa**: El funcionario no está vigente en la unidad seleccionada según `CARGO_FUNCIONARIO`.

**Solución**:
1. Verificar en qué unidad está vigente el funcionario
2. Asignar la unidad correcta
3. Si el funcionario debe estar en otra unidad, actualizar primero `CARGO_FUNCIONARIO`

---

### 🔴 Problema: "Error al registrar en Autentificatic (HTTP 401)"

**Causa**: Token de sesión expirado.

**Solución**:
1. Cerrar sesión
2. Iniciar sesión nuevamente
3. Reintentar la operación

---

### 🔴 Problema: "Error al registrar en Autentificatic (HTTP 500)"

**Causa**: Error interno en el servidor de Autentificatic.

**Solución**:
1. Esperar unos minutos
2. Reintentar la operación
3. Si persiste, contactar a soporte técnico

---

### 🔴 Problema: La sesión se cierra antes de 15 minutos

**Causa**: Posible problema con el monitor de sesión.

**Solución**:
1. Limpiar caché del navegador
2. Cerrar y abrir el navegador
3. Iniciar sesión nuevamente
4. Si persiste, contactar a soporte técnico

---

### 🔴 Problema: No aparece la advertencia de inactividad

**Causa**: JavaScript deshabilitado o bloqueado.

**Solución**:
1. Verificar que JavaScript esté habilitado en el navegador
2. Desactivar bloqueadores de scripts (AdBlock, NoScript)
3. Recargar la página

---

### 🔴 Problema: No puedo buscar unidades

**Causa**: Error en la conexión a la base de datos.

**Solución**:
1. Recargar la página (F5)
2. Si persiste, contactar a soporte técnico

---

## 📞 CONTACTO Y SOPORTE

### Soporte Técnico

- **Email**: [Insertar email de soporte]
- **Teléfono**: [Insertar teléfono]
- **Horario**: Lunes a Viernes, 09:00 - 18:00

### Información a Proporcionar al Reportar un Problema

Para una atención más rápida, proporcione:

1. **Código de funcionario** del usuario afectado
2. **Acción que estaba realizando** (crear, editar, eliminar)
3. **Mensaje de error exacto** (captura de pantalla si es posible)
4. **Fecha y hora** del incidente
5. **Navegador utilizado** (Chrome, Firefox, IE)

---

## 📝 GLOSARIO

| Término | Definición |
|---------|------------|
| **Autentificatic** | Sistema institucional de autenticación de Carabineros de Chile |
| **Código de Funcionario** | Identificador único de 7 caracteres asignado a cada funcionario |
| **Perfil** | Tipo de usuario que determina los permisos en el sistema |
| **Unidad** | Comisaría, prefectura u otra dependencia de Carabineros |
| **US_ACTIVO** | Campo de la base de datos que indica si un usuario está activo (1) o inactivo (0) |
| **Rollback** | Reversión automática de una operación en caso de error |
| **Token de sesión** | Credencial temporal que valida la sesión del usuario |

---

## 📚 ANEXOS

### Anexo A: Códigos de Perfil

| Código | Descripción | Permisos |
|--------|-------------|----------|
| 90 | Mesa de Ayuda | Crear y editar usuarios |
| 310 | Administrador | Crear, editar y eliminar usuarios |

### Anexo B: Códigos de Error HTTP

| Código | Significado | Acción |
|--------|-------------|--------|
| 200 | OK | Operación exitosa |
| 201 | Created | Usuario creado exitosamente |
| 401 | Unauthorized | Token expirado, re-autenticar |
| 403 | Forbidden | Sin permisos para la operación |
| 404 | Not Found | Usuario o funcionario no encontrado |
| 409 | Conflict | Usuario ya existe (se considera éxito) |
| 500 | Internal Server Error | Error del servidor, contactar soporte |

---

**Versión del Manual**: 1.0.0  
**Última actualización**: Noviembre 2025  
**Elaborado por**: Ingeniero C.P.R. Denis Quezada Lemus