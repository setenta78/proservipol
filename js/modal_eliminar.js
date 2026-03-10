function confirmarEliminarAjax(codigo) {
    if (!confirm(`¿Está seguro de eliminar al usuario con código ${codigo}?\nEsta acción desactivará al usuario en PROSERVIPOL y lo eliminará de AutentificaTIC.`)) {
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
                alert('Error: ' + (response.message || 'No se pudo desactivar el usuario.'));
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en eliminación:', error);
            alert('Error de conexión al eliminar usuario.');
        }
    });
}