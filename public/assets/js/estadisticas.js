// Gráficos del panel de estadísticas. Los datos llegan en window.ESTADISTICAS desde estadisticas.php.
document.addEventListener('DOMContentLoaded', function() {
    const datos = window.ESTADISTICAS;
    const nombreCiudad = clave => datos.ciudades[clave] || clave;

    function filtrar(serie, ciudad) {
        return ciudad ? { [ciudad]: serie[ciudad] } : serie;
    }

    function crearGraficoPorCiudad(id, tipo, etiqueta, serie, colores, opciones = {}) {
        return new Chart(document.getElementById(id), {
            type: tipo,
            data: {
                labels: Object.keys(serie).map(nombreCiudad),
                datasets: [{
                    label: etiqueta,
                    data: Object.values(serie),
                    backgroundColor: colores.fondo,
                    borderColor: colores.borde,
                    borderWidth: 1,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ...opciones.y } }
            }
        });
    }

    const graficoAhorros = crearGraficoPorCiudad('graficoAhorros', 'bar', 'Ahorro Mensual Promedio ($)', datos.ahorro,
        { fondo: 'rgba(67, 97, 238, 0.5)', borde: 'rgba(67, 97, 238, 1)' },
        { y: { ticks: { callback: valor => '$' + valor.toLocaleString('es-CO') } } });

    const graficoEnergia = crearGraficoPorCiudad('graficoEnergia', 'line', 'Energía Generada (kWh/mes)', datos.energia,
        { fondo: 'rgba(46, 204, 113, 0.2)', borde: 'rgba(46, 204, 113, 1)' });

    const graficoRetorno = crearGraficoPorCiudad('graficoRetorno', 'bar', 'Retorno Promedio (años)', datos.retorno,
        { fondo: 'rgba(241, 196, 15, 0.5)', borde: 'rgba(241, 196, 15, 1)' });

    const graficoAreaEnergia = new Chart(document.getElementById('graficoAreaEnergia'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Relación Área-Energía',
                data: datos.areaEnergia,
                backgroundColor: 'rgba(67, 97, 238, 0.5)',
                borderColor: 'rgba(67, 97, 238, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { beginAtZero: true, title: { display: true, text: 'Área (m²)' } },
                y: { beginAtZero: true, title: { display: true, text: 'Energía Generada (kWh/mes)' } }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: context => `${nombreCiudad(context.raw.ciudad)} - Área: ${context.raw.x} m², Energía: ${context.raw.y} kWh/mes`
                    }
                }
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
