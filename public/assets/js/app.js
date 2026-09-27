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
            // Al recargar, la página detecta que no hay sesión, recuerda dónde estaba y lleva al login
            window.location.reload();
        }
        return response.json();
    },

    /** Escapa texto antes de insertarlo como HTML. */
    escape(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    },

    /** Colores de la marca para gráficos y alertas (iguales a los tokens de style.css). */
    colores: {
        navy: '#16294a',
        navySuave: 'rgba(30, 58, 100, 0.75)',
        sol: '#f59e0b',
        verde: '#059669',
        gris: '#64748b',
        rejilla: '#e2e8f0'
    },

    /** 203000 -> "$203.000" */
    moneda(valor) {
        return '$' + Math.round(Number(valor)).toLocaleString('es-CO');
    },

    /** 395.56, 1 -> "395,6" */
    numero(valor, decimales = 0) {
        return Number(valor).toLocaleString('es-CO', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
    },

    exito(mensaje, titulo = 'Listo') {
        return Swal.fire({ icon: 'success', title: titulo, text: mensaje, confirmButtonColor: SolarSim.colores.navy });
    },

    error(mensaje, titulo = 'Algo salió mal') {
        return Swal.fire({ icon: 'error', title: titulo, text: mensaje, confirmButtonColor: SolarSim.colores.navy });
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
            confirmButtonColor: SolarSim.colores.navy,
            cancelButtonColor: SolarSim.colores.gris,
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
    // Menú hamburguesa (pantallas medianas y móviles)
    const navToggle = document.getElementById('navToggle');
    const menu = document.getElementById('menuPrincipal');
    if (navToggle && menu) {
        navToggle.addEventListener('click', function() {
            const abierto = menu.classList.toggle('abierto');
            navToggle.setAttribute('aria-expanded', abierto);
            navToggle.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
            navToggle.querySelector('i').className = abierto ? 'fas fa-times' : 'fas fa-bars';
        });
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            SolarSim.cerrarSesion();
        });
    }
});
