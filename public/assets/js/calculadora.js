// Calculadora solar. Los cálculos los hace el servidor (src/Calculadora.php) a través de la API.
let graficoAhorro = null;
let datosSimulacion = null; // datos de entrada de la última simulación calculada

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('calculadoraForm').addEventListener('submit', calcularAhorro);
    document.getElementById('btnGuardar').addEventListener('click', guardarSimulacion);
    document.getElementById('btnNuevaSimulacion').addEventListener('click', nuevaSimulacion);

    // Validaciones en tiempo real (los límites vienen de los atributos min/max del HTML)
    document.getElementById('consumo_mensual').addEventListener('input', e => validarRango(e.target, 'consumo', 'kWh/mes'));
    document.getElementById('area_disponible').addEventListener('input', e => validarRango(e.target, 'área', 'm²'));
});

function validarRango(input, nombre, unidad) {
    const valor = parseFloat(input.value);
    const min = parseFloat(input.min);
    const max = parseFloat(input.max);

    if (valor < min) {
        input.setCustomValidity(`El ${nombre} mínimo es ${min} ${unidad}`);
        input.classList.add('error');
    } else if (valor > max) {
        input.setCustomValidity(`El ${nombre} máximo es ${max} ${unidad}`);
        input.classList.add('error');
    } else {
        input.setCustomValidity('');
        input.classList.remove('error');
    }
}

async function calcularAhorro(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const datos = {
        ubicacion: formData.get('ubicacion'),
        estrato: parseInt(formData.get('estrato'), 10),
        consumo_mensual: parseFloat(formData.get('consumo_mensual')),
        area_disponible: parseFloat(formData.get('area_disponible')),
        tipo_energia: formData.get('tipo_energia')
    };

    try {
        const data = await SolarSim.api('api/simulaciones/calcular.php', { method: 'POST', body: datos });
        if (!data.success) {
            SolarSim.error(data.message || 'No se pudo calcular la simulación.');
            return;
        }

        datosSimulacion = datos;
        mostrarResultados(data.resultados);
        crearGrafico(data.resultados);

        document.getElementById('resultadosCard').style.display = 'block';
        document.getElementById('infoCard').style.display = 'none';
        document.getElementById('resultadosCard').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('Error de conexión con el servidor.');
    }
}

function mostrarResultados(resultados) {
    // Actualizar valores en la interfaz de resultados principales
    document.getElementById('energiaGenerada').textContent = `${resultados.energiaGenerada} kWh/mes`;
    document.getElementById('ahorroMensual').textContent = `$${SolarSim.formatearNumero(resultados.ahorroMensual)}`;
    document.getElementById('ahorroAnual').textContent = `$${SolarSim.formatearNumero(resultados.ahorroAnual)}`;
    document.getElementById('retornoInversion').textContent = `${resultados.retornoInversion} años`;
    document.getElementById('inversionNecesaria').textContent = `$${SolarSim.formatearNumero(resultados.costoInstalacion)}`;
    
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

async function guardarSimulacion() {
    if (!datosSimulacion) {
        SolarSim.error('No hay una simulación para guardar. Por favor, realiza una simulación primero.');
        return;
    }

    SolarSim.cargando('Guardando simulación...');

    try {
        // Solo se envían los datos de entrada: el servidor recalcula los resultados antes de guardar.
        const data = await SolarSim.api('api/simulaciones/guardar.php', { method: 'POST', body: datosSimulacion });
        if (data.success) {
            SolarSim.exito('¡Simulación guardada exitosamente!');
        } else {
            SolarSim.error(data.message || 'Error al guardar la simulación.');
        }
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('Error de conexión con el servidor.');
    }
}

function nuevaSimulacion() {
    document.getElementById('calculadoraForm').reset();
    document.getElementById('resultadosCard').style.display = 'none';
    document.getElementById('infoCard').style.display = 'block';
    document.querySelector('.paneles-recomendados-section').style.display = 'none';
    if (graficoAhorro) {
        graficoAhorro.destroy();
        graficoAhorro = null;
    }
    datosSimulacion = null;
}
