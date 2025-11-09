<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/controllers/ReclamationController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/models/Reclamation.php';

$controller = new ReclamationController();
$reclamations = $controller->getAllReclamations();

// Changer le statut d'une réclamation
if (isset($_POST['reclamation_id']) && isset($_POST['statut'])) {
    $id = (int)$_POST['reclamation_id'];
    $newStatus = $_POST['statut'];
    $controller->editReclamationStatus($id, $newStatus); // Met à jour uniquement le statut
    header("Location: Reclamations.php");
    exit();
}

// Supprimer une réclamation
if (isset($_GET['delete'])) {
    $controller->deleteReclamation((int)$_GET['delete']);
    header("Location: Reclamations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Réclamations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI'; background: #f5f7fa; }
        header { background: #0a9396; color: white; text-align: center; padding: 20px 0; font-size: 24px; }
        .container { width: 80%; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; text-align: center; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #0a9396; color: white; }
        .back-btn { display: inline-block; margin-bottom: 20px; background: #0a9396; color: white; padding: 8px 14px; border-radius: 5px; text-decoration: none; }
        .back-btn:hover { background: #94d2bd; color: #0a9396; }
        select { width: 120px; padding: 5px; border-radius: 5px; border: 1px solid #ccc; }
        .btn-delete { background: #ae2012; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .btn-delete:hover { background: #9b2226; }
    </style>
</head>
<body>

<header>Gestion des Réclamations - Admin</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <h2>Liste des réclamations</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Sujet</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <tbody>
        <?php if (!empty($reclamations)): ?>
            <?php foreach ($reclamations as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['user_name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['subject']) ?></td>
                    <td><?= htmlspecialchars($r['message']) ?></td>
                    <td><?= htmlspecialchars($r['date_reclamation']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="reclamation_id" value="<?= $r['id'] ?>">
                            <select name="statut" onchange="this.form.submit()">
                                <option value="pending" <?= $r['statut']=='pending'?'selected':'' ?>>En attente</option>
                                <option value="accepte" <?= $r['statut']=='accepte'?'selected':'' ?>>Accepté</option>
                                <option value="refuse" <?= $r['statut']=='refuse'?'selected':'' ?>>Refusé</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="?delete=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8">Aucune réclamation trouvée.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
