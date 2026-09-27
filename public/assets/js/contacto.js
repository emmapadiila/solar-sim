// Formulario de la página de contacto.
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const datos = Object.fromEntries(new FormData(contactForm));

        try {
            const data = await SolarSim.api('api/contacto.php', { method: 'POST', body: datos });

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Mensaje Enviado!',
                    text: data.message || 'Tu mensaje ha sido enviado correctamente.',
                    showConfirmButton: false,
                    timer: 3000
                });
                contactForm.reset();
            } else {
                SolarSim.error(data.message || 'Lo sentimos, hubo un error al enviar tu mensaje.', 'Error al Enviar Mensaje');
            }
        } catch (error) {
            console.error('Error:', error);
            SolarSim.error('Error al conectar con el servidor para enviar el mensaje.', 'Error de Conexión');
        }
    });
});
