<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/controllers/UserC.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/User.php';

$userC = new UserC();

// Ajouter un utilisateur
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $userC->ajouterUser(new User(null, $name, $email));
    header("Location: users.php"); exit();
}

// Supprimer un utilisateur
if (isset($_GET["delete"])) {
    $userC->supprimerUser((int)$_GET["delete"]);
    header("Location: users.php"); exit();
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
        body { font-family:'Segoe UI'; background:#f5f7fa; }
        header { background:#005f73; color:white; text-align:center; padding:20px 0; font-size:24px; }
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
        .back-btn { display:inline-block; margin-bottom:20px; background:#005f73; color:white; padding:8px 14px; border-radius:5px; text-decoration:none; }
        .back-btn:hover { background:#0a9396; }
    </style>
</head>
<body>

<header>👥 Gestion des Utilisateurs - Admin</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

    <h2>Ajouter un utilisateur</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des utilisateurs</h2>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Actions</th></tr>
        <?php if(!empty($users)): foreach($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user->getId()) ?></td>
            <td><?= htmlspecialchars($user->getName()) ?></td>
            <td><?= htmlspecialchars($user->getEmail()) ?></td>
            <td>
                <a href="users.php?delete=<?= $user->getId() ?>" class="delete-btn" onclick="return confirm('Supprimer cet utilisateur ?')">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="4">Aucun utilisateur trouvé.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
