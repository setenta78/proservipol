<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "queries/config.php";
require_once "queries/general_queries.php";

$codigo = isset($_GET['codigo']) ? escape($_GET['codigo']) : '';

$funcionario = obtenerFuncionario($codigo);
$res_accesos = obtenerUltimosAccesos($codigo);
$perfiles = obtenerPerfiles();
$unidades = obtenerUnidades();
?>
<!-- Modal INGRESOS -->
<div class="flex justify-between items-center border-b pb-2 mb-4">
    <h2 class="text-xl font-bold">Buscar ingresos al sistema</h2>
    <button onclick="cerrarModalIngresos()" class="text-gray-500 hover:text-black">&times;</button>
</div>

<!-- Filtros -->
<form id="formIngresos" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Código funcionario o Rut -->
    <div>
        <label class="block text-sm font-medium">Código funcionario o Rut</label>
        <input type="text" name="codigo" class="w-full border rounded-lg px-3 py-2">
    </div>

    <!-- Perfil -->
    <div>
        <label class="block text-sm font-medium">Perfil</label>
        <select name="perfil" class="w-full border rounded-lg px-3 py-2">
            <option value="">-- Seleccionar --</option>
        </select>
    </div>

    <!-- Unidad -->
    <div>
        <label class="block text-sm font-medium">Unidad</label>
        <select name="unidad" class="w-full border rounded-lg px-3 py-2">
            <option value="">-- Seleccionar --</option>
        </select>
    </div>

    <!-- Fechas -->
    <div class="flex gap-2">
        <div class="flex-1">
            <label class="block text-sm font-medium">Fecha desde</label>
            <input type="date" name="fecha_desde" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium">Fecha hasta</label>
            <input type="date" name="fecha_hasta" class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>

    <!-- Botones -->
    <div class="md:col-span-2 flex justify-end gap-2 mt-2">
        <button type="reset" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg">Limpiar</button>
        <button type="button" onclick="buscarIngresos()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Buscar</button>
    </div>
</form>

<!-- Resultados -->
<div class="mt-6">
    <table class="w-full border-collapse border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Fecha y Hora</th>
                <th class="border px-2 py-1">Nombre</th>
                <th class="border px-2 py-1">Unidad</th>
                <th class="border px-2 py-1">Perfil</th>
                <th class="border px-2 py-1">IP</th>
            </tr>
        </thead>
        <tbody id="resultadosIngresos">
            <tr>
                <td colspan="5" class="text-center py-3">Esperando búsqueda...</td>
            </tr>
        </tbody>
    </table>
</div>