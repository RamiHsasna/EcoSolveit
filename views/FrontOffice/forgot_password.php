<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EcoSolve</title>
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
            margin: 0;
        }

        /* FORM CONTAINER */
        .container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            width: 100%;
            max-width: 450px;
            padding: 5rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        /* FORM TITLE */
        .form-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
            color: #2e8b57;
            /* fallback color */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* MESSAGES */
        .message {
            text-align: center;
            margin: 1rem 0;
            font-size: 14px;
            font-weight: 500;
        }

        .message.error {
            color: #e74c3c;
        }

        .message.success {
            color: #2ecc71;
        }

        /* DESCRIPTION TEXT */
        .description {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* INPUT GROUP */
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
            z-index: 1;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            /* Adjusted padding for icon space */
            border: none;
            border-bottom: 2px solid #e0e0e0;
            outline: none;
            font-size: 16px;
            color: #333;
            background: transparent;
            transition: all 0.3s ease;
            border-radius: 0;
            /* Removed border-radius to match bottom-border style */
        }

        .input-group input:focus {
            border-bottom-color: #00a19e;
            box-shadow: 0 2px 8px rgba(0, 161, 158, 0.2);
        }

        .input-group label {
            position: absolute;
            left: 45px;
            /* Align with input padding after icon */
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            background: transparent;
        }

        .input-group input:focus+label,
        .input-group input:valid+label {
            top: -10px;
            left: 15px;
            font-size: 12px;
            color: #00a19e;
            background: rgba(255, 255, 255, 0.8);
            padding: 0 5px;
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

        .btn:hover,
        .btn:focus {
            background: linear-gradient(135deg, #2e8b57, #228b22, #006400);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 161, 158, 0.4);
            outline: none;
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

        .btn-link:hover,
        .btn-link:focus {
            color: #228b22;
            text-decoration: underline;
            outline: none;
        }

        /* GENERAL PARAGRAPHS (fallback) */
        p {
            font-size: 14px;
            color: #666;
        }

        /* RESPONSIVE */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                margin: 10px;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .input-group input,
            .input-group label {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="form-title">Forgot Password</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p style='text-align:center;margin:1rem;color:red;'>
                <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p style='text-align:center;margin:1rem;color:green;'>
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <p style="text-align:center; color:#666; margin-bottom:20px; font-size:14px;">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        <form method="POST" action="../../controllers/UserController.php?action=forgot-password">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
                <label for="email">Email</label>
            </div>
            <button type="submit" name="reset" class="btn">Send Reset Link</button>
        </form>

        <div class="links">
            <p>Remember your password?</p>
            <a href="login.php" class="btn-link">Back to Login</a>
        </div>
    </div>
</body>

</html>