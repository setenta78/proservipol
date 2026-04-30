<?php
session_start();
require_once 'middleware_auth.php';
// Obtener la URL actual
$current_page = basename($_SERVER['SCRIPT_NAME']); // Versión más robusta para PHP 5.1.2
?>
<aside class="fixed top-16 w-64 h-full bg-gray-500 text-white">
    <ul class="space-y-1 p-4 text-sm">
        <li>
            <div class="mt-10 max-w-sm mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="sm:flex sm:items-center px-6 py-4">
                    <img
                        class="block mx-auto sm:mx-0 sm:flex-shrink-0 h-12 sm:h-16 rounded-full"
                        src="img/usuario.png"
                        alt="Photo Profile" />
                    <div class="mt-4 sm:mt-0 sm:ml-4 text-center sm:text-left">
                        <p class="text-sm leading-tight text-gray-600">Usuario(a)</p>
                        <p class="text-sm leading-tight text-gray-600 font-semibold"><?php echo $nombreUsuario; ?></p>
                        <!--<p class="text-sm leading-tight text-gray-600">Developer</p>-->
                        <div class="mt-4">
                            <a href="logout.php" class="text-green-500 hover:text-white hover:bg-green-500 border border-green-500 text-xs font-semibold rounded-full px-4 py-1 leading-normal">
                                SALIR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <?php
            $target_page = (version_compare(phpversion(), '5.1.2', '==')) ? 'gestor_usuarios.php' : 'usuarios_listado.php';
            ?>
            <a href="<?php echo $target_page; ?>"
                class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == $target_page) ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fa fa-address-card"></i>
                <span>Gestor de Usuarios</span>
            </a>
        </li>

        <li>
            <a href="buscar_ingresos_usuario.php"
                class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'buscar_ingresos_usuario.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-sign-in-alt"></i>
                <span>Buscar Ingresos</span>
            </a>
        </li>

        <li>
            <?php
            $target_page = (version_compare(phpversion(), '5.1.2', '==')) ? 'camaras.php' : 'camaras_local.php';
            ?>
            <a href="<?php echo $target_page; ?>"
                class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == $target_page) ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-camera"></i>
                <span>Cámaras SAP</span>
            </a>
        </li>

        <!--
        <li>
            <a href="ingreso_vehiculos.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'ingreso_vehiculos.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-car"></i>
                <span>Ingreso Vehículos</span>
            </a>
        </li>

        <li>
            <a href="cambio_fecha.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'cambio_fecha.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Cambio Fecha</span>
            </a>
        </li>

        <li>
            <a href="licencias_medicas.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'licencias_medicas.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-notes-medical"></i>
                <span>Licencias Médicas</span>
            </a>
        </li>

        <li>
            <a href="permisos.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'permisos.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-clipboard-check"></i>
                <span>Permisos</span>
            </a>
        </li>

        <li>
            <a href="desvalidador.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'desvalidador.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-sync"></i>
                <span>Desvalidador</span>
            </a>
        </li>

        <li>
            <a href="reintegrados.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'reintegrados.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-undo-alt"></i>
                <span>Reintegrados</span>
            </a>
        </li>

        <li>
            <a href="movimientos.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'movimientos.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span>Movimientos</span>
            </a>
        </li>

        <li>
            <a href="listado_servicios.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'listado_servicios.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-list"></i>
                <span>Listado Servicios</span>
            </a>
        </li>

        <li>
            <a href="ingreso_armas.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'ingreso_armas.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-gun"></i>
                <span>Ingreso Armas</span>
            </a>
        </li>

        <li>
            <a href="ingreso_animales.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'ingreso_animales.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-paw"></i>
                <span>Ingreso Animales</span>
            </a>
        </li>

        <li>
            <a href="solicitudes.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'solicitudes.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Solicitudes</span>
            </a>
        </li>

        <li>
            <a href="usuarios_listado.php" class="flex items-center space-x-2 py-2 px-4 rounded <?php //echo ($current_page == 'gestor_usuarios.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>">
                <i class="fas fa-user-alt"></i>
                <span>Gestor de Usuarios</span>
            </a>
        </li>
        -->

        <!--
        <li>
            <a href="manual.php"
                class="flex items-center space-x-2 py-2 px-4 rounded <?php echo ($current_page == 'manual.php') ? 'bg-green-700 text-white' : 'hover:bg-gray-700'; ?>"
                onclick="window.open('manual.php', 'Manual de Uso', 'width=800,height=600'); return false;">
                <i class="fas fa-book"></i>
                <span>Manual Cámaras</span>
            </a>
        </li>
        -->

    </ul>
</aside>