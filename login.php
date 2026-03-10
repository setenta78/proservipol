<?php
/**
 * SISTEMA DE APLICACIONES DE PROSERVIPOL
 * Página de Inicio de Sesión - Versión 4.0
 * @compatibility PHP 5.1.2 + MySQL 5.0.77
 * @author Ingeniero C.P.R. Denis Quezada Lemus
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
    <!-- Estilos -->
    <link rel="stylesheet" href="css/login.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <!-- ========== FORMULARIO DE LOGIN ========== -->
        <div class="login-form">
            <!-- Logo -->
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
        <!-- ========== IMAGEN DE FONDO ========== -->
        <div class="login-image"></div>
    </div>
    <!-- Scripts -->
    <script src="js/login.js"></script>
</body>
</html>