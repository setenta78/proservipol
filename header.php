<?php
session_start();
require_once 'middleware_auth.php';
?>
<nav class="fixed w-full bg-green-800 py-6 shadow-lg z-10">
    <div class="flex items-center justify-between px-6">
        <a href="index.php" class="flex items-center space-x-3">
            <img src="img/logobanner.png" alt="Web Developer CPR Daniela Parra" width="150">
        </a>
        <div class="text-white text-sm text-center">
            <h5>Departamento Control de Gestión</h5>
            <p>Sistemas de Información</p>
        </div>
    </div>
</nav>
<script src="js/proservipol_alerts.js"></script>