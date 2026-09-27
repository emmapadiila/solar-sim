// Calculadora solar. Los cálculos los hace el servidor (src/Calculadora.php) a través de la API.
let graficoAhorro = null;
let datosSimulacion = null; // datos de entrada de la última simulación calculada
let simulacionGuardada = false; // evita guardar dos veces la misma simulación
const TEXTO_GUARDAR = '<i class="fas fa-floppy-disk"></i> Guardar simulación';

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

    const boton = e.target.querySelector('button[type="submit"]');
    const textoBoton = boton.innerHTML;
    boton.disabled = true;
    boton.classList.add('is-loading');
    boton.innerHTML = '<i class="fas fa-circle-notch"></i> Calculando...';

    try {
        const data = await SolarSim.api('api/simulaciones/calcular.php', { method: 'POST', body: datos });
        if (!data.success) {
            SolarSim.error(data.message || 'No se pudo calcular la simulación.');
            return;
        }

        datosSimulacion = datos;
        reiniciarGuardado();
        document.getElementById('infoCard').classList.add('oculto');
        document.getElementById('resultadosCard').classList.remove('oculto');
        mostrarResultados(data.resultados);
        SolarSim.animarNumeros(document.getElementById('resultadosCard'));
        crearGrafico(data.resultados);
        document.getElementById('resultadosCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (error) {
        console.error('Error:', error);
        SolarSim.error('Error de conexión con el servidor.');
    } finally {
        boton.disabled = false;
        boton.classList.remove('is-loading');
        boton.innerHTML = textoBoton;
    }
}

function mostrarResultados(r) {
    const unidad = texto => `<span class="stat__unit">${texto}</span>`;

    document.getElementById('ahorroMensual').textContent = SolarSim.moneda(r.ahorroMensual);
    document.getElementById('ahorroAnual').textContent = SolarSim.moneda(r.ahorroAnual);
    document.getElementById('energiaGenerada').innerHTML = SolarSim.numero(r.energiaGenerada, 1) + unidad('kWh/mes');
    document.getElementById('inversionNecesaria').textContent = SolarSim.moneda(r.costoInstalacion);
    document.getElementById('retornoInversion').innerHTML = SolarSim.numero(r.retornoInversion, 1) + unidad('años');

    document.querySelector('#panelesNecesarios .valor-panel').textContent = r.panelesNecesarios;
    document.querySelector('#panelesInstalables .valor-panel').textContent = r.panelesInstalables;
    document.querySelector('#coberturaConsumo .valor-panel').textContent = `${SolarSim.numero(r.porcentajeCobertura, 1)} %`;
    document.getElementById('mensajePaneles').textContent = r.mensajePaneles;
}

function crearGrafico(resultados) {
    if (graficoAhorro) {
        graficoAhorro.destroy();
    }

    graficoAhorro = new Chart(document.getElementById('graficoAhorro'), {
        type: 'bar',
        data: {
            labels: ['Consumo actual', 'Generación solar'],
            datasets: [{
                data: [datosSimulacion.consumo_mensual, resultados.energiaGenerada],
                backgroundColor: [SolarSim.colores.navy, SolarSim.colores.sol],
                borderRadius: 6,
                maxBarThickness: 90
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: context => `${SolarSim.numero(context.parsed.y, 1)} kWh/mes`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'kWh/mes' },
                    grid: { color: SolarSim.colores.rejilla }
                },
                x: { grid: { display: false } }
            }
        }
    });
}

async function guardarSimulacion() {
    if (!datosSimulacion) {
        SolarSim.error('No hay una simulación para guardar. Realiza una simulación primero.');
        return;
    }
    if (simulacionGuardada) {
        return;
    }

    const btnGuardar = document.getElementById('btnGuardar');
    btnGuardar.disabled = true;
    SolarSim.cargando('Guardando simulación...');

    try {
        // Solo se envían los datos de entrada: el servidor recalcula los resultados antes de guardar.
        const data = await SolarSim.api('api/simulaciones/guardar.php', { method: 'POST', body: datosSimulacion });
        if (!data.success) {
            btnGuardar.disabled = false;
            SolarSim.error(data.message || 'Error al guardar la simulación.');
            return;
        }

        simulacionGuardada = true;
        btnGuardar.innerHTML = '<i class="fas fa-check"></i> Guardada';
        document.getElementById('btnVerHistorial').classList.remove('oculto');

        const respuesta = await Swal.fire({
            icon: 'success',
            title: 'Simulación guardada',
            text: 'Ya está en tu historial, donde puedes ver el detalle o descargar el reporte.',
            showCancelButton: true,
            confirmButtonText: 'Ver en historial',
            cancelButtonText: 'Seguir aquí',
            confirmButtonColor: SolarSim.colores.navy,
            cancelButtonColor: SolarSim.colores.gris
        });
        if (respuesta.isConfirmed) {
            window.location.href = 'historial.php';
        }
    } catch (error) {
        console.error('Error:', error);
        btnGuardar.disabled = false;
        SolarSim.error('Error de conexión con el servidor.');
    }
}

function reiniciarGuardado() {
    simulacionGuardada = false;
    const btnGuardar = document.getElementById('btnGuardar');
    btnGuardar.disabled = false;
    btnGuardar.innerHTML = TEXTO_GUARDAR;
    document.getElementById('btnVerHistorial').classList.add('oculto');
}

function nuevaSimulacion() {
    document.getElementById('calculadoraForm').reset();
    document.getElementById('resultadosCard').classList.add('oculto');
    document.getElementById('infoCard').classList.remove('oculto');
    if (graficoAhorro) {
        graficoAhorro.destroy();
        graficoAhorro = null;
    }
    datosSimulacion = null;
    reiniciarGuardado();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
