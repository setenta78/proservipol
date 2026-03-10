# 📊 DIAGRAMAS DE FLUJO - SISTEMA DE GESTIÓN DE USUARIOS PROSERVIPOL

**Versión**: 1.0.0  
**Fecha**: Noviembre 2025

---

## 📑 ÍNDICE

1. [Flujo de Autenticación](#1-flujo-de-autenticación)
2. [Flujo de Creación de Usuario](#2-flujo-de-creación-de-usuario)
3. [Flujo de Edición de Usuario](#3-flujo-de-edición-de-usuario)
4. [Flujo de Eliminación de Usuario](#4-flujo-de-eliminación-de-usuario)
5. [Flujo de Control de Sesión](#5-flujo-de-control-de-sesión)
6. [Flujo de Migración Masiva](#6-flujo-de-migración-masiva)
7. [Arquitectura del Sistema](#7-arquitectura-del-sistema)

---

## 1. FLUJO DE AUTENTICACIÓN

```
┌─────────────────────────────────────────────────────────────────┐
│                    INICIO: Usuario accede a login.php            │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Ingresar RUT y       │
              │ Contraseña           │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ POST a Autentificatic│
              │ API                  │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ HTTP 200 │      │ HTTP 4xx │
         │ (Éxito)  │      │ (Error)  │
         └─────┬────┘      └─────┬────┘
               │                 │
               ▼                 ▼
    ┌──────────────────┐  ┌──────────────────┐
    │ Obtener token y  │  │ Mostrar error:   │
    │ datos usuario    │  │ "Credenciales    │
    └─────┬────────────┘  │  incorrectas"    │
          │               └──────────────────┘
          ▼
    ┌──────────────────┐
    │ POST a           │
    │ save_token.php   │
    └─────┬────────────┘
          │
          ▼
    ┌──────────────────┐
    │ Consultar BD:    │
    │ SELECT US_ACTIVO,│
    │ TUS_CODIGO       │
    │ FROM USUARIO     │
    └─────┬────────────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│US_ACTIVO│ │US_ACTIVO│
│= 1      │ │= 0      │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────────┐
│TUS_CODIGO│ │ Error:      │
│IN (90,   │ │ "Usuario    │
│310)?     │ │  inactivo"  │
└────┬────┘ └─────────────┘
     │
┌────┴────┐
│         │
▼         ▼
┌───┐   ┌───┐
│Sí │   │No │
└─┬─┘   └─┬─┘
  │       │
  ▼       ▼
┌─────┐ ┌──────────┐
│Crear│ │Error:    │
│sesión│ │"Sin      │
│$_    │ │permisos" │
│SESSION│ └──────────┘
└──┬──┘
   │
   ▼
┌──────────────────┐
│ Redirect a       │
│ gestor_usuarios  │
│ .php             │
└──────────────────┘
```

---

## 2. FLUJO DE CREACIÓN DE USUARIO

```
┌─────────────────────────────────────────────────────────────────┐
│         INICIO: Usuario hace clic en "Nuevo Usuario"             │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Mostrar formulario   │
              │ modal                │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Usuario completa:    │
              │ - FUN_CODIGO         │
              │ - TUS_CODIGO         │
              │ - UNI_CODIGO         │
              │ - US_PASSWORD        │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Validar campos       │
              │ obligatorios         │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ Válidos  │      │ Inválidos│
         └─────┬────┘      └─────┬────┘
               │                 │
               │                 ▼
               │          ┌──────────────┐
               │          │ Mostrar      │
               │          │ errores      │
               │          └──────────────┘
               │
               ▼
    ┌──────────────────────┐
    │ POST a               │
    │ /api/crearUsuario/   │
    │ index.php            │
    └──────────┬───────────┘
               │
               ▼
    ┌──────────────────────┐
    │ Validar FUN_CODIGO   │
    │ existe en            │
    │ FUNCIONARIO          │
    └──────────┬───────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
   ┌────────┐    ┌────────┐
   │ Existe │    │No existe│
   └───┬────┘    └───┬────┘
       │             │
       │             ▼
       │        ┌─────────────┐
       │        │ Error 404:  │
       │        │ "Funcionario│
       │        │  no existe" │
       │        └─────────────┘
       │
       ▼
┌──────────────────────┐
│ Validar pertenencia  │
│ a unidad:            │
│ SELECT 1 FROM        │
│ CARGO_FUNCIONARIO    │
│ WHERE FUN_CODIGO=?   │
│ AND UNI_CODIGO=?     │
│ AND FECHA_HASTA      │
│ IS NULL              │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│Vigente │    │No      │
│        │    │vigente │
└───┬────┘    └───┬────┘
    │             │
    │             ▼
    │        ┌─────────────┐
    │        │ Error 400:  │
    │        │ "No         │
    │        │  pertenece  │
    │        │  a unidad"  │
    │        └─────────────┘
    │
    ▼
┌──────────────────────┐
│ Verificar si usuario │
│ ya existe:           │
│ SELECT US_ACTIVO     │
│ FROM USUARIO         │
│ WHERE FUN_CODIGO=?   │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│No      │    │Existe  │
│existe  │    │        │
└───┬────┘    └───┬────┘
    │             │
    │        ┌────┴────┐
    │        │         │
    │        ▼         ▼
    │    ┌───────┐ ┌───────┐
    │    │US_    │ │US_    │
    │    │ACTIVO │ │ACTIVO │
    │    │= 1    │ │= 0    │
    │    └───┬───┘ └───┬───┘
    │        │         │
    │        ▼         ▼
    │    ┌───────┐ ┌───────────┐
    │    │Error: │ │Llamar a   │
    │    │"Ya    │ │reactivar  │
    │    │existe"│ │Usuario()  │
    │    └───────┘ └─────┬─────┘
    │                    │
    │◄───────────────────┘
    │
    ▼
┌──────────────────────┐
│ INSERT INTO USUARIO  │
│ (..., US_ACTIVO=1)   │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Normalizar RUT       │
│ (sin puntos/guión)   │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Llamar a             │
│ AutentificaticHttp   │
│ Client::registrar    │
│ Usuario()            │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ shell_exec(          │
│ 'curl -X POST        │
│  -H "Authorization:  │
│   Bearer $token"     │
│  -d "rut=$rut"       │
│  $url'               │
│ )                    │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│HTTP    │    │HTTP    │
│200/201 │    │4xx/5xx │
│/409    │    │        │
└───┬────┘    └───┬────┘
    │             │
    ▼             ▼
┌────────┐    ┌─────────────┐
│ÉXITO   │    │ ROLLBACK:   │
│        │    │ UPDATE      │
│Retornar│    │ USUARIO SET │
│success │    │ US_ACTIVO=0 │
│= true  │    │             │
└────────┘    │ Retornar    │
              │ success=    │
              │ false       │
              └─────────────┘
```

---

## 3. FLUJO DE EDICIÓN DE USUARIO

```
┌─────────────────────────────────────────────────────────────────┐
│         INICIO: Usuario hace clic en "Editar"                    │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Cargar datos         │
              │ actuales del usuario │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Mostrar formulario   │
              │ con datos            │
              │ pre-cargados         │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Usuario modifica:    │
              │ - TUS_CODIGO         │
              │ - UNI_CODIGO         │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ PUT a                │
              │ /api/editarUsuario/  │
              │ index.php            │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Validar usuario      │
              │ existe y está activo │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ Activo   │      │ Inactivo │
         │          │      │ o no     │
         │          │      │ existe   │
         └─────┬────┘      └─────┬────┘
               │                 │
               │                 ▼
               │          ┌──────────────┐
               │          │ Error 400    │
               │          └──────────────┘
               │
               ▼
    ┌──────────────────────┐
    │ Validar pertenencia  │
    │ a nueva unidad       │
    └──────────┬───────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
   ┌────────┐    ┌────────┐
   │Válida  │    │Inválida│
   └───┬────┘    └───┬────┘
       │             │
       │             ▼
       │        ┌─────────────┐
       │        │ Error 400   │
       │        └─────────────┘
       │
       ▼
┌──────────────────────┐
│ UPDATE USUARIO       │
│ SET TUS_CODIGO=?,    │
│     UNI_CODIGO=?     │
│ WHERE FUN_CODIGO=?   │
└──────────┬───────────┘
           │
           ▼
    ┌──────────────┐
    │ Retornar     │
    │ success=true │
    └──────────────┘
```

---

## 4. FLUJO DE ELIMINACIÓN DE USUARIO

```
┌─────────────────────────────────────────────────────────────────┐
│         INICIO: Usuario hace clic en "Eliminar"                  │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Validar perfil       │
              │ = 310 (Admin)        │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ Es Admin │      │ No es    │
         │          │      │ Admin    │
         └─────┬────┘      └─────┬────┘
               │                 │
               │                 ▼
               │          ┌──────────────┐
               │          │ Error 403:   │
               │          │ "Sin         │
               │          │  permisos"   │
               │          └──────────────┘
               │
               ▼
    ┌──────────────────────┐
    │ Mostrar confirmación │
    │ "¿Está seguro?"      │
    └──────────┬───────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
   ┌────────┐    ┌────────┐
   │ SÍ     │    │ NO     │
   └───┬────┘    └───┬────┘
       │             │
       │             ▼
       │        ┌─────────┐
       │        │ Cancelar│
       │        └─────────┘
       │
       ▼
┌──────────────────────┐
│ Validar que no sea   │
│ el mismo usuario     │
│ logueado             │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│Diferente│   │Mismo   │
└───┬────┘    └───┬────┘
    │             │
    │             ▼
    │        ┌─────────────┐
    │        │ Error 400:  │
    │        │ "No puede   │
    │        │  eliminar   │
    │        │  su propio  │
    │        │  usuario"   │
    │        └─────────────┘
    │
    ▼
┌──────────────────────┐
│ DELETE a             │
│ /api/eliminarUsuario/│
│ index.php            │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Obtener RUT del      │
│ funcionario          │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Llamar a             │
│ AutentificaticHttp   │
│ Client::eliminar     │
│ Usuario()            │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ shell_exec(          │
│ 'curl -X DELETE      │
│  -H "Authorization:  │
│   Bearer $token"     │
│  -d "rut=$rut"       │
│  $url'               │
│ )                    │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│HTTP    │    │HTTP    │
│200     │    │4xx/5xx │
└───┬────┘    └───┬────┘
    │             │
    ▼             ▼
┌────────────┐ ┌─────────────┐
│UPDATE      │ │ Error:      │
│USUARIO SET │ │ Retornar    │
│US_ACTIVO=0 │ │ success=    │
│WHERE       │ │ false       │
│FUN_CODIGO=?│ └─────────────┘
└─────┬──────┘
      │
      ▼
┌──────────────┐
│ Retornar     │
│ success=true │
└──────────────┘
```

---

## 5. FLUJO DE CONTROL DE SESIÓN

```
┌─────────────────────────────────────────────────────────────────┐
│         INICIO: Usuario inicia sesión                            │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Inicializar          │
              │ session_monitor.js   │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ ultimaActividad =    │
              │ new Date().getTime() │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Registrar eventos:   │
              │ - mousedown          │
              │ - mousemove          │
              │ - keypress           │
              │ - scroll             │
              │ - touchstart         │
              │ - click              │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Cada evento →        │
              │ actualizarActividad()│
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ setInterval(         │
              │   verificar          │
              │   Inactividad,       │
              │   30000              │
              │ )                    │
              └──────────┬───────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Cada 30 segundos:    │
              │ Calcular tiempo      │
              │ inactivo             │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ < 13 min │      │ >= 13 min│
         └─────┬────┘      └─────┬────┘
               │                 │
               ▼                 ▼
         ┌──────────┐      ┌──────────────┐
         │ Continuar│      │ Mostrar modal│
         │ esperando│      │ de advertencia│
         └──────────┘      └──────┬───────┘
                                  │
                                  ▼
                           ┌──────────────┐
                           │ Mostrar      │
                           │ contador:    │
                           │ "Tiempo      │
                           │  restante:   │
                           │  2:00"       │
                           └──────┬───────┘
                                  │
                           ┌──────┴──────┐
                           │             │
                           ▼             ▼
                    ┌──────────┐  ┌──────────┐
                    │ Usuario  │  │ Usuario  │
                    │ hace clic│  │ no hace  │
                    │ "Continuar│  │ nada     │
                    │  Sesión" │  │          │
                    └─────┬────┘  └─────┬────┘
                          │             │
                          ▼             ▼
                    ┌──────────┐  ┌──────────┐
                    │ Cerrar   │  │ Esperar  │
                    │ modal    │  │ hasta    │
                    │          │  │ 15:01 min│
                    │ Actualizar│  └─────┬────┘
                    │ última   │        │
                    │ actividad│        ▼
                    └──────────┘  ┌──────────────┐
                                  │ Cerrar modal │
                                  │              │
                                  │ alert("Su    │
                                  │  sesión ha   │
                                  │  expirado")  │
                                  └──────┬───────┘
                                         │
                                         ▼
                                  ┌──────────────┐
                                  │ window.      │
                                  │ location.href│
                                  │ = "logout.php│
                                  │ ?reason=     │
                                  │ inactivity"  │
                                  └──────────────┘
```

---

## 6. FLUJO DE MIGRACIÓN MASIVA

```
┌─────────────────────────────────────────────────────────────────┐
│  INICIO: Admin ejecuta migrar_usuarios_a_autentificatic.php     │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Validar sesión       │
              │ activa               │
              └──────────┬───────────┘
                         │
                ┌────────┴────────┐
                │                 │
                ▼                 ▼
         ┌──────────┐      ┌──────────┐
         │ Activa   │      │ No activa│
         └─────┬────┘      └─────┬────┘
               │                 │
               │                 ▼
               │          ┌──────────────┐
               │          │ Error:       │
               │          │ "Debe estar  │
               │          │  autenticado"│
               │          └──────────────┘
               │
               ▼
    ┌──────────────────────┐
    │ Validar perfil       │
    │ = 310 (Admin)        │
    └──────────┬───────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
   ┌────────┐    ┌────────┐
   │ Es 310 │    │ No es  │
   │        │    │ 310    │
   └───┬────┘    └───┬────┘
       │             │
       │             ▼
       │        ┌─────────────┐
       │        │ Error:      │
       │        │ "Solo Admin"│
       │        └─────────────┘
       │
       ▼
┌──────────────────────┐
│ Crear archivo CSV:   │
│ migracion_resultado_ │
│ YYYYMMDD_HHMMSS.csv  │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ SELECT FUN_CODIGO,   │
│ FUN_RUT, NOMBRE      │
│ FROM USUARIO U       │
│ INNER JOIN           │
│ FUNCIONARIO F        │
│ WHERE US_ACTIVO = 1  │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ PARA CADA usuario:   │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Normalizar RUT       │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│ Válido │    │Inválido│
└───┬────┘    └───┬────┘
    │             │
    │             ▼
    │        ┌─────────────┐
    │        │ Escribir CSV│
    │        │ FALLA,      │
    │        │ "RUT        │
    │        │  inválido"  │
    │        └─────────────┘
    │
    ▼
┌──────────────────────┐
│ Llamar a             │
│ AutentificaticHttp   │
│ Client::registrar    │
│ Usuario()            │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ shell_exec(curl...)  │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐    ┌────────┐
│HTTP 200│    │HTTP 409│
│o 201   │    │        │
└───┬────┘    └───┬────┘
    │             │
    ▼             ▼
┌────────┐    ┌────────────┐
│Escribir│    │ Escribir   │
│CSV:    │    │ CSV:       │
│EXITO   │    │ YA_EXISTE  │
└───┬────┘    └─────┬──────┘
    │               │
    │◄──────────────┘
    │
    ▼
┌────────────┐
│ usleep(    │
│ 100000     │
│ )          │
│ (pausa     │
│  100ms)    │
└─────┬──────┘
      │
      ▼
┌──────────────┐
│ Siguiente    │
│ usuario      │
└──────┬───────┘
       │
       ▼
┌──────────────────────┐
│ FIN DEL BUCLE        │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Cerrar archivo CSV   │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Mostrar resumen:     │
│ - Total procesados   │
│ - Exitosos           │
│ - Ya existentes      │
│ - Fallidos           │
│ - Tasa de éxito      │
└──────────────────────┘
```

---

## 7. ARQUITECTURA DEL SISTEMA

```
┌─────────────────────────────────────────────────────────────────┐
│                         CAPA DE PRESENTACIÓN                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ login.php    │  │ gestor_      │  │ nuevo_       │          │
│  │              │  │ usuarios.php │  │ usuario.php  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│  ┌──────────────┐  ┌──────────────┐                            │
│  │ editar_      │  │ buscar_      │                            │
│  │ usuario.php  │  │ usuario.php  │                            │
│  └──────────────┘  └──────────────┘                            │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CAPA DE SEGURIDAD                           │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ middleware_auth.php                                       │  │
│  │ - Validar sesión activa                                  │  │
│  │ - Validar token no expirado                              │  │
│  │ - Validar perfil autorizado                              │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ session_monitor.js                                        │  │
│  │ - Monitorear inactividad                                 │  │
│  │ - Cerrar sesión automáticamente                          │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CAPA DE LÓGICA DE NEGOCIO                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ /api/crearUsuario/index.php                              │  │
│  │ - Validar datos                                          │  │
│  │ - Llamar a usuario_queries.php                           │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ /api/editarUsuario/index.php                             │  │
│  │ - Validar datos                                          │  │
│  │ - Llamar a usuario_queries.php                           │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ /api/eliminarUsuario/index.php                           │  │
│  │ - Validar permisos                                       │  │
│  │ - Llamar a usuario_queries.php                           │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   CAPA DE ACCESO A DATOS                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ /queries/usuario_queries.php                             │  │
│  │ ┌────────────────────────────────────────────────────┐  │  │
│  │ │ crearUsuario()                                      │  │  │
│  │ │ 1. Validar funcionario                              │  │  │
│  │ │ 2. Validar pertenencia a unidad                     │  │  │
│  │ │ 3. INSERT en USUARIO                                │  │  │
│  │ │ 4. Registrar en Autentificatic                      │  │  │
│  │ │ 5. Si falla → Rollback (US_ACTIVO=0)               │  │  │
│  │ └────────────────────────────────────────────────────┘  │  │
│  │ ┌────────────────────────────────────────────────────┐  │  │
│  │ │ editarUsuario()                                     │  │  │
│  │ │ 1. Validar usuario activo                           │  │  │
│  │ │ 2. Validar pertenencia a unidad                     │  │  │
│  │ │ 3. UPDATE USUARIO                                   │  │  │
│  │ └────────────────────────────────────────────────────┘  │  │
│  │ ┌────────────────────────────────────────────────────┐  │  │
│  │ │ eliminarUsuario()                                   │  │  │
│  │ │ 1. Validar usuario activo                           │  │  │
│  │ │ 2. Eliminar de Autentificatic                       │  │  │
│  │ │ 3. UPDATE USUARIO SET US_ACTIVO=0                  │  │  │
│  │ │ 4. Si falla → Rollback (re-registrar)              │  │  │
│  │ └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌──────────────────┐          ┌──────────────────────┐
│  BD Local MySQL  │          │  Autentificatic API  │
│  proservipol_test│          │  (Intranet)          │
│                  │          │                      │
│  ┌────────────┐ │          │  ┌────────────────┐ │
│  │ USUARIO    │ │          │  │ POST /api/     │ │
│  │ FUNCIONARIO│ │          │  │ institutional- │ │
│  │ CARGO_...  │ │          │  │ app-user...    │ │
│  │ UNIDAD     │ │          │  │                │ │
│  │ TIPO_...   │ │          │  │ DELETE /api/   │ │
│  └────────────┘ │          │  │ institutional- │ │
│                  │          │  │ app-user...    │ │
│  mysql_query()   │          │  └────────────────┘ │
│  mysql_real_     │          │                      │
│  escape_string() │          │  shell_exec(curl)    │
└──────────────────┘          └──────────────────────┘
```

---

**Versión**: 1.0.0  
**Última actualización**: Noviembre 2025  
**Elaborado por**: Ingeniero C.P.R. Denis Quezada Lemus