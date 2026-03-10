// ABRIR MODAL NUEVO USUARIO
function abrirModalNuevo() {
    const modal = document.getElementById("modalNuevo");
    const contenido = document.getElementById("modalContenidoNuevo");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    // Reset estilos antes de animar
    contenido.classList.remove("scale-95", "opacity-0");
    contenido.classList.add("scale-100", "opacity-100");
    fetch("nuevo_usuario.php")
        .then((response) => response.text())
        .then((html) => {
            document.getElementById("contenidoModalNuevo").innerHTML = html;
            // Inicializar variables después de cargar el HTML
            setTimeout(() => {
                if (typeof inicializarVariables === 'function') {
                    inicializarVariables();
                }
            }, 100);
            // Configurar el evento submit del formulario
            const form = document.getElementById("form-nuevo-usuario");
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    crearUsuario();
                });
            }
        })
        .catch((err) => {
            document.getElementById("contenidoModalNuevo").innerHTML =
                '<p class="text-red-600 text-center">Error cargando el formulario.</p>';
            console.error(err);
        });
}
// CERRAR MODAL NUEVO USUARIO
function cerrarModalNuevo() {
    const modal = document.getElementById("modalNuevo");
    const contenido = document.getElementById("modalContenidoNuevo");
    // Aplica animación de salida
    contenido.classList.remove("scale-100", "opacity-100");
    contenido.classList.add("scale-95", "opacity-0");
    setTimeout(() => {
        modal.classList.remove("flex");
        modal.classList.add("hidden");
        document.getElementById("contenidoModalNuevo").innerHTML =
            '<p class="text-center text-gray-500">Cargando...</p>';
        location.reload();
    }, 300);
}
// Función cerrarModal genérica (para compatibilidad)
function cerrarModal() {
    cerrarModalNuevo();
}