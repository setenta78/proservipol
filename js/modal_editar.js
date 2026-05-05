// ============================================================
// MODAL EDITAR USUARIO — PROSERVIPOL
// Compatible con jQuery 1.12.4 | PHP 5.1.2
// ============================================================

function abrirModal(codigo) {
    var modal     = document.getElementById("modalEditar");
    var contenido = document.getElementById("modalContenido");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    contenido.classList.remove("scale-95", "opacity-0");
    contenido.classList.add("scale-100", "opacity-100");

    fetch("editar_usuario.php?codigo=" + encodeURIComponent(codigo))
        .then(function(response) { return response.text(); })
        .then(function(html) {
            document.getElementById("contenidoModal").innerHTML = html;
        })
        .catch(function(err) {
            document.getElementById("contenidoModal").innerHTML =
                '<p class="text-red-600 text-center">Error cargando el formulario.</p>';
            console.error(err);
        });
}

function cerrarModal() {
    var modal     = document.getElementById("modalEditar");
    var contenido = document.getElementById("modalContenido");
    contenido.classList.remove("scale-100", "opacity-100");
    contenido.classList.add("scale-95", "opacity-0");
    setTimeout(function() {
        modal.classList.remove("flex");
        modal.classList.add("hidden");
        document.getElementById("contenidoModal").innerHTML =
            '<p class="text-center text-gray-500">Cargando...</p>';
    }, 300);
    location.reload();
}

function confirmarYGuardar() {
    var codigo   = document.getElementById("codigo")   ? document.getElementById("codigo").value   : "";
    var perfil   = document.getElementById("perfil")   ? document.getElementById("perfil").value   : "";
    var unidad   = document.getElementById("unidad")   ? document.getElementById("unidad").value   : "";
    var password = document.getElementById("password") ? document.getElementById("password").value : "";

    if (!codigo || !perfil || !unidad) {
        PSAlert.advertencia("Por favor, complete el Perfil y la Unidad.");
        return;
    }
    if (password && password.length < 6) {
        PSAlert.advertencia("La contraseña nueva debe tener al menos 6 caracteres.");
        return;
    }

    PSAlert.confirmar(
        '¿Está seguro de modificar los datos de este usuario?',
        function() { guardarCambios(); },
        'Confirmar modificación',
        null,
        'warning'
    );
}

function guardarCambios() {
    var codigo   = document.getElementById("codigo")   ? document.getElementById("codigo").value   : "";
    var perfil   = document.getElementById("perfil")   ? document.getElementById("perfil").value   : "";
    var unidad   = document.getElementById("unidad")   ? document.getElementById("unidad").value   : "";
    var password = document.getElementById("password") ? document.getElementById("password").value : "";

    if (!codigo || !perfil || !unidad) {
        PSAlert.advertencia("Por favor, complete el Perfil y la Unidad.");
        return;
    }
    if (password && password.length < 6) {
        PSAlert.advertencia("La contraseña nueva debe tener al menos 6 caracteres.");
        return;
    }

    var zonaBotones = document.getElementById("zona-botones-editar");
    if (zonaBotones) {
        zonaBotones.innerHTML =
            '<p class="text-xs text-blue-700 font-semibold text-center">⏳ Guardando cambios...</p>';
    }

    var datos = new FormData();
    datos.append("codigo",   codigo);
    datos.append("perfil",   perfil);
    datos.append("unidad",   unidad);
    datos.append("password", password);

    fetch("queries/editar_queries.php", {
        method: "POST",
        body: datos
    })
    .then(function(response) { return response.text(); })
    .then(function(textoRespuesta) {
        var respuesta;
        try {
            respuesta = JSON.parse(textoRespuesta.trim());
        } catch(e) {
            console.error("Error parseando JSON:", e, textoRespuesta);
            PSAlert.error("Error en la respuesta del servidor. Revise la consola.");
            cancelarConfirmacion();
            return;
        }

        if (respuesta.success) {
            if (zonaBotones) {
                zonaBotones.innerHTML =
                    '<p class="text-xs text-green-700 font-semibold text-center">✅ ' + respuesta.message + '</p>';
            }
            PSAlert.exito(respuesta.message, 'Datos actualizados');
            setTimeout(function() { cerrarModal(); }, 1500);
        } else {
            PSAlert.error(respuesta.message, 'Error al modificar');
            if (zonaBotones) {
                zonaBotones.innerHTML =
                    '<button type="button" onclick="confirmarYGuardar()" ' +
                        'class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded">' +
                        'MODIFICAR USUARIO' +
                    '</button>';
            }
        }
    })
    .catch(function(error) {
        PSAlert.error("Ocurrió un error de conexión al guardar los cambios.");
        console.error(error);
        cancelarConfirmacion();
    });
}

function cancelarConfirmacion() {
    var zonaBotones = document.getElementById("zona-botones-editar");
    if (!zonaBotones) return;
    zonaBotones.innerHTML =
        '<button type="button" onclick="confirmarYGuardar()" ' +
            'class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 text-sm rounded">' +
            'MODIFICAR USUARIO' +
        '</button>' +
        '<div id="mensaje-respuesta" class="text-sm p-2 hidden rounded"></div>';
}

function recargarDatosModal(codigo) {
    fetch("editar_usuario.php?codigo=" + codigo)
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var contenedor = document.getElementById("contenidoModal");
            if (contenedor) { contenedor.innerHTML = html; }
        })
        .catch(function(err) { console.error("Error al recargar modal:", err); });
}