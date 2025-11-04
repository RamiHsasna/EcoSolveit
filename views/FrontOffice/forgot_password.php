<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EcoSolve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/EcoSolveit/assets/css/auth.css">
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

        <form method="POST" action="index.php?action=forgot-password-submit">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
                <label for="email">Email</label>
            </div>
            <button type="submit" name="reset" class="btn">Send Reset Link</button>
        </form>

        <div class="links">
            <p>Remember your password?</p>
            <a href="index.php?action=login-page" class="btn-link">Back to Login</a>
        </div>
    </div>
</body>

</html>