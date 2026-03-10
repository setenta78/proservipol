<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Uso - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Contenedor principal -->
    <div class="container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">

        <!-- Título de la página -->
        <h1 class="text-3xl font-bold text-green-700 mb-6">Manual de Uso</h1>
        <p class="text-gray-600 mb-4">En este manual encontrarás información sobre cómo usar el sistema de gestión, incluyendo las funcionalidades clave y opciones de navegación.</p>

        <!-- Índice de secciones -->
        <ul class="list-disc list-inside mb-6">
            <li><a href="#importar-csv" class="text-green-700 hover:underline">Importar Archivo CSV</a></li>
            <li><a href="#listar-tabla" class="text-green-700 hover:underline">Listar y Ordenar Tabla de Contenidos</a></li>
            <li><a href="#buscar-datos" class="text-green-700 hover:underline">Buscar Datos</a></li>
        </ul>

        <!-- Sección: Importar CSV -->
        <section id="importar-csv" class="mb-8">
            <h2 class="text-2xl font-semibold text-green-700 mb-2">Importar Archivo CSV</h2>
            <p class="text-gray-700">Esta función permite cargar un archivo CSV al sistema para su procesamiento y visualización en una tabla.</p>
            <p class="text-gray-700 mt-2">Pasos:</p>
            <ol class="list-decimal list-inside text-gray-600">
                <li>Selecciona el archivo CSV en el formulario de carga.</li>
                <li>Haz clic en el botón <strong>Subir Excel</strong> para cargar el archivo.</li>
                <li>El sistema procesará los datos y los mostrará en la tabla de contenidos.</li>
            </ol>
        </section>

        <!-- Sección: Listar y Ordenar Tabla -->
        <section id="listar-tabla" class="mb-8">
            <h2 class="text-2xl font-semibold text-green-700 mb-2">Listar y Ordenar Tabla de Contenidos</h2>
            <p class="text-gray-700">El sistema muestra los datos importados en una tabla, que puedes ordenar según diferentes criterios (Marca, Modelo, etc.).</p>
            <p class="text-gray-700 mt-2">Para ordenar los datos, haz clic en el encabezado de la columna correspondiente.</p>
        </section>

        <!-- Sección: Buscar Datos -->
        <section id="buscar-datos" class="mb-8">
            <h2 class="text-2xl font-semibold text-green-700 mb-2">Buscar Datos</h2>
            <p class="text-gray-700">Utiliza el campo de búsqueda para filtrar los datos en la tabla. Puedes buscar por Marca, Modelo, Procedencia, y más.</p>
            <p class="text-gray-700 mt-2">Escribe el término de búsqueda en el campo de texto y la tabla se actualizará automáticamente con los resultados coincidentes.</p>
        </section>

    </div>
</body>

</html>