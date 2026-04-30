// ============================================================
// BUSCAR INGRESOS — GACC-0011 — PROSERVIPOL
// Compatible con jQuery 1.12.4
// ============================================================

var paginaActual = 1;
var totalPaginas = 1;
var datosCache   = [];  // Guarda últimos resultados para exportar CSV

// REALIZAR BÚSQUEDA
function realizarBusqueda(pagina) {
    pagina = pagina || 1;

    var fecha_desde   = document.getElementById('fecha_desde').value;
    var fecha_hasta   = document.getElementById('fecha_hasta').value;
    var busqueda      = document.getElementById('busqueda').value;
    var unidad_filter = document.getElementById('unidad_filter').value;

    // Validación: fechas obligatorias
    if (!fecha_desde || !fecha_hasta) {
        alert('⚠️ Debe ingresar Fecha Desde y Fecha Hasta para realizar la búsqueda.');
        return;
    }

    // Validar rango de fechas
    if (fecha_desde > fecha_hasta) {
        alert('La fecha DESDE no puede ser mayor que la fecha HASTA.');
        return;
    }

    mostrarCargando(true);
    ocultarTabla();
    ocultarSinResultados();

    var formData = new FormData();
    formData.append('fecha_desde',   fecha_desde);
    formData.append('fecha_hasta',   fecha_hasta);
    formData.append('busqueda',      busqueda);
    formData.append('unidad_filter', unidad_filter);
    formData.append('pagina',        pagina);

    fetch('queries/buscar_ingresos_queries.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.text(); })
    .then(function(texto) {
        var respuesta;
        try {
            respuesta = JSON.parse(texto.trim());
        } catch(e) {
            console.error('Error JSON:', e, texto);
            alert('Error en la respuesta del servidor.');
            mostrarCargando(false);
            return;
        }

        mostrarCargando(false);

        if (!respuesta.success) {
            alert('Error: ' + respuesta.message);
            return;
        }

        if (!respuesta.datos || respuesta.datos.length === 0) {
            mostrarSinResultados();
            return;
        }

        paginaActual = respuesta.pagina;
        totalPaginas = respuesta.paginas;
        datosCache   = respuesta.datos;

        renderizarTabla(respuesta.datos, respuesta.total, respuesta.pagina, respuesta.por_pagina);
        renderizarPaginacion(respuesta.pagina, respuesta.paginas);
        mostrarEstado(respuesta.total, respuesta.pagina, respuesta.por_pagina);
        mostrarTabla();

        document.getElementById('btn-exportar').classList.remove('hidden');
    })
    .catch(function(err) {
        mostrarCargando(false);
        console.error(err);
        alert('Error de conexión al buscar ingresos.');
    });
}

// FORMATEAR FECHA de YYYY-MM-DD HH:MM:SS a DD-MM-YYYY HH:MM:SS
function formatearFecha(fechaStr) {
    if (!fechaStr) return '<span class="text-gray-400">—</span>';
    // Formato esperado: "2026-04-30 00:01:35"
    var partes = fechaStr.split(' ');
    if (partes.length < 1) return htmlEscape(fechaStr);
    var fecha = partes[0].split('-');
    if (fecha.length !== 3) return htmlEscape(fechaStr);
    var hora  = partes[1] ? partes[1] : '';
    return fecha[2] + '-' + fecha[1] + '-' + fecha[0] + (hora ? ' ' + hora : '');
}

// RENDERIZAR FILAS DE LA TABLA
function renderizarTabla(datos, total, pagina, porPagina) {
    var tbody  = document.getElementById('tabla-cuerpo');
    tbody.innerHTML = '';
    var inicio = (pagina - 1) * porPagina + 1;

    for (var i = 0; i < datos.length; i++) {
        var d       = datos[i];
        var num     = inicio + i;
        //var termino = d.US_FECHAHORA_TERMINO ? formatearFecha(d.US_FECHAHORA_TERMINO) : '<span class="text-gray-400">—</span>';
        var evento  = d.US_EVENTO            ? htmlEscape(d.US_EVENTO)                : '<span class="text-gray-400">—</span>';

        tbody.innerHTML +=
            '<tr class="text-center hover:bg-green-50 transition-colors duration-150">' +
                '<td class="border px-3 py-1">'                   + num                                  + '</td>' +
                '<td class="border px-3 py-1">'                   + formatearFecha(d.US_FECHAHORA_INICIO) + '</td>' +
                //'<td class="border px-3 py-1">'                   + termino                              + '</td>' +
                '<td class="border px-3 py-1 font-mono">'         + htmlEscape(d.FUN_CODIGO)              + '</td>' +
                '<td class="border px-3 py-1">'                   + htmlEscape(d.FUN_RUT)                 + '</td>' +
                '<td class="border px-3 py-1 text-left">'         + htmlEscape(d.NOMBRE_COMPLETO)         + '</td>' +
                '<td class="border px-3 py-1">'                   + htmlEscape(d.GRA_DESCRIPCION)         + '</td>' +
                '<td class="border px-3 py-1 text-left">'         + htmlEscape(d.UNI_DESCRIPCION)         + '</td>' +
                '<td class="border px-3 py-1">'                   + htmlEscape(d.TUS_DESCRIPCION)         + '</td>' +
                '<td class="border px-3 py-1 font-mono text-xs">' + htmlEscape(d.US_DIRECCION_IP)         + '</td>' +
                '<td class="border px-3 py-1">'                   + evento                               + '</td>' +
            '</tr>';
    }
}

// RENDERIZAR PAGINACIÓN
function renderizarPaginacion(pagina, total) {
    var nav = document.getElementById('paginacion');
    nav.innerHTML = '';

    if (total <= 1) return;

    var html = '<ul class="flex justify-center space-x-2">';

    if (pagina > 1) {
        html += '<li><a href="#" onclick="realizarBusqueda(' + (pagina - 1) + '); return false;" ' +
                'class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded text-xs">Anterior</a></li>';
    }

    html += '<li><span class="text-gray-700 py-1 px-3 text-xs">' + pagina + ' de ' + total + '</span></li>';

    if (pagina < total) {
        html += '<li><a href="#" onclick="realizarBusqueda(' + (pagina + 1) + '); return false;" ' +
                'class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-1 px-3 rounded text-xs">Siguiente</a></li>';
    }

    html += '</ul>';
    nav.innerHTML = html;
}

// LIMPIAR FILTROS
function limpiarFiltros() {
    document.getElementById('fecha_desde').value   = '';
    document.getElementById('fecha_hasta').value   = '';
    document.getElementById('busqueda').value       = '';
    document.getElementById('unidad_filter').value = '';

    ocultarTabla();
    ocultarSinResultados();

    var estado = document.getElementById('estado-busqueda');
    estado.classList.add('hidden');
    estado.textContent = '';

    document.getElementById('btn-exportar').classList.add('hidden');
    document.getElementById('tabla-cuerpo').innerHTML = '';
    document.getElementById('paginacion').innerHTML   = '';
    datosCache = [];
}

// EXPORTAR CSV
function exportarCSV() {
    if (!datosCache || datosCache.length === 0) {
        alert('No hay datos para exportar.');
        return;
    }

    var cabecera = [
        'Fecha Ingreso', /*'Fecha Termino',*/ 'Codigo', 'RUT',
        'Nombre Completo', 'Grado', 'Unidad', 'Perfil', 'IP', 'Evento'
    ];

    var filas = [cabecera.join(';')];

    for (var i = 0; i < datosCache.length; i++) {
        var d    = datosCache[i];
        var fila = [
            csvEscape(d.US_FECHAHORA_INICIO),
            //csvEscape(d.US_FECHAHORA_TERMINO || ''),
            csvEscape(d.FUN_CODIGO),
            csvEscape(d.FUN_RUT),
            csvEscape(d.NOMBRE_COMPLETO),
            csvEscape(d.GRA_DESCRIPCION),
            csvEscape(d.UNI_DESCRIPCION),
            csvEscape(d.TUS_DESCRIPCION),
            csvEscape(d.US_DIRECCION_IP),
            csvEscape(d.US_EVENTO || '')
        ];
        filas.push(fila.join(';'));
    }

    var contenido = filas.join('\n');
    var blob      = new Blob(['\uFEFF' + contenido], { type: 'text/csv;charset=utf-8;' });
    var url       = URL.createObjectURL(blob);
    var a         = document.createElement('a');
    a.href        = url;
    a.download    = 'ingresos_proservipol_' + fechaHoy() + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// ── UTILIDADES ──────────────────────────────────────────────

function mostrarCargando(mostrar) {
    var el = document.getElementById('cargando');
    if (mostrar) { el.classList.remove('hidden'); }
    else         { el.classList.add('hidden'); }
}

function mostrarTabla() {
    document.getElementById('contenedor-tabla').classList.remove('hidden');
}

function ocultarTabla() {
    document.getElementById('contenedor-tabla').classList.add('hidden');
}

function mostrarSinResultados() {
    document.getElementById('sin-resultados').classList.remove('hidden');
    document.getElementById('btn-exportar').classList.add('hidden');
}

function ocultarSinResultados() {
    document.getElementById('sin-resultados').classList.add('hidden');
}

function mostrarEstado(total, pagina, porPagina) {
    var estado = document.getElementById('estado-busqueda');
    var inicio = (pagina - 1) * porPagina + 1;
    var fin    = Math.min(pagina * porPagina, total);
    estado.textContent = 'Mostrando ' + inicio + ' a ' + fin + ' de ' + total + ' registros.';
    estado.classList.remove('hidden');
}

function htmlEscape(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;');
}

function csvEscape(val) {
    if (!val) return '';
    val = String(val).replace(/"/g, '""');
    return '"' + val + '"';
}

function fechaHoy() {
    var d = new Date();
    return d.getFullYear() + '-' +
           String(d.getMonth() + 1).padStart(2, '0') + '-' +
           String(d.getDate()).padStart(2, '0');
}

// Permitir búsqueda con tecla Enter en el campo de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    var inputBusqueda = document.getElementById('busqueda');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                realizarBusqueda(1);
            }
        });
    }
});
