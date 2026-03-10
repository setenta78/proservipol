<?php
session_start();
require_once 'middleware_auth.php';
// Ajustar configuraciones de errores para compatibilidad
if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
}

// Incluir configuración
require_once "queries/config.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="img/logo.png">
    <title>Sistema de Importación de Datos: SAP -> PROSERVIPOL</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-700">
    <!-- Incluir cabecera -->
    <?php include "header.php"; ?>

    <div class="flex">
        <!-- Incluir menú de navegación lateral -->
        <?php include "nav.php"; ?>

        <!-- Contenido Principal -->
        <div class="ml-64 w-full mt-20 p-5">
            <h3 class="text-center text-green-800 font-semibold mt-5">Sistema de Importación de Datos: SAP -> PROSERVIPOL.</h3>
            <hr class="my-4 border-gray-400">
            <div class="flex justify-center mb-4">

                <!-- Formulario de carga de archivo -->
                <div class="w-full max-w-md">
                    <form action="queries/recibe_csv_validando2.php" method="POST" enctype="multipart/form-data" class="bg-white shadow-lg p-6 rounded-lg">
                        <div class="file-input text-center mb-4">
                            <input type="file" name="dataCamara" id="file-input" class="hidden" />
                            <label for="file-input" class="cursor-pointer inline-flex items-center justify-center space-x-2 p-2 text-white bg-green-700 hover:bg-green-800 rounded-md text-lg">
                                <i class="zmdi zmdi-upload zmdi-hc-2x"></i>
                                <span>Elegir Archivo Excel</span>
                            </label>
                        </div>

                        <!-- Aquí mostrarás el nombre del archivo seleccionado -->
                        <div class="mt-2 text-center text-gray-700" id="file-name">Ningún archivo seleccionado</div>

                        <div class="text-center mt-3">
                            <input type="submit" name="subir" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded" value="Subir Excel" />
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border-gray-400">
            <div class="container mx-auto mt-20 p-5">
                <!-- Campo de búsqueda -->
                <div class="flex justify-center mb-4">
                    <form action="" method="GET" class="w-full max-w-lg flex items-center">
                        <input type="text" name="search" placeholder="Buscar por Marca, Modelo, Procedencia, etc." class="w-full p-2 border rounded-l-lg focus:outline-none focus:ring focus:border-green-500" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-r-lg hover:bg-green-700">Buscar</button>
                    </form>
                </div>
                <hr class="border-gray-400">
                <?php
                // Búsqueda
                $records_per_page = 5;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($current_page - 1) * $records_per_page;
                $search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';

                // Ordenación segura
                $allowed_sort_columns = array('VC_CODIGO', 'MVC_DESCRIPCION', 'MODVC_DESCRIPCION');
                $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'VC_CODIGO';
                $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
                $new_order = $order === 'asc' ? 'desc' : 'asc';

                // Modificación de la consulta SQL con filtro de búsqueda
                $sql = "SELECT VIDEOCAMARA.VC_CODIGO, VIDEOCAMARA.UNI_CODIGO, VIDEOCAMARA.VC_COD_ACTIVO_SAP, VIDEOCAMARA.VC_COD_EQUIPO_SAP, VIDEOCAMARA.VC_NRO_SERIE,
                        MARCA_VIDEOCAMARA.MVC_DESCRIPCION, MODELO_VIDEOCAMARA.MODVC_DESCRIPCION, PROCEDENCIA_RECURSO.PREC_DESCRIPCION, UNIDAD.UNI_DESCRIPCION
                        FROM VIDEOCAMARA
                        INNER JOIN MODELO_VIDEOCAMARA ON VIDEOCAMARA.MODVC_CODIGO = MODELO_VIDEOCAMARA.MODVC_CODIGO
                        INNER JOIN MARCA_VIDEOCAMARA ON VIDEOCAMARA.MVC_CODIGO = MARCA_VIDEOCAMARA.MVC_CODIGO
                        INNER JOIN PROCEDENCIA_RECURSO ON VIDEOCAMARA.PREC_CODIGO = PROCEDENCIA_RECURSO.PREC_CODIGO
                        LEFT JOIN UNIDAD ON VIDEOCAMARA.UNI_CODIGO = UNIDAD.UNI_CODIGO
                        WHERE (MARCA_VIDEOCAMARA.MVC_DESCRIPCION LIKE '%$search%' 
                        OR MODELO_VIDEOCAMARA.MODVC_DESCRIPCION LIKE '%$search%' 
                        OR PROCEDENCIA_RECURSO.PREC_DESCRIPCION LIKE '%$search%' 
                        OR UNIDAD.UNI_DESCRIPCION LIKE '%$search%' 
                        OR VIDEOCAMARA.VC_COD_ACTIVO_SAP LIKE '%$search%' 
                        OR VIDEOCAMARA.VC_COD_EQUIPO_SAP LIKE '%$search%' 
                        OR VIDEOCAMARA.VC_NRO_SERIE LIKE '%$search%')
                        ORDER BY $sort $order
                        LIMIT $records_per_page OFFSET $offset";

                if ($result = mysqli_query($link, $sql)) {
                    $total_cam = mysqli_num_rows($result);
                    if ($total_cam > 0) {
                        echo "<h6 class='text-center text-lg font-semibold text-gray-800 mb-4 mt-5'>Listado de Cámaras Registradas</h6>";
                        echo "<div class='overflow-x-auto'>";
                        echo "<table class='min-w-full bg-white border border-gray-200 rounded-lg'>";
                        echo "<thead><tr class='bg-gray-100'>";
                        echo "<th class='border border-gray-200 px-4 py-2'>Nro.</th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=VC_CODIGO&order=$new_order'>Código</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=MVC_DESCRIPCION&order=$new_order'>Marca</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=MODVC_DESCRIPCION&order=$new_order'>Modelo</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=PREC_DESCRIPCION&order=$new_order'>Procedencia</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=UNI_DESCRIPCION&order=$new_order'>Unidad</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=VC_COD_ACTIVO_SAP&order=$new_order'>Código Activo</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=VC_COD_EQUIPO_SAP&order=$new_order'>Código Equipo</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'><a href='?sort=VC_NRO_SERIE&order=$new_order'>Número Serie</a></th>";
                        echo "<th class='border border-gray-200 px-4 py-2'>Acciones</th>";
                        echo "</tr></thead><tbody>";

                        $i = $offset + 1;
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr class='text-center'>";
                            echo "<td class='border px-4 py-2'>$i</td>";
                            echo "<td class='border px-4 py-2'>{$row['VC_CODIGO']}</td>";
                            echo "<td class='border px-4 py-2'>{$row['MVC_DESCRIPCION']}</td>";
                            echo "<td class='border px-4 py-2'>{$row['MODVC_DESCRIPCION']}</td>";
                            echo "<td class='border px-4 py-2'>{$row['PREC_DESCRIPCION']}</td>";
                            if ($row['UNI_DESCRIPCION'] != "") {
                                echo "<td class='border px-4 py-2'>{$row['UNI_DESCRIPCION']} - COD: {$row['UNI_CODIGO']}</td>";
                            } else {
                                echo "<td class='border px-4 py-2 text-blue-400'> SIN UNIDAD ASIGNADA </td>";
                            }
                            echo "<td class='border px-4 py-2'>{$row['VC_COD_ACTIVO_SAP']}</td>";
                            echo "<td class='border px-4 py-2'>{$row['VC_COD_EQUIPO_SAP']}</td>";
                            echo "<td class='border px-4 py-2'>{$row['VC_NRO_SERIE']}</td>";
                            echo "<td class='border px-4 py-2'><a href='editar.php?id={$row['VC_CODIGO']}' class='bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600'>Editar</a></td>";
                            echo "</tr>";
                            $i++;
                        }
                        echo "</tbody></table></div>";
                        // Paginación
                        $count_sql = "SELECT COUNT(*) as total FROM VIDEOCAMARA";
                        $count_result = mysqli_query($link, $count_sql);
                        $row = mysqli_fetch_assoc($count_result);
                        $total_rows = $row['total'];
                        mysqli_free_result($count_result);
                        $total_pages = ceil($total_rows / $records_per_page);

                        echo "<nav class='mt-4'><ul class='flex justify-center space-x-2'>";
                        if ($current_page > 1) {
                            $prev_page = $current_page - 1;
                            echo "<li><a href='?page=$prev_page&sort=$sort&order=$order' class='bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded'>Anterior</a></li>";
                        }
                        echo "<li><span class='text-gray-700 py-1 px-3'>$current_page de $total_pages</span></li>";
                        if ($current_page < $total_pages) {
                            $next_page = $current_page + 1;
                            echo "<li><a href='?page=$next_page&sort=$sort&order=$order' class='bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded'>Siguiente</a></li>";
                        }
                        echo "</ul></nav>";
                    } else {
                        echo "<p class='text-center text-gray-600'><em>No existen registros.</em></p>";
                    }
                    mysqli_free_result($result);
                } else {
                    echo "ERROR: No se pudo ejecutar la consulta $sql. " . mysqli_error($link);
                }

                mysqli_close($link);
                ?>
            </div>
        </div>
        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $(window).on('load', function() {
                    $(".cargando").fadeOut(1000);
                });
            });

            // JavaScript para mostrar el nombre del archivo seleccionado
            document.getElementById('file-input').addEventListener('change', function(event) {
                var fileName = event.target.files[0] ? event.target.files[0].name : 'Ningún archivo seleccionado';
                document.getElementById('file-name').textContent = fileName;
            });
        </script>
</body>

</html>