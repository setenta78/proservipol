/**
 * SISTEMA DE APLICACIONES DE PROSERVIPOL
 * Lógica de Autenticación con Autentificatic API - Denis Quezada Lemus
 * @version 4.2
 * @date 2025
 * Mejoras: 
 * - Validación completa de RUT chileno con dígito verificador K
 * - SEGURIDAD: Ocultamiento de detalles técnicos en mensajes de error (Códigos de perfil)
 */

// ========== CONFIGURACIÓN ========== 
var CONFIG = {
    API_URL: 'http://autentificaticapi.carabineros.cl',
    ENDPOINTS: {
        LOGIN: '/api/auth/login',
        USER: '/api/auth/user'
    },
    SAVE_TOKEN_URL: 'save_token.php',
    REDIRECT_URL: 'index.php'
};

// ========== MENSAJES DE ERROR ========== 
var ERROR_MESSAGES = {
    EMPTY_FIELDS: 'Por favor, ingrese RUT y contraseña.',
    INVALID_RUT: 'El RUT ingresado no es válido. Verifique el dígito verificador.',
    INVALID_CREDENTIALS: 'Las credenciales no son válidas.',
    INACTIVE_USER: 'Su cuenta no está activa.',
    EXPIRED_PASSWORD: 'Su contraseña ha caducado, debe actualizarla.',
    NO_ACCESS: 'No tiene acceso a esta plataforma.',
    UNAUTHORIZED: 'Usuario inactivo o sin permisos para este sistema.',
    NETWORK_ERROR: 'Error de conexión. Verifique su red e intente nuevamente.',
    UNKNOWN_ERROR: 'Error desconocido. Intente nuevamente más tarde.',
    SESSION_ERROR: 'Error al guardar la sesión. Contacte al administrador.',
    // Nuevo mensaje genérico para errores de permisos
    PERMISSION_DENIED_GENERIC: 'Acceso denegado. Su usuario no está autorizado para utilizar este sistema.'
};

// ========== UTILIDADES DE RUT ========== 

/**
 * Limpia el RUT eliminando puntos, guiones y espacios
 * @param {string} rut - RUT a limpiar
 * @returns {string} RUT limpio en mayúsculas
 */
function limpiarRUT(rut) {
    return rut.replace(/\./g, '').replace(/-/g, '').replace(/\s/g, '').trim().toUpperCase();
}

/**
 * Calcula el dígito verificador de un RUT chileno
 * @param {string} rutSinDV - RUT sin dígito verificador (solo números)
 * @returns {string} Dígito verificador calculado (0-9 o K)
 */
function calcularDV(rutSinDV) {
    var suma = 0;
    var multiplicador = 2;
    
    // Recorrer de derecha a izquierda
    for (var i = rutSinDV.length - 1; i >= 0; i--) {
        suma += parseInt(rutSinDV.charAt(i)) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }
    
    var dvEsperado = 11 - (suma % 11);
    
    // Casos especiales
    if (dvEsperado === 11) return '0';
    if (dvEsperado === 10) return 'K';
    return dvEsperado.toString();
}

/**
 * Valida RUT chileno con dígito verificador completo (incluye K)
 * Ejemplos válidos: 12345678-9, 12345678-K, 12345678K, 123456789
 * @param {string} rut - RUT a validar
 * @returns {boolean} true si el RUT es válido
 */
function validarRUT(rut) {
    if (!rut || typeof rut !== 'string') {
        return false;
    }
    
    // Limpiar el RUT
    var rutLimpio = limpiarRUT(rut);
    
    // Validar formato: 7-8 dígitos + 1 dígito verificador (0-9 o K)
    var regex = /^[0-9]{7,8}[0-9K]$/;
    if (!regex.test(rutLimpio)) {
        return false;
    }
    
    // Separar cuerpo y dígito verificador
    var cuerpo = rutLimpio.slice(0, -1);
    var dvIngresado = rutLimpio.slice(-1);
    
    // Calcular dígito verificador esperado
    var dvEsperado = calcularDV(cuerpo);
    
    // Comparar
    return dvIngresado === dvEsperado;
}

/**
 * Formatea un RUT al formato chileno estándar: 12.345.678-9
 * @param {string} rut - RUT a formatear
 * @returns {string} RUT formateado
 */
function formatearRUT(rut) {
    var rutLimpio = limpiarRUT(rut);
    
    if (rutLimpio.length < 2) {
        return rutLimpio;
    }
    
    var cuerpo = rutLimpio.slice(0, -1);
    var dv = rutLimpio.slice(-1);
    
    // Agregar puntos cada 3 dígitos de derecha a izquierda
    cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    return cuerpo + '-' + dv;
}

/**
 * Permite solo números y la letra K en el input de RUT
 * @param {Event} event - Evento de teclado
 * @returns {boolean} true si el carácter es válido
 */
function validarSoloNumerosYK(event) {
    var charCode = (event.which) ? event.which : event.keyCode;
    var char = String.fromCharCode(charCode).toUpperCase();
    
    // Permitir números (0-9)
    if (charCode >= 48 && charCode <= 57) {
        return true;
    }
    
    // Permitir K o k (solo si no existe ya una K en el input)
    if (char === 'K') {
        var input = event.target;
        var valor = input.value.toUpperCase();
        
        // No permitir más de una K
        if (valor.indexOf('K') === -1) {
            return true;
        }
    }
    
    // Bloquear todo lo demás
    event.preventDefault();
    return false;
}

// ========== UTILIDADES DE UI ========== 

function mostrarCarga() {
    var overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loading-overlay';
    
    overlay.innerHTML = 
        '<div class="loading-spinner">' +
        '  <div class="spinner"></div>' +
        '  <p style="color: #374151; font-weight: 600; margin-bottom: 0.5rem;">Autenticando...</p>' +
        '  <p style="color: #6b7280; font-size: 0.875rem;">Por favor espere</p>' +
        '</div>';
    
    document.body.appendChild(overlay);
    return overlay;
}

function ocultarCarga() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) {
        document.body.removeChild(overlay);
    }
}

function toggleFormulario(disabled) {
    var inputs = document.querySelectorAll('#form_login input');
    var btnLogin = document.getElementById('btn-login');
    
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].disabled = disabled;
    }
    
    if (btnLogin) {
        btnLogin.disabled = disabled;
    }
}

function mostrarError(mensaje) {
    alert('❌ ' + mensaje);
}

/**
 * Obtiene el mensaje de error adecuado, filtrando información sensible
 * @param {Object} errorData - Objeto de error recibido
 * @returns {string} Mensaje de error seguro para mostrar al usuario
 */
function obtenerMensajeError(errorData) {
    if (!errorData) return ERROR_MESSAGES.UNKNOWN_ERROR;
    
    var mensajeOriginal = '';

    // 1. Extraer el mensaje original de diversas fuentes posibles
    if (errorData.errors) {
        var errors = errorData.errors;
        
        if (errors.rut) {
            mensajeOriginal = Array.isArray(errors.rut) ? errors.rut[0] : errors.rut;
        } else if (errors.password) {
            mensajeOriginal = Array.isArray(errors.password) ? errors.password[0] : errors.password;
        } else {
            var primerError = errors[Object.keys(errors)[0]];
            mensajeOriginal = Array.isArray(primerError) ? primerError[0] : primerError;
        }
    } else if (errorData.message) {
        mensajeOriginal = errorData.message;
    }
    
    // Si no hay mensaje original, usar desconocido
    if (!mensajeOriginal) {
        return ERROR_MESSAGES.UNKNOWN_ERROR;
    }

    // 2. FILTRO DE SEGURIDAD: Detectar si el mensaje revela información interna
    // Buscamos palabras clave como "perfil", "90", "310", "Mesa de Ayuda", "Administrador"
    var mensajeLower = mensajeOriginal.toLowerCase();
    
    if (mensajeLower.indexOf('perfil') !== -1 || 
        mensajeLower.indexOf('90') !== -1 || 
        mensajeLower.indexOf('310') !== -1 || 
        mensajeLower.indexOf('mesa de ayuda') !== -1 || 
        mensajeLower.indexOf('administrador') !== -1 ||
        mensajeLower.indexOf('permisos') !== -1) {
        
        // Si detecta información sensible, devolver mensaje genérico
        console.warn('⚠️ Mensaje de error original ocultado por seguridad:', mensajeOriginal);
        return ERROR_MESSAGES.PERMISSION_DENIED_GENERIC;
    }

    // 3. Si es seguro, devolver el mensaje original (o uno mapeado si es muy técnico)
    return mensajeOriginal;
}

// ========== PROCESO DE AUTENTICACIÓN ========== 

function autenticarUsuario(rut, password) {
    return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', CONFIG.API_URL + CONFIG.ENDPOINTS.LOGIN, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            resolve(response.success);
                        } else {
                            reject({ message: ERROR_MESSAGES.INVALID_CREDENTIALS });
                        }
                    } catch (e) {
                        reject({ message: ERROR_MESSAGES.UNKNOWN_ERROR });
                    }
                } else {
                    try {
                        var errorData = JSON.parse(xhr.responseText);
                        reject(errorData);
                    } catch (e) {
                        reject({ message: 'HTTP ' + xhr.status });
                    }
                }
            }
        };
        
        xhr.onerror = function() {
            reject({ message: ERROR_MESSAGES.NETWORK_ERROR });
        };
        
        var body = 'rut=' + encodeURIComponent(rut) + 
                   '&password=' + encodeURIComponent(password);
        xhr.send(body);
    });
}

function obtenerDatosUsuario(accessToken) {
    return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', CONFIG.API_URL + CONFIG.ENDPOINTS.USER, true);
        xhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            resolve(response.success);
                        } else {
                            reject({ message: 'Error al obtener datos del usuario' });
                        }
                    } catch (e) {
                        reject({ message: ERROR_MESSAGES.UNKNOWN_ERROR });
                    }
                } else {
                    reject({ message: 'No autorizado' });
                }
            }
        };
        
        xhr.onerror = function() {
            reject({ message: ERROR_MESSAGES.NETWORK_ERROR });
        };
        
        xhr.send();
    });
}

function guardarSesion(loginData, userData) {
    return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', CONFIG.SAVE_TOKEN_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        reject({ message: ERROR_MESSAGES.SESSION_ERROR });
                    }
                } else {
                    reject({ message: ERROR_MESSAGES.SESSION_ERROR });
                }
            }
        };
        
        xhr.onerror = function() {
            reject({ message: ERROR_MESSAGES.NETWORK_ERROR });
        };
        
        var body = 'access_token=' + encodeURIComponent(loginData.access_token) +
                   '&expires_at=' + encodeURIComponent(loginData.expires_at) +
                   '&token_type=' + encodeURIComponent(loginData.token_type) +
                   '&codigo_funcionario=' + encodeURIComponent(userData.user.codigo_funcionario);
        
        xhr.send(body);
    });
}

function iniciarSesion() {
    var rutInput = document.getElementById('rut_funcionario');
    var passwordInput = document.getElementById('password');
    
    var rut = rutInput.value.trim();
    var password = passwordInput.value.trim();
    
    // Validar campos vacíos
    if (!rut || !password) {
        mostrarError(ERROR_MESSAGES.EMPTY_FIELDS);
        return;
    }
    
    // Validar formato y dígito verificador del RUT
    if (!validarRUT(rut)) {
        mostrarError(ERROR_MESSAGES.INVALID_RUT);
        rutInput.focus();
        return;
    }
    
    // Limpiar RUT para enviar sin puntos ni guiones
    var rutLimpio = limpiarRUT(rut);
    
    toggleFormulario(true);
    mostrarCarga();
    
    autenticarUsuario(rutLimpio, password)
        .then(function(loginData) {
            return obtenerDatosUsuario(loginData.access_token)
                .then(function(userData) {
                    return { loginData: loginData, userData: userData };
                });
        })
        .then(function(data) {
            return guardarSesion(data.loginData, data.userData);
        })
        .then(function(saveResponse) {
            if (saveResponse.success) {
                window.location.href = CONFIG.REDIRECT_URL;
            } else {
                throw new Error(saveResponse.message || ERROR_MESSAGES.SESSION_ERROR);
            }
        })
        .catch(function(error) {
            ocultarCarga();
            toggleFormulario(false);
            
            // Aquí se aplica el filtro de seguridad
            var mensaje = obtenerMensajeError(error);
            mostrarError(mensaje);
            
            // El error completo se mantiene en consola solo para debug del desarrollador
            console.error('Error de autenticación (detalle interno):', error);
        });
}

// ========== INICIALIZACIÓN ========== 

document.addEventListener('DOMContentLoaded', function() {
    var btnLogin = document.getElementById('btn-login');
    var form = document.getElementById('form_login');
    var rutInput = document.getElementById('rut_funcionario');
    
    if (!btnLogin) {
        console.error('No se encontró el botón de login');
        return;
    }
    
    // Validar solo números y K en RUT
    if (rutInput) {
        rutInput.addEventListener('keypress', validarSoloNumerosYK);
        
        // Opcional: Formatear RUT automáticamente al escribir
        rutInput.addEventListener('blur', function() {
            var valor = rutInput.value.trim();
            if (valor && validarRUT(valor)) {
                rutInput.value = formatearRUT(valor);
            }
        });
    }
    
    // Click en botón de login
    btnLogin.addEventListener('click', function(e) {
        e.preventDefault();
        iniciarSesion();
    });
    
    // Enter en el formulario
    form.addEventListener('keypress', function(e) {
        if (e.keyCode === 13 || e.which === 13) {
            e.preventDefault();
            iniciarSesion();
        }
    });
    
    console.log('✅ Sistema de login inicializado correctamente (v4.2 - Seguridad Mejorada)');
    console.log('✅ Validación completa de RUT con dígito verificador K habilitada');
    console.log('✅ Filtrado de mensajes de error sensibles activado');
});