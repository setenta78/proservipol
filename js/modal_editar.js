// CARGAMOS EL MODAL CON SUS DATOS
function abrirModal(codigo) {
    const modal = document.getElementById("modalEditar");
    const contenido = document.getElementById("modalContenido");

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    // Reset estilos antes de animar
    contenido.classList.remove("scale-95", "opacity-0");
    contenido.classList.add("scale-100", "opacity-100");

    fetch("editar_usuario.php?codigo=" + encodeURIComponent(codigo))
        .then((response) => response.text())
        .then((html) => {
            document.getElementById("contenidoModal").innerHTML = html;
        })
        .catch((err) => {
            document.getElementById("contenidoModal").innerHTML = 
                '<p class="text-red-600 text-center">Error cargando el formulario.</p>';
            console.error(err);
        });
}

function cerrarModal() {
    const modal = document.getElementById("modalEditar");
    const contenido = document.getElementById("modalContenido");
    
    // Aplica animación de salida
    contenido.classList.remove("scale-100", "opacity-100");
    contenido.classList.add("scale-95", "opacity-0");

    setTimeout(() => {
        modal.classList.remove("flex");
        modal.classList.add("hidden");
        document.getElementById("contenidoModal").innerHTML =
            '<p class="text-center text-gray-500">Cargando...</p>';
    }, 300);
    
    location.reload();
}

function guardarCambios() {
    const form = document.getElementById("form-editar-usuario");
    const codigo = document.getElementById("codigo")?.value || "";
    const perfil = document.getElementById("perfil")?.value || "";
    const unidad = document.getElementById("unidad")?.value || "";
    const passwordInput = document.getElementById("password");
    const password = passwordInput ? passwordInput.value : "";

    // Validar campos obligatorios (Password NO es obligatorio en edición)
    if (!codigo || !perfil || !unidad) {
        alert("Por favor, complete el Perfil y la Unidad.");
        return;
    }

    // Validar longitud de password si se ingresó uno nuevo
    if (password && password.length < 6) {
        alert("La contraseña nueva debe tener al menos 6 caracteres.");
        return;
    }

    const datos = new FormData();
    datos.append("codigo", codigo);
    datos.append("perfil", perfil);
    datos.append("unidad", unidad);
    datos.append("password", password);

    fetch("queries/editar_queries.php", {
        method: "POST",
        body: datos,
    })
    .then((response) => response.text())
    .then((textoRespuesta) => {
        // Intentar parsear JSON manualmente para compatibilidad PHP 5.1.2 si falla el auto-parse
        let respuesta;
        try {
            // Limpieza básica por si hay espacios en blanco extra
            textoRespuesta = textoRespuesta.trim();
            respuesta = JSON.parse(textoRespuesta);
        } catch (e) {
            console.error("Error parseando JSON:", e, "Texto recibido:", textoRespuesta);
            alert("Error en la respuesta del servidor. Revise la consola.");
            return;
        }

        if (respuesta.success) {
            alert("✅ " + respuesta.message);
            cerrarModal();
        } else {
            alert("⚠️ Error: " + respuesta.message);
        }
    })
    .catch((error) => {
        alert("Ocurrió un error de conexión al guardar los cambios.");
        console.error(error);
    });
}

// RECARGAMOS EL MODAL CON SUS DATOS (Si fuera necesario)
function recargarDatosModal(codigo) {
    fetch(`editar_usuario.php?codigo=${codigo}`)
        .then((res) => res.text())
        .then((html) => {
            const contenedor = document.getElementById("contenidoModal");
            if (contenedor) {
                contenedor.innerHTML = html;
            }
        })
        .catch((err) => console.error("Error al recargar modal:", err));
}