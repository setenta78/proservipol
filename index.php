<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL & ~E_NOTICE);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="img/logo.png">
    <title>Sistema de Aplicaciones de PROSERVIPOL</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Monitor de Sesión -->
    <script src="js/session_monitor.js"></script>
</head>
<body class="bg-gray-50 text-gray-700">
    <!-- Cabecera fija -->
    <?php include "header.php"; ?>
    <!-- Menú lateral fijo -->
    <div class="flex">
        <?php include "nav.php"; ?>
        <!-- Contenido Principal -->
        <div class="ml-64 w-full mt-20 p-5">
            <h3 class="text-center text-green-800 font-semibold mt-5">Sistema de Aplicaciones de PROSERVIPOL</h3>
            <hr class="my-2 border-gray-400">
            <!-- Bienvenida -->
            <div class="text-center mb-8">
                <p class="text-lg">Bienvenido(a), <strong class="text-green-700"><?php echo $nombreUsuario; ?></strong></p>
                <p class="text-sm text-gray-600 mt-2">Seleccione una opción para comenzar</p>
            </div>
            <!-- Tarjetas de Módulos (solo activos) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-6">
                <!-- Gestión de Usuarios -->
                <a href="gestor_usuarios.php" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300 border border-green-200">
                        <div class="text-green-600 mb-4">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-gray-800">Gestor de Usuarios</h4>
                        <p class="text-sm text-gray-600 mt-2">Administrar altas, bajas y perfiles de usuarios en PROSERVIPOL</p>
                    </div>
                </a>
                <!-- Buscar Ingresos -->
                <a href="buscar_ingresos_usuario.php" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300 border border-green-200">
                        <div class="text-green-600 mb-4">
                            <i class="fas fa-sign-in-alt fa-3x"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-gray-800">Buscar Ingresos</h4>
                        <p class="text-sm text-gray-600 mt-2">Consultar historial de ingresos al sistema con filtros por fecha, unidad y funcionario</p>
                    </div>
                </a>
                <!-- Cámaras SAP -->
                <a href="camaras.php" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300 border border-green-200">
                        <div class="text-green-600 mb-4">
                            <i class="fas fa-camera fa-3x"></i>
                        </div>
                        <h4 class="font-semibold text-lg text-gray-800">Cámaras SAP</h4>
                        <p class="text-sm text-gray-600 mt-2">Gestión de cámaras corporales y carga de datos desde SAP</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>