<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/controllers/EventController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/models/EcoEvent.php';


$eventController = new EventController();

// Liste des événements
$opportunites = $eventController->getAllEvents();

// Supprimer un événement
if (isset($_GET["delete"])) {
    $eventController->deleteEvent((int)$_GET["delete"]);
    header("Location: Opportunities.php");
    exit();
}

// Handle update submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_event"])) {
    $id = (int)$_POST['event_id'];
    $updateData = [
        ':event_name' => $_POST['event_name'],
        'description' => $_POST['description'],
        'ville' => $_POST['ville'],
        'pays' => $_POST['pays'],
        'event_date' => $_POST['event_date'],
        'status' => $_POST['status']
    ];

    try {
        $eventController->updateEvent($id, $updateData);
        header("Location: Opportunities.php?success=1");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Opportunités</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI';
            background: #f5f7fa;
        }

        header {
            background: #0a9396;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        input,
        textarea,
        button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: #0a9396;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #94d2bd;
            color: #001219;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #0a9396;
            color: white;
        }

        a.delete-btn {
            background: #ae2012;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        a.delete-btn:hover {
            background: #9b2226;
        }

        a.modify-btn {
            background: #005f73;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            text-decoration: none;
        }

        a.modify-btn:hover {
            background: #0a9396;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background: #0a9396;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #0a9396;
        }
    </style>
</head>

<body>

    <header>Gestion des Opportunités - Admin</header>

    <div class="container">
        <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

        <h2>Liste des opportunités</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Participants</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($opportunites)): foreach ($opportunites as $e): ?>
                    <tr>
                        <td><?= $e['id'] ?></td>
                        <td><?= htmlspecialchars($e['event_name']) ?></td>
                        <td><?= htmlspecialchars($e['description']) ?></td>
                        <td><?= htmlspecialchars($e['ville']) ?></td>
                        <td><?= htmlspecialchars($e['pays']) ?></td>
                        <td><?= htmlspecialchars($e['category_id']) ?></td>
                        <td><?= htmlspecialchars($e['event_date']) ?></td>
                        <td><?= htmlspecialchars($e['participant_limit'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($e['status']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick='editEvent(<?php echo json_encode($e); ?>)'>
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                            <a href="?delete=<?= $e['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="10">Aucune opportunité trouvée.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier l'événement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="">
                        <input type="hidden" name="update_event" value="1">
                        <input type="hidden" name="event_id" id="event_id">

                        <div class="mb-3">
                            <label for="event_name" class="form-label">Nom de l'événement</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="pays" name="pays" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Date de l'événement</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending">En attente</option>
                                    <option value="in_progress">Actif</option>
                                    <option value="completed">Terminé</option>
                                    <option value="cancelled">Annulé</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('editForm').submit()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editEvent(event) {
            // Populate the modal with event data
            document.getElementById('event_id').value = event.id;
            document.getElementById('event_name').value = event.event_name;
            document.getElementById('description').value = event.description;
            document.getElementById('ville').value = event.ville;
            document.getElementById('pays').value = event.pays;
            document.getElementById('event_date').value = event.event_date.split(' ')[0]; // Get only the date part
            document.getElementById('status').value = event.status;

            // Show the modal
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>

</html>