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
            background-color: #005f73;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: white;
            width: 250px;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #001219;
            font-size: 18px;
            font-weight: bold;
        }

        .card:hover {
            background-color: #0a9396;
            color: white;
        }
    </style>
</head>
<body>

<header>Tableau de bord Admin</header>

<div class="container">
    <a href="categories.php" class="card">🗂 Gestion des Catégories</a>
    <a href="users.php" class="card">👥 Gestion des Utilisateurs</a>
    <a href="opportunites.php" class="card">🎯 Gestion des Opportunités</a>
</div>

</body>
</html>
