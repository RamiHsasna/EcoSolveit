<?php
require_once __DIR__ . '/../../controllers/EventController.php';
$controller = new EventController();

// Récupérer catégories pour les filtres
$categories = $controller->getCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Événements Écologiques Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
     /* ========== BODY & TITRE ========== */
body {
    background: linear-gradient(135deg, #e9f5ec, #f6fbf7);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    margin: 0;
    padding: 30px;
}

h1 {
    text-align: center;
    color: #00796b;
    font-weight: 700;
    margin-bottom: 2rem;
}

/* ========== CONTAINER PRINCIPAL ========== */
.container-main {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    max-width: 1300px;
    margin: 0 auto;
}

/* ========== SIDEBAR ========== */
.filter-sidebar {
    flex: 0 0 280px;
    background: #fff;
    padding: 25px 20px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    height: fit-content;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filter-sidebar:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.filter-sidebar h5 {
    color: #00796b;
    font-weight: 700;
    margin-bottom: 15px;
    border-bottom: 2px solid #e0f2f1;
    padding-bottom: 5px;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-sidebar label {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-weight: 500;
    color: #555;
    cursor: pointer;
    transition: color 0.3s ease;
}

.filter-sidebar label:hover {
    color: #00796b;
}

.filter-sidebar input[type="checkbox"] {
    margin-right: 10px;
    accent-color: #00796b;
    width: 18px;
    height: 18px;
}

.filter-sidebar select.form-select {
    border-radius: 10px;
    border: 1px solid #b2dfdb;
    padding: 8px 12px;
    transition: all 0.3s ease;
    background-color: #f9fdfa;
}

.filter-sidebar select.form-select:focus {
    border-color: #00796b;
    box-shadow: 0 0 8px rgba(0,121,107,0.2);
    outline: none;
}

/* Bouton filtrer optionnel */
.filter-sidebar button {
    display: block;
    width: 100%;
    background-color: #00796b;
    color: #fff;
    border: none;
    padding: 10px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.filter-sidebar button:hover {
    background-color: #004d40;
    transform: translateY(-2px);
}

/* ========== LISTE DES ÉVÉNEMENTS ========== */
.event-container {
    flex: 1;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-radius: 20px;
    overflow: hidden;
    background: #fff;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.card-title {
    color: #00796b;
    font-weight: 600;
    margin-bottom: 1rem;
}

.card-text {
    color: #555;
    line-height: 1.6;
}

.text-muted {
    color: #777 !important;
}

.bi {
    margin-right: 0.5rem;
}

.location-text {
    font-weight: 500;
    color: #00796b !important;
}

/* ========== RESPONSIVE ========== */
@media(max-width:992px) {
    .container-main {
        flex-direction: column;
    }
    .filter-sidebar {
        width: 100%;
    }
}

    </style>
</head>
<body>
<h1>Événements Écologiques Disponibles</h1>

<div class="container-main">
    <div class="filter-sidebar">
        <form id="filterForm">
            <h5>Catégorie</h5>
            <div class="filter-group">
                <?php foreach($categories as $cat): ?>
                    <label>
                        <input type="checkbox" class="cat-check" name="categorie[]" value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <h5>Pays</h5>
            <div class="filter-group">
                <select name="pays" id="pays" class="form-select">
                    <option value="">Sélectionnez un pays</option>
                </select>
            </div>

            <h5>Ville</h5>
            <div class="filter-group">
                <select name="ville" id="ville" class="form-select" disabled>
                    <option value="">Sélectionnez d'abord un pays</option>
                </select>
            </div>

            <h5>Date</h5>
            <div class="filter-group">
                <?php
                setlocale(LC_TIME,'fr_FR.UTF-8');
                $today = new DateTime();
                $jourActuel = strftime('%e %b %Y', $today->getTimestamp());
                $debutSemaine = (clone $today)->modify('monday this week');
                $finSemaine = (clone $today)->modify('sunday this week');
                $semaineTexte = strftime('%e', $debutSemaine->getTimestamp()) . '-' . strftime('%e %b', $finSemaine->getTimestamp());
                $moisTexte = strftime('%b %Y', $today->getTimestamp());
                ?>
                <label><input type="checkbox" name="date[]" value="today"> Aujourd'hui (<?= $jourActuel ?>)</label>
                <label><input type="checkbox" name="date[]" value="week"> Cette semaine (<?= $semaineTexte ?>)</label>
                <label><input type="checkbox" name="date[]" value="month"> Ce mois (<?= $moisTexte ?>)</label>
                <label><input type="checkbox" name="date[]" value="future"> À venir (après <?= $jourActuel ?>)</label>
            </div>
        </form>
    </div>

    <div class="event-container">
        <div id="events-grid" class="row"></div>
    </div>
</div>

<script>
const CSC_API_KEY = "<?php echo getenv('CSC_API_KEY') ?: 'ZTFSZnU2UTVBNGZkYlpzam4wNVdSalpsbUVQMmZFeDJmcG91bVFicg=='; ?>";

// Collecte les filtres
function collectFilters(){
    const filters = {};
    const catChecks = document.querySelectorAll('.cat-check:checked');
    if(catChecks.length) filters.category = Array.from(catChecks).map(cb => parseInt(cb.value));

    const ville = document.getElementById('ville').value;
    if(ville) filters.ville = [ville];

    const pays = document.getElementById('pays').value;
    if(pays) filters.pays = pays;

    // Dates
    const dateChecks = Array.from(document.querySelectorAll('input[name="date[]"]:checked')).map(cb=>cb.value);
    const today = new Date();
    dateChecks.forEach(d=>{
        if(d==='today'){ filters.date_from=filters.date_to = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`;}
        if(d==='week'){ const start=new Date(today); start.setDate(today.getDate()-today.getDay()+1); const end=new Date(start); end.setDate(start.getDate()+6); filters.date_from=`${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`; filters.date_to=`${end.getFullYear()}-${end.getMonth()+1}-${end.getDate()}`;}
        if(d==='month'){ const start=new Date(today.getFullYear(),today.getMonth(),1); const end=new Date(today.getFullYear(),today.getMonth()+1,0); filters.date_from=`${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`; filters.date_to=`${end.getFullYear()}-${end.getMonth()+1}-${end.getDate()}`;}
        if(d==='future'){ const start=new Date(today); start.setDate(start.getDate()+1); filters.date_from=`${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`;}
    });

    return filters;
}

// Met à jour les événements
async function updateEvents(){
    const filters = collectFilters();
    const grid = document.getElementById('events-grid');
    try{
        const res = await fetch('/EcoSolveit/api/search_events.php?filters='+encodeURIComponent(JSON.stringify(filters)));
        const data = await res.json();
        grid.innerHTML='';
        if(!data.events || data.events.length===0){
            grid.innerHTML='<div class="col-12"><div class="alert alert-info text-center">Aucun événement trouvé.</div></div>';
            return;
        }
        data.events.forEach(event=>{
            const col=document.createElement('div'); col.className='col-md-6 col-lg-4 mb-4';
            const location=(event.ville+(event.pays?(', '+event.pays):'')).trim()||'Inconnue';
            const event_date=event.event_date?new Date(event.event_date).toLocaleDateString('fr-FR'):'Date non définie';
            col.innerHTML=`<div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${event.event_name}</h5>
                    <p class="card-text flex-grow-1">${event.description}</p>
                    <div class="mt-auto">
                        <p class="mb-1 small text-muted location-text"><i class="bi bi-geo-alt"></i> ${location}</p>
                        <p class="mb-1 small text-muted"><i class="bi bi-calendar"></i> ${event_date}</p>
                        <p class="mb-0 small text-muted"><i class="bi bi-tag"></i> ${event.category_name}</p>
                    </div>
                </div>
            </div>`;
            grid.appendChild(col);
        });
    }catch(err){
        console.error(err);
        grid.innerHTML='<div class="col-12"><div class="alert alert-danger text-center">Erreur lors du chargement.</div></div>';
    }
}

// Initialisation LocationSelector + affichage événements
function initializePage(){
    if(typeof LocationSelector !== "undefined"){
        new LocationSelector("pays","ville",CSC_API_KEY);
    }

    const checkCountriesLoaded = setInterval(()=>{
        const paysSelect = document.getElementById("pays");
        if(paysSelect.options.length>1){ // options chargées
            clearInterval(checkCountriesLoaded);
            updateEvents();
        }
    },100);
}

// Déclenche updateEvents sur changement de filtres
document.querySelectorAll('#filterForm input,#filterForm select').forEach(el=>el.addEventListener('change',updateEvents));

// Lancement initial
window.addEventListener('DOMContentLoaded', initializePage);
</script>

<script src="http://localhost/EcoSolveit/assets/js/location-selector.js"></script>
</body>
</html>
