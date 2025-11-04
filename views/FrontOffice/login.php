<?php
session_start();
require_once __DIR__ . '/../../controllers/UserController.php';

use Controllers\UserController;

$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->login();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - EcoSolve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/EcoSolveit/assets/css/main.css">

</head>

<body>

    <?php if (isset($_SESSION['error'])): ?>
        <p style='text-align:center;margin:1rem;color:red;'><?php echo htmlspecialchars($_SESSION['error']);
                                                            unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p style='text-align:center;margin:1rem;color:green;'><?php echo htmlspecialchars($_SESSION['success']);
                                                                unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <!-- SIGNIN FORM -->
    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="/EcoSolveit/views/FrontOffice/login.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <p class="recover">
                <a href="/EcoSolveit/views/FrontOffice/forgot_password.php">Forgot Password?</a>
            </p>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
            <p>Don't have an account yet?</p>
            <a href="http://localhost/EcoSolveit/views/FrontOffice/register.php" class="btn-link">Sign Up</a>
        </div>
    </div>

    <script src="/EcoSolveit/assets/js/main.js"></script>
</body>

</html>