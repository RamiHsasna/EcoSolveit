<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/OpportuniteC.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Opportunite.php';

$opC = new OpportuniteC();

// Ajouter une opportunité
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter"])) {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $opC->ajouterOpportunite(new Opportunite(null, $titre, $description));
    header("Location: opportunites.php"); exit();
}

// Supprimer une opportunité
if (isset($_GET["delete"])) {
    $opC->supprimerOpportunite((int)$_GET["delete"]);
    header("Location: opportunites.php"); exit();
}

// Liste des opportunités
$opportunites = $opC->afficherOpportunites();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Opportunités</title>
    <style>
        body { font-family:'Segoe UI'; background:#f5f7fa; }
        header { background:#005f73; color:white; text-align:center; padding:20px 0; font-size:24px; }
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
        .back-btn { display:inline-block; margin-bottom:20px; background:#005f73; color:white; padding:8px 14px; border-radius:5px; text-decoration:none; }
        .back-btn:hover { background:#0a9396; }
    </style>
</head>
<body>

<header>🎯 Gestion des Opportunités - Admin</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <h2>Ajouter une opportunité</h2>
    <form method="POST">
        <input type="text" name="titre" placeholder="Titre" required>
        <textarea name="description" placeholder="Description..." required></textarea>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des opportunités</h2>
    <table>
        <tr><th>ID</th><th>Titre</th><th>Description</th><th>Actions</th></tr>
        <?php if(!empty($opportunites)): foreach($opportunites as $op): ?>
        <tr>
            <td><?= htmlspecialchars($op->getId()) ?></td>
            <td><?= htmlspecialchars($op->getTitre()) ?></td>
            <td><?= htmlspecialchars($op->getDescription()) ?></td>
            <td>
                <a href="opportunites.php?delete=<?= $op->getId() ?>" class="delete-btn" onclick="return confirm('Supprimer cette opportunité ?')">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="4">Aucune opportunité trouvée.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
