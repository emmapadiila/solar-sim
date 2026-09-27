// Página "Aprende": glosario de términos técnicos (la aparición de tarjetas la hace app.js).
document.addEventListener('DOMContentLoaded', function() {
    // Glosario: subraya los términos técnicos y muestra su definición al pasar el ratón
    const glosario = {
        fotovoltaico: 'Tecnología que convierte la luz solar en electricidad',
        fotovoltaicos: 'Tecnología que convierte la luz solar en electricidad',
        silicio: 'Semiconductor con el que se fabrican las células solares',
        inversor: 'Equipo que convierte la corriente continua en alterna',
        CO2: 'Dióxido de carbono, principal gas de efecto invernadero',
        renovable: 'Energía que se regenera de forma natural',
        fotones: 'Partículas de luz que excitan los electrones',
        electrones: 'Partículas que transportan la corriente eléctrica'
    };
    const patron = new RegExp(`\\b(${Object.keys(glosario).join('|')})\\b`, 'g');

    document.querySelectorAll('.topic-card p, .topic-card li').forEach(elemento => {
        const walker = document.createTreeWalker(elemento, NodeFilter.SHOW_TEXT);
        const nodos = [];
        while (walker.nextNode()) nodos.push(walker.currentNode);

        nodos.forEach(nodo => {
            if (!patron.test(nodo.nodeValue)) return;
            patron.lastIndex = 0;

            const fragmento = document.createDocumentFragment();
            nodo.nodeValue.split(patron).forEach(parte => {
                if (glosario[parte]) {
                    const termino = document.createElement('span');
                    termino.className = 'technical-term';
                    termino.title = glosario[parte];
                    termino.textContent = parte;
                    fragmento.appendChild(termino);
                } else if (parte) {
                    fragmento.appendChild(document.createTextNode(parte));
                }
            });
            nodo.replaceWith(fragmento);
        });
    });
});
