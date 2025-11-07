<?php
session_start();

require_once __DIR__ . '/../../controllers/UserController.php';

use Controllers\UserController;
use Models\User;

$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    $user = new User();
    $user->setUsername($_POST['username']);
    $user->setEmail($_POST['email']);
    $user->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
    $user->setVille($_POST['ville'] ?? '');
    $user->setPays($_POST['pays'] ?? '');

    try {
        $userController->create($user);
        $_SESSION['success'] = 'Registration successful! You can now login.';
        header('Location: /EcoSolveit/views/FrontOffice/register.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header('Location: /EcoSolveit/views/FrontOffice/register.php');
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EcoSolve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/EcoSolveit/assets/css/main.css">
</head>

<body>


    <!-- SIGNUP FORM -->
    <div class="container" id="signup">
        <h1 class="form-title">Register</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color:green; text-align:center; margin-bottom:10px;">
                <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <form method="post" action="/EcoSolveit/views/FrontOffice/register.php">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
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
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="ville" id="ville" placeholder="Ville">
                <label for="ville">Ville</label>
            </div>
            <div class="input-group">
                <i class="fas fa-flag"></i>
                <input type="text" name="pays" id="pays" placeholder="Pays">
                <label for="pays">Pays</label>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
            <p>Already have an account?</p>
            <a href="/EcoSolveit/views/FrontOffice/login.php" class="btn-link">Sign In</a>
        </div>
    </div>

    <script src="/EcoSolveit/assets/js/main.js"></script>
</body>

</html>