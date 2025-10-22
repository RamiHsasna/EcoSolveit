<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/CategoryC.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Category.php';

$categoryC = new CategoryC();

// Ajout d'une catégorie
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter"])) {
    $category_name = $_POST["category_name"];
    $description = $_POST["description"];
    $categoryC->ajouterCategory(new Category(null, $category_name, $description));
    header("Location: categories.php"); exit();
}

// Suppression
if (isset($_GET["delete"])) {
    $categoryC->supprimerCategory((int)$_GET["delete"]);
    header("Location: categories.php"); exit();
}

// Liste des catégories
$categories = $categoryC->afficherCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories</title>
    <style>
        /* Styles similaires au dashboard, tableau propre */
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

<header>🗂 Gestion des Catégories - Admin</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <h2>Ajouter une catégorie</h2>
    <form method="POST">
        <input type="text" name="category_name" placeholder="Nom de la catégorie" required>
        <textarea name="description" placeholder="Description..." required></textarea>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des catégories</h2>
    <table>
        <tr>
            <th>ID</th><th>Nom</th><th>Description</th><th>Actions</th>
        </tr>
        <?php if(!empty($categories)): foreach($categories as $cat): ?>
        <tr>
            <td><?= htmlspecialchars($cat->getId()) ?></td>
            <td><?= htmlspecialchars($cat->getCategoryName()) ?></td>
            <td><?= htmlspecialchars($cat->getDescription()) ?></td>
            <td>
                <a href="categories.php?delete=<?= $cat->getId() ?>" class="delete-btn" onclick="return confirm('Supprimer cette catégorie ?')">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="4">Aucune catégorie trouvée.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
