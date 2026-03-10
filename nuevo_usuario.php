<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once "queries/config.php";
require_once "queries/general_queries.php";
$perfiles = obtenerPerfiles();
$unidades = obtenerUnidades();
?>
<div class="w-full bg-white rounded-md p-1 mt-1">
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-green-700 border-b pb-1 mb-3">NUEVO USUARIO</h2>
        <form id="form-nuevo-usuario">
            <div class="flex gap-2 border rounded-lg overflow-hidden items-center">
                <!-- Columna 2 y 3: Formulario -->
                <div class="w-3/4 grid grid-cols-2 border-l">
                    <!-- Etiquetas -->
                    <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                        <label for="codigo" class="block text-right font-medium text-gray-600 text-xs">Código:</label>
                        <label for="rut" class="block text-right font-medium text-gray-600 text-xs">Rut:</label>
                        <label for="nombres" class="block text-right font-medium text-gray-600 text-xs">Nombres:</label>
                        <label for="apellidos" class="block text-right font-medium text-gray-600 text-xs">Apellidos:</label>
                        <label for="grado" class="block text-right font-medium text-gray-600 text-xs">Grado:</label>
                        <label for="unidad" class="block text-right font-medium text-gray-600 text-xs">Unidad PROSERVIPOL:</label>
                        <label for="cargo" class="block text-right font-medium text-gray-600 text-xs">Cargo registrado en Unidad:</label>
                        <label for="fechaCargo" class="block text-right font-medium text-gray-600 text-xs">Fecha Cargo actual:</label>
                        <label for="unidadAgregado" class="block text-right font-medium text-gray-600 text-xs">Unidad en que ejerce cargo registrado:</label>
                        <label for="curso" class="block text-right font-medium text-gray-600 text-xs">Curso Realizado:</label>
                    </div>
                    <!-- Inputs -->
                    <div id="formularioRegistro" class="bg-gray-100 p-2 space-y-2 text-xs">
                        <input type="text" name="codigo" id="codigo" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="rut" id="rut" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="nombres" id="nombres" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="apellidos" id="apellidos" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="grado" id="grado" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="unidad" id="unidad" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="cargo" id="cargo" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="fechaCargo" id="fechaCargo" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="unidadAgregado" id="unidadAgregado" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                        <input type="text" name="curso" id="curso" class="form-input w-full text-xs" readonly placeholder="Datos se cargarán automáticamente">
                    </div>
                </div>
                <!-- Busqueda del bloque anterior -->
                <div id="formularioBusqueda" class="w-1/4 flex items-center gap-2 p-2">
                    <input type="text" id="textCodFuncionarioBusqueda" name="textCodFuncionarioBusqueda" placeholder="Código" maxlength="7" autocomplete="off"
                        class="border border-gray-300 rounded px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" />
                    <input class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded" type="button" id="btnBuscar" onclick="cargarUsuario()" value="BUSCAR" />
                    <input class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 text-sm rounded" type="button" id="btnLimpiar" onclick="borrarValores()" value="LIMPIAR" />
                </div>
            </div>
            <br>
            <h2 class="text-sm font-semibold text-green-700 border-b pb-1 mb-3">DATOS PERFIL DE ACCESO</h2>

            <div class="flex gap-2 border rounded-lg overflow-hidden items-center">
                <div class="w-3/4 grid grid-cols-2 border-l">
                    <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                        <label for="codigo" class="block text-right font-medium text-gray-600 text-xs">Perfil:</label>
                        <label for="unidad" class="block text-right font-medium text-gray-600 text-xs">Unidad:</label>
                        <br>
                        <label for="usuarioProservipol" class="block text-right font-medium text-gray-600 text-xs">Usuario PROSERVIPOL:</label>
                        <label for="proClave" class="block text-right font-medium text-gray-600 text-xs">Clave PROSERVIPOL:</label>
                    </div>
                    <div class="bg-gray-100 p-2 space-y-2 text-xs">
                        <select name="perfil" id="perfil" class="w-full border rounded focus:outline-none focus:ring focus:border-green-500 text-xs" required>
                            <option value="" disabled selected>Seleccionar</option>
                            <?php
                            foreach ($perfiles as $perfil) {
                                $selected = ($perfil['TUS_CODIGO'] == $funcionario['TUS_CODIGO']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($perfil['TUS_CODIGO']) . '" ' . $selected . '>' . htmlspecialchars($perfil['TUS_DESCRIPCION']) . '</option>';
                            }
                            ?>
                        </select>
                        <div class="search-box">
                            <input type="text" placeholder="Buscar unidad..." autocomplete="off" class="w-full border rounded text-xs px-2 py-1" required>
                            <div class="result"></div>
                            <input type="hidden" name="unidadPerfil" class="unidadPerfil" value="" required>
                        </div>
                        <input type="text" name="proUsuario" class="form-input w-full text-xs" placeholder="Ingrese código de funcionario" required>
                        <input type="password" name="proClave" class="form-input w-full text-xs" placeholder="Ingrese una clave diferente a Autentificatic" required>
                    </div>
                </div>
            </div>
            <div class="w-1/4 flex items-center gap-2 p-2 justify-end">
                <button onclick="cerrarModal()" type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 text-sm rounded">
                    CANCELAR
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-md shadow-sm text-sm">
                    CREAR USUARIO
                </button>

            </div>
    </div>
    </form>
</div>
</div>