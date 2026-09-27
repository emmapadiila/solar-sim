<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Contenido Educativo', 'paginaActiva' => 'educativo']);
?>

    <main class="educativo-content">
        <div class="educativo-container">
            <div class="educativo-header">
                <h1><i class="fas fa-graduation-cap"></i>Contenido Educativo</h1>
                <p>Descubre todo lo que necesitas saber sobre energía solar, paneles solares y cómo pueden transformar tu consumo energético. Aprende de manera interactiva y divertida.</p>
            </div>

            <div class="topics-grid">
                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <h3>¿Qué es la Energía Solar?</h3>
                    <p>La energía solar es una fuente de energía renovable que aprovecha la radiación electromagnética del sol para generar electricidad o calor.</p>
                    <ul class="topic-features">
                        <li>Es completamente renovable e inagotable</li>
                        <li>No produce emisiones de gases de efecto invernadero</li>
                        <li>Reduce la dependencia de combustibles fósiles</li>
                        <li>Es gratuita una vez instalada</li>
                        <li>Requiere poco mantenimiento</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-solar-panel"></i>
                    </div>
                    <h3>¿Cómo Funcionan los Paneles Solares?</h3>
                    <p>Los paneles solares fotovoltaicos convierten la luz solar directamente en electricidad mediante el efecto fotovoltaico.</p>
                    <ul class="topic-features">
                        <li>Las células fotovoltaicas capturan la luz solar</li>
                        <li>Los fotones excitan los electrones del silicio</li>
                        <li>Se genera una corriente eléctrica continua</li>
                        <li>Un inversor convierte la corriente a alterna</li>
                        <li>La electricidad se puede usar o vender a la red</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Beneficios para tu Hogar</h3>
                    <p>Instalar paneles solares en tu hogar ofrece múltiples ventajas económicas, ambientales y sociales.</p>
                    <ul class="topic-features">
                        <li>Ahorro significativo en la factura eléctrica</li>
                        <li>Valor agregado a tu propiedad</li>
                        <li>Independencia energética</li>
                        <li>Protección contra aumentos de tarifas</li>
                        <li>Contribución a la sostenibilidad</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3>Factores que Afectan la Eficiencia</h3>
                    <p>Varios factores influyen en la eficiencia y producción de energía de los paneles solares.</p>
                    <ul class="topic-features">
                        <li>Orientación e inclinación de los paneles</li>
                        <li>Horas de sol directo en tu ubicación</li>
                        <li>Sombras y obstáculos</li>
                        <li>Calidad y tipo de paneles</li>
                        <li>Mantenimiento y limpieza</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Impacto Ambiental</h3>
                    <p>La energía solar es una de las formas más limpias de generar electricidad y tiene un impacto positivo en el medio ambiente.</p>
                    <ul class="topic-features">
                        <li>Reduce las emisiones de CO2</li>
                        <li>No contamina el aire ni el agua</li>
                        <li>Conserva recursos naturales</li>
                        <li>Combate el cambio climático</li>
                        <li>Promueve la sostenibilidad</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3>Inversión y Retorno</h3>
                    <p>Los paneles solares son una inversión inteligente que se paga por sí misma con el tiempo.</p>
                    <ul class="topic-features">
                        <li>Período de recuperación de 5-10 años</li>
                        <li>Vida útil de 25-30 años</li>
                        <li>Incentivos y subsidios disponibles</li>
                        <li>Valorización de la propiedad</li>
                        <li>Protección contra inflación energética</li>
                    </ul>
                </div>
            </div>

            
            <div class="resources-section">
                <h2><i class="fas fa-book"></i> Recursos Adicionales</h2>
                <div class="resources-grid">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <h3>Videos Educativos</h3>
                        <p>Explora nuestra colección de videos que explican de manera visual cómo funciona la energía solar.</p>
                        <a href="https://youtu.be/jeT5lZ9t7c0?si=HxI_F_67k3E7sBct" class="btn-resource" target="_blank" rel="noopener">Ver Videos</a>
                    </div>

                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3>Guías Educativas</h3>
                        <p>Guías completas sobre instalación, mantenimiento y optimización de paneles solares.</p>
                        <a href="https://nalelectricos.com.co/paneles-solares-lo-que-debes-saber/" class="btn-resource" target="_blank" rel="noopener">Ver Guías</a>
                    </div>

                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Calculadoras Avanzadas</h3>
                        <p>Utiliza nuestras herramientas avanzadas para calcular el potencial solar de tu ubicación.</p>
                        <a href="calculadora.php" class="btn-resource">Calcular</a>
                    </div>

                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Comunidad</h3>
                        <p>Únete a nuestra comunidad de usuarios y comparte experiencias sobre energía solar.</p>
                        <a href="#" class="btn-resource">Unirse</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['educativo.js']]); ?>
