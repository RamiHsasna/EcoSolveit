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
<style>
/* ===== GLOBAL STYLES ===== */
body {
    background: linear-gradient(135deg, #2e8b57, #00a19e, #228b22);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
}

/* ===== FORM CONTAINER ===== */
.container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    max-width: 450px;
    width: 100%;
    padding: 2rem 2.5rem;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    text-align: center;
    transition: all 0.3s ease;
}

.container:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

/* ===== FORM TITLE ===== */
.form-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ===== INPUT GROUP ===== */
.input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.input-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 1.1rem;
    z-index: 1;
}

.input-group input {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: none;
    border-bottom: 2px solid #e0e0e0;
    outline: none;
    font-size: 16px;
    color: #333;
    background: transparent;
    border-radius: 5px 5px 0 0;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

.input-group input:focus {
    border-bottom: 2px solid #00a19e;
    box-shadow: 0 2px 8px rgba(0,161,158,0.2);
}

/* ===== FLOATING LABELS ===== */
.input-group label {
    position: absolute;
    left: 45px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    pointer-events: none;
    transition: all 0.3s ease;
    font-size: 16px;
    background: rgba(255,255,255,0.95);
    padding: 0 4px;
}

.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label {
    top: -10px;
    left: 12px;
    font-size: 12px;
    color: #00a19e;
}

/* ===== BUTTON ===== */
.btn {
    width: 100%;
    padding: 14px 0;
    border-radius: 50px;
    border: none;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #fff;
    background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,161,158,0.3);
}

.btn:hover {
    background: linear-gradient(135deg, #2e8b57, #228b22, #006400);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,161,158,0.4);
}

/* ===== LINKS ===== */
.links {
    margin-top: 1.2rem;
}

.links p {
    margin-bottom: 0.5rem;
    color: #228b22;
}

.btn-link {
    color: #00a19e;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-link:hover {
    color: #228b22;
    text-decoration: underline;
}

/* ===== MESSAGES ===== */
.message {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 1rem;
    text-align: center;
}

.message.error {
    color: #e74c3c;
    background: rgba(231,76,60,0.1);
    border: 1px solid #e74c3c;
}

.message.success {
    color: #27ae60;
    background: rgba(39,174,96,0.1);
    border: 1px solid #27ae60;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 480px) {
    .container {
        padding: 1.5rem 2rem;
    }
    
    .form-title {
        font-size: 1.6rem;
    }

    .input-group input {
        font-size: 15px;
    }

    .btn {
        font-size: 1rem;
        padding: 12px 0;
    }
}


    </style>
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
        <form method="post" action="login.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <p class="recover">
                <a href="forgot_password.php">Forgot Password?</a>
            </p>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
            <p>Don't have an account yet?</p>
            <a href="register.php" class="btn-link">Sign Up</a>
        </div>
    </div>

    <script src="/EcoSolveit/assets/js/main.js"></script>
</body>

</html>