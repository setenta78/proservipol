<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL & ~E_NOTICE);
require_once "queries/config.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="img/logo.png">
    <title>Sistema de Administracion de usuarios de PROSERVIPOL</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-700">
    <?php include "header.php"; ?>

    <div class="flex">
        <?php include "nav.php"; ?>

        <div class="ml-64 w-full mt-20 p-5">
            <h3 class="text-center text-green-800 font-semibold mt-5">SISTEMA DE ADMINISTRACION DE USUARIOS DE PROSERVIPOL</h3>
            <hr class="my-2 border-gray-400">

            <div class="flex justify-between items-center mb-4 flex-wrap gap-4 px-4">
                <button onclick="abrirModalNuevo()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 text-xs rounded">REGISTRAR NUEVO USUARIO</button>
                <form action="" method="GET" class="flex w-full sm:w-auto max-w-lg items-center">
                    <input type="text" name="search" placeholder="Ingrese codigo, nombre, apellido, unidad, perfil, etc." class="w-full sm:w-96 p-2 border rounded-l-lg focus:outline-none focus:ring focus:border-green-500 text-sm" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_COMPAT) : ''; ?>">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-r-lg text-sm hover:bg-green-700">BUSCAR</button>
                </form>
                <button onclick="abrirModalBusquedaParametrica()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 text-xs rounded">BUSQUEDA PARAMETRICA</button>
            </div>

            <div class="w-full mx-auto">
                <hr class="border-gray-400">
                <?php
                // ---------------------------
                // Recuperar filtros de busqueda
                // ---------------------------
                $apellido1 = isset($_GET['apellido1']) ? $_GET['apellido1'] : '';
                $apellido2 = isset($_GET['apellido2']) ? $_GET['apellido2'] : '';
                $nombre1   = isset($_GET['nombre1']) ? $_GET['nombre1'] : '';
                $nombre2   = isset($_GET['nombre2']) ? $_GET['nombre2'] : '';
                $unidades  = isset($_GET['unidades_ids']) ? $_GET['unidades_ids'] : '';
                $perfiles  = isset($_GET['perfiles']) ? $_GET['perfiles'] : '';
                $search    = isset($_GET['search']) ? mysql_real_escape_string($_GET['search']) : '';

                // Paginacion
                $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                $records_per_page = 100;
                $offset = ($current_page - 1) * $records_per_page;

                // Ordenacion segura
                $allowed_sort_columns = array(
                    'FUN_CODIGO',
                    'FUN_APELLIDOPATERNO',
                    'FUN_APELLIDOMATERNO',
                    'FUN_NOMBRE',
                    'FUN_NOMBRE2',
                    'GRA_DESCRIPCION',
                    'CARGO',
                    'UNI_DESCRIPCION',
                    'TUS_DESCRIPCION',
                    'CAPACITACION'
                );
                $sort = (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns)) ? $_GET['sort'] : 'FUN_CODIGO';
                $order = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'desc' : 'asc';
                $new_order = ($order === 'asc') ? 'desc' : 'asc';

                // Incluir funciones de consulta
                require_once "queries/usuarios_listado.php";

                // ---------------------------
                // FIX: query_params SIN "page" para evitar duplicado en URL de paginacion
                // ---------------------------
                $query_params = "search=" . urlencode($search) .
                    "&apellido1=" . urlencode($apellido1) .
                    "&apellido2=" . urlencode($apellido2) .
                    "&nombre1="   . urlencode($nombre1) .
                    "&nombre2="   . urlencode($nombre2) .
                    "&unidades_ids=" . urlencode($unidades) .
                    "&perfiles="  . urlencode(is_array($perfiles) ? implode(',', $perfiles) : $perfiles);

                // ---------------------------
                // Obtener resultados
                // ---------------------------
                if ($apellido1 || $apellido2 || $nombre1 || $nombre2 || $unidades || $perfiles) {
                    $result = obtenerUsuariosParametricos($link, $apellido1, $apellido2, $nombre1, $nombre2, $unidades, $perfiles, $sort, $order, $offset, $records_per_page);
                    $total_rows = contarUsuariosParametricos($link, $apellido1, $apellido2, $nombre1, $nombre2, $unidades, $perfiles);
                } else {
                    $result = obtenerUsuarios($link, $search, $sort, $order, $offset, $records_per_page);
                    $total_rows = contarUsuarios($link);
                }

                // ---------------------------
                // Mostrar tabla
                // ---------------------------
                if ($result) {
                    $total_cam = mysql_num_rows($result);
                    if ($total_cam > 0) {
                        echo "<h6 class='text-center text-sm font-semibold text-gray-700 mb-4 mt-5'>LISTADO DE USUARIOS(AS) REGISTRADOS EN PROSERVIPOL</h6>";
                        echo "<div class='overflow-x-auto'>";
                        echo "<table class='min-w-full bg-white border border-gray-200 rounded-lg text-xs'>";
                        echo "<thead><tr class='bg-gray-200'>";
                        echo "<th class='border border-gray-200 px-4 py-2'>Nro.</th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=FUN_CODIGO&order=$new_order&$query_params'>Codigo</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=FUN_NOMBRE&order=$new_order&$query_params'>Primer Nombre</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=FUN_NOMBRE2&order=$new_order&$query_params'>Segundo Nombre</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=FUN_APELLIDOPATERNO&order=$new_order&$query_params'>Primer Apellido</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=FUN_APELLIDOMATERNO&order=$new_order&$query_params'>Segundo Apellido</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=GRA_DESCRIPCION&order=$new_order&$query_params'>Grado</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=CARGO&order=$new_order&$query_params'>Cargo</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=UNI_DESCRIPCION&order=$new_order&$query_params'>Unidad</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=TUS_DESCRIPCION&order=$new_order&$query_params'>Perfil</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>
                                <a href='?sort=CAPACITACION&order=$new_order&$query_params'>Capacitacion</a>
                              </th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>Acciones</th>";
                        echo "</tr></thead><tbody>";

                        $i = $offset + 1;
                        while ($row = mysql_fetch_array($result)) {
                            echo "<tr class='text-center hover:bg-green-100 hover:font-bold transition-colors duration-200 cursor-pointer'>";
                            echo "<td class='border px-4 py-2'>$i</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['FUN_CODIGO']) . "</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['FUN_NOMBRE'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['FUN_NOMBRE2'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['FUN_APELLIDOPATERNO'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['FUN_APELLIDOMATERNO'], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['GRA_DESCRIPCION']) . "</td>";
							echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['CARGO']) . "</td>";
							echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['UNI_DESCRIPCION']) . "</td>";
							echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['TUS_DESCRIPCION']) . "</td>";
							echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['CAPACITACION']) . "</td>";
							echo "<td class='border px-4 py-2'>
	                                    <div class='flex items-center gap-2'>
	                                        <button
	                                            onclick=\"abrirModal('{$row['FUN_CODIGO']}')\"
	                                            class='text-blue-600 hover:text-blue-800'
	                                            title='Editar'>
	                                            <i class='fas fa-edit fa-lg'></i>
	                                        </button>
	                                        <button type='button' onclick=\"confirmarEliminarAjax('{$row['FUN_CODIGO']}')\" class='text-red-600 hover:text-red-800' title='Eliminar'>
	                                            <i class='fas fa-trash-alt fa-lg'></i>
	                                        </button>
	                                    </div>
	                                  </td>";
	                            echo "</tr>";
	                            $i++;
	                        }                        
						
                        echo "</tbody></table></div>";

                        // Paginacion
                        $total_pages = ceil($total_rows / $records_per_page);

                        echo "<nav class='mt-4'><ul class='flex justify-center space-x-2'>";
                        if ($current_page > 1) {
                            $prev_page = $current_page - 1;
                            echo "<li><a href='?page=$prev_page&sort=$sort&order=$order&$query_params' class='bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded'>Anterior</a></li>";
                        }
                        echo "<li><span class='text-gray-700 py-1 px-3'>$current_page de $total_pages</span></li>";
                        if ($current_page < $total_pages) {
                            $next_page = $current_page + 1;
                            echo "<li><a href='?page=$next_page&sort=$sort&order=$order&$query_params' class='bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded'>Siguiente</a></li>";
                        }
                        echo "</ul></nav>";
                    } else {
                        echo "<p class='text-center text-gray-600'><em>No existen usuarios registrados que cumplan con los parámetros de búsqueda indicados</em></p>";
                    }
                } else {
                    echo "ERROR: " . mysql_error();
                }

                mysql_close($link);
                ?>
            </div>
        </div>

        <div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 overflow-y-auto">
            <div id="modalContenido" class="bg-white rounded-lg shadow-lg w-11/12 max-w-7xl p-6 relative transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh]">
                <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
                <div id="contenidoModal" class="mt-10 max-h-[70vh] overflow-y-auto pr-2">
                    <p class="text-center text-gray-500">Cargando...</p>
                </div>
            </div>
        </div>
        <div id="modalNuevo" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 overflow-y-auto">
            <div id="modalContenidoNuevo" class="bg-white rounded-lg shadow-lg w-11/12 max-w-7xl p-6 relative transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh]">
                <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
                <div id="contenidoModalNuevo" class="mt-10 max-h-[70vh] overflow-y-auto pr-2">
                    <p class="text-center text-gray-500">Cargando...</p>
                </div>
            </div>
        </div>
        <div id="modalBusquedaParametrica" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 overflow-y-auto">
            <div id="modalContenidoBusqueda" class="bg-white rounded-lg shadow-lg w-11/12 max-w-7xl p-6 relative transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh]">
                <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
                <div id="contenidoModalBusquedaParametrica" class="mt-10 max-h-[70vh] overflow-y-auto pr-2">
                    <p class="text-center text-gray-500">Cargando...</p>
                </div>
            </div>
        </div>

        <script src="js/jquery-1.12.4.js"></script>
        <script src="js/nuevo_usuario.js"></script>
        <script src="js/modal_editar.js"></script>
        <script src="js/modal_eliminar.js"></script>
        <script src="js/modal_nuevo.js"></script>
        <script src="js/modal_busquedaParametrica.js"></script>
        <script src="js/modal_ingresos.js"></script>

        <script>
            $(document).ready(function() {
                $(document).on("keyup input", ".search-box input[type='text']", function() {
                    var inputVal = $(this).val();
                    var resultDropdown = $(this).siblings(".result");

                    if (inputVal.length) {
                        $.get("queries/buscar_unidad.php", { term: inputVal })
                            .done(function(data) {
                                resultDropdown.html(data);
                            });
                    } else {
                        resultDropdown.empty();
                    }
                });

                $(document).on("click", ".result p", function() {
                    var selectedText = $(this).text();
                    var selectedId = $(this).data('id');
                    var searchBox = $(this).closest(".search-box");

                    searchBox.find('input[type="text"]').val(selectedText);
                    searchBox.find('input.unidadPerfil').val(selectedId);

                    $(this).parent(".result").empty();
                });
            });

            document.addEventListener("buscarUsuarios", function(e) {
                const filtros = e.detail;
                fetch("buscar_usuario.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(filtros)
                })
                .then(res => res.json())
                .then(data => { actualizarTablaUsuarios(data); })
                .catch(err => console.error("Error en busqueda:", err));
            });
        </script>

    </div>
</body>

</html>
