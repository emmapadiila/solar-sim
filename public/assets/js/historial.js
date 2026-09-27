// Historial de simulaciones del usuario.
document.addEventListener('DOMContentLoaded', function() {
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

async function verDetalle(idSimulacion) {
    SolarSim.cargando('Cargando detalles...', 'Obteniendo información de la simulación');

    try {
        const data = await SolarSim.api(`api/simulaciones/detalle.php?id=${encodeURIComponent(idSimulacion)}`);
        if (data.success) {
            mostrarModalDetalle(data.simulacion);
        } else {
            SolarSim.error(data.message || 'Error al obtener detalles de la simulación');
        }
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('No se pudo conectar con el servidor', 'Error de conexión');
    }
}

function mostrarModalDetalle(simulacion) {
    const fecha = new Date(simulacion.fecha.replace(' ', 'T')).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const esc = SolarSim.escape;

    Swal.fire({
        title: `Simulación - ${esc(simulacion.ciudad)}`,
        html: `
            <div class="detalle-simulacion">
                <div class="detalle-section">
                    <h4><i class="fas fa-calendar-alt"></i> Fecha de Simulación</h4>
                    <p>${esc(fecha)}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                    <p>${esc(simulacion.ciudad)}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-layer-group"></i> Estrato</h4>
                    <p>${esc(simulacion.estrato)}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-bolt"></i> Consumo Mensual</h4>
                    <p>${esc(simulacion.consumo_mensual)} kWh/mes</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-home"></i> Área Disponible</h4>
                    <p>${esc(simulacion.area_disponible)} m²</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-plug"></i> Tipo de Energía</h4>
                    <p>${esc(simulacion.tipo_energia)}</p>
                </div>
                
                <hr>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-solar-panel"></i> Energía Generada</h4>
                    <p class="resultado-destacado">${esc(simulacion.energia_generada)} kWh/mes</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-piggy-bank"></i> Ahorro Mensual</h4>
                    <p class="resultado-destacado">$${SolarSim.formatearNumero(Math.round(simulacion.ahorro_mensual))}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-calendar-alt"></i> Ahorro Anual</h4>
                    <p class="resultado-destacado">$${SolarSim.formatearNumero(Math.round(simulacion.ahorro_anual))}</p>
                </div>
                
                <div class="detalle-section">
                    <h4><i class="fas fa-clock"></i> Retorno de Inversión</h4>
                    <p class="resultado-destacado">${esc(simulacion.retorno_inversion)} años</p>
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
    // El servidor responde con Content-Disposition: attachment, así que el navegador descarga sin salir de la página.
    window.location.href = `api/simulaciones/exportar.php?id=${encodeURIComponent(idSimulacion)}`;

    Swal.fire({
        icon: 'success',
        title: 'Descargando reporte',
        text: 'Puedes abrir el archivo en el navegador e imprimirlo como PDF.',
        confirmButtonColor: '#4361ee',
        timer: 3000,
        timerProgressBar: true
    });
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
