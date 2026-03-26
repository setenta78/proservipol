function confirmarEliminarAjax(codigo) {
    if (!confirm('¿Está seguro de eliminar al usuario con código ' + codigo + '?\nEsta acción desactivará al usuario en PROSERVIPOL y lo eliminará de AutentificaTIC.')) {
        return;
    }
    
    $.ajax({
        url: 'api/eliminarUsuario/index.php',
        method: 'POST',
        data: { codigo: codigo },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                var mensajeError = response.message || 'No se pudo desactivar el usuario.';
                
                // Manejar mensajes específicos de error de AutentificaTIC
                if (mensajeError.indexOf('Autentificatic') !== -1) {
                    alert('Error de conexión con AutentificaTIC:\n\n' + mensajeError);
                } else {
                    alert('Error: ' + mensajeError);
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en eliminación:', error);
            
            var mensajeError = 'Error de conexión al eliminar usuario.';
            
            // Intentar obtener mensaje del servidor
            if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        mensajeError = response.message;
                    }
                } catch (e) {
                    // No es JSON, usar mensaje por defecto
                }
            }
            
            alert(mensajeError);
        }
    });
}