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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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

<header>Tableau de bord Admin - EcoSolveit</header>
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