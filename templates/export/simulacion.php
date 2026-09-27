<?php
/**
 * Reporte descargable de una simulación (HTML autocontenido, imprimible como PDF).
 * @var array $simulacion  fila de SimulacionRepository::buscarDeUsuario()
 */
$fecha = date('d/m/Y H:i', strtotime($simulacion['fecha']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulación solar #<?= (int)$simulacion['id_simulacion'] ?> · SolarSim</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Roboto, Arial, sans-serif; color: #0f172a; background: #f8fafc; padding: 32px 16px; line-height: 1.5; }
        .reporte { max-width: 760px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; }
        .cabecera { background: #0f1d33; color: #fff; padding: 28px 32px; display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
        .marca { font-size: 20px; font-weight: 700; }
        .marca span { display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; margin-right: 8px; }
        .cabecera p { color: #cbd5e1; font-size: 14px; }
        .contenido { padding: 32px; }
        h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin: 28px 0 12px; }
        h2:first-child { margin-top: 0; }
        .destacado { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 20px 24px; }
        .destacado .etiqueta { color: #047857; font-size: 14px; font-weight: 600; }
        .destacado .valor { color: #047857; font-size: 34px; font-weight: 700; }
        .destacado .nota { color: #475569; font-size: 14px; }
        .datos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .dato { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; }
        .dato dt { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .dato dd { font-weight: 600; }
        .notas { color: #475569; font-size: 14px; padding-left: 18px; }
        .notas li + li { margin-top: 4px; }
        .pie { border-top: 1px solid #e2e8f0; padding: 16px 32px; color: #64748b; font-size: 12px; }
        @media (max-width: 560px) { .datos { grid-template-columns: repeat(2, 1fr); } }
        @media print { body { background: #fff; padding: 0; } .reporte { border: 0; } }
    </style>
</head>
<body>
    <div class="reporte">
        <div class="cabecera">
            <div>
                <div class="marca"><span></span>SolarSim</div>
                <p>Reporte de simulación de paneles solares</p>
            </div>
            <p>Simulación #<?= (int)$simulacion['id_simulacion'] ?> · <?= $fecha ?></p>
        </div>

        <div class="contenido">
            <h2>Resultado</h2>
            <div class="destacado">
                <p class="etiqueta">Ahorro mensual estimado</p>
                <p class="valor"><?= formato_moneda($simulacion['ahorro_mensual']) ?></p>
                <p class="nota"><?= formato_moneda($simulacion['ahorro_anual']) ?> al año · retorno de la inversión en <?= formato_numero($simulacion['retorno_inversion'], 1) ?> años</p>
            </div>

            <h2>Datos de la vivienda</h2>
            <dl class="datos">
                <div class="dato"><dt>Ciudad</dt><dd><?= e(Calculadora::nombreCiudad($simulacion['ubicacion'])) ?></dd></div>
                <div class="dato"><dt>Estrato</dt><dd><?= e($simulacion['estrato']) ?></dd></div>
                <div class="dato"><dt>Tipo de energía</dt><dd><?= e(ucfirst($simulacion['tipo_energia'])) ?></dd></div>
                <div class="dato"><dt>Consumo mensual</dt><dd><?= formato_numero($simulacion['consumo_mensual']) ?> kWh</dd></div>
                <div class="dato"><dt>Área disponible</dt><dd><?= formato_numero($simulacion['area_disponible']) ?> m²</dd></div>
                <div class="dato"><dt>Energía generada</dt><dd><?= formato_numero($simulacion['energia_generada'], 1) ?> kWh/mes</dd></div>
            </dl>

            <h2>Referencias</h2>
            <ul class="notas">
                <li>Un hogar de 4 personas consume en promedio 350 kWh al mes.</li>
                <li>Una instalación básica requiere al menos 20 m² de techo.</li>
                <li>El retorno típico de la inversión es de 5 a 8 años.</li>
            </ul>
        </div>

        <div class="pie">Estimación de referencia generada por SolarSim. Los valores reales dependen del equipo instalado, la orientación del techo y las tarifas vigentes.</div>
    </div>
</body>
</html>
