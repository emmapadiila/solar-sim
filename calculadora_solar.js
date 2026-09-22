// Variables globales
let graficoAhorro = null;
let datosSimulacion = null;

// Factores de irradiación solar por ciudad (kWh/m²/día)
const irradiacionSolar = {
    'bogota': 4.2,
    'medellin': 4.8,
    'cali': 5.1,
    'barranquilla': 5.5,
    'cartagena': 5.7,
    'bucaramanga': 4.9,
    'pereira': 4.6,
    'manizales': 4.4,
    'ibague': 4.7,
    'villavicencio': 4.5
};

// Precios por kWh según estrato (pesos colombianos)
const preciosKWh = {
    1: 450,
    2: 520,
    3: 580,
    4: 650,
    5: 720,
    6: 800
};

// Eficiencia del panel solar (mejorada a 20-22%)
const eficienciaPanel = 0.21;

// Factor de pérdidas del sistema (mejorado a 0.90-0.92)
const factorPerdidas = 0.91;

// Costo promedio por kW instalado (pesos colombianos)
const costoPorKW = 3500000;

// Factor de capacidad (horas de sol efectivas por día)
const factorCapacidad = 4.5;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('calculadoraForm');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnNuevaSimulacion = document.getElementById('btnNuevaSimulacion');
    const logoutBtn = document.getElementById('logoutBtn');

    // Event listeners
    form.addEventListener('submit', calcularAhorro);
    btnGuardar.addEventListener('click', guardarSimulacion);
    btnNuevaSimulacion.addEventListener('click', nuevaSimulacion);
    logoutBtn.addEventListener('click', cerrarSesion);

    // Validaciones en tiempo real
    document.getElementById('consumo_mensual').addEventListener('input', validarConsumo);
    document.getElementById('area_disponible').addEventListener('input', validarArea);
});

function validarConsumo(e) {
    const valor = parseInt(e.target.value);
    const input = e.target;
    
    if (valor < 50) {
        input.setCustomValidity('El consumo mínimo es 50 kWh/mes');
        input.classList.add('error');
    } else if (valor > 2000) {
        input.setCustomValidity('El consumo máximo es 2000 kWh/mes');
        input.classList.add('error');
    } else {
        input.setCustomValidity('');
        input.classList.remove('error');
    }
}

function validarArea(e) {
    const valor = parseInt(e.target.value);
    const input = e.target;
    
    if (valor < 10) {
        input.setCustomValidity('El área mínima es 10 m²');
        input.classList.add('error');
    } else if (valor > 500) {
        input.setCustomValidity('El área máxima es 500 m²');
        input.classList.add('error');
    } else {
        input.setCustomValidity('');
        input.classList.remove('error');
    }
}

function calcularAhorro(e) {
    e.preventDefault();
    
    // Obtener datos del formulario
    const formData = new FormData(e.target);
    const datos = {
        ubicacion: formData.get('ubicacion'),
        estrato: parseInt(formData.get('estrato')),
        consumo_mensual: parseFloat(formData.get('consumo_mensual')),
        area_disponible: parseFloat(formData.get('area_disponible')),
        tipo_energia: formData.get('tipo_energia')
    };

    // Validar datos
    if (!validarDatos(datos)) {
        return;
    }

    // Realizar cálculos
    const resultados = realizarCalculos(datos);
    
    // Guardar datos para uso posterior
    datosSimulacion = {
        ...datos,
        ...resultados
    };

    // Mostrar resultados
    mostrarResultados(resultados);
    
    // Crear gráfico
    crearGrafico(resultados);
    
    // Mostrar tarjeta de resultados
    document.getElementById('resultadosCard').style.display = 'block';
    document.getElementById('infoCard').style.display = 'none';
    
    // Scroll suave a resultados
    document.getElementById('resultadosCard').scrollIntoView({ 
        behavior: 'smooth' 
    });
}

function validarDatos(datos) {
    if (!datos.ubicacion) {
        mostrarError('Por favor selecciona una ubicación');
        return false;
    }
    
    if (!datos.estrato || datos.estrato < 1 || datos.estrato > 6) {
        mostrarError('Por favor selecciona un estrato válido');
        return false;
    }
    
    if (!datos.consumo_mensual || datos.consumo_mensual < 50 || datos.consumo_mensual > 2000) {
        mostrarError('El consumo mensual debe estar entre 50 y 2000 kWh');
        return false;
    }
    
    if (!datos.area_disponible || datos.area_disponible < 10 || datos.area_disponible > 500) {
        mostrarError('El área disponible debe estar entre 10 y 500 m²');
        return false;
    }
    
    if (!datos.tipo_energia) {
        mostrarError('Por favor selecciona el tipo de energía');
        return false;
    }
    
    return true;
}

function realizarCalculos(datos) {
    // Obtener irradiación solar de la ubicación
    const irradiacion = irradiacionSolar[datos.ubicacion];
    
    // Constantes para los cálculos de paneles (valores promedio y estimados)
    const VATIOS_PANEL_PROMEDIO = 400; // Vatios por panel solar
    const AREA_PANEL_PROMEDIO = 1.7;   // Área por panel solar en m²
    const DIAS_EN_MES = 30.44; // Promedio de días en un mes

    // 1. Energía generada por panel al día (kWh)
    // (Vatios del panel * Horas sol pico) / 1000 (para convertir a kWh)
    const energia_por_panel_dia = (VATIOS_PANEL_PROMEDIO * irradiacion) / 1000;

    // 2. Energía generada por panel al mes (kWh)
    const energia_por_panel_mes = energia_por_panel_dia * DIAS_EN_MES;

    // 3. Paneles necesarios para cubrir el consumo mensual (idealmente)
    // Se considera el consumo sin la eficiencia del sistema para calcular el requerimiento bruto
    const consumo_requerido_bruto = datos.consumo_mensual / factorPerdidas; // Ajuste por pérdidas
    const paneles_necesarios_float = consumo_requerido_bruto / energia_por_panel_mes;
    const paneles_necesarios = Math.ceil(paneles_necesarios_float);

    // 4. Paneles que se pueden instalar según el área disponible
    const paneles_instalables = Math.floor(datos.area_disponible / AREA_PANEL_PROMEDIO);

    // 5. Determinar la energía real que se generará y el porcentaje de cobertura
    let energia_generada_final_kwh;
    let paneles_a_instalar;
    let mensaje_paneles = '';
    let porcentaje_cubierto = 0;

    if (paneles_instalables >= paneles_necesarios) {
        paneles_a_instalar = paneles_necesarios;
        energia_generada_final_kwh = paneles_a_instalar * energia_por_panel_mes * factorPerdidas;
        porcentaje_cubierto = Math.min(100, (energia_generada_final_kwh / datos.consumo_mensual) * 100);
        mensaje_paneles = `¡Excelente! El área disponible (${datos.area_disponible} m²) es suficiente para instalar ${paneles_a_instalar} paneles y cubrir aproximadamente el ${porcentaje_cubierto.toFixed(1)}% de tu consumo.`;
    } else {
        paneles_a_instalar = paneles_instalables;
        energia_generada_final_kwh = paneles_a_instalar * energia_por_panel_mes * factorPerdidas;
        porcentaje_cubierto = Math.min(100, (energia_generada_final_kwh / datos.consumo_mensual) * 100);
        mensaje_paneles = `Con el área disponible (${datos.area_disponible} m²) puedes instalar ${paneles_a_instalar} paneles, cubriendo aproximadamente el ${porcentaje_cubierto.toFixed(1)}% de tu consumo mensual. Necesitas ${paneles_necesarios} paneles para cubrir el 100%.`;
    }

    // Calcular precio por kWh según estrato
    const precioKWh = preciosKWh[datos.estrato];
    
    // Calcular ahorro mensual (considerando hasta el 100% del consumo cubierto)
    const ahorroMensual = Math.min(energia_generada_final_kwh, datos.consumo_mensual) * precioKWh;
    
    // Calcular ahorro anual
    const ahorroAnual = ahorroMensual * 12;
    
    // Calcular costo de instalación
    const costoInstalacion = (paneles_a_instalar * VATIOS_PANEL_PROMEDIO / 1000) * costoPorKW; // Costo por kW instalado
    
    // Calcular retorno de inversión (años)
    const retornoInversion = ahorroAnual > 0 ? costoInstalacion / ahorroAnual : 0;
    
    return {
        energiaGenerada: Math.round(energia_generada_final_kwh * 100) / 100,
        ahorroMensual: Math.round(ahorroMensual),
        ahorroAnual: Math.round(ahorroAnual),
        retornoInversion: Math.round(retornoInversion * 10) / 10,
        costoInstalacion: Math.round(costoInstalacion),
        panelesNecesarios: paneles_necesarios,
        panelesInstalables: paneles_a_instalar,
        porcentajeCobertura: porcentaje_cubierto,
        mensajePaneles: mensaje_paneles
    };
}

function mostrarResultados(resultados) {
    // Actualizar valores en la interfaz de resultados principales
    document.getElementById('energiaGenerada').textContent = `${resultados.energiaGenerada} kWh/mes`;
    document.getElementById('ahorroMensual').textContent = `$${resultados.ahorroMensual.toLocaleString()}`;
    document.getElementById('ahorroAnual').textContent = `$${resultados.ahorroAnual.toLocaleString()}`;
    document.getElementById('retornoInversion').textContent = `${resultados.retornoInversion} años`;
    document.getElementById('inversionNecesaria').textContent = `$${resultados.costoInstalacion.toLocaleString()}`;
    
    // Actualizar la nueva sección de paneles recomendados
    document.getElementById('panelesNecesarios').querySelector('.valor-panel').textContent = resultados.panelesNecesarios;
    document.getElementById('panelesInstalables').querySelector('.valor-panel').textContent = resultados.panelesInstalables;
    document.getElementById('coberturaConsumo').querySelector('.valor-panel').textContent = `${resultados.porcentajeCobertura.toFixed(1)}%`;
    document.getElementById('mensajePaneles').textContent = resultados.mensajePaneles;
    
    // Aplicar animaciones a los elementos de resultados principales (ya existentes)
    const elementos = document.querySelectorAll('.resultados-grid .resultado-valor');
    elementos.forEach((elemento, index) => {
        setTimeout(() => {
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateY(20px)';
            setTimeout(() => {
                elemento.style.opacity = '1';
                elemento.style.transform = 'translateY(0)';
            }, 100);
        }, index * 200);
    });

    // Mostrar la nueva sección de paneles
    document.querySelector('.paneles-recomendados-section').style.display = 'block';
}

function crearGrafico(resultados) {
    const ctx = document.getElementById('graficoAhorro').getContext('2d');
    
    // Destruir gráfico anterior si existe
    if (graficoAhorro) {
        graficoAhorro.destroy();
    }
    
    graficoAhorro = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Consumo Actual', 'Energía Solar Generada'],
            datasets: [{
                label: 'Consumo vs. Generación',
                data: [datosSimulacion.consumo_mensual, resultados.energiaGenerada],
                backgroundColor: [
                    'rgba(67, 97, 238, 0.7)', // Azul para consumo
                    'rgba(46, 204, 113, 0.7)' // Verde para energía generada
                ],
                borderColor: [
                    'rgba(67, 97, 238, 1)',
                    'rgba(46, 204, 113, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Consumo / Generación (kWh/mes)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Comparativa de Consumo vs. Generación Solar'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += `${context.parsed.y} kWh/mes`;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function guardarSimulacion() {
    if (!datosSimulacion) {
        mostrarError('No hay una simulación para guardar. Por favor, realiza una simulación primero.');
        return;
    }

    Swal.fire({
        title: 'Guardando simulación...',
        text: 'Por favor, espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('guardar_simulacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datosSimulacion)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarExito('¡Simulación guardada exitosamente!');
        } else {
            mostrarError(data.message || 'Error al guardar la simulación.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error de conexión con el servidor.');
    });
}

function nuevaSimulacion() {
    document.getElementById('calculadoraForm').reset();
    document.getElementById('resultadosCard').style.display = 'none';
    document.getElementById('infoCard').style.display = 'block';
    if (graficoAhorro) {
        graficoAhorro.destroy();
        graficoAhorro = null;
    }
    // Ocultar la sección de paneles recomendados también
    document.querySelector('.paneles-recomendados-section').style.display = 'none';
}

function cerrarSesion(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Cerrando sesión...',
        text: 'Redirigiendo al inicio',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    fetch('logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.removeItem('usuario');
            window.location.href = 'index.html';
        } else {
            mostrarError('Error al cerrar sesión.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error de conexión al cerrar sesión.');
    });
}

function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        confirmButtonColor: '#4361ee'
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#e74c3c'
    });
}

// Función para formatear números con separadores de miles
function formatearNumero(numero) {
    return numero.toLocaleString('es-CO');
}

// Añadir estilos CSS para la nueva sección de paneles
const style = document.createElement('style');
style.textContent = `
    .paneles-recomendados-section {
        background-color: var(--white);
        padding: var(--space-lg);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-medium);
        margin-top: var(--space-lg);
        text-align: center;
        border: 1px solid var(--border-color);
        display: none; /* Oculto por defecto */
    }

    .paneles-recomendados-section h2 {
        color: var(--primary-color);
        font-size: var(--font-size-xl);
        margin-bottom: var(--space-md);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-sm);
    }

    .paneles-recomendados-section h2 i {
        font-size: var(--font-size-xl);
        color: var(--primary-color);
    }

    .panel-recommendation-details p {
        font-size: var(--font-size-md);
        color: var(--text-color-light);
        margin-bottom: var(--space-xs);
    }

    .panel-recommendation-details .valor-panel {
        font-weight: var(--font-weight-bold);
        color: var(--primary-color);
        font-size: var(--font-size-lg);
    }

    .panel-recommendation-details #mensajePaneles {
        margin-top: var(--space-md);
        padding: var(--space-sm);
        background-color: var(--secondary-light);
        border-left: 5px solid var(--secondary-color);
        border-radius: var(--border-radius-md);
        color: var(--text-color);
        font-weight: var(--font-weight-medium);
    }
`;
document.head.appendChild(style); 