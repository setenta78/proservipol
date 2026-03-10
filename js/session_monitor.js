/**
 * Monitor de Sesión - PROSERVIPOL
 * Controla la inactividad del usuario y cierra sesión automáticamente
 */

(function() {
    'use strict';
    
    // Configuración
    const CONFIG = {
        INACTIVITY_TIME: 15 * 60 * 1000, // 15 minutos en milisegundos
        CHECK_INTERVAL: 60 * 1000,        // Verificar cada 1 minuto
        WARNING_TIME: 2 * 60 * 1000,      // Advertir 2 minutos antes
        API_CHECK_SESSION: 'api/check_session.php'
    };
    
    let inactivityTimer = null;
    let warningTimer = null;
    let lastActivity = Date.now();
    let warningShown = false;
    
    /**
     * Reinicia el temporizador de inactividad
     */
    function resetInactivityTimer() {
        lastActivity = Date.now();
        warningShown = false;
        
        // Limpiar temporizadores existentes
        if (inactivityTimer) clearTimeout(inactivityTimer);
        if (warningTimer) clearTimeout(warningTimer);
        
        // Configurar advertencia
        warningTimer = setTimeout(showWarning, CONFIG.INACTIVITY_TIME - CONFIG.WARNING_TIME);
        
        // Configurar cierre de sesión
        inactivityTimer = setTimeout(logoutUser, CONFIG.INACTIVITY_TIME);
    }
    
    /**
     * Muestra advertencia de cierre de sesión inminente
     */
    function showWarning() {
        if (warningShown) return;
        warningShown = true;
        
        const remainingTime = Math.floor(CONFIG.WARNING_TIME / 1000 / 60);
        
        if (confirm(`Su sesión expirará en ${remainingTime} minutos por inactividad.\n\n¿Desea continuar trabajando?`)) {
            resetInactivityTimer();
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    function logoutUser() {
        alert('Su sesión ha expirado por inactividad. Será redirigido al login.');
        window.location.href = 'logout.php?reason=inactivity';
    }
    
    /**
     * Verifica el estado de la sesión en el servidor
     */
    function checkSessionStatus() {
        fetch(CONFIG.API_CHECK_SESSION, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.active) {
                alert('Su sesión ha expirado. Será redirigido al login.');
                window.location.href = 'logout.php?reason=expired';
            }
        })
        .catch(error => {
            console.error('Error al verificar sesión:', error);
        });
    }
    
    /**
     * Registra actividad del usuario
     */
    function registerActivity() {
        resetInactivityTimer();
    }
    
    /**
     * Inicializa el monitor de sesión
     */
    function init() {
        // Eventos que indican actividad del usuario
        const activityEvents = [
            'mousedown',
            'mousemove',
            'keypress',
            'scroll',
            'touchstart',
            'click'
        ];
        
        // Registrar eventos de actividad
        activityEvents.forEach(event => {
            document.addEventListener(event, registerActivity, true);
        });
        
        // Iniciar temporizador
        resetInactivityTimer();
        
        // Verificar sesión periódicamente
        setInterval(checkSessionStatus, CONFIG.CHECK_INTERVAL);
        
        console.log('Monitor de sesión iniciado - Tiempo de inactividad:', CONFIG.INACTIVITY_TIME / 1000 / 60, 'minutos');
    }
    
    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();