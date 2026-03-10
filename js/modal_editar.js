//CARGAMOS EL MODAL CON SUS DATOS
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
    const password = document.getElementById("password")?.value || "";

    // Validar antes de enviar
    if (!codigo || !perfil || !unidad || !password) {
        alert("Por favor, complete todos los campos.");
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
    .then((respuesta) => {
        const r = respuesta.trim().toLowerCase();
        if (r === "ok" || r.includes("actualizados correctamente")) {
            alert("Cambios guardados correctamente.");
            recargarDatosModal(codigo);
        } else {
            alert("Error: " + respuesta);
        }
    })
    .catch((error) => {
        alert("Ocurrió un error al guardar los cambios.");
        console.error(error);
    });
}

//RECARGAMOS EL MODAL CON SUS DATOS
function recargarDatosModal(codigo) {
    fetch(`editar_usuario.php?codigo=${codigo}`)
        .then((res) => res.text())
        .then((html) => {
            const contenedor = document.getElementById("contenidoModal");
            if (contenedor) {
                contenedor.innerHTML = html;
            } else {
                console.warn("No se encontró el contenedor del modal.");
            }
        })
        .catch((err) => console.error("Error al recargar modal:", err));
}