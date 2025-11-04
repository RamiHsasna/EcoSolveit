<?php
include __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance()->getConnection();

$message = "";
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expire > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (isset($_POST['update'])) {
            $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password=?, reset_token=NULL, token_expire=NULL WHERE id=?")
                ->execute([$newPass, $user['id']]);
            $message = "✅ Password updated successfully!";
        }
    } else {
        $message = "❌ Invalid or expired token.";
    }
} else {
    $message = "❌ No token provided.";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (!empty($message)) echo "<p style='text-align:center;'>$message</p>"; ?>
        <?php if (isset($user)) : ?>
            <form method="POST">
                <div class="input-group">
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>
                <button type="submit" name="update" class="btn">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>