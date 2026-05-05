function confirmarEliminarAjax(codigo) {
    PSAlert.confirmar(
        'Esta acción desactivará al usuario con código <strong>' + codigo + '</strong> en PROSERVIPOL y lo eliminará de AutentificaTIC.',
        function() {
            // Confirmó — ejecutar eliminación
            $.ajax({
                url: 'http://aplicativos.des-proservipol.carabineros.cl/api/eliminarUsuario/index.php',
                method: 'POST',
                data: { codigo: codigo },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        PSAlert.exito(response.message, 'Usuario eliminado');
                        setTimeout(function() { location.reload(); }, 1400);
                    } else {
                        var mensajeError = response.message || 'No se pudo desactivar el usuario.';
                        if (mensajeError.indexOf('AutentificaTIC') !== -1 || mensajeError.indexOf('Autentificatic') !== -1) {
                            PSAlert.error('Error de conexión con AutentificaTIC:<br><br>' + mensajeError);
                        } else {
                            PSAlert.error(mensajeError);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en eliminación:', error);
                    var mensajeError = 'Error de conexión al eliminar usuario.';
                    if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                mensajeError = response.message;
                            }
                        } catch(e) {}
                    }
                    PSAlert.error(mensajeError);
                }
            });
        },
        '¿Eliminar usuario ' + codigo + '?',
        null,
        'warning'
    );
}
