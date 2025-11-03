<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/UserController.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/User.php';

use Controllers\UserController;
use Models\User;

$userC = new UserController();

// Modifier un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $editUser = new User(
        (int)$_POST['id'],
        $_POST['username'],
        $_POST['email'],
        $_POST['role'],
        $_POST['password'] ?? null
    );
    $userC->update($editUser);
    header("Location: user.php");
    exit();
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $userC->delete((int)$_GET['delete']);
    header("Location: user.php");
    exit();
}

// Liste des utilisateurs
$users = $userC->findAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des Utilisateurs</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f7fa; margin:0; padding:0; }
header { background:#0a9396; color:white; text-align:center; padding:20px; font-size:24px; }
.container { width:80%; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; text-align:center; }
th, td { border:1px solid #ccc; padding:8px; }
th { background:#0a9396; color:white; }
a.delete-btn { background:#ae2012; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#9b2226; }
a.edit-btn { background:#005f73; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.edit-btn:hover { background:#0a9396; }
#form-modifier { display:none; margin-bottom:20px; }
input, button { padding:10px; border-radius:5px; border:1px solid #ccc; }
button { background:#0a9396; color:white; border:none; cursor:pointer; }
button:hover { background:#94d2bd; color:#001219; }
.back-btn { display:inline-block; margin-bottom:20px; background:#0a9396; color:white; padding:8px 14px; border-radius:5px; text-decoration:none; }
.back-btn:hover { background:#0a9396; }
</style>
<script>
function toggleForm(id, username, email, role, password='') {
    const form = document.getElementById('form-modifier');
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-username').value = username;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role;
    document.getElementById('edit-password').value = password;
    form.style.display = 'block';
}
</script>
</head>
<body>
<header>Gestion des Utilisateurs</header>
<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <!-- Formulaire Modifier -->
    <div id="form-modifier">
        <form method="POST">
            <input type="hidden" id="edit-id" name="id">
            <input type="text" id="edit-username" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" id="edit-email" name="email" placeholder="Email" required>
            <input type="text" id="edit-role" name="role" placeholder="Rôle" required>
            <input type="password" id="edit-password" name="password" placeholder="Mot de passe">
            <button type="submit" name="modifier">Modifier</button>
        </form>
    </div>

    <h2>Liste des utilisateurs</h2>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr>
        <?php foreach($users as $u): ?>
        <tr>
            <td><?= $u->getId() ?></td>
            <td><?= htmlspecialchars($u->getUsername()) ?></td>
            <td><?= htmlspecialchars($u->getEmail()) ?></td>
            <td><?= htmlspecialchars($u->getRole()) ?></td>
            <td>
                <a href="javascript:void(0)" class="edit-btn" 
                   onclick='toggleForm(
                       <?= $u->getId() ?>, 
                       <?= json_encode($u->getUsername()) ?>, 
                       <?= json_encode($u->getEmail()) ?>, 
                       <?= json_encode($u->getRole()) ?>,
                       ""
                   )'>Modifier</a>
                <a href="user.php?delete=<?= $u->getId() ?>" class="delete-btn" 
                   onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
