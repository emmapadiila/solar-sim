<?php
session_start();

// Verificar si el usuario ha iniciado sesión y si es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

require_once 'conexion.php';

// Obtener estadísticas generales
$stats = [
    'total_simulaciones' => 0,
    'ahorro_promedio' => 0,
    'energia_generada_promedio' => 0,
    'retorno_promedio' => 0
];

// Obtener ahorros por ciudad
$ahorros_ciudad = [];
$query_ahorros = "SELECT ubicacion, AVG(ahorro_mensual) as ahorro_promedio 
                 FROM tbl_simulacion s 
                 JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK 
                 GROUP BY ubicacion";
$result_ahorros = $conn->query($query_ahorros);
while ($row = $result_ahorros->fetch_assoc()) {
    $ahorros_ciudad[$row['ubicacion']] = $row['ahorro_promedio'];
}

// Obtener energía generada por ciudad y área
$energia_ciudad = [];
$query_energia = "SELECT ubicacion, area_disponible, AVG(energia_generada) as energia_promedio 
                 FROM tbl_simulacion s 
                 JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK 
                 GROUP BY ubicacion, area_disponible";
$result_energia = $conn->query($query_energia);
while ($row = $result_energia->fetch_assoc()) {
    $energia_ciudad[$row['ubicacion']][$row['area_disponible']] = $row['energia_promedio'];
}

// Obtener retorno por ciudad y estrato
$retorno_ciudad = [];
$query_retorno = "SELECT ubicacion, estrato, AVG(retorno_inversion) as retorno_promedio 
                 FROM tbl_simulacion s 
                 JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK 
                 GROUP BY ubicacion, estrato";
$result_retorno = $conn->query($query_retorno);
while ($row = $result_retorno->fetch_assoc()) {
    $retorno_ciudad[$row['ubicacion']][$row['estrato']] = $row['retorno_promedio'];
}

// Obtener últimas simulaciones
$ultimas_simulaciones = [];
$query_ultimas = "SELECT s.*, r.*, u.nombre as nombre_usuario 
                 FROM tbl_simulacion s 
                 JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK 
                 JOIN tbl_usuarios u ON s.id_usuarioFK = u.id_usuario 
                 ORDER BY s.fecha DESC LIMIT 10";
$result_ultimas = $conn->query($query_ultimas);
while ($row = $result_ultimas->fetch_assoc()) {
    $ultimas_simulaciones[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - SolarSim</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-solar-panel"></i>
                <span>SolarSim</span>
            </a>
            <nav>
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="dashboard.php" class="nav__link">Inicio</a>
                    </li>
                    <li class="nav__item">
                        <a href="calculadora_solar.php" class="nav__link">Calculadora</a>
                    </li>
                    <li class="nav__item">
                        <a href="historial_simulaciones.php" class="nav__link">Historial</a>
                    </li>
                    <li class="nav__item">
                        <a href="educativo.html" class="nav__link">Contenido Educativo</a>
                    </li>
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                    <li class="nav__item">
                        <a href="estadisticas.php" class="nav__link active">Estadísticas</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav__item">
                        <a href="about.php" class="nav__link">Acerca de Nosotros</a>
                    </li>
                    <li class="nav__item">
                        <a href="contact.php" class="nav__link">Contacto</a>
                    </li>
                    <li class="nav__item">
                        <a href="#" id="logoutBtn" class="nav__link logout-btn">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="stats-content">
        <div class="stats-container">
            <h1 class="page-title"><i class="fas fa-chart-line"></i> Estadísticas del Sistema</h1>
            
            <!-- Filtros -->
            <div class="stats-filters">
                <div class="filter-group">
                    <label for="filterCiudad">Ciudad:</label>
                    <select id="filterCiudad">
                        <option value="">Todas las ciudades</option>
                        <?php foreach ($ahorros_ciudad as $ciudad => $ahorro): ?>
                            <option value="<?php echo $ciudad; ?>"><?php echo ucfirst($ciudad); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="stats-grid">
                <!-- Ahorros por Ciudad -->
                <div class="stats-card">
                    <h3><i class="fas fa-piggy-bank"></i> Ahorros Promedio por Ciudad</h3>
                    <canvas id="graficoAhorros"></canvas>
                </div>

                <!-- Energía Generada -->
                <div class="stats-card">
                    <h3><i class="fas fa-bolt"></i> Energía Generada por Ciudad</h3>
                    <canvas id="graficoEnergia"></canvas>
                </div>

                <!-- Retorno de Inversión -->
                <div class="stats-card">
                    <h3><i class="fas fa-clock"></i> Retorno de Inversión</h3>
                    <canvas id="graficoRetorno"></canvas>
                </div>

                <!-- Relación Área-Energía -->
                <div class="stats-card">
                    <h3><i class="fas fa-chart-scatter"></i> Relación Área-Energía</h3>
                    <div class="stats-summary">
                        <?php
                        // Calcular promedios
                        $total_area = 0;
                        $total_energia = 0;
                        $count = 0;
                        
                        foreach ($energia_ciudad as $ciudad => $areas) {
                            foreach ($areas as $area => $energia) {
                                $total_area += $area;
                                $total_energia += $energia;
                                $count++;
                            }
                        }
                        
                        $promedio_area = $count > 0 ? round($total_area / $count) : 0;
                        $promedio_energia = $count > 0 ? round($total_energia / $count) : 0;
                        ?>
                        <p class="stats-info">
                            <i class="fas fa-info-circle"></i>
                            Con un promedio de <strong><?php echo $promedio_area; ?> m²</strong>, 
                            se están generando <strong><?php echo $promedio_energia; ?> kWh/mes</strong> en promedio.
                        </p>
                    </div>
                    <canvas id="graficoAreaEnergia"></canvas>
                </div>
            </div>

            <!-- Últimas Simulaciones -->
            <div class="ultimas-simulaciones">
                <h3><i class="fas fa-history"></i> Últimas Simulaciones</h3>
                <div class="table-responsive">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Ciudad</th>
                                <th>Estrato</th>
                                <th>Área</th>
                                <th>Ahorro Mensual</th>
                                <th>Retorno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimas_simulaciones as $simulacion): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($simulacion['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($simulacion['nombre_usuario']); ?></td>
                                <td><?php echo ucfirst($simulacion['ubicacion']); ?></td>
                                <td><?php echo $simulacion['estrato']; ?></td>
                                <td><?php echo $simulacion['area_disponible']; ?> m²</td>
                                <td>$<?php echo number_format($simulacion['ahorro_mensual'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($simulacion['retorno_inversion'], 1); ?> años</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Datos para los gráficos
        const ahorrosPorCiudad = <?php echo json_encode($ahorros_ciudad); ?>;
        const energiaPorCiudad = <?php echo json_encode($energia_ciudad); ?>;
        const retornoPorCiudad = <?php echo json_encode($retorno_ciudad); ?>;

        // Función para filtrar datos por ciudad
        function filtrarPorCiudad(ciudad) {
            if (!ciudad) {
                return {
                    ahorros: ahorrosPorCiudad,
                    energia: energiaPorCiudad,
                    retorno: retornoPorCiudad
                };
            }

            return {
                ahorros: { [ciudad]: ahorrosPorCiudad[ciudad] },
                energia: { [ciudad]: energiaPorCiudad[ciudad] },
                retorno: { [ciudad]: retornoPorCiudad[ciudad] }
            };
        }

        // Función para actualizar gráficos
        function actualizarGraficos() {
            const ciudadSeleccionada = document.getElementById('filterCiudad').value;
            const datosFiltrados = filtrarPorCiudad(ciudadSeleccionada);

            // Actualizar gráfico de ahorros
            graficoAhorros.data.labels = Object.keys(datosFiltrados.ahorros).map(ciudad => ucfirst(ciudad));
            graficoAhorros.data.datasets[0].data = Object.values(datosFiltrados.ahorros);
            graficoAhorros.update();

            // Actualizar gráfico de energía
            graficoEnergia.data.labels = Object.keys(datosFiltrados.energia);
            graficoEnergia.data.datasets[0].data = Object.values(datosFiltrados.energia).map(areas => 
                Object.values(areas).reduce((a, b) => a + b, 0) / Object.keys(areas).length
            );
            graficoEnergia.update();

            // Actualizar gráfico de retorno
            graficoRetorno.data.labels = Object.keys(datosFiltrados.retorno);
            graficoRetorno.data.datasets[0].data = Object.values(datosFiltrados.retorno).map(estratos => 
                Object.values(estratos).reduce((a, b) => a + b, 0) / Object.keys(estratos).length
            );
            graficoRetorno.update();
        }

        // Inicializar gráficos
        const graficoAhorros = new Chart(document.getElementById('graficoAhorros'), {
            type: 'bar',
            data: {
                labels: Object.keys(ahorrosPorCiudad).map(ciudad => ucfirst(ciudad)),
                datasets: [{
                    label: 'Ahorro Mensual Promedio ($)',
                    data: Object.values(ahorrosPorCiudad),
                    backgroundColor: 'rgba(67, 97, 238, 0.5)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        const graficoEnergia = new Chart(document.getElementById('graficoEnergia'), {
            type: 'line',
            data: {
                labels: Object.keys(energiaPorCiudad),
                datasets: [{
                    label: 'Energía Generada (kWh/mes)',
                    data: Object.values(energiaPorCiudad).map(areas => 
                        Object.values(areas).reduce((a, b) => a + b, 0) / Object.keys(areas).length
                    ),
                    borderColor: 'rgba(46, 204, 113, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const graficoRetorno = new Chart(document.getElementById('graficoRetorno'), {
            type: 'bar',
            data: {
                labels: Object.keys(retornoPorCiudad),
                datasets: [{
                    label: 'Retorno Promedio (años)',
                    data: Object.values(retornoPorCiudad).map(estratos => 
                        Object.values(estratos).reduce((a, b) => a + b, 0) / Object.keys(estratos).length
                    ),
                    backgroundColor: 'rgba(241, 196, 15, 0.5)',
                    borderColor: 'rgba(241, 196, 15, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Relación Área-Energía
        const datosAreaEnergia = [];
        <?php
        foreach ($energia_ciudad as $ciudad => $areas) {
            foreach ($areas as $area => $energia) {
                echo "datosAreaEnergia.push({x: $area, y: $energia});\n";
            }
        }
        ?>

        const graficoAreaEnergia = new Chart(document.getElementById('graficoAreaEnergia'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Relación Área-Energía',
                    data: datosAreaEnergia,
                    backgroundColor: 'rgba(67, 97, 238, 0.5)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Área (m²)'
                        },
                        beginAtZero: true
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Energía Generada (kWh/mes)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Área: ${context.raw.x} m²\nEnergía: ${context.raw.y} kWh/mes`;
                            }
                        }
                    }
                }
            }
        });

        // Función auxiliar para capitalizar primera letra
        function ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Event listener para el filtro de ciudad
        document.getElementById('filterCiudad').addEventListener('change', actualizarGraficos);

        // Función para cerrar sesión
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
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
                        window.location.href = 'index.html';
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
        });
    </script>

    <style>
        .stats-summary {
            margin-bottom: var(--space-md);
            padding: var(--space-sm);
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: var(--border-radius-md);
        }

        .stats-info {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            color: var(--text-color);
            font-size: var(--font-size-md);
        }

        .stats-info i {
            color: var(--primary-color);
            font-size: var(--font-size-lg);
        }

        .stats-info strong {
            color: var(--primary-color);
        }
    </style>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
</body>
</html> 