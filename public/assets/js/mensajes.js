// Bandeja de mensajes de contacto (solo administradores).
async function marcarLeido(idMensaje) {
    try {
        const data = await SolarSim.api('api/mensajes/leido.php', { method: 'POST', body: { id: idMensaje } });
        if (!data.success) {
            SolarSim.error(data.message || 'No se pudo marcar el mensaje.');
            return;
        }

        const tarjeta = document.getElementById(`mensaje-${idMensaje}`);
        tarjeta.classList.add('message--read');
        tarjeta.querySelector('.badge')?.remove();
        tarjeta.querySelector('.btn-group')?.remove();
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('No se pudo conectar con el servidor.', 'Error de conexión');
    }
}
