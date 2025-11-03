<?php
// Inclure le EventController et EcoEvent
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/EventController.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/EcoEvent.php';

// Initialiser le controller
$eventController = new EventController();

// Supprimer une opportunité si demandé
if(isset($_GET['delete'])) {
    $eventController->deleteEvent((int)$_GET['delete']);
    header("Location: Opportunities.php");
    exit();
}

// Modifier une opportunité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $editEvent = new EcoEvent(
        (int)$_POST['id'],
        $_POST['event_name'],
        $_POST['description'],
        $_POST['ville'],
        $_POST['pays'],
        $_POST['category_name'],
        $_POST['event_date']
    );
    $eventController->updateEvent($editEvent);
    header("Location: Opportunities.php");
    exit();
}

// Récupérer toutes les opportunités
$opportunites = $eventController->getAllEvents();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Backoffice - Opportunités</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f7fa; padding:0; margin:0; }
header { background:#0a9396; color:white; text-align:center; padding:20px; font-size:24px; }
.container { width:90%; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; text-align:center; }
th, td { border:1px solid #ccc; padding:8px; }
th { background:#0a9396; color:white; }
a.delete-btn { background:#ae2012; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#9b2226; }
a.edit-btn { background:#005f73; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.edit-btn:hover { background:#0a9396; }
#form-modifier { display:none; margin-bottom:20px; background:#e0f2f1; padding:15px; border-radius:8px; }
input, textarea, button { padding:8px; border-radius:5px; border:1px solid #ccc; margin:5px 0; width:100%; box-sizing:border-box; }
button { background:#0a9396; color:white; border:none; cursor:pointer; }
button:hover { background:#94d2bd; color:#001219; }
.back-btn { display:inline-block; margin-bottom:20px; background:#0a9396; color:white; padding:8px 14px; border-radius:5px; text-decoration:none; }
.back-btn:hover { background:#0a9396; }
</style>
<script>
function toggleForm(id, name, description, ville, pays, category, date) {
    const form = document.getElementById('form-modifier');
    if (!form) return;

    document.getElementById('edit-id').value = id;
    document.getElementById('edit-event_name').value = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-ville').value = ville;
    document.getElementById('edit-pays').value = pays;
    document.getElementById('edit-category_name').value = category;
    document.getElementById('edit-event_date').value = date;

    form.style.display = 'block';
    form.scrollIntoView({behavior: "smooth"});
}
</script>
</head>
<body>
<header>Backoffice - Opportunités</header>
<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <!-- Formulaire Modifier -->
    <div id="form-modifier">
        <h3>Modifier l'opportunité</h3>
        <form method="POST">
            <input type="hidden" id="edit-id" name="id">
            <input type="text" id="edit-event_name" name="event_name" placeholder="Nom de l'événement" required>
            <textarea id="edit-description" name="description" placeholder="Description" required></textarea>
            <input type="text" id="edit-ville" name="ville" placeholder="Ville" required>
            <input type="text" id="edit-pays" name="pays" placeholder="Pays" required>
            <input type="text" id="edit-category_name" name="category_name" placeholder="Catégorie">
            <input type="date" id="edit-event_date" name="event_date" required>
            <button type="submit" name="modifier">Modifier</button>
        </form>
    </div>

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
            <th>Actions</th>
        </tr>
        <?php foreach($opportunites as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['event_name']) ?></td>
            <td><?= htmlspecialchars($o['description']) ?></td>
            <td><?= htmlspecialchars($o['ville']) ?></td>
            <td><?= htmlspecialchars($o['pays']) ?></td>
            <td><?= htmlspecialchars($o['category_name'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($o['event_date']) ?></td>
            <td>
                <a href="javascript:void(0)" class="edit-btn"
                   onclick='toggleForm(
                       <?= json_encode($o['id']) ?>,
                       <?= json_encode($o['event_name']) ?>,
                       <?= json_encode($o['description']) ?>,
                       <?= json_encode($o['ville']) ?>,
                       <?= json_encode($o['pays']) ?>,
                       <?= json_encode($o['category_name'] ?? "") ?>,
                       <?= json_encode($o['event_date']) ?>
                   )'>Modifier</a>
                <a href="Opportunities.php?delete=<?= $o['id'] ?>" class="delete-btn" onclick="return confirm('Supprimer cette opportunité ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
