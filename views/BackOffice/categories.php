<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/controllers/CategoryController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/models/Category.php';

use Controllers\CategoryController;

$categoryC = new CategoryController();

// Liste des catégories
$categories = $categoryC->findAll();

// Supprimer une catégorie
if (isset($_GET['delete'])) {
    $categoryC->delete((int)$_GET['delete']);
    header("Location: category.php");
    exit();
}

// Ajouter ou modifier une catégorie
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id'] ? (int)$_POST['id'] : null;
    $categoryC->update($id, [
        ':category_name' => $_POST['category_name'],
        ':description' => $_POST['description']
    ]);
    header("Location: category.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des Catégories</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; margin:0; padding:0; }
    header { background: #0a9396; color: white; text-align: center; padding: 20px; font-size: 24px; }
    .container { width: 80%; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; text-align: center; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #0a9396; color: white; }
    .btn-edit { background: #0a9396; color: white; border: none; margin-right: 5px; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
    .btn-edit:hover { background: #008b88; }
    .btn-delete { background: #ae2012; color: white; border: none; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
    .btn-delete:hover { background: #9b2226; }
    .back-btn { display: inline-block; margin-bottom: 20px; background: #0a9396; color: white; padding: 8px 14px; border-radius: 5px; text-decoration: none; }
    .back-btn:hover { background: #008b88; }
    .success-message { background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCategory(cat) {
    document.getElementById('category_id').value = cat.id;
    document.getElementById('category_name').value = cat.category_name;
    document.getElementById('category_description').value = cat.description;
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
</head>
<body>
<header>Gestion des Catégories</header>
<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <?php if(isset($_GET['success'])): ?>
        <div class="success-message">Opération effectuée avec succès ✅</div>
    <?php endif; ?>

    <!-- Bouton Ajouter catégorie -->
    <button class="btn btn-edit mb-3" style="float:right;" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-plus-lg"></i> Ajouter une catégorie
    </button>

    <h2>Liste des catégories</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($categories)): foreach($categories as $cat): ?>
            <tr>
                <td><?= $cat->getId() ?></td>
                <td><?= htmlspecialchars($cat->getCategoryName()) ?></td>
                <td><?= htmlspecialchars($cat->getDescription()) ?></td>
                <td>
                    <button class="btn-edit btn-sm"
                        onclick='editCategory(<?= json_encode([
                            "id"=>$cat->getId(),
                            "category_name"=>$cat->getCategoryName(),
                            "description"=>$cat->getDescription()
                        ]) ?>)'>
                        <i class="bi bi-pencil"></i> Modifier
                    </button>
                    <a href="category.php?delete=<?= $cat->getId() ?>" class="btn-delete btn-sm"
                       onclick="return confirm('Supprimer cette catégorie ?')">
                       <i class="bi bi-trash"></i> Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4">Aucune catégorie trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Ajout/Modification -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Catégorie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="category_id">

          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="category_name" id="category_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" id="category_description" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-edit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
