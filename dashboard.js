document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    const contactForm = document.getElementById('contactForm');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                const data = await response.json();
                if (data.success) {
                    localStorage.removeItem('usuario'); // Clear user data from local storage
                    window.location.href = 'index.html'; // Redirect to login page
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cerrar sesión.'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'Error al conectar con el servidor para cerrar sesión.'
                });
            }
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(contactForm);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            try {
                const response = await fetch('send_contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(jsonData)
                });

                const data = await response.json();

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al Enviar Mensaje',
                        text: data.message || 'Lo sentimos, hubo un error al enviar tu mensaje.'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'Error al conectar con el servidor para enviar el mensaje.'
                });
            }
        });
    }
}); 