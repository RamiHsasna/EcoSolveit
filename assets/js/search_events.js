document.addEventListener('DOMContentLoaded', () => {
    loadFilters();
    document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
        el.addEventListener('change', applyFilters);
    });
});

async function loadFilters() {
    // Catégories
    const catRes = await fetch('/EcoSolveit/api/search_events.php?get=categories');
    const categories = await catRes.json();
    const catContainer = document.getElementById('categories-list');
    categories.forEach(cat => {
        const div = document.createElement('div');
        div.innerHTML = `<label><input type="checkbox" class="cat-check" value="${cat.id}"> ${cat.category_name}</label>`;
        catContainer.appendChild(div);
    });

    // Villes
    const villeRes = await fetch('/EcoSolveit/api/search_events.php?get=villes');
    const villes = await villeRes.json();
    const villeContainer = document.getElementById('villes-list');
    villes.forEach(v => {
        const div = document.createElement('div');
        div.innerHTML = `<label><input type="checkbox" class="ville-check" value="${v}"> ${v}</label>`;
        villeContainer.appendChild(div);
    });

    applyFilters(); // Afficher tout au chargement
}

function collectFilters() {
    const filters = {};
    const cats = Array.from(document.querySelectorAll('.cat-check:checked')).map(cb => parseInt(cb.value));
    if (cats.length) filters.category = cats;
    const villes = Array.from(document.querySelectorAll('.ville-check:checked')).map(cb => cb.value);
    if (villes.length) filters.ville = villes;
    const pays = document.getElementById('pays').value;
    if (pays) filters.pays = pays;
    const dateFrom = document.getElementById('date_from').value;
    if (dateFrom) filters.date_from = dateFrom;
    const dateTo = document.getElementById('date_to').value;
    if (dateTo) filters.date_to = dateTo;
    return filters;
}

async function applyFilters() {
    const filters = collectFilters();
    const params = new URLSearchParams({ filters: JSON.stringify(filters) });
    try {
        const res = await fetch(`/EcoSolveit/api/search_events.php?${params}`);
        const data = await res.json();
        const grid = document.getElementById('events-grid');
        grid.innerHTML = '';
        if (!data.events.length) {
            grid.innerHTML = '<p class="text-center">Aucun événement trouvé.</p>';
            return;
        }
        data.events.forEach(e => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 mb-4';
            col.innerHTML = `
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">${escapeHtml(e.event_name)}</h5>
                        <p class="card-text">${escapeHtml(e.description)}</p>
                        <p class="text-muted">${e.ville}, ${e.pays}</p>
                        <p class="text-muted">${new Date(e.event_date).toLocaleDateString('fr-FR')}</p>
                        <p class="text-muted">${e.category_name}</p>
                    </div>
                </div>`;
            grid.appendChild(col);
        });
    } catch (err) {
        console.error(err);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
