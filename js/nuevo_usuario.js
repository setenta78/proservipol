// Variables globales para acceder a los elementos del formulario
let textCodFuncionarioBusqueda, codigo, rut, nombres, apellidos, grado, unidad, cargo, fechaCargo, unidadAgregado, curso;
let tieneCursoAprobado = true;

// ✅ URL base centralizada — un solo lugar para cambiar
var BASE_URL = 'http://aplicativos.des-proservipol.carabineros.cl';

document.addEventListener('DOMContentLoaded', function() {
    inicializarVariables();
});

function inicializarVariables() {
    textCodFuncionarioBusqueda = document.getElementById("textCodFuncionarioBusqueda");
    codigo      = document.getElementById("codigo");
    rut         = document.getElementById("rut");
    nombres     = document.getElementById("nombres");
    apellidos   = document.getElementById("apellidos");
    grado       = document.getElementById("grado");
    unidad      = document.getElementById("unidad");
    cargo       = document.getElementById("cargo");
    fechaCargo  = document.getElementById("fechaCargo");
    unidadAgregado = document.getElementById("unidadAgregado");
    curso       = document.getElementById("curso");
}

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
    
    if (!funcionarioPersonal) {
        alert("❌ Error de comunicación con el servidor de personal.");
        textCodFuncionarioBusqueda.value = "";
        return;
    }

    if (funcionarioPersonal.yaRegistrado === true) {
        alert("ℹ️ " + funcionarioPersonal.message);
        textCodFuncionarioBusqueda.value = "";
        return;
    }
    
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
    
    if (funcionarioPersonal.success === true && funcionarioPersonal.data) {
        console.log("✅ Funcionario encontrado:", funcionarioPersonal);
        
        let cursoInfo = funcionarioPersonal.curso || { tieneCurso: false, mensaje: "Sin información" };
        
        if (!cursoInfo.tieneCurso) {
            let continuar = confirm(
                "⚠️ ADVERTENCIA: EL USUARIO NO TIENE EL CURSO PROSERVIPOL APROBADO.\n\n" +
                "¿Desea continuar con el registro de todos modos?"
            );
            
            if (!continuar) {
                borrarValores();
                cerrarModalNuevo();
                return; 
            }
            tieneCursoAprobado = false;
        } else {
            tieneCursoAprobado = true;
        }

        asignarValores(funcionarioPersonal);
        return;
    }
    
    alert("❌ Código de Funcionario no encontrado en BD Personal, verifique y reintente");
    textCodFuncionarioBusqueda.value = "";
}

function buscarFuncionarioPersonal() {
    // ✅ FIX: usar BASE_URL
    return fetchAPI(
        BASE_URL + "/api/buscarFuncionarioPersonal/?codFuncionario=" +
        encodeURIComponent(textCodFuncionarioBusqueda.value)
    );
}

async function crearUsuario() {
    if (!codigo) {
        inicializarVariables();
    }
    
    const codFuncionario = codigo.value;
    const codigoUnidad   = document.querySelector("input[name='unidadPerfil']").value;
    const tipoUsuario    = document.getElementById("perfil").value;
    const password       = document.querySelector("input[name='proClave']").value;

    if (!codFuncionario || !codigoUnidad || !tipoUsuario || !password) {
        alert("⚠️ Todos los campos son obligatorios");
        return;
    }
    
    if (password.length < 6) {
        alert("⚠️ La contraseña debe tener al menos 6 caracteres");
        return;
    }
    
    const datos = {
        codFuncionario: codFuncionario,
        codigoUnidad:   codigoUnidad,
        tipoUsuario:    tipoUsuario,
        password:       password
    };

    try {
        console.log("📤 Enviando datos:", datos);
        
        // ✅ FIX: usar BASE_URL
        const response = await fetch(BASE_URL + "/api/crearUsuario/", {
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
        
        console.log("✅ Usuario creado/reactivado:", data);
        
        if (data.message) {
            alert("✅ " + data.message);
        } else {
            alert("✅ Usuario procesado correctamente");
        }
        
        cerrarModalNuevo();
        location.reload();
        
    } catch (err) {
        console.error("❌ Error de conexión:", err);
        alert("⚠️ Error de conexión con el servidor. Verifique su conexión a internet.");
    }
}

async function fetchAPI(url) {
    try {
        console.log("📡 Llamando API:", url);
        const response = await fetch(url);
        const text = await response.text();
        console.log("📥 Respuesta RAW:", text);
        
        let data = null;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error("❌ Error al parsear JSON:", parseError, "Texto recibido:", text);
            alert("⚠️ Error en la respuesta del servidor. Formato de datos incorrecto.");
            return null;
        }

        if (response.status === 200) {
            return data;
        }
        
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
    if (codigo)        codigo.value        = "";
    if (rut)           rut.value           = "";
    if (nombres)       nombres.value       = "";
    if (apellidos)     apellidos.value     = "";
    if (grado)         grado.value         = "";
    if (unidad)        unidad.value        = "";
    if (cargo)         cargo.value         = "";
    if (fechaCargo)    fechaCargo.value    = "";
    if (unidadAgregado) unidadAgregado.value = "";
    if (curso)         curso.value         = "";
    
    tieneCursoAprobado = true;
    
    const perfil = document.getElementById("perfil");
    if (perfil) perfil.selectedIndex = 0;
    
    const unidadPerfil = document.querySelector("input[name='unidadPerfil']");
    if (unidadPerfil) unidadPerfil.value = "";
    
    const searchUnidad = document.querySelector(".search-box input[type='text']");
    if (searchUnidad) searchUnidad.value = "";
    
    const proClave = document.querySelector("input[name='proClave']");
    if (proClave) proClave.value = "";

    const proUsuario = document.querySelector("input[name='proUsuario']");
    if (proUsuario) proUsuario.value = "";
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
    
    codigo.value      = data.codigo || textCodFuncionarioBusqueda.value || "";
    rut.value         = data.rut    || "";
    nombres.value     = data.nombre || "";
    apellidos.value   = ((data.apellidoPaterno || "") + " " + (data.apellidoMaterno || "")).trim();
    grado.value       = data.grado  || "";
    unidad.value      = data.unidad || "";
    cargo.value       = "Por definir";
    fechaCargo.value  = "-";
    unidadAgregado.value = data.departamento || "-";
    
    const proUsuario = document.querySelector("input[name='proUsuario']");
    if (proUsuario) {
        proUsuario.value = data.codigo || textCodFuncionarioBusqueda.value || "";
    }

    if (valores.curso) {
        if (valores.curso.tieneCurso === true) {
            curso.value = "APROBADO - " + (valores.curso.fechaAprobacion || "");
            curso.style.color      = "green";
            curso.style.fontWeight = "bold";
        } else {
            curso.value = valores.curso.mensaje || "Sin Curso";
            curso.style.color      = "red";
            curso.style.fontWeight = "bold";
        }
    } else {
        curso.value = "Sin información de curso";
    }
    
    console.log("✅ Valores asignados correctamente. ¿Tiene curso?:", tieneCursoAprobado);
}

function cerrarModalNuevo() {
    const modal = document.getElementById("modalNuevoUsuario");
    if (modal) {
        modal.style.display = "none";
    }
    
    const modalGenerico = document.getElementById("modalNuevo");
    if (modalGenerico) {
        modalGenerico.classList.add("hidden");
        modalGenerico.classList.remove("flex");
        
        const contenido = document.getElementById("modalContenidoNuevo");
        if (contenido) {
            contenido.classList.remove("scale-100", "opacity-100");
            contenido.classList.add("scale-95", "opacity-0");
        }
    }

    borrarValores();
}