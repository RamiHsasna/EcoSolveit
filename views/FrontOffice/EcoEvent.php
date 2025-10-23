<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Événements Écologiques Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #e9f5ec, #f6fbf7);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #00a19e;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .event-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            color: #00a19e;
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
            color: #00a19e !important;
        }
    </style>
</head>

<body>
    <h1>Événements Écologiques Disponibles</h1>
    <div class="event-container">
        <?php
        // Resolve controller path reliably from this view's directory
        $controllerPath = __DIR__ . '/../../controllers/EventController.php';

        if (!file_exists($controllerPath)) {
            // Helpful error message for developers instead of a PHP fatal with unclear path
            echo '<div class="alert alert-danger">Controller not found: ' . htmlspecialchars($controllerPath) . '</div>';
            // Stop processing this view to avoid calling undefined classes
            return;
        }

        require_once $controllerPath;

        $controller = new EventController();
        $events = $controller->getAllEvents();

        if (empty($events)) {
            echo '<p class="text-center">Aucun événement disponible pour le moment.</p>';
        } else {
            echo '<div class="row">';
            foreach ($events as $event) {
                // Safe extraction with fallbacks to avoid undefined index notices
                $event_name = htmlspecialchars($event['event_name'] ?? 'Nom non défini');
                $description = htmlspecialchars($event['description'] ?? '');
                $ville = $event['ville'] ?? '';
                $pays = $event['pays'] ?? '';
                $location = trim($ville . ($pays ? ', ' . $pays : '')) ?: 'Localisation non définie';
                $location = htmlspecialchars($location);

                if (!empty($event['event_date'])) {
                    $timestamp = strtotime($event['event_date']);
                    $event_date = $timestamp ? date('Y-m-d', $timestamp) : htmlspecialchars($event['event_date']);
                } else {
                    $event_date = 'Date non définie';
                }

                $category = htmlspecialchars($event['category_name'] ?? 'Autre');

                echo '<div class="col-md-3 mb-4">';
                echo '<div class="card h-100">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h5 class="card-title">' . $event_name . '</h5>';
                echo '<p class="card-text flex-grow-1">' . $description . '</p>';
                echo '<div class="mt-auto">';
                echo '<p class="mb-1"><small class="text-muted location-text"><i class="bi bi-geo-alt"></i> ' . $location . '</small></p>';
                echo '<p class="mb-1"><small class="text-muted"><i class="bi bi-calendar"></i> ' . $event_date . '</small></p>';
                echo '<p class="mb-0"><small class="text-muted"><i class="bi bi-tag"></i> ' . $category . '</small></p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>