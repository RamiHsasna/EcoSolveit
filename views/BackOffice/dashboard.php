<?php
require_once __DIR__ . '/../../core/auth.php';

// Check if user is admin - redirect to login if not
requireAdmin();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #0a9396;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 28px;
        }

        .container {
            width: 80%;
            margin: 40px auto;
            display: flex;
            justify-content: space-around;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background-color: white;
            width: 250px;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            color: #0a9396;
            margin-bottom: 15px;
        }

        .card a {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            background-color: #0a9396;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .card a:hover {
            background-color: #94d2bd;
            color: #001219;
        }
    </style>
</head>

<body>

    <header>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 2rem;">
            <div>
                Tableau de bord Admin - EcoSolveit
            </div>
            <div style="color: #e0f2f1; font-size: 14px;">
                <?php
                $currentUser = getCurrentUser();
                if ($currentUser) {
                    echo "Connecté: " . htmlspecialchars($currentUser['username']) . " (" . htmlspecialchars($currentUser['email']) . ")";
                }
                ?>
                <a href="#"
                    onclick="event.preventDefault(); if(confirm('Êtes-vous sûr de vouloir vous déconnecter?')) { logout(); }"
                    style="margin-left: 15px; color: #e0f2f1; text-decoration: underline; cursor: pointer;">
                    Se déconnecter
                </a>
            </div>
        </div>
    </header>

    <script>
        // Logout function for dashboard
        async function logout() {
            try {
                const response = await fetch('/EcoSolveit/api/logout.php', {
                    method: 'POST',
                    credentials: 'include'
                });

                if (response.ok) {
                    window.location.href = '/EcoSolveit/index.html';
                } else {
                    alert('Erreur lors de la déconnexion');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Erreur lors de la déconnexion');
            }
        }
    </script>

    <div style="text-align:left; margin:20px 20px 20px 40px;">
        <a href="/ecosolveit"
            style="padding:10px 18px; border-radius:6px; background-color:#0a9396; color:white; text-decoration:none; font-weight:bold; box-shadow:0 2px 6px rgba(0,0,0,0.2);">
            Retour à l'accueil
        </a>

    </div>
    <div class="container">
        <div class="card">
            <h3>Catégories</h3>
            <p>Gérer toutes les catégories du site</p>
            <a href="categories.php">Accéder</a>
        </div>

        <div class="card">
            <h3>Utilisateurs</h3>
            <p>Gérer les utilisateurs et leurs droits</p>
            <a href="user.php">Accéder</a>
        </div>

        <div class="card">
            <h3>Opportunités</h3>
            <p>Gérer toutes les opportunités disponibles</p>
            <a href="Opportunities.php">Accéder</a>
        </div>

        <div class="card">
            <h3>Réclamations</h3>
            <p>Gérer les réclamations disponibles</p>
            <a href="reclamations.php">Accéder</a>
        </div>

</body>

</html>