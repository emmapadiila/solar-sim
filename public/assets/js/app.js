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

    /** true si la persona pidió al sistema reducir el movimiento. */
    sinMovimiento: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

    /**
     * Cuenta desde 0 hasta el número que ya muestra el elemento ("$228.867", "4,0", "420").
     * Solo anima el primer texto con dígitos, así la unidad (<span>) se conserva.
     */
    animarNumero(el, duracion = 900) {
        if (SolarSim.sinMovimiento || el.dataset.animando) return;
        const nodo = [...el.childNodes].find(n => n.nodeType === Node.TEXT_NODE && /\d/.test(n.nodeValue));
        if (!nodo) return;

        const original = nodo.nodeValue;
        const m = original.trim().match(/^(\$?)(\d{1,3}(?:\.\d{3})*|\d+)(?:,(\d+))?$/);
        if (!m) return;

        const [, prefijo, entero, fraccion = ''] = m;
        const valor = parseFloat(entero.replace(/\./g, '') + (fraccion ? '.' + fraccion : ''));
        if (!valor) return;

        el.dataset.animando = '1';
        const inicio = performance.now();
        const paso = ahora => {
            const t = Math.min(1, (ahora - inicio) / duracion);
            const suavizado = 1 - Math.pow(1 - t, 3);
            nodo.nodeValue = t < 1 ? prefijo + SolarSim.numero(valor * suavizado, fraccion.length) : original;
            if (t < 1) {
                requestAnimationFrame(paso);
            } else {
                delete el.dataset.animando;
            }
        };
        requestAnimationFrame(paso);
    },

    animarNumeros(contenedor) {
        contenedor.querySelectorAll('.stat__value, .highlight__value').forEach(el => SolarSim.animarNumero(el));
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

/**
 * Apariciones al entrar en pantalla: tarjetas e indicadores suben y se desvanecen en cascada,
 * y los números cuentan desde 0 la primera vez que se ven.
 */
function activarApariciones() {
    // head.php solo añade la clase si hay IntersectionObserver y no se pidió reducir el movimiento
    if (!document.documentElement.classList.contains('con-apariciones')) return;

    const elementos = document.querySelectorAll('.page .card, .page .stat, .page .disclosure, .page .alert');

    const observer = new IntersectionObserver(entries => {
        const visibles = entries.filter(e => e.isIntersecting);
        visibles.forEach((entry, i) => {
            const el = entry.target;
            el.style.setProperty('--reveal-delay', `${Math.min(i * 70, 420)}ms`);
            el.classList.add('is-visible');
            SolarSim.animarNumeros(el);
            observer.unobserve(el);

            // Al terminar se quitan las clases para que las transiciones de hover no hereden el retraso
            el.addEventListener('transitionend', function limpiar(ev) {
                if (ev.target !== el) return;
                el.classList.add('revelado');
                el.classList.remove('is-visible');
                el.style.removeProperty('--reveal-delay');
                el.removeEventListener('transitionend', limpiar);
            });
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });

    elementos.forEach(el => observer.observe(el));
}

// app.js se carga al final del <body>: el contenido ya existe, así que las apariciones arrancan
// de inmediato en lugar de esperar a DOMContentLoaded.
activarApariciones();

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
