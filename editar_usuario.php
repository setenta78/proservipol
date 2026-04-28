<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL & ~E_NOTICE);
require_once "queries/config.php";
require_once "queries/general_queries.php";
require_once "queries/editar_queries.php";
require_once "queries/eliminar_queries.php";

$codigo = isset($_GET['codigo']) ? mysql_real_escape_string($_GET['codigo'], $link) : '';

if (!$codigo) {
    die("Codigo de usuario no proporcionado.");
}

$funcionario = obtenerFuncionario($codigo);

if (!$funcionario) {
    die("Usuario no encontrado.");
}

$perfiles = obtenerPerfiles();
$unidades = obtenerUnidades();
$accesos  = obtenerUltimosAccesos($codigo);
?>

<div class="w-full bg-white rounded-md p-1 mt-1">
    <div class="space-y-4">
        <h2 class="text-xs font-semibold text-green-700 mb-1 text-left">DATOS GENERALES DE USUARIO</h2>
        <div class="flex gap-2 border rounded-lg overflow-hidden items-center">
            <div class="w-1/4 flex items-start justify-center p-2">
                <img src="img/default_user.png" alt="Foto del funcionario" class="w-32 h-32 object-cover rounded-full border shadow">
            </div>
            <div class="w-3/4 grid grid-cols-2 border-l">
                <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                    <p>Codigo:</p>
                    <p>Rut:</p>
                    <p>Nombres:</p>
                    <p>Apellidos:</p>
                    <p>Grado:</p>
                    <p>Dotacion Segun PROSERVIPOL:</p>
                    <p>Cargo registrado en PROSERVIPOL:</p>
                </div>
                <div class="bg-gray-100 p-2 space-y-2 text-xs">
                    <p><?php echo htmlspecialchars($funcionario['FUN_CODIGO'],          ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_RUT'],             ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_NOMBRE'] . ' ' . $funcionario['FUN_NOMBRE2'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_APELLIDOPATERNO'] . ' ' . $funcionario['FUN_APELLIDOMATERNO'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['GRA_DESCRIPCION'],     ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['UNI_DESCRIPCION'],     ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['CARGO'],               ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>

        <h2 class="text-xs font-semibold text-green-700 mb-1 text-left">DATOS PERFIL DE ACCESO</h2>
        <form id="form-editar-usuario"
              data-codigo="<​?php echo htmlspecialchars($funcionario['FUN_CODIGO'], ENT_QUOTES, 'UTF-8'); ?>"
              data-rut="<​?php echo htmlspecialchars($funcionario['FUN_RUT'],       ENT_QUOTES, 'UTF-8'); ?>">
            <div class="flex gap-2 border rounded-lg overflow-hidden items-center">
                <div class="w-1/4 h-full flex flex-col items-center justify-center gap-2 p-2">
                    <button onclick="guardarCambios()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded">
                        MODIFICAR USUARIO
                    </button>
                    <div id="mensaje-respuesta" class="text-sm p-2 hidden rounded mb-2"></div>
                </div>
                <div class="w-3/4 grid grid-cols-2 border-l">
                    <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                        <p>Perfil:</p>
                        <p>Unidad Acceso:</p>
                        <p>Usuario PROSERVIPOL:</p>
                        <p>Clave PROSERVIPOL:</p>
                        <p>Curso Realizado:</p>
                    </div>
                    <div class="bg-gray-100 p-2 space-y-2 text-xs">
                        <select name="perfil" id="perfil" class="w-full border rounded focus:outline-none focus:ring focus:border-green-500 text-xs">
                            <?php foreach ($perfiles as $perfil): ?>
                                <option value="<​?php echo htmlspecialchars($perfil['TUS_CODIGO'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($perfil['TUS_CODIGO'] == $funcionario['TUS_CODIGO']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($perfil['TUS_DESCRIPCION'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="unidad" id="unidad" class="w-full border rounded focus:outline-none focus:ring focus:border-green-500 text-xs">
                            <?php foreach ($unidades as $unidad): ?>
                                <option value="<​?php echo htmlspecialchars($unidad['UNI_CODIGO'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($unidad['UNI_CODIGO'] == $funcionario['UNI_CODIGO']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unidad['UNI_DESCRIPCION'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <p><?php echo htmlspecialchars($funcionario['US_LOGIN'], ENT_QUOTES, 'UTF-8'); ?></p>

                        <input type="password" id="password" name="password" value=""
                            placeholder="Dejar vacio para no cambiar"
                            class="w-full border rounded focus:outline-none focus:ring focus:border-green-500 text-xs p-1" />

                        <p><?php echo htmlspecialchars($funcionario['CAPACITACION'], ENT_QUOTES, 'UTF-8'); ?></p>

                        <input type="hidden" id="codigo" name="codigo" value="<​?php echo htmlspecialchars($funcionario['FUN_CODIGO'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" id="rut"    name="rut"    value="<​?php echo htmlspecialchars($funcionario['FUN_RUT'],    ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <form id="form-eliminar-usuario" class="flex justify-end mt-2">
        <input type="hidden" name="codigo" value="<​?php echo htmlspecialchars($codigo,               ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="rut"    value="<​?php echo htmlspecialchars($funcionario['FUN_RUT'], ENT_QUOTES, 'UTF-8'); ?>">
        <button onclick="confirmarEliminarAjax('<?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>')"
            type="button" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 text-sm rounded">
            ELIMINAR USUARIO
        </button>
    </form>

    <h2 class="text-xs font-semibold text-green-700 mb-1 text-left mt-4">ULTIMOS 10 INGRESOS AL SISTEMA PROSERVIPOL</h2>
    <div class="border rounded-lg overflow-hidden">
        <div class="w-full overflow-x-auto p-2">
            <table class="min-w-full divide-y divide-gray-200 text-xs text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-2 py-1">Fecha y Hora</th>
                        <th class="px-2 py-1">Nombre</th>
                        <th class="px-2 py-1">Grado</th>
                        <th class="px-2 py-1">Unidad</th>
                        <th class="px-2 py-1">Perfil</th>
                        <th class="px-2 py-1">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($accesos)): ?>
                        <?php foreach ($accesos as $acceso): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['US_FECHAHORA_INICIO'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['NOMBRE_COMPLETO'],     ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['GRA_DESCRIPCION'],     ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['UNI_DESCRIPCION'],     ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['TUS_DESCRIPCION'],     ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-2 py-1"><?php echo htmlspecialchars($acceso['US_DIRECCION_IP'],     ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-2 py-1 text-center text-gray-400">No hay registros de ingresos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-1 flex justify-between items-center">
        <button onclick="cerrarModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
            CERRAR
        </button>
        <button type="button"
            onclick="cerrarModal(); abrirModalIngresos('<?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>');"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded">
            BUSCAR MAS INGRESOS
        </button>
    </div>
</div>

<script>
// ✅ URL base centralizada
var BASE_URL = 'http://aplicativos.des-proservipol.carabineros.cl';

function guardarCambios() {
    const codigo    = document.getElementById('codigo').value;
    const rut       = document.getElementById('rut').value;
    const perfil    = document.getElementById('perfil').value;
    const unidad    = document.getElementById('unidad').value;
    const password  = document.getElementById('password').value;

    if (!perfil || !unidad) {
        mostrarMensaje('Perfil y Unidad son obligatorios', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('codigo',   codigo);
    formData.append('rut',      rut);
    formData.append('perfil',   perfil);
    formData.append('unidad',   unidad);
    formData.append('password', password);

    mostrarMensaje('Guardando cambios...', 'info');

    // ✅ FIX: URL absoluta
    fetch(BASE_URL + '/api/editarUsuario/', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const respuesta = data.trim().toLowerCase();
        if (respuesta === 'ok' || respuesta.includes('actualizados correctamente')) {
            mostrarMensaje('Cambios guardados correctamente', 'success');
            setTimeout(() => { recargarDatosModal(codigo); }, 2000);
        } else {
            mostrarMensaje('Error: ' + data, 'error');
        }
    })
    .catch(error => {
        mostrarMensaje('Error al guardar los cambios', 'error');
    });
}

function mostrarMensaje(mensaje, tipo) {
    const mensajeDiv = document.getElementById('mensaje-respuesta');
    mensajeDiv.textContent = mensaje;
    mensajeDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700', 'bg-blue-100', 'text-blue-700');
    if (tipo === 'success')      mensajeDiv.classList.add('bg-green-100', 'text-green-700');
    else if (tipo === 'error')   mensajeDiv.classList.add('bg-red-100',   'text-red-700');
    else ifProcessing