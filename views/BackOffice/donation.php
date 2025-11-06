<?php
// donation.php

// Handle form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';

    if ($name && $email && $amount && is_numeric($amount) && $paymentMethod) {
        // Ici tu peux enregistrer les infos dans la base ou lancer un paiement
        $successMessage = "Merci, $name ! Votre don de $amount € via $paymentMethod a été reçu.";
    } else {
        $errorMessage = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Faire un Don - EcoSolveit</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f7fa; margin:0; padding:0; }
header { background:#0a9396; color:white; text-align:center; padding:20px; font-size:24px; }
.container { width:90%; max-width:600px; margin:40px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
h2 { text-align:center; margin-bottom:20px; }
form { display:flex; flex-direction:column; gap:15px; }
input[type=text], input[type=email], input[type=number], select { padding:12px; border-radius:6px; border:1px solid #ccc; font-size:16px; }
button { padding:12px; border-radius:6px; background-color:#0a9396; color:white; font-weight:bold; border:none; cursor:pointer; font-size:16px; transition:0.3s; }
button:hover { background-color:#94d2bd; color:#001219; }
.message { padding:12px; border-radius:6px; margin-bottom:20px; font-weight:bold; text-align:center; }
.success { background-color:#d1fae5; color:#065f46; }
.error { background-color:#fee2e2; color:#991b1b; }
.back-btn { display:inline-block; margin-top:20px; color:#0a9396; text-decoration:none; font-weight:bold; padding:8px 14px; border-radius:5px; background:#e0f2f1; transition:0.3s; }
.back-btn:hover { background:#94d2bd; color:#001219; }
</style>
</head>
<body>
<header>Faire un Don</header>

<div class="container">
    <h2>Contribuez à EcoSolveit</h2>

    <?php if($successMessage): ?>
        <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif($errorMessage): ?>
        <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre email" required>
        <input type="number" name="amount" placeholder="Montant (€)" min="1" required>
        <select name="payment_method" required>
            <option value="">Choisissez un mode de paiement</option>
            <option value="Chèque">Chèque</option>
            <option value="Carte">Carte</option>
            <option value="Espèces">Espèces</option>
        </select>
        <button type="submit">Faire un Don</button>
    </form>

    <a href="/EcoSolveit/index.html" class="back-btn">⬅ Retour à l'accueil</a>
</div>
</body>
</html>
