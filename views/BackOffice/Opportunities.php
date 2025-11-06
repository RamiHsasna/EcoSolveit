<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/EventController.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/EcoEvent.php';

$eventController = new EventController();

// Liste des événements
$opportunites = $eventController->getAllEvents();

// Supprimer un événement
if (isset($_GET["delete"])) {
    $eventController->deleteEvent((int)$_GET["delete"]);
    header("Location: Opportunities.php");
    exit();
}

// Préparer la modification
$editEvent = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    foreach ($opportunites as $e) {
        if ($e['id'] === $id) {
            $editEvent = $e;
            break;
        }
    }
}

// Modifier après soumission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["modifier"])) {
    $event = new EcoEvent();
    $event->setId((int)$_POST['id']);
    $event->setEventName($_POST['event_name']);
    $event->setDescription($_POST['description']);
    $event->setVille($_POST['ville']);
    $event->setPays($_POST['pays']);
    $event->setCategoryId($_POST['category_id']);
    $event->setEventDate($_POST['event_date']);
    $event->setParticipantLimit($_POST['participant_limit'] ?? null);
    $event->setStatus($_POST['status']);

    $eventController->updateEvent($event);

    header("Location: Opportunities.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des Opportunités</title>
<style>
    body { font-family:'Segoe UI'; background:#f5f7fa; }
    header { background:#0a9396; color:white; text-align:center; padding:20px 0; font-size:24px; }
    .container { width:80%; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    form { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; }
    input, textarea, button { padding:10px; border-radius:5px; border:1px solid #ccc; }
    button { background:#0a9396; color:white; border:none; cursor:pointer; }
    button:hover { background:#94d2bd; color:#001219; }
    table { width:100%; border-collapse:collapse; text-align:center; }
    th, td { border:1px solid #ccc; padding:8px; }
    th { background:#0a9396; color:white; }
    a.delete-btn { background:#ae2012; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
    a.delete-btn:hover { background:#9b2226; }
    a.modify-btn { background:#005f73; color:white; padding:5px 10px; border-radius:5px; margin-right:5px; text-decoration:none; }
    a.modify-btn:hover { background:#0a9396; }
    .back-btn { display:inline-block; margin-bottom:20px; background:#0a9396; color:white; padding:8px 14px; border-radius:5px; text-decoration:none; }
    .back-btn:hover { background:#0a9396; }
</style>
</head>
<body>

<header>Gestion des Opportunités - Admin</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <?php if($editEvent): ?>
    <h2>Modifier l'événement #<?= $editEvent['id'] ?></h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editEvent['id'] ?>">
        <input type="text" name="event_name" placeholder="Nom de l'événement" required value="<?= htmlspecialchars($editEvent['event_name']) ?>">
        <textarea name="description" placeholder="Description..." required><?= htmlspecialchars($editEvent['description']) ?></textarea>
        <input type="text" name="ville" placeholder="Ville" required value="<?= htmlspecialchars($editEvent['ville']) ?>">
        <input type="text" name="pays" placeholder="Pays" required value="<?= htmlspecialchars($editEvent['pays']) ?>">
        <input type="number" name="category_id" placeholder="Catégorie ID" required value="<?= htmlspecialchars($editEvent['category_id']) ?>">
        <input type="date" name="event_date" required value="<?= $editEvent['event_date'] ?>">
        <input type="number" name="participant_limit" placeholder="Participants" value="<?= htmlspecialchars($editEvent['participant_limit'] ?? '') ?>">
        <input type="text" name="status" placeholder="Statut" required value="<?= htmlspecialchars($editEvent['status']) ?>">
        <button type="submit" name="modifier">Modifier</button>
    </form>
    <?php endif; ?>

    <h2>Liste des opportunités</h2>
    <table>
        <tr>
            <th>ID</th><th>Nom</th><th>Description</th><th>Ville</th><th>Pays</th><th>Catégorie</th><th>Date</th><th>Participants</th><th>Status</th><th>Actions</th>
        </tr>
        <?php if(!empty($opportunites)): foreach($opportunites as $e): ?>
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
                <a href="?edit=<?= $e['id'] ?>" class="modify-btn">✏ Modifier</a>
                <a href="?delete=<?= $e['id'] ?>" class="delete-btn" onclick="return confirm('Supprimer cet événement ?')">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="10">Aucune opportunité trouvée.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
