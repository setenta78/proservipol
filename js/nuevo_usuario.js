// Variables globales para acceder a los elementos del formulario
var textCodFuncionarioBusqueda, codigo, rut, nombres, apellidos, grado, unidad, cargo, fechaCargo, unidadAgregado, curso;
var tieneCursoAprobado = true;

var BASE_URL = 'http://aplicativos.des-proservipol.carabineros.cl';

document.addEventListener('DOMContentLoaded', function() {
    inicializarVariables();
});

function inicializarVariables() {
    textCodFuncionarioBusqueda = document.getElementById("textCodFuncionarioBusqueda");
    codigo         = document.getElementById("codigo");
    rut            = document.getElementById("rut");
    nombres        = document.getElementById("nombres");
    apellidos      = document.getElementById("apellidos");
    grado          = document.getElementById("grado");
    unidad         = document.getElementById("unidad");
    cargo          = document.getElementById("cargo");
    fechaCargo     = document.getElementById("fechaCargo");
    unidadAgregado = document.getElementById("unidadAgregado");
    curso          = document.getElementById("curso");
}

async function cargarUsuario() {
    if (!textCodFuncionarioBusqueda) {
        inicializarVariables();
    }

    if (!textCodFuncionarioBusqueda || !textCodFuncionarioBusqueda.value) {
        PSAlert.advertencia("Debe ingresar un código de funcionario.");
        return;
    }

    var codFuncionario = textCodFuncionarioBusqueda.value.trim();
    console.log("Buscando funcionario:", codFuncionario);

    var funcionarioPersonal = await buscarFuncionarioPersonal();

    // fetchAPI retorna null ante cualquier error (red, parseo, PHP warnings, etc.)
    // cargarUsuario es el ÚNICO responsable de mostrar el mensaje al usuario
    if (!funcionarioPersonal) {
        PSAlert.error("Error de comunicación con el servidor de personal.", "Ha ocurrido un error");
        textCodFuncionarioBusqueda.value = "";
        return;
    }

    // ── Ya registrado y activo ───────────────────────────────
    if (funcionarioPersonal.yaRegistrado === true) {
        PSAlert.info(funcionarioPersonal.message, "Funcionario ya registrado");
        textCodFuncionarioBusqueda.value = "";
        return;
    }

    // ── Usuario inactivo ─────────────────────────────────────
    if (funcionarioPersonal.usuarioInactivo === true) {
        var d = funcionarioPersonal.data;
        var cursoInfoInactivo = funcionarioPersonal.curso || { tieneCurso: false, mensaje: "Sin información" };

        var estadoCurso = cursoInfoInactivo.tieneCurso === true
            ? '<br>Curso: <strong style="color:green;">APROBADO - ' + (cursoInfoInactivo.fechaAprobacion || '') + '</strong>'
            : '<br>Curso: <strong style="color:red;">SIN CURSO PROSERVIPOL</strong>';

        var detalle =
            'Código: <strong>' + d.codigo + '</strong><br>' +
            'RUT: ' + d.rut + '<br>' +
            'Nombre: ' + d.nombre + ' ' + d.apellidoPaterno + ' ' + d.apellidoMaterno + '<br>' +
            'Grado: ' + d.grado + '<br>' +
            'Unidad: ' + d.unidad +
            estadoCurso + '<br><br>' +
            '¿Desea reactivar este usuario?';

        PSAlert.confirmar(
            detalle,
            function() {
                if (cursoInfoInactivo.tieneCurso === true) {
                    tieneCursoAprobado = true;
                    asignarValores(funcionarioPersonal);
                    PSAlert.exito(
                        'El Funcionario cuenta con el Curso PROSERVIPOL aprobado con fecha: <strong>' +
                        (cursoInfoInactivo.fechaAprobacion || 'fecha no disponible') + '</strong>',
                        'Curso verificado'
                    );
                    ocultarBannerSinCurso();
                } else {
                    PSAlert.confirmar(
                        'ADVERTENCIA: El funcionario NO tiene el curso PROSERVIPOL aprobado.<br><br>' +
                        '¿Desea continuar con la reactivación de todas formas?',
                        function() {
                            tieneCursoAprobado = false;
                            asignarValores(funcionarioPersonal);
                            mostrarBannerSinCurso();
                        },
                        'Sin curso aprobado',
                        function() {
                            borrarValores();
                            cerrarModalNuevo();
                        },
                        'warning'
                    );
                }
            },
            'Funcionario inactivo',
            function() {
                textCodFuncionarioBusqueda.value = "";
            }
        );
        return;
    }

    // ── Funcionario encontrado activo ────────────────────────
    if (funcionarioPersonal.success === true && funcionarioPersonal.data) {
        console.log("Funcionario encontrado:", funcionarioPersonal);

        var cursoInfo = funcionarioPersonal.curso || { tieneCurso: false, mensaje: "Sin información" };

        // ESCENARIO 1: Tiene curso aprobado
        if (cursoInfo.tieneCurso === true) {
            var fechaAprobacion = cursoInfo.fechaAprobacion || 'fecha no disponible';
            tieneCursoAprobado = true;
            asignarValores(funcionarioPersonal);
            PSAlert.exito(
                'El Funcionario cuenta con el Curso PROSERVIPOL aprobado con fecha: <strong>' + fechaAprobacion + '</strong>',
                'Curso verificado'
            );
            ocultarBannerSinCurso();
            return;
        }

        // ESCENARIO 2: No tiene curso
        PSAlert.confirmar(
            'ADVERTENCIA: El funcionario NO tiene el curso PROSERVIPOL aprobado.<br><br>' +
            '¿Desea continuar con el registro de todas formas?',
            function() {
                tieneCursoAprobado = false;
                asignarValores(funcionarioPersonal);
                mostrarBannerSinCurso();
            },
            'Sin curso aprobado',
            function() {
                borrarValores();
                cerrarModalNuevo();
            },
            'warning'
        );
        return;
    }

    PSAlert.error("Código de Funcionario no encontrado en BD Personal, verifique y reintente.", "No encontrado");
    textCodFuncionarioBusqueda.value = "";
}

// ── BANNER SIN CURSO ─────────────────────────────────────────

function mostrarBannerSinCurso() {
    ocultarBannerSinCurso();

    var banner = document.createElement('div');
    banner.id = 'banner-sin-curso';
    banner.style.cssText = [
        'background:#fef9c3;',
        'border:1px solid #ca8a04;',
        'border-radius:8px;',
        'padding:10px 16px;',
        'margin-bottom:12px;',
        'font-size:12px;',
        'font-weight:600;',
        'color:#854d0e;',
        'text-align:center;'
    ].join('');
    banner.innerHTML = 'Se creara el usuario SIN curso PROSERVIPOL aprobado';

    var formulario = document.getElementById("codigo");
    if (formulario && formulario.closest('form')) {
        formulario.closest('form').insertBefore(banner, formulario.closest('form').firstChild);
    } else {
        var contenedor = document.getElementById("contenidoModal") ||
                         document.getElementById("modalContenidoNuevo") ||
                         document.getElementById("contenidoModalNuevo");
        if (contenedor) {
            contenedor.insertBefore(banner, contenedor.firstChild);
        }
    }
}

function ocultarBannerSinCurso() {
    var banner = document.getElementById('banner-sin-curso');
    if (banner && banner.parentNode) {
        banner.parentNode.removeChild(banner);
    }
}

// ── BUSCAR FUNCIONARIO ───────────────────────────────────────

function buscarFuncionarioPersonal() {
    return fetchAPI(
        BASE_URL + "/api/buscarFuncionarioPersonal/?codFuncionario=" +
        encodeURIComponent(textCodFuncionarioBusqueda.value)
    );
}

// ── CREAR USUARIO ────────────────────────────────────────────

async function crearUsuario() {
    if (!codigo) {
        inicializarVariables();
    }

    var perfilSelect     = document.getElementById("perfil");
    var codFuncionario   = codigo.value;
    var codigoUnidad     = document.querySelector("input[name='unidadPerfil']").value;
    var tipoUsuario      = perfilSelect.value;
    var tipoUsuarioTexto = perfilSelect.options[perfilSelect.selectedIndex].text;
    var password         = document.querySelector("input[name='proClave']").value;

    if (!codFuncionario || !codigoUnidad || !tipoUsuario || !password) {
        PSAlert.advertencia("Todos los campos son obligatorios.");
        return;
    }

    if (password.length < 6) {
        PSAlert.advertencia("La contraseña debe tener al menos 6 caracteres.");
        return;
    }

    PSAlert.confirmar(
        'Esta a punto de registrar al funcionario <strong>' + codFuncionario + '</strong>.<br><br>' +
        'Perfil: <strong>' + tipoUsuarioTexto + '</strong><br>' +
        (!tieneCursoAprobado ? '<br><span style="color:#854d0e;font-weight:600;">Sin curso PROSERVIPOL aprobado</span><br>' : '') +
        '<br>¿Confirma la creacion del usuario?',
        function() {
            ejecutarCreacion(codFuncionario, codigoUnidad, tipoUsuario, password);
        },
        'Confirmar registro de usuario'
    );
}

async function ejecutarCreacion(codFuncionario, codigoUnidad, tipoUsuario, password) {
    var datos = {
        codFuncionario: codFuncionario,
        codigoUnidad:   codigoUnidad,
        tipoUsuario:    tipoUsuario,
        password:       password
    };

    try {
        console.log("Enviando datos:", datos);

        var response = await fetch(BASE_URL + "/api/crearUsuario/", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        var raw = await response.text();
        console.log("RAW RESPONSE:", raw);

        var data = null;
        try {
            data = JSON.parse(raw);
        } catch (parseError) {
            // Respuesta corrompida (PHP emitió warnings antes del JSON)
            // Conforme GACC-0010: un solo mensaje funcional de AutentificaTIC
            console.error("Error al parsear JSON en crearUsuario:", parseError, "RAW:", raw);
            PSAlert.error(
                "En este momento existe un problema con el servicio de registro de usuarios de AutentificaTIC. Inténtelo más tarde.",
                "Error de conexión con AutentificaTIC"
            );
            return;
        }

        if (!response.ok || !data.success) {
            var errorMsg = "Error al crear usuario. Revisa los datos.";
            if (data && data.message) {
                errorMsg = data.message;
            } else if (data && data.error) {
                errorMsg = Object.values(data.error).join("<br>");
            }
            console.error("Error al crear usuario:", errorMsg);

            // Detectar si el error proviene específicamente de AutentificaTIC
            var esErrorAutentificaTIC =
                errorMsg.indexOf('AutentificaTIC') !== -1 ||
                errorMsg.indexOf('Autentificatic') !== -1 ||
                errorMsg.indexOf('autentificatic') !== -1;

            if (esErrorAutentificaTIC) {
                PSAlert.error(
                    "En este momento existe un problema con el servicio de registro de usuarios de AutentificaTIC. Inténtelo más tarde.",
                    "Error de conexión con AutentificaTIC"
                );
            } else {
                PSAlert.error(errorMsg, "Error al registrar");
            }
            return;
        }

        console.log("Usuario creado/reactivado:", data);
        PSAlert.exito(data.message || "Usuario procesado correctamente.", "Registro exitoso");
        cerrarModalNuevo();
        setTimeout(function() { location.reload(); }, 2800);

    } catch (err) {
        // Error de red puro — no llegó respuesta del servidor
        console.error("Error de conexión en ejecutarCreacion:", err);
        PSAlert.error(
            "En este momento existe un problema con el servicio de registro de usuarios de AutentificaTIC. Inténtelo más tarde.",
            "Error de conexión con AutentificaTIC"
        );
    }
}

// ── FETCH API ────────────────────────────────────────────────
// REGLA: fetchAPI NUNCA muestra PSAlert.
// Solo registra errores técnicos en consola y retorna null.
// El llamador (cargarUsuario) es el único responsable de mostrar mensajes al usuario.

async function fetchAPI(url) {
    try {
        console.log("Llamando API:", url);
        var response = await fetch(url);
        var text     = await response.text();
        console.log("Respuesta RAW:", text);

        var data = null;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            // Solo log técnico — NO llamar PSAlert aquí
            console.error("Error al parsear JSON:", parseError, "Texto recibido:", text);
            return null;
        }

        if (response.status === 200) return data;
        if (response.status === 404) {
            console.warn("Funcionario no encontrado (404)");
            return data;
        }

        console.warn("Código de estado no esperado:", response.status);
        return data;

    } catch (networkError) {
        // Solo log técnico — NO llamar PSAlert aquí
        console.error("Error de red en fetchAPI:", networkError);
        return null;
    }
}

// ── UTILIDADES ───────────────────────────────────────────────

function borrarValores() {
    if (!textCodFuncionarioBusqueda) {
        inicializarVariables();
    }

    if (textCodFuncionarioBusqueda) textCodFuncionarioBusqueda.value = "";
    if (codigo)         codigo.value         = "";
    if (rut)            rut.value            = "";
    if (nombres)        nombres.value        = "";
    if (apellidos)      apellidos.value      = "";
    if (grado)          grado.value          = "";
    if (unidad)         unidad.value         = "";
    if (cargo)          cargo.value          = "";
    if (fechaCargo)     fechaCargo.value     = "";
    if (unidadAgregado) unidadAgregado.value = "";
    if (curso)          curso.value          = "";

    tieneCursoAprobado = true;
    ocultarBannerSinCurso();

    var perfil = document.getElementById("perfil");
    if (perfil) perfil.selectedIndex = 0;

    var unidadPerfil = document.querySelector("input[name='unidadPerfil']");
    if (unidadPerfil) unidadPerfil.value = "";

    var searchUnidad = document.querySelector(".search-box input[type='text']");
    if (searchUnidad) searchUnidad.value = "";

    var proClave = document.querySelector("input[name='proClave']");
    if (proClave) proClave.value = "";

    var proUsuario = document.querySelector("input[name='proUsuario']");
    if (proUsuario) proUsuario.value = "";
}

function asignarValores(valores) {
    console.log("Asignando valores:", valores);

    if (!codigo) {
        inicializarVariables();
    }

    if (!valores || !valores.data) {
        PSAlert.advertencia("No llegaron datos del funcionario.");
        return;
    }

    var data = valores.data;

    codigo.value         = data.codigo || textCodFuncionarioBusqueda.value || "";
    rut.value            = data.rut    || "";
    nombres.value        = data.nombre || "";
    apellidos.value      = ((data.apellidoPaterno || "") + " " + (data.apellidoMaterno || "")).trim();
    grado.value          = data.grado  || "";
    unidad.value         = data.unidad || "";
    cargo.value          = "Por definir";
    fechaCargo.value     = "-";
    unidadAgregado.value = data.departamento || "-";

    var proUsuario = document.querySelector("input[name='proUsuario']");
    if (proUsuario) {
        proUsuario.value = data.codigo || textCodFuncionarioBusqueda.value || "";
    }

    if (valores.curso) {
        if (valores.curso.tieneCurso === true) {
            curso.value            = "APROBADO - " + (valores.curso.fechaAprobacion || "");
            curso.style.color      = "green";
            curso.style.fontWeight = "bold";
        } else {
            curso.value            = valores.curso.mensaje || "Sin Curso";
            curso.style.color      = "red";
            curso.style.fontWeight = "bold";
        }
    } else {
        curso.value = "Sin información de curso";
    }

    console.log("Valores asignados. Tiene curso:", tieneCursoAprobado);
}

function cerrarModalNuevo() {
    var modal = document.getElementById("modalNuevoUsuario");
    if (modal) {
        modal.style.display = "none";
    }

    var modalGenerico = document.getElementById("modalNuevo");
    if (modalGenerico) {
        modalGenerico.classList.add("hidden");
        modalGenerico.classList.remove("flex");

        var contenido = document.getElementById("modalContenidoNuevo");
        if (contenido) {
            contenido.classList.remove("scale-100", "opacity-100");
            contenido.classList.add("scale-95", "opacity-0");
        }
    }

    borrarValores();
}