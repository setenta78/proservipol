// Variables globales para acceder a los elementos del formulario
let textCodFuncionarioBusqueda, codigo, rut, nombres, apellidos, grado, unidad, cargo, fechaCargo, unidadAgregado, curso;
let tieneCursoAprobado = true; // Bandera global para controlar el estado del curso

// Inicializar variables cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    inicializarVariables();
});

function inicializarVariables() {
    textCodFuncionarioBusqueda = document.getElementById("textCodFuncionarioBusqueda");
    codigo = document.getElementById("codigo");
    rut = document.getElementById("rut");
    nombres = document.getElementById("nombres");
    apellidos = document.getElementById("apellidos");
    grado = document.getElementById("grado");
    unidad = document.getElementById("unidad");
    cargo = document.getElementById("cargo");
    fechaCargo = document.getElementById("fechaCargo");
    unidadAgregado = document.getElementById("unidadAgregado");
    curso = document.getElementById("curso");
}

// Función principal: busca funcionario en BD PERSONAL únicamente
async function cargarUsuario() {
    if (!textCodFuncionarioBusqueda) {
        inicializarVariables();
    }
    
    if (!textCodFuncionarioBusqueda || !textCodFuncionarioBusqueda.value) {
        alert("⚠️ Debe ingresar un código de funcionario");
        return;
    }
    
    const codFuncionario = textCodFuncionarioBusqueda.value.trim();
    console.log("🔍 Buscando funcionario:", codFuncionario);
    
    let funcionarioPersonal = await buscarFuncionarioPersonal();
    
    // Validación básica de respuesta
    if (!funcionarioPersonal) {
        alert("❌ Error de comunicación con el servidor de personal.");
        textCodFuncionarioBusqueda.value = "";
        return;
    }

    // === CASO 1: Usuario ya registrado y activo ===
    if (funcionarioPersonal.yaRegistrado === true) {
        alert("ℹ️ " + funcionarioPersonal.message);
        textCodFuncionarioBusqueda.value = "";
        return;
    }
    
    // === CASO 2: Usuario existe pero está INACTIVO ===
    if (funcionarioPersonal.usuarioInactivo === true) {
        const confirmar = confirm(
            "⚠️ " + funcionarioPersonal.message + "\n\n" +
            "Datos del usuario:\n" +
            "Código: " + funcionarioPersonal.data.codigo + "\n" +
            "RUT: " + funcionarioPersonal.data.rut + "\n" +
            "Nombre: " + funcionarioPersonal.data.nombre + " " + 
            funcionarioPersonal.data.apellidoPaterno + " " + 
            funcionarioPersonal.data.apellidoMaterno + "\n" +
            "Grado: " + funcionarioPersonal.data.grado + "\n" +
            "Unidad: " + funcionarioPersonal.data.unidad + "\n\n" +
            "¿Desea reactivar este usuario?"
        );
        
        if (confirmar) {
            tieneCursoAprobado = true; 
            asignarValores(funcionarioPersonal);
        } else {
            textCodFuncionarioBusqueda.value = "";
        }
        return;
    }
    
    // === CASO 3: Funcionario encontrado en PERSONAL (nuevo usuario) ===
    if (funcionarioPersonal.success === true && funcionarioPersonal.data) {
        console.log("✅ Funcionario encontrado:", funcionarioPersonal);
        
        // Verificar estado del curso ANTES de asignar valores
        let cursoInfo = funcionarioPersonal.curso || { tieneCurso: false, mensaje: "Sin información" };
        
        if (!cursoInfo.tieneCurso) {
            // CORRECCIÓN 1: Mensaje limpio sin repetición
            let continuar = confirm(
                "⚠️ ADVERTENCIA: EL USUARIO NO TIENE EL CURSO PROSERVIPOL APROBADO.\n\n" +
                "¿Desea continuar con el registro de todos modos?"
            );
            
            if (!continuar) {
                // CORRECCIÓN 2: Si cancela, limpiar todo y CERRAR el modal
                borrarValores();
                cerrarModalNuevo();
                return; 
            }
            // Si continúa, establecemos la bandera en falso pero permitimos el flujo
            tieneCursoAprobado = false;
        } else {
            tieneCursoAprobado = true;
        }

        asignarValores(funcionarioPersonal);
        return;
    }
    
    // === CASO 4: Error genérico o no encontrado ===
    alert("❌ Código de Funcionario no encontrado en BD Personal, verifique y reintente");
    textCodFuncionarioBusqueda.value = "";
}

function buscarFuncionarioPersonal() {
    return fetchAPI(
        "http://aplicativos.des-proservipol.carabineros.cl/api/buscarFuncionarioPersonal/?codFuncionario=" +
        textCodFuncionarioBusqueda.value
    );
}

async function crearUsuario() {
    if (!codigo) {
        inicializarVariables();
    }
    
    const codFuncionario = codigo.value;
    const codigoUnidad = document.querySelector("input[name='unidadPerfil']").value;
    const tipoUsuario = document.getElementById("perfil").value;
    const password = document.querySelector("input[name='proClave']").value;

    if (!codFuncionario || !codigoUnidad || !tipoUsuario || !password) {
        alert("⚠️ Todos los campos son obligatorios");
        return;
    }
    
    // Validar que la contraseña tenga al menos 6 caracteres
    if (password.length < 6) {
        alert("⚠️ La contraseña debe tener al menos 6 caracteres");
        return;
    }
    
    // Preparar los datos como un objeto JSON
    const datos = {
        codFuncionario: codFuncionario,
        codigoUnidad: codigoUnidad,
        tipoUsuario: tipoUsuario,
        password: password
    };

    try {
        console.log("📤 Enviando datos:", datos);
        
        const response = await fetch("http://aplicativos.des-proservipol.carabineros.cl/api/crearUsuario/", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });
        
        const raw = await response.text();
        console.log("📥 RAW RESPONSE:", raw);
        
        let data = null;
        try {
            data = JSON.parse(raw);
        } catch (parseError) {
            console.error("❌ Error al parsear JSON:", parseError);
            alert("⚠️ Error en la respuesta del servidor. Revise la consola.");
            return;
        }
        
        // Manejar respuestas de error
        if (!response.ok || !data.success) {
            let errorMsg = "Error al crear usuario. Revisa los datos.";
            
            if (data.message) {
                errorMsg = data.message;
            } else if (data.error) {
                errorMsg = Object.values(data.error).join("\n");
            }
            
            console.error("❌ Error al crear usuario:", errorMsg);
            alert("⚠️ " + errorMsg);
            return;
        }
        
        // Éxito
        console.log("✅ Usuario creado/reactivado:", data);
        
        if (data.message) {
            alert("✅ " + data.message);
        } else {
            alert("✅ Usuario procesado correctamente");
        }
        
        // Cerrar modal y recargar
        cerrarModalNuevo();
        location.reload();
        
    } catch (err) {
        console.error("❌ Error de conexión:", err);
        alert("⚠️ Error de conexión con el servidor. Verifique su conexión a internet.");
    }
}

// === FUNCIÓN CORREGIDA: fetchAPI ===
async function fetchAPI(url) {
    try {
        console.log("📡 Llamando API:", url);
        const response = await fetch(url);
        const text = await response.text();
        console.log("📥 Respuesta RAW:", text);
        
        // Intentar parsear el JSON
        let data = null;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error("❌ Error al parsear JSON:", parseError, "Texto recibido:", text);
            alert("⚠️ Error en la respuesta del servidor. Formato de datos incorrecto.");
            return null;
        }

        // La lógica clave: si el status es 200, asumimos éxito en la consulta
        if (response.status === 200) {
            return data;
        }
        
        // Para otros códigos de estado (404, 500, etc.)
        if (response.status === 404) {
            console.warn("⚠️ Funcionario no encontrado (404)");
            return data; 
        }
        
        console.warn("⚠️ Código de estado no esperado:", response.status);
        return data;
        
    } catch (networkError) {
        console.error("❌ Error de red en fetchAPI:", networkError);
        alert("⚠️ Error de conexión con el servidor");
        return null;
    }
}

function borrarValores() {
    if (!textCodFuncionarioBusqueda) {
        inicializarVariables();
    }
    
    if (textCodFuncionarioBusqueda) textCodFuncionarioBusqueda.value = "";
    if (codigo) codigo.value = "";
    if (rut) rut.value = "";
    if (nombres) nombres.value = "";
    if (apellidos) apellidos.value = "";
    if (grado) grado.value = "";
    if (unidad) unidad.value = "";
    if (cargo) cargo.value = "";
    if (fechaCargo) fechaCargo.value = "";
    if (unidadAgregado) unidadAgregado.value = "";
    if (curso) curso.value = "";
    
    // Resetear bandera de curso
    tieneCursoAprobado = true;
    
    const perfil = document.getElementById("perfil");
    if (perfil) perfil.selectedIndex = 0;
    
    const unidadPerfil = document.querySelector("input[name='unidadPerfil']");
    if (unidadPerfil) unidadPerfil.value = "";
    
    const searchUnidad = document.querySelector(".search-box input[type='text']");
    if (searchUnidad) searchUnidad.value = "";
    
    const proClave = document.querySelector("input[name='proClave']");
    if (proClave) proClave.value = "";
}

function asignarValores(valores) {
    console.log("📋 Asignando valores:", valores);
    
    if (!codigo) {
        inicializarVariables();
    }
    
    if (!valores || !valores.data) {
        alert("⚠️ No llegaron datos del funcionario");
        return;
    }
    
    const data = valores.data;
    
    // Asignar valores directamente desde el objeto
    codigo.value = data.codigo || textCodFuncionarioBusqueda.value || "";
    rut.value = data.rut || "";
    nombres.value = data.nombre || "";
    apellidos.value = ((data.apellidoPaterno || "") + " " + (data.apellidoMaterno || "")).trim();
    grado.value = data.grado || "";
    unidad.value = data.unidad || "";
    cargo.value = "Por definir";
    fechaCargo.value = "-";
    unidadAgregado.value = data.departamento || "-";
    
    // Asignar información del curso con estilo visual
    if (valores.curso) {
        if (valores.curso.tieneCurso === true) {
            curso.value = "APROBADO - " + (valores.curso.fechaAprobacion || "");
            curso.style.color = "green";
            curso.style.fontWeight = "bold";
        } else {
            curso.value = valores.curso.mensaje || "Sin Curso";
            curso.style.color = "red";
            curso.style.fontWeight = "bold";
        }
    } else {
        curso.value = "Sin información de curso";
    }
    
    console.log("✅ Valores asignados correctamente. ¿Tiene curso?:", tieneCursoAprobado);
}

// Función auxiliar para cerrar el modal
function cerrarModalNuevo() {
    // Opción 1: Modal por ID directo (estilo antiguo)
    const modal = document.getElementById("modalNuevoUsuario");
    if (modal) {
        modal.style.display = "none";
    }
    
    // Opción 2: Modal con clases Tailwind (estilo nuevo en gestor_usuarios.php)
    const modalGenerico = document.getElementById("modalNuevo");
    if (modalGenerico) {
        modalGenerico.classList.add("hidden");
        modalGenerico.classList.remove("flex");
        
        // Resetear opacidad y escala para la próxima apertura
        const contenido = document.getElementById("modalContenidoNuevo");
        if (contenido) {
            contenido.classList.remove("scale-100", "opacity-100");
            contenido.classList.add("scale-95", "opacity-0");
        }
    }

    borrarValores();
}