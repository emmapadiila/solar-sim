// Animaciones y glosario de la página de contenido educativo.
document.addEventListener('DOMContentLoaded', function() {
    
    // Animación de entrada para las tarjetas de temas
    const topicCards = document.querySelectorAll('.topic-card');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    topicCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Animación para los iconos de recursos
    const resourceIcons = document.querySelectorAll('.resource-icon i');
    resourceIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2) rotate(10deg)';
            this.style.transition = 'all 0.3s ease';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    // Tooltip informativo para términos técnicos
    const technicalTerms = document.querySelectorAll('.topic-card p, .topic-card h3');
    technicalTerms.forEach(term => {
        const words = term.textContent.split(' ');
        words.forEach(word => {
            if (isTechnicalTerm(word)) {
                const span = document.createElement('span');
                span.textContent = word;
                span.className = 'technical-term';
                span.style.cursor = 'help';
                span.style.borderBottom = '1px dotted #4361ee';
                span.title = getTermDefinition(word);
                term.innerHTML = term.innerHTML.replace(word, span.outerHTML);
            }
        });
    });

    function isTechnicalTerm(word) {
        const technicalTerms = [
            'fotovoltaico', 'silicio', 'inversor', 'kWh', 'CO2', 
            'renovable', 'sostenibilidad', 'efecto', 'fotones', 'electrones'
        ];
        return technicalTerms.some(term => 
            word.toLowerCase().includes(term.toLowerCase())
        );
    }

    function getTermDefinition(word) {
        const definitions = {
            'fotovoltaico': 'Tecnología que convierte la luz solar en electricidad',
            'silicio': 'Elemento semiconductor usado en células solares',
            'inversor': 'Dispositivo que convierte corriente continua a alterna',
            'kWh': 'Kilovatio-hora, unidad de medida de energía',
            'CO2': 'Dióxido de carbono, gas de efecto invernadero',
            'renovable': 'Energía que se regenera naturalmente',
            'sostenibilidad': 'Desarrollo que satisface necesidades sin comprometer el futuro',
            'efecto': 'Fenómeno físico que permite la conversión de luz a electricidad',
            'fotones': 'Partículas de luz que excitan electrones',
            'electrones': 'Partículas subatómicas que transportan electricidad'
        };
        
        for (let term in definitions) {
            if (word.toLowerCase().includes(term)) {
                return definitions[term];
            }
        }
        return 'Término técnico relacionado con energía solar';
    }
});
