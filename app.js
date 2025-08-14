document.addEventListener('DOMContentLoaded', () => {

    fetchRecommendations();


    const refreshButton = document.getElementById('refreshAll');
    if (refreshButton) {
        refreshButton.addEventListener('click', fetchRecommendations);
    }
});

async function fetchRecommendations() {
    const recommendationsSection = document.getElementById('recommendations-section');
    const recommendationsContainer = document.getElementById('recommendations-container');
    const placeholder = document.getElementById('recommendations-placeholder');


    recommendationsSection.style.display = 'block';
    recommendationsContainer.innerHTML = '<p id="recommendations-placeholder">Analizando datos y generando recomendaciones con IA...</p>';

    try {

        const printerResponse = await fetch('obtener_data.php');
        if (!printerResponse.ok) {
            throw new Error(`Error al obtener datos: ${printerResponse.statusText}`);
        }
        const printers = await printerResponse.json();

        if (printers.length === 0) {
            recommendationsContainer.innerHTML = '<p>No hay impresoras para analizar.</p>';
            return;
        }


        const recommendationResponse = await fetch('obtener_recomendaciones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(printers),
        });

        if (!recommendationResponse.ok) {
            throw new Error(`Error del servidor de recomendaciones: ${recommendationResponse.statusText}`);
        }

        const recommendations = await recommendationResponse.json();
        

        displayRecommendations(printers, recommendations);

    } catch (error) {
        console.error('Error en el proceso:', error);
        recommendationsContainer.innerHTML = `<p class="text-red-600"><strong>Error:</strong> No se pudieron generar las recomendaciones. ${error.message}</p>`;
    }
}

function displayRecommendations(printers, recommendations) {
    const recommendationsContainer = document.getElementById('recommendations-container');
    recommendationsContainer.innerHTML = ''; // Limpiar el contenido anterior

    if (recommendations.length === 0) {
        recommendationsContainer.innerHTML = '<p class="text-green-600">¡Todo en orden! No se requieren acciones inmediatas en las impresoras.</p>';
        return;
    }
    
    const recommendationsList = document.createElement('ul');
    recommendationsList.className = 'list-disc pl-5 space-y-2';

    recommendations.forEach(rec => {

        const printer = printers.find(p => p.id === rec.id);
        if (printer) {
            const listItem = document.createElement('li');
            listItem.innerHTML = `<strong>${printer.name} (${printer.location}):</strong> ${rec.recommendation}`;
            recommendationsList.appendChild(listItem);
        }
    });
    
    recommendationsContainer.appendChild(recommendationsList);
}
