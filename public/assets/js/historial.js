// Historial de simulaciones del usuario.

async function verDetalle(idSimulacion) {
    SolarSim.cargando('Cargando detalle...', 'Obteniendo la información de la simulación');

    try {
        const data = await SolarSim.api(`api/simulaciones/detalle.php?id=${encodeURIComponent(idSimulacion)}`);
        if (data.success) {
            mostrarModalDetalle(data.simulacion);
        } else {
            SolarSim.error(data.message || 'No se pudo obtener el detalle de la simulación.');
        }
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('No se pudo conectar con el servidor.', 'Error de conexión');
    }
}

function mostrarModalDetalle(s) {
    const esc = SolarSim.escape;
    const fecha = new Date(s.fecha.replace(' ', 'T')).toLocaleString('es-CO', {
        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    const tipo = s.tipo_energia.charAt(0).toUpperCase() + s.tipo_energia.slice(1);

    Swal.fire({
        title: `Simulación en ${esc(s.ciudad)}`,
        html: `
            <p class="text-muted" style="margin-bottom: 1rem">${esc(fecha)}</p>
            <dl class="data-list">
                <div><dt>Estrato</dt><dd>${esc(s.estrato)}</dd></div>
                <div><dt>Tipo de energía</dt><dd>${esc(tipo)}</dd></div>
                <div><dt>Consumo mensual</dt><dd>${SolarSim.numero(s.consumo_mensual)} kWh</dd></div>
                <div><dt>Área disponible</dt><dd>${SolarSim.numero(s.area_disponible)} m²</dd></div>
            </dl>
            <dl class="data-list">
                <div><dt>Ahorro mensual</dt><dd class="is-positive">${SolarSim.moneda(s.ahorro_mensual)}</dd></div>
                <div><dt>Ahorro anual</dt><dd class="is-positive">${SolarSim.moneda(s.ahorro_anual)}</dd></div>
                <div><dt>Energía generada</dt><dd>${SolarSim.numero(s.energia_generada, 1)} kWh/mes</dd></div>
                <div><dt>Retorno de inversión</dt><dd>${SolarSim.numero(s.retorno_inversion, 1)} años</dd></div>
            </dl>
        `,
        width: 560,
        showCloseButton: true,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: SolarSim.colores.navy,
        customClass: { popup: 'detalle-modal' }
    });
}

function exportarSimulacion(idSimulacion) {
    // El servidor responde con Content-Disposition: attachment, así que el navegador descarga sin salir de la página.
    window.location.href = `api/simulaciones/exportar.php?id=${encodeURIComponent(idSimulacion)}`;

    Swal.fire({
        icon: 'success',
        title: 'Descargando PDF',
        text: 'El reporte de la simulación se está descargando.',
        confirmButtonColor: SolarSim.colores.navy,
        timer: 3000,
        timerProgressBar: true
    });
}

async function eliminarSimulacion(idSimulacion) {
    const confirmacion = await Swal.fire({
        icon: 'warning',
        title: '¿Eliminar esta simulación?',
        text: 'Se borrará de tu historial y de las estadísticas. Esta acción no se puede deshacer.',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: SolarSim.colores.gris,
        focusCancel: true
    });
    if (!confirmacion.isConfirmed) return;

    try {
        const data = await SolarSim.api('api/simulaciones/eliminar.php', { method: 'POST', body: { id: idSimulacion } });
        if (data.success) {
            window.location.reload(); // recarga para actualizar los indicadores del historial
        } else {
            SolarSim.error(data.message || 'No se pudo eliminar la simulación.');
        }
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('No se pudo conectar con el servidor.', 'Error de conexión');
    }
}
