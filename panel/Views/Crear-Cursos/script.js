document.addEventListener('DOMContentLoaded', function() {
    const rolSelect = document.getElementById('rolSelect');
    const codigoVerificacionGroup = document.getElementById('codigo-verificacion-group');
    const codigoVerificacionInput = document.getElementById('codigoVerificacion');

    rolSelect.addEventListener('change', function() {
        // Los roles que necesitan el código de verificación
        const rolesConCodigo = ['profesor', 'Auxiliar', 'Administrador'];
        
        // Comprueba si el rol seleccionado está en la lista de roles que requieren código
        if (rolesConCodigo.includes(this.value)) {
            codigoVerificacionGroup.style.display = 'block';
            codigoVerificacionInput.setAttribute('required', 'required'); // Hace que el campo sea obligatorio
        } else {
            codigoVerificacionGroup.style.display = 'none';
            codigoVerificacionInput.removeAttribute('required'); // Quita la obligatoriedad
        }
    });
});