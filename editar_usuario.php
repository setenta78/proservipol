<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL & ~E_NOTICE);
require_once "queries/config.php";
require_once "queries/general_queries.php";
require_once "queries/editar_queries.php";

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
                <img src="img/default_user.png" alt="Foto" class="w-32 h-32 object-cover rounded-full border shadow">
            </div>
            <div class="w-3/4 grid grid-cols-2 border-l">
                <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                    <p>Codigo:</p><p>Rut:</p><p>Nombres:</p><p>Apellidos:</p>
                    <p>Grado:</p><p>Dotacion:</p><p>Cargo:</p>
                </div>
                <div class="bg-gray-100 p-2 space-y-2 text-xs">
                    <p><?php echo htmlspecialchars($funcionario['FUN_CODIGO']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_RUT']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_NOMBRE'] . ' ' . $funcionario['FUN_NOMBRE2']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['FUN_APELLIDOPATERNO'] . ' ' . $funcionario['FUN_APELLIDOMATERNO']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['GRA_DESCRIPCION']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['UNI_DESCRIPCION']); ?></p>
                    <p><?php echo htmlspecialchars($funcionario['CARGO']); ?></p>
                </div>
            </div>
        </div>

        <h2 class="text-xs font-semibold text-green-700 mb-1 text-left">DATOS PERFIL DE ACCESO</h2>
        <form id="form-editar-usuario">
            <div class="flex gap-2 border rounded-lg overflow-hidden items-center">
                <div class="w-1/4 h-full flex flex-col items-center justify-center gap-2 p-2">
                    <button type="button" onclick="guardarCambios()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded">
                        MODIFICAR USUARIO
                    </button>
                    <div id="mensaje-respuesta" class="text-sm p-2 hidden rounded"></div>
                </div>
                <div class="w-3/4 grid grid-cols-2 border-l">
                    <div class="p-2 border-r space-y-2 text-right font-semibold text-xs">
                        <p>Perfil:</p><p>Unidad:</p><p>Usuario:</p><p>Clave:</p><p>Curso:</p>
                    </div>
                    <div class="bg-gray-100 p-2 space-y-2 text-xs">
                        <select name="perfil" id="perfil" class="w-full border rounded text-xs">
                            <?php foreach ($perfiles as $p): ?>
                                <option value="<?php echo $p['TUS_CODIGO']; ?>" <?php echo ($p['TUS_CODIGO'] == $funcionario['TUS_CODIGO']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['TUS_DESCRIPCION']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="unidad" id="unidad" class="w-full border rounded text-xs">
                            <?php foreach ($unidades as $u): ?>
                                <option value="<?php echo $u['UNI_CODIGO']; ?>" <?php echo ($u['UNI_CODIGO'] == $funcionario['UNI_CODIGO']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['UNI_DESCRIPCION']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <p><?php echo htmlspecialchars($funcionario['US_LOGIN']); ?></p>

                        <input type="password" id="password" name="password" value="" placeholder="Dejar vacío para no cambiar" class="w-full border rounded text-xs p-1" />

                        <p><?php echo htmlspecialchars($funcionario['CAPACITACION']); ?></p>

                        <!-- Inputs ocultos CORREGIDOS sin caracteres raros -->
                        <input type="hidden" id="codigo" value="<?php echo $funcionario['FUN_CODIGO']; ?>">
                        <input type="hidden" id="rut" value="<?php echo $funcionario['FUN_RUT']; ?>">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="flex justify-end mt-2">
        <button type="button" onclick="confirmarEliminarAjax('<?php echo $funcionario['FUN_CODIGO']; ?>')" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 text-sm rounded">
            ELIMINAR USUARIO
        </button>
    </div>

    <h2 class="text-xs font-semibold text-green-700 mb-1 text-left mt-4">ULTIMOS 10 INGRESOS</h2>
    <div class="border rounded-lg overflow-hidden">
        <table class="min-w-full text-xs text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1">Fecha</th><th class="px-2 py-1">Nombre</th><th class="px-2 py-1">Grado</th>
                    <th class="px-2 py-1">Unidad</th><th class="px-2 py-1">Perfil</th><th class="px-2 py-1">IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($accesos)): ?>
                    <?php foreach ($accesos as $acceso): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-1"><?php echo $acceso['US_FECHAHORA_INICIO']; ?></td>
                        <td class="px-2 py-1"><?php echo $acceso['NOMBRE_COMPLETO']; ?></td>
                        <td class="px-2 py-1"><?php echo $acceso['GRA_DESCRIPCION']; ?></td>
                        <td class="px-2 py-1"><?php echo $acceso['UNI_DESCRIPCION']; ?></td>
                        <td class="px-2 py-1"><?php echo $acceso['TUS_DESCRIPCION']; ?></td>
                        <td class="px-2 py-1"><?php echo $acceso['US_DIRECCION_IP']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center p-2">Sin registros</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-2 flex justify-between">
        <button onclick="cerrarModal()" class="bg-blue-600 text-white px-4 py-2 rounded">CERRAR</button>
        <button onclick="cerrarModal(); abrirModalIngresos('<?php echo $funcionario['FUN_CODIGO']; ?>')" class="bg-green-600 text-white px-4 py-2 rounded">BUSCAR MAS</button>
    </div>
</div>

<script>
var BASE_URL = 'http://aplicativos.des-proservipol.carabineros.cl';

function guardarCambios() {
    var codigo = document.getElementById('codigo').value;
    var perfil = document.getElementById('perfil').value;
    var unidad = document.getElementById('unidad').value;
    var password = document.getElementById('password').value;
    var msgDiv = document.getElementById('mensaje-respuesta');

    if (!perfil || !unidad) {
        alert("Perfil y Unidad son obligatorios");
        return;
    }

    var formData = new FormData();
    formData.append('codigo', codigo);
    formData.append('perfil', perfil);
    formData.append('unidad', unidad);
    formData.append('password', password);

    msgDiv.className = 'text-sm p-2 bg-blue-100 text-blue-700 rounded';
    msgDiv.textContent = 'Guardando...';
    msgDiv.classList.remove('hidden');

    fetch(BASE_URL + '/queries/editar_queries.php', {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            msgDiv.className = 'text-sm p-2 bg-green-100 text-green-700 rounded';
            msgDiv.textContent = data.message;
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            msgDiv.className = 'text-sm p-2 bg-red-100 text-red-700 rounded';
            msgDiv.textContent = 'Error: ' + data.message;
        }
    })
    .catch(function(err) {
        console.error(err);
        alert("Error de conexión al guardar");
    });
}
</script>