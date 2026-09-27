// Gráficos del panel de estadísticas. Los datos llegan en window.ESTADISTICAS desde estadisticas.php.
document.addEventListener('DOMContentLoaded', function() {
    const datos = window.ESTADISTICAS;
    const c = SolarSim.colores;
    const nombreCiudad = clave => datos.ciudades[clave] || clave;

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = c.gris;

    function filtrar(serie, ciudad) {
        return ciudad ? { [ciudad]: serie[ciudad] } : serie;
    }

    function crearGraficoPorCiudad(id, tipo, serie, color, formato) {
        return new Chart(document.getElementById(id), {
            type: tipo,
            data: {
                labels: Object.keys(serie).map(nombreCiudad),
                datasets: [{
                    data: Object.values(serie),
                    backgroundColor: color,
                    borderColor: color,
                    borderRadius: 6,
                    maxBarThickness: 48,
                    pointRadius: 4,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => formato(context.parsed.y) } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: c.rejilla }, ticks: { callback: formato } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const graficoAhorros = crearGraficoPorCiudad('graficoAhorros', 'bar', datos.ahorro, c.verde, v => SolarSim.moneda(v));
    const graficoEnergia = crearGraficoPorCiudad('graficoEnergia', 'bar', datos.energia, c.sol, v => `${SolarSim.numero(v)} kWh`);
    const graficoRetorno = crearGraficoPorCiudad('graficoRetorno', 'bar', datos.retorno, c.navy, v => `${SolarSim.numero(v, 1)} años`);

    const graficoAreaEnergia = new Chart(document.getElementById('graficoAreaEnergia'), {
        type: 'scatter',
        data: {
            datasets: [{
                data: datos.areaEnergia,
                backgroundColor: c.navySuave,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: context => `${nombreCiudad(context.raw.ciudad)}: ${SolarSim.numero(context.raw.x)} m² → ${SolarSim.numero(context.raw.y)} kWh/mes`
                    }
                }
            },
            scales: {
                x: { beginAtZero: true, title: { display: true, text: 'Área (m²)' }, grid: { color: c.rejilla } },
                y: { beginAtZero: true, title: { display: true, text: 'Energía (kWh/mes)' }, grid: { color: c.rejilla } }
            }
        }
    });

    document.getElementById('filterCiudad').addEventListener('change', function() {
        const ciudad = this.value;

        [[graficoAhorros, datos.ahorro], [graficoEnergia, datos.energia], [graficoRetorno, datos.retorno]].forEach(([grafico, serie]) => {
            const filtrada = filtrar(serie, ciudad);
            grafico.data.labels = Object.keys(filtrada).map(nombreCiudad);
            grafico.data.datasets[0].data = Object.values(filtrada);
            grafico.update();
        });

        graficoAreaEnergia.data.datasets[0].data = ciudad
            ? datos.areaEnergia.filter(punto => punto.ciudad === ciudad)
            : datos.areaEnergia;
        graficoAreaEnergia.update();
    });
});
