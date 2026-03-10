<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "queries/config.php";
require_once "queries/general_queries.php";

$perfiles = obtenerPerfiles();
$unidades = obtenerUnidades();
?>
<form method="GET" action="gestor_usuarios.php" class="space-y-4">
    <div class="w-full bg-white rounded-md p-4 mt-1">
        <h2 class="text-sm font-semibold text-green-700 mb-3 text-left">BÚSQUEDA PARAMÉTRICA</h2>

        <!-- Nombre de usuario -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Por Nombre de Usuario</label>
            <div class="grid grid-cols-2 gap-2">
                <input type="text" name="nombre1" placeholder="Primer Nombre" class="border rounded px-2 py-1 text-xs w-full">
                <input type="text" name="nombre2" placeholder="Segundo Nombre" class="border rounded px-2 py-1 text-xs w-full">
                <input type="text" name="apellido1" placeholder="Primer Apellido" class="border rounded px-2 py-1 text-xs w-full">
                <input type="text" name="apellido2" placeholder="Segundo Apellido" class="border rounded px-2 py-1 text-xs w-full">
            </div>
        </div>

        <!-- Unidades con búsqueda -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Por una o más Unidad</label>
            <!-- Campo de búsqueda -->
            <input type="text" id="unidad-buscar" placeholder="Buscar unidad..."
                class="border rounded px-2 py-1 text-xs w-full mb-2">

            <div class="grid grid-cols-3 gap-2">
                <!-- Select con todas las unidades -->
                <select id="unidad-select" size="5" class="border rounded px-2 py-1 text-xs col-span-1 w-full">
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?php echo $u['UNI_CODIGO']; ?>">
                            <?php echo htmlspecialchars($u['UNI_DESCRIPCION']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Botones agregar/quitar -->
                <div class="flex flex-col gap-1">
                    <button type="button" id="btn-agregar" class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-1 rounded">AGREGAR &gt;&gt;</button>
                    <!-- <button type="button" id="btn-quitar" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-2 py-1 rounded">&lt;&lt; QUITAR</button>-->
                </div>

                <!-- Lista de unidades agregadas -->
                <div id="lista-unidades" class="border rounded px-2 py-1 text-xs col-span-1 h-20 overflow-y-auto space-y-1"></div>
                <input type="hidden" name="unidades_ids" id="unidades-ids">
            </div>
        </div>

        <!-- Perfiles -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Por uno o más Perfil de Usuario</label>
            <div class="grid grid-cols-3 gap-2">
                <?php foreach ($perfiles as $p): ?>
                    <label class="flex items-center space-x-2 border rounded p-1 cursor-pointer hover:bg-green-50">
                        <input type="checkbox" name="perfiles[]" value="<?php echo $p['TUS_CODIGO']; ?>" class="accent-green-600">
                        <span class="text-xs"><?php echo htmlspecialchars($p['TUS_DESCRIPCION']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Botones -->
        <div class="mt-4 flex justify-between">
            <button onclick="cerrarModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded text-sm">
                CANCELAR
            </button>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-sm">
                BUSCAR
            </button>
        </div>
    </div>
</form>