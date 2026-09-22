document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', cerrarSesion);
    }
    
    // Agregar animaciones a las tarjetas
    const cards = document.querySelectorAll('.simulacion-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

function verDetalle(idSimulacion) {
    // Mostrar modal con detalles de la simulación
    Swal.fire({
        title: 'Cargando detalles...',
        text: 'Obteniendo información de la simulación',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Hacer petición AJAX para obtener detalles
    fetch(`obtener_detalle_simulacion.php?id=${idSimulacion}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarModalDetalle(data.simulacion);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al obtener detalles de la simulación',
                    confirmButtonColor: '#e74c3c'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonColor: '#e74c3c'
            });
        });
}

function mostrarModalDetalle(simulacion) {
    const fecha = new Date(simulacion.fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    Swal.fire({
        title: `Simulación - ${simulacion.ubicacion}`,
        html: `
            <div class="detalle-simulacion">
                <div class="detalle-section">
                    <h4><i class="fas fa-calendar-alt"></i> Fecha de Simulación</h4>
                    <p>${fecha}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                    <p>${simulacion.ubicacion}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-layer-group"></i> Estrato</h4>
                    <p>${simulacion.estrato}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-bolt"></i> Consumo Mensual</h4>
                    <p>${simulacion.consumo_mensual} kWh/mes</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-home"></i> Área Disponible</h4>
                    <p>${simulacion.area_disponible} m²</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-plug"></i> Tipo de Energía</h4>
                    <p>${simulacion.tipo_energia}</p>
                </div>
                
                <hr>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-solar-panel"></i> Energía Generada</h4>
                    <p class="resultado-destacado">${simulacion.energia_generada} kWh/mes</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-piggy-bank"></i> Ahorro Mensual</h4>
                    <p class="resultado-destacado">$${parseInt(simulacion.ahorro_mensual).toLocaleString()}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-calendar-alt"></i> Ahorro Anual</h4>
                    <p class="resultado-destacado">$${parseInt(simulacion.ahorro_anual).toLocaleString()}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-clock"></i> Retorno de Inversión</h4>
                    <p class="resultado-destacado">${simulacion.retorno_inversion} años</p>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#4361ee',
        showCloseButton: true,
        customClass: {
            popup: 'detalle-modal'
        }
    });
}

function exportarSimulacion(idSimulacion) {
    Swal.fire({
        title: 'Exportando simulación...',
        text: 'Generando archivo PDF',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Crear un enlace temporal para descargar
    const link = document.createElement('a');
    link.href = `exportar_simulacion.php?id=${idSimulacion}`;
    link.download = `simulacion_${idSimulacion}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Exportado!',
            text: 'La simulación se ha exportado correctamente',
            confirmButtonColor: '#4361ee',
            timer: 2000,
            timerProgressBar: true
        });
    }, 1000);
}

function cerrarSesion(e) {
    e.preventDefault();
    try {
        fetch('logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
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
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'Error al conectar con el servidor para cerrar sesión.'
            });
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'Error al conectar con el servidor para cerrar sesión.'
        });
    }
}

// Función para filtrar simulaciones (futura implementación)
function filtrarSimulaciones(filtro) {
    const cards = document.querySelectorAll('.simulacion-card');
    
    cards.forEach(card => {
        const ubicacion = card.querySelector('.simulacion-ubicacion').textContent.toLowerCase();
        const fecha = card.querySelector('.simulacion-fecha').textContent;
        
        let mostrar = true;
        
        if (filtro.ubicacion && !ubicacion.includes(filtro.ubicacion.toLowerCase())) {
            mostrar = false;
        }
        
        if (filtro.fechaDesde) {
            const fechaCard = new Date(fecha);
            const fechaDesde = new Date(filtro.fechaDesde);
            if (fechaCard < fechaDesde) {
                mostrar = false;
            }
        }
        
        if (filtro.fechaHasta) {
            const fechaCard = new Date(fecha);
            const fechaHasta = new Date(filtro.fechaHasta);
            if (fechaCard > fechaHasta) {
                mostrar = false;
            }
        }
        
        card.style.display = mostrar ? 'block' : 'none';
    });
}

// Función para ordenar simulaciones
function ordenarSimulaciones(criterio) {
    const container = document.querySelector('.simulaciones-grid');
    const cards = Array.from(container.querySelectorAll('.simulacion-card'));
    
    cards.sort((a, b) => {
        let valorA, valorB;
        
        switch (criterio) {
            case 'fecha':
                const fechaA = new Date(a.querySelector('.simulacion-fecha').textContent);
                const fechaB = new Date(b.querySelector('.simulacion-fecha').textContent);
                return fechaB - fechaA; // Más reciente primero
                
            case 'ahorro':
                valorA = parseInt(a.querySelector('.result-value').textContent.replace(/[^0-9]/g, ''));
                valorB = parseInt(b.querySelector('.result-value').textContent.replace(/[^0-9]/g, ''));
                return valorB - valorA; // Mayor ahorro primero
                
            case 'ubicacion':
                valorA = a.querySelector('.simulacion-ubicacion').textContent;
                valorB = b.querySelector('.simulacion-ubicacion').textContent;
                return valorA.localeCompare(valorB);
                
            default:
                return 0;
        }
    });
    
    // Reinsertar las tarjetas ordenadas
    cards.forEach(card => {
        container.appendChild(card);
    });
}

// Agregar estilos CSS para el modal de detalles
const style = document.createElement('style');
style.textContent = `
    .detalle-modal .detalle-simulacion {
        text-align: left;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .detalle-modal .detalle-section {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #4361ee;
    }
    
    .detalle-modal .detalle-section h4 {
        margin: 0 0 5px 0;
        color: #4361ee;
        font-size: 14px;
        font-weight: 600;
    }
    
    .detalle-modal .detalle-section p {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
    }
    
    .detalle-modal .resultado-destacado {
        color: #2ecc71 !important;
        font-weight: 700 !important;
        font-size: 18px !important;
    }
    
    .detalle-modal hr {
        margin: 20px 0;
        border: none;
        border-top: 2px solid #e9ecef;
    }
`;
document.head.appendChild(style); 