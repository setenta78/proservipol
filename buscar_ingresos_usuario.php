<?php
session_start();
require_once 'middleware_auth.php';
error_reporting(E_ALL & ~E_NOTICE);
require_once "queries/config.php";
require_once "queries/general_queries.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="img/logo.png">
    <title>Buscar Ingresos — PROSERVIPOL</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-700">
    <?php include "header.php"; ?>

    <div class="flex">
        <?php include "nav.php"; ?>

        <div class="ml-64 w-full mt-20 p-5">

            <h3 class="text-center text-green-800 font-semibold mt-5">HISTORIAL DE INGRESOS AL SISTEMA PROSERVIPOL</h3>
            <hr class="my-2 border-gray-400">

            <!-- FORMULARIO DE FILTROS -->
            <div class="bg-white border rounded-lg p-4 mb-4 shadow-sm">
                <h4 class="text-xs font-semibold text-green-700 mb-3">FILTROS DE BÚSQUEDA</h4>
                <div class="grid grid-cols-2 gap-4 mb-3">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Fecha Desde <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_desde"
                               class="w-full border rounded text-xs p-2 focus:outline-none focus:ring focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Fecha Hasta <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_hasta"
                               class="w-full border rounded text-xs p-2 focus:outline-none focus:ring focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Buscar Funcionario</label>
                        <input type="text" id="busqueda"
                               placeholder="RUT, código, nombre o apellido"
                               class="w-full border rounded text-xs p-2 focus:outline-none focus:ring focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad</label>
                        <select id="unidad_filter"
                                class="w-full border rounded text-xs p-2 focus:outline-none focus:ring focus:border-green-500">
                            <option value="">— Todas las unidades —</option>
                            <?php
                            $unidades = obtenerUnidades();
                            foreach ($unidades as $u):
                            ?>
                                <option value="<?php echo $u['UNI_CODIGO']; ?>">
                                    <?php echo htmlspecialchars($u['UNI_DESCRIPCION']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- Nota de campos obligatorios -->
                <p class="text-xs text-gray-400 mb-3"><span class="text-red-500">*</span> Campos obligatorios</p>

                <div class="flex gap-2 flex-wrap">
                    <button onclick="realizarBusqueda(1)"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 text-xs rounded">
                        <i class="fas fa-search mr-1"></i> BUSCAR
                    </button>
                    <button onclick="limpiarFiltros()"
                            class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-6 text-xs rounded">
                        <i class="fas fa-times mr-1"></i> LIMPIAR
                    </button>
                    <button onclick="exportarCSV()"
                            id="btn-exportar"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 text-xs rounded hidden">
                        <i class="fas fa-file-csv mr-1"></i> EXPORTAR CSV
                    </button>
                </div>
            </div>

            <!-- ESTADO: contador de resultados -->
            <div id="estado-busqueda" class="text-xs text-gray-500 mb-2 hidden"></div>

            <!-- CARGANDO -->
            <div id="cargando" class="hidden text-center text-green-700 text-sm mt-10">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <p>Buscando registros...</p>
            </div>

            <!-- SIN RESULTADOS -->
            <div id="sin-resultados" class="hidden text-center text-gray-500 text-sm mt-10">
                <i class="fas fa-inbox fa-2x mb-2 text-gray-300 block mx-auto"></i>
                <p><em>No se encontraron ingresos con los parámetros indicados.</em></p>
            </div>

            <!-- TABLA DE RESULTADOS -->
            <div id="contenedor-tabla" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg text-xs">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-200 px-3 py-2">Nro.</th>
                                <th class="border border-gray-200 px-3 py-2">Fecha Ingreso</th>
                                <!--<th class="border border-gray-200 px-3 py-2">Fecha Término</th> -->
                                <th class="border border-gray-200 px-3 py-2">Código</th>
                                <th class="border border-gray-200 px-3 py-2">RUT</th>
                                <th class="border border-gray-200 px-3 py-2">Nombre Completo</th>
                                <th class="border border-gray-200 px-3 py-2">Grado</th>
                                <th class="border border-gray-200 px-3 py-2">Unidad</th>
                                <th class="border border-gray-200 px-3 py-2">Perfil</th>
                                <th class="border border-gray-200 px-3 py-2">IP</th>
                                <th class="border border-gray-200 px-3 py-2">Evento</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-cuerpo">
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <nav id="paginacion" class="mt-4"></nav>
            </div>

        </div>
    </div>

    <script src="js/jquery-1.12.4.js"></script>
    <script src="js/buscar_ingresos.js"></script>

</body>
</html>
