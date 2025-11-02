<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/UserC.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/User.php';

$userC = new UserC();

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $newUser = new User(
        null,
        $_POST['username'],
        $_POST['email'],
        $_POST['password']
    );
    $userC->ajouterUser($newUser);
    header("Location: user.php");
    exit();
}

// Modifier un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $editUser = new User(
        (int)$_POST['id'],
        $_POST['username'],
        $_POST['email'],
        $_POST['password']
    );
    $userC->modifierUser($editUser);
    header("Location: user.php");
    exit();
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $userC->supprimerUser((int)$_GET['delete']);
    header("Location: user.php");
    exit();
}

// Liste des utilisateurs
$users = $userC->afficherUsers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des Utilisateurs</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f7fa; padding:0; margin:0; }
header { background:#005f73; color:white; text-align:center; padding:20px; font-size:24px; }
.container { width:80%; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
form { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; }
input, button { padding:10px; border-radius:5px; border:1px solid #ccc; }
button { background:#0a9396; color:white; border:none; cursor:pointer; }
button:hover { background:#94d2bd; color:#001219; }
table { width:100%; border-collapse:collapse; text-align:center; }
th, td { border:1px solid #ccc; padding:8px; }
th { background:#0a9396; color:white; }
a.delete-btn { background:#ae2012; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#9b2226; }
a.edit-btn { background:#005f73; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.edit-btn:hover { background:#0a9396; }
#form-ajout, #form-modifier { display:none; margin-bottom:20px; }
</style>
<script>
function toggleForm(id=null, username='', email='', password='') {
    const formAdd = document.getElementById('form-ajout');
    const formEdit = document.getElementById('form-modifier');
    if(id) {
        // remplir formulaire de modification
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-password').value = password;
        formEdit.style.display = 'block';
        formAdd.style.display = 'none';
    } else {
        formAdd.style.display = formAdd.style.display === 'none' ? 'block' : 'none';
        formEdit.style.display = 'none';
    }
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
        <input type="password" id="edit-password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="modifier">Modifier</button>
    </form>
</div>

<h2>Liste des utilisateurs</h2>
<table>
<tr><th>ID</th><th>Nom</th><th>Email</th><th>Actions</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= $u->getId() ?></td>
<td><?= htmlspecialchars($u->getUsername()) ?></td>
<td><?= htmlspecialchars($u->getEmail()) ?></td>
<td>
    <a href="javascript:void(0)" class="edit-btn" onclick="toggleForm(<?= $u->getId() ?>, '<?= htmlspecialchars($u->getUsername()) ?>', '<?= htmlspecialchars($u->getEmail()) ?>', '')">Modifier</a>
    <a href="user.php?delete=<?= $u->getId() ?>" class="delete-btn" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>