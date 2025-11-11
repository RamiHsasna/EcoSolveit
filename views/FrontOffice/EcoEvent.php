<?php

use Controllers\CategoryController;

require_once __DIR__ . '/../../controllers/CategoryController.php';
$controller = new CategoryController();

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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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
            box-shadow: 0 0 8px rgba(0, 121, 107, 0.2);
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

        .card-category .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .event-details {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .event-details .location,
        .event-details .date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-details i {
            color: #007bff;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .card-text {
            font-size: 0.9rem;
            line-height: 1.5;
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
                    <?php foreach ($categories as $cat): ?>
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
                    setlocale(LC_TIME, 'fr_FR.UTF-8');
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
        function collectFilters() {
            const filters = {};
            const catChecks = document.querySelectorAll('.cat-check:checked');
            if (catChecks.length) filters.category = Array.from(catChecks).map(cb => parseInt(cb.value));

            const ville = document.getElementById('ville').value;
            if (ville) filters.ville = [ville];

            const pays = document.getElementById('pays').value;
            if (pays) filters.pays = pays;

            // Dates
            const dateChecks = Array.from(document.querySelectorAll('input[name="date[]"]:checked')).map(cb => cb.value);
            const today = new Date();
            dateChecks.forEach(d => {
                if (d === 'today') {
                    filters.date_from = filters.date_to = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`;
                }
                if (d === 'week') {
                    const start = new Date(today);
                    start.setDate(today.getDate() - today.getDay() + 1);
                    const end = new Date(start);
                    end.setDate(start.getDate() + 6);
                    filters.date_from = `${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`;
                    filters.date_to = `${end.getFullYear()}-${end.getMonth()+1}-${end.getDate()}`;
                }
                if (d === 'month') {
                    const start = new Date(today.getFullYear(), today.getMonth(), 1);
                    const end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    filters.date_from = `${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`;
                    filters.date_to = `${end.getFullYear()}-${end.getMonth()+1}-${end.getDate()}`;
                }
                if (d === 'future') {
                    const start = new Date(today);
                    start.setDate(start.getDate() + 1);
                    filters.date_from = `${start.getFullYear()}-${start.getMonth()+1}-${start.getDate()}`;
                }
            });

            return filters;
        }

        // Met à jour les événements
        async function updateEvents() {
            const filters = collectFilters();
            const grid = document.getElementById('events-grid');
            try {
                const res = await fetch('/EcoSolveit/api/search_events.php?filters=' + encodeURIComponent(JSON.stringify(filters)));
                const data = await res.json();
                grid.innerHTML = '';
                if (!data.events || data.events.length === 0) {
                    grid.innerHTML = '<div class="col-12"><div class="alert alert-info text-center">Aucun événement trouvé.</div></div>';
                    return;
                }
                data.events.forEach(event => {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-6 mb-4';
                    const location = (event.ville + (event.pays ? (', ' + event.pays) : '')).trim() || 'Inconnue';
                    const event_date = event.event_date ? new Date(event.event_date).toLocaleDateString('fr-FR') : 'Date non définie';
                    col.innerHTML = `<div class="card h-100" data-aos="fade-up">
                <div class="card-body d-flex flex-column">
                    <div class="card-category mb-2">
                        <span class="badge bg-primary">
                            ${event.category_name || 'Autre'}
                        </span>
                    </div>
                    <h5 class="card-title mb-3">${event.event_name}</h5>
                    <p class="card-text flex-grow-1">${event.description || ''}</p>
                    <div class="event-details mt-3">
                        <div class="location mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                            ${location}
                        </div>
                        <div class="date mb-2">
                            <i class="bi bi-calendar-event-fill"></i>
                            ${event_date}
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" 
                            onclick="participateInEvent('${event.id}')" 
                            id="btn-event-${event.id}">
                        <i class="bi bi-person-plus-fill me-2"></i>Participer
                    </button>
                </div>
            </div>`;
                    grid.appendChild(col);
                });

                // Check participation status for loaded events
                checkParticipationStatus(data.events);
            } catch (err) {
                console.error(err);
                grid.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center">Erreur lors du chargement.</div></div>';
            }
        }

        // Check participation status for each event
        function checkParticipationStatus(events) {
            // Check if user is logged in first
            fetch('/EcoSolveit/api/get_session.php')
                .then(response => response.json())
                .then(sessionData => {
                    if (sessionData.logged_in) {
                        // User is logged in, check each event
                        events.forEach(event => {
                            fetch('/EcoSolveit/api/check_participation.php?event_id=' + event.id)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.is_participating) {
                                        updateParticipationButton(event.id, true);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error checking participation for event:', event.id, error);
                                });
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking session:', error);
                });
        }

        // Initialisation LocationSelector + affichage événements
        function initializePage() {
            if (typeof LocationSelector !== "undefined") {
                new LocationSelector("pays", "ville", CSC_API_KEY);
            }

            const checkCountriesLoaded = setInterval(() => {
                const paysSelect = document.getElementById("pays");
                if (paysSelect.options.length > 1) { // options chargées
                    clearInterval(checkCountriesLoaded);
                    updateEvents();
                }
            }, 100);
        }

        // Déclenche updateEvents sur changement de filtres
        document.querySelectorAll('#filterForm input,#filterForm select').forEach(el => el.addEventListener('change', updateEvents));

        // Lancement initial
        window.addEventListener('DOMContentLoaded', initializePage);
    </script>

    <script src="../../assets/js/location-selector.js"></script>
    <script src="../../assets/js/events.js"></script>
</body>

</html>