<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/OpportuniteC.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Opportunite.php';

$oppC = new OpportuniteC();

// Ajouter une opportunité
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ajouter'])) {
    $newOpp = new Opportunite(
        null,
        $_POST['title'],
        $_POST['description'] ?? '',
        $_POST['date']
    );
    $oppC->ajouterOpportunite($newOpp);
    header("Location: Opportunities.php");
    exit();
}

// Supprimer une opportunité
if (isset($_GET['delete'])) {
    $oppC->supprimerOpportunite((int)$_GET['delete']);
    header("Location: Opportunities.php");
    exit();
}

// Liste des opportunités
$opportunites = $oppC->afficherOpportunites();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des Opportunités</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f7fa; padding:0; margin:0; }
header { background:#005f73; color:white; text-align:center; padding:20px; font-size:24px; }
.container { width:80%; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.add-btn { background:#0a9396; color:white; padding:8px 15px; border-radius:5px; text-decoration:none; display:inline-block; margin-bottom:15px; }
.add-btn:hover { background:#94d2bd; color:#001219; }
form { display:none; flex-direction:column; gap:10px; margin-bottom:20px; }
input, textarea, button { padding:10px; border-radius:5px; border:1px solid #ccc; font-size:16px; }
textarea { height:60px; resize: vertical; }
button { background:#0a9396; color:white; border:none; cursor:pointer; }
button:hover { background:#94d2bd; color:#001219; }
table { width:100%; border-collapse:collapse; text-align:center; }
th, td { border:1px solid #ccc; padding:8px; }
th { background:#0a9396; color:white; }
a.modify-btn { background:#005f73; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; margin-right:5px; }
a.modify-btn:hover { background:#0a9396; }
a.delete-btn { background:#ae2012; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#9b2226; }
</style>
<script>
function toggleForm() {
    const form = document.getElementById('addForm');
    form.style.display = (form.style.display === 'none' ? 'flex' : 'none');
}
</script>
</head>
<body>
<header>Gestion des Opportunités</header>
<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>


<h2>Liste des opportunités</h2>
<table>
<tr><th>ID</th><th>Titre</th><th>Description</th><th>Date</th><th>Actions</th></tr>
<?php foreach($opportunites as $o): ?>
<tr>
<td><?= $o->getId() ?></td>
<td><?= htmlspecialchars($o->getTitle()) ?></td>
<td><?= htmlspecialchars($o->getDescription()) ?></td>
<td><?= htmlspecialchars($o->getDate()) ?></td>
<td>
    <a href="Opportunities.php?edit=<?= $o->getId() ?>" class="modify-btn">Modifier</a>
    <a href="Opportunities.php?delete=<?= $o->getId() ?>" class="delete-btn" onclick="return confirm('Supprimer ?')">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>