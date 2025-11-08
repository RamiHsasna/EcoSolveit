<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/controllers/UserController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/EcoSolveit/models/User.php';

use Controllers\UserController;

$userC = new UserController();

// Liste des utilisateurs
$users = $userC->findAll();

// Modifier un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = (int)$_POST['user_id'];
    $updateData = [
        ':username' => $_POST['username'],
        ':email' => $_POST['email'],
        ':user_type' => $_POST['type']
    ];

    // Only include password in update if it's not empty
    if (!empty($_POST['password'])) {
        $updateData[':password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    } else {
        $updateData[':password'] = null;
    }

    try {
        $userC->update($id, $updateData);
        header("Location: user.php?success=1");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $userC->delete((int)$_GET['delete']);
    header("Location: user.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background: #0a9396;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #0a9396;
            color: white;
        }

        .btn-edit {
            background: #0a9396;
            color: white;
            border: none;
            margin-right: 5px;
        }

        .btn-edit:hover {
            background: #008b88;
            color: white;
        }

        .btn-delete {
            background: #ae2012;
            color: white;
            border: none;
        }

        .btn-delete:hover {
            background: #9b2226;
            color: white;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background: #0a9396;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #008b88;
            color: white;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            // Populate the modal with user data
            document.getElementById('user_id').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('type').value = user.type;
            document.getElementById('password').value = '';

            // Show the modal
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</head>

<body>
    <header>Gestion des Utilisateurs</header>
    <div class="container">
        <a href="dashboard.php" class="back-btn">⬅ Retour au tableau de bord</a>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                L'utilisateur a été mis à jour avec succès!
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <h2>Liste des utilisateurs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u->getId() ?></td>
                    <td><?= htmlspecialchars($u->getUsername()) ?></td>
                    <td><?= htmlspecialchars($u->getEmail()) ?></td>
                    <td><?= htmlspecialchars($u->getUserType()) ?></td>
                    <td>
                        <button class="btn btn-sm btn-edit" onclick='editUser(<?= json_encode([
                                                                                    "id" => $u->getId(),
                                                                                    "username" => $u->getUsername(),
                                                                                    "email" => $u->getEmail(),
                                                                                    "type" => $u->getUserType()
                                                                                ]) ?>)'>
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <a href="user.php?delete=<?= $u->getId() ?>" class="btn btn-sm btn-delete"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                            <i class="bi bi-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="">
                        <input type="hidden" name="update_user" value="1">
                        <input type="hidden" name="user_id" id="user_id">

                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('editForm').submit()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>