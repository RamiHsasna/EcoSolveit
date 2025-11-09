<?php
session_start();

require_once __DIR__ . '/../../controllers/UserController.php';

use Controllers\UserController;
use Models\User;

$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    // Basic server-side validation
    $errors = [];
    if (empty($_POST['username'])) {
        $errors[] = 'Username is required.';
    }
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        $user = new User();
        $user->setUsername(trim($_POST['username']));
        $user->setEmail(trim($_POST['email']));
        $user->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
        $user->setVille(trim($_POST['ville'] ?? ''));
        $user->setPays(trim($_POST['pays'] ?? ''));

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
    } else {
        $_SESSION['error'] = implode(' ', $errors);
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
    <link
        href="../../assets/vendor/bootstrap/css/bootstrap.min.css"
        rel="stylesheet" />
    <link
        href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css"
        rel="stylesheet" />
    <link href="../../assets/vendor/aos/aos.css" rel="stylesheet" />
    <link
        href="../../assets/vendor/glightbox/css/glightbox.min.css"
        rel="stylesheet" />
    <link href="../../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        /* GLOBAL STYLES */
        body {
            background: linear-gradient(135deg, #2e8b57 0%, #00a19e 50%, #228b22 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* FORM CONTAINER */
        .container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        /* FORM TITLE */
        .form-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
            color: #2e8b57;
            /* fallback color */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* INPUT GROUP WITH FLOATING LABELS */
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .input-group label {
            position: absolute;
            left: 45px;
            /* Offset for icon */
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            pointer-events: none;
            transition: all 0.3s ease;
            font-size: 16px;
            background: transparent;
            z-index: 1;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            /* Adjusted for icon + label space */
            border: none;
            border-bottom: 2px solid #e0e0e0;
            outline: none;
            font-size: 16px;
            color: #333;
            background: transparent;
            transition: all 0.3s ease;
            border-radius: 5px 5px 0 0;
            box-sizing: border-box;
        }

        .input-group input:focus {
            border-bottom: 2px solid #00a19e;
            box-shadow: 0 2px 8px rgba(0, 161, 158, 0.2);
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -10px;
            left: 15px;
            font-size: 12px;
            color: #00a19e;
            background: rgba(255, 255, 255, 0.98);
            padding: 0 5px;
        }

        .input-group input:invalid:focus+label {
            color: #e74c3c;
        }

        /* Hide placeholder when label is floating */
        .input-group input:focus::placeholder,
        .input-group input:not(:placeholder-shown)::placeholder {
            opacity: 0;
        }

        /* BUTTON */
        .btn {
            font-size: 1.1rem;
            padding: 15px 30px;
            border-radius: 50px;
            border: none;
            width: 100%;
            background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 161, 158, 0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #2e8b57, #228b22, #006400);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 161, 158, 0.4);
        }

        /* LINKS */
        .links {
            text-align: center;
            margin-top: 1rem;
        }

        .links p {
            margin-bottom: 0.5rem;
            color: #228b22;
        }

        .btn-link {
            color: #00a19e;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-link:hover {
            color: #228b22;
            text-decoration: underline;
        }

        /* MESSAGES */
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
        }

        .success {
            color: #27ae60;
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid #27ae60;
        }

        /* RESPONSIVE */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- SIGNUP FORM -->
    <div class="container" id="signup">
        <h1 class="form-title">Register</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <div class="input-group">
                <input type="text" name="username" id="username" placeholder=" " required minlength="3">
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder=" " required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder=" " required minlength="6">
                <i class="fas fa-lock"></i>
            </div>
            <div class="mb-3">
                <label for="pays" class="form-label">Pays</label>
                <select
                    name="pays"
                    id="pays"
                    class="form-select location-select"
                    required>
                    <option value="">Sélectionnez un pays</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ville" class="form-label">Ville</label>
                <select
                    name="ville"
                    id="ville"
                    class="form-select location-select"
                    required
                    disabled>
                    <option value="">Sélectionnez d'abord un pays</option>
                </select>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
            <p>Already have an account?</p>
            <a href="login.php" class="btn-link">Sign In</a>
        </div>
    </div>

    <script src="/EcoSolveit/assets/js/main.js"></script>
    <script src="../../assets/js/location-selector.js"></script>
</body>

</html>