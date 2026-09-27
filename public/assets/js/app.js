// Utilidades comunes a todas las páginas autenticadas (se carga desde templates/layout/footer.php).
const SolarSim = {
    /** Llama a un endpoint JSON de api/ y devuelve la respuesta ya parseada. */
    async api(url, { method = 'GET', body } = {}) {
        const opciones = { method, headers: {} };
        if (body !== undefined) {
            opciones.headers['Content-Type'] = 'application/json';
            opciones.body = JSON.stringify(body);
        }

        const response = await fetch(url, opciones);
        if (response.status === 401 && !url.startsWith('api/auth/')) {
            window.location.href = 'index.php';
        }
        return response.json();
    },

    /** Escapa texto antes de insertarlo como HTML. */
    escape(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    },

    formatearNumero(numero) {
        return Number(numero).toLocaleString('es-CO');
    },

    exito(mensaje, titulo = '¡Éxito!') {
        return Swal.fire({ icon: 'success', title: titulo, text: mensaje, confirmButtonColor: '#4361ee' });
    },

    error(mensaje, titulo = 'Error') {
        return Swal.fire({ icon: 'error', title: titulo, text: mensaje, confirmButtonColor: '#e74c3c' });
    },

    cargando(titulo, texto = 'Por favor, espera') {
        Swal.fire({ title: titulo, text: texto, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    },

    async cerrarSesion() {
        const confirmacion = await Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro de que quieres cerrar sesión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        });
        if (!confirmacion.isConfirmed) return;

        try {
            const data = await SolarSim.api('api/auth/logout.php', { method: 'POST' });
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                SolarSim.error('Error al cerrar sesión.');
            }
        } catch (error) {
            console.error('Error:', error);
            SolarSim.error('Error al conectar con el servidor para cerrar sesión.', 'Error de Conexión');
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            SolarSim.cerrarSesion();
        });
    }
});
