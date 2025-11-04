<?php
require_once __DIR__ . '/../../core/database.php';
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="http://localhost/EcoSolveit/assets/css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #2e8b57 0%, #00a19e 50%, #228b22 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #00a19e, #2e8b57, #228b22);
            color: #2e8b57;
            /* fallback color */
            -webkit-background-clip: text;
            /* Safari/Chrome */
            -webkit-text-fill-color: transparent;
            /* Safari/Chrome */
            background-clip: text;
            /* Standard property for other browsers */
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-bottom: 2px solid #e0e0e0;
            outline: none;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
            background: transparent;
        }

        input:focus {
            border-bottom: 2px solid #00a19e;
            box-shadow: 0 2px 8px rgba(0, 161, 158, 0.2);
        }

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

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

        .recover {
            text-align: center;
            color: #228b22;
            margin-top: 1rem;
        }

        .recover a {
            text-decoration: none;
            color: #00a19e;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .recover a:hover {
            color: #228b22;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="form-title">Reset Your Password</h2>

        <?php if (!empty($message)) echo "<p class='recover'>$message</p>"; ?>

        <?php if (isset($user)) : ?>
            <form method="POST">
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="New Password" required>
                    <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
                </div>

                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <span class="eye-icon" onclick="togglePassword('confirm_password')">👁️</span>
                </div>

                <button type="submit" name="update" class="btn">Update Password</button>
            </form>

            <p class="recover">
            <div style="text-align:center; margin-top:10px;">
                <a href="http://localhost/EcoSolveit/views/FrontOffice/login.php"
                    style="color:#007bff; text-decoration:none; font-size:14px;">
                    ← Back to Login
                </a>
            </div>
            </p>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        document.querySelector("form")?.addEventListener("submit", (e) => {
            const pass = document.getElementById("password").value;
            const confirm = document.getElementById("confirm_password").value;
            if (pass !== confirm) {
                e.preventDefault();
                alert("Passwords do not match!");
            }
        });
    </script>
</body>

</html>