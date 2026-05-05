<?php
/**
 * SISTEMA DE APLICACIONES DE PROSERVIPOL
 * Página de Inicio de Sesión - Versión 4.4
 * @compatibility PHP 5.1.2 + MySQL 5.0.77
 * @autor Ingeniero C.P.R. Denis Quezada Lemus
 * @date 2025
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Redirigir si ya está autenticado
if (isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Iniciar Sesión - PROSERVIPOL</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/logo.png">
    <!-- Precarga de imágenes (no bloquean render) -->
    <link rel="preload" as="image" href="img/fondo-login.webp" type="image/webp">
    <link rel="preload" as="image" href="img/logo_aniversario.png" type="image/png">
    <link rel="preload" as="image" href="img/carabineros.png" type="image/png">
    <link rel="preload" as="image" href="img/logo_dcgs.png" type="image/png">
    <!-- Estilos -->
    <link rel="stylesheet" href="css/login.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <!-- ========== FORMULARIO DE LOGIN ========== -->
        <div class="login-form">
            <!-- Logo superior del sistema -->
            <div class="logo">
                <img src="img/logobanner.png" alt="Carabineros de Chile">
            </div>
            <!-- Título -->
            <h1 class="title">Sistema de aplicativos PROSERVIPOL</h1>
            <p class="subtitle">
                Para iniciar sesión, se necesita ingresar con su cuenta de <strong>Autentificatic</strong>
            </p>
            <!-- Formulario -->
            <form id="form_login" method="POST" autocomplete="off">
                <!-- Campo RUT -->
                <div class="input-group">
                    <label for="rut_funcionario">
                        <i class="fas fa-id-card"></i> RUT
                    </label>
                    <input 
                        type="text" 
                        id="rut_funcionario" 
                        name="rut_funcionario" 
                        placeholder="Ingresa tu RUT sin guiones ni puntos" 
                        required
                        maxlength="9"
                        pattern="[0-9]{7,9}"
                        title="Ingrese entre 7 y 9 dígitos numéricos"
                    >
                </div>
                <!-- Campo Contraseña -->
                <div class="input-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingresa tu contraseña" 
                        required
                    >
                </div>
                <!-- Botón de Login -->
                <button type="button" id="btn-login" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
                <!-- Enlaces -->
                <div class="links">
                    <a href="http://autentificatic.carabineros.cl/password/reset" target="_blank">
                        <i class="fas fa-key"></i> Recuperar contraseña
                    </a>
                    <a href="http://autentificatic.carabineros.cl/register" target="_blank">
                        <i class="fas fa-user-plus"></i> Regístrate
                    </a>
                </div>
            </form>
            <!-- Footer -->
            <div class="login-footer">
                <div class="footer-box">
                    <p class="footer-title">
                        Desarrollado por Departamento de Control de Gestión y Sistemas de Información
                    </p>
                    <p class="text-xs">
                        I.P. Mesa de Ayuda:
                    </p>
                    <p class="text-xs">
                        20828 - 20843 - 20844
                    </p>
                    <p class="text-xs">
                        © 2025 Carabineros de Chile
                    </p>
                    <a href="http://proservipol.carabineros.cl" 
                       target="_blank" 
                       class="btn-outline">
                        <i class="fas fa-desktop"></i> Ir a PROSERVIPOL
                    </a>
                </div>
            </div>
        </div>

        <!-- ========== COLUMNA DERECHA: FONDO + ELEMENTOS ========== -->
        <div class="login-image" id="login-image-bg">
            <!-- Carabineros arriba izquierda, con ligera transparencia -->
            <img src="img/carabineros.png" 
                 alt="Carabineros de Chile" 
                 class="bg-layer bg-carabineros">
            <!-- Logos inferiores: izquierda DCGS, derecha Aniversario -->
            <img src="img/logo_dcgs.png" 
                 alt="D.C.G.S.I." 
                 class="bg-layer bg-logo-dcgs">
            <img src="img/logo_aniversario.png" 
                 alt="Aniversario Carabineros de Chile" 
                 class="bg-layer bg-logo-aniversario">
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/proservipol_alerts.js"></script>
    <script src="js/login.js"></script>

    <!-- Carga diferida de imagen de fondo -->
    <script>
    (function() {
        var mediaQuery = window.matchMedia('(min-width: 1024px)');

        function cargarFondo() {
            if (!mediaQuery.matches) return; // no cargar en móvil

            var bg = document.getElementById('login-image-bg');
            if (!bg) return;

            var img = new Image();
            img.onload = function() {
                bg.style.backgroundImage = "url('img/fondo-login.webp')";
                bg.classList.add('bg-cargada');
            };
            img.onerror = function() {
                bg.classList.add('bg-cargada');
            };
            img.src = 'img/fondo-login.webp';
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cargarFondo);
        } else {
            cargarFondo();
        }
    })();
    </script>
</body>
</html>