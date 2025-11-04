<?php


namespace Controllers;

require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/User.php';


use Models\User;

class UserController
{
    private \PDO $db;

    public function __construct()
    {
        // Database class in this project is defined in the global namespace
        $this->db = \Database::getInstance()->getConnection();
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, ville, pays, user_type, status, reset_token, token_expire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getVille(),
            $user->getPays(),
            $user->getUserType(),
            $user->getStatus(),
            $user->getResetToken(),
            $user->getTokenExpire()
        ]);
        $user->setId((int)$this->db->lastInsertId());
        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        $user = new User();
        $user->setId((int)$row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setVille($row['ville']);
        $user->setPays($row['pays']);
        $user->setUserType($row['user_type']);
        $user->setStatus($row['status']);
        $user->setCreatedAt($row['created_at']);
        $user->setResetToken($row['reset_token']);
        $user->setTokenExpire($row['token_expire']);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row) return null;

        $user = new User();
        $user->setId((int)$row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setVille($row['ville']);
        $user->setPays($row['pays']);
        $user->setUserType($row['user_type']);
        $user->setStatus($row['status']);
        $user->setCreatedAt($row['created_at']);
        $user->setResetToken($row['reset_token']);
        $user->setTokenExpire($row['token_expire']);

        return $user;
    }

    public function update(User $user): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET username=?, email=?, password=?, ville=?, pays=?, user_type=?, status=?, reset_token=?, token_expire=? WHERE id=?');
        return $stmt->execute([
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getVille(),
            $user->getPays(),
            $user->getUserType(),
            $user->getStatus(),
            $user->getResetToken(),
            $user->getTokenExpire(),
            $user->getId()
        ]);
    }

    // ========== PASSWORD RESET METHODS ==========

    /**
     * Generate and save reset token for user
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);
        if (!$user) return null;

        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user->setResetToken($token);
        $user->setTokenExpire($expire);

        if ($this->update($user)) return $token;
        return null;
    }

    /**
     * Verify if reset token is valid and not expired
     */
    public function verifyResetToken(string $token): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE reset_token = ? AND token_expire > NOW()');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if (!$row) return null;

        $user = new User();
        $user->setId((int)$row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setVille($row['ville']);
        $user->setPays($row['pays']);
        $user->setUserType($row['user_type']);
        $user->setStatus($row['status']);
        $user->setCreatedAt($row['created_at']);
        $user->setResetToken($row['reset_token']);
        $user->setTokenExpire($row['token_expire']);

        return $user;
    }

    /**
     * Reset password using valid token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->verifyResetToken($token);
        if (!$user) return false;

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);
        $user->setResetToken(null);
        $user->setTokenExpire(null);

        return $this->update($user);
    }


    /**
     * Check if email exists in database
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    // ========== FORM HANDLING METHODS ==========

    /**
     * Show forgot password form
     */
    public function showForgotPassword(): void
    {
        include 'views/FrontOffice/forgot_password.php';
    }

    /**
     * Handle forgot password form submission
     */
    public function handleForgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            header('Location: index.php?action=forgot-password');
            exit;
        }

        // Check if email exists
        if (!$this->emailExists($email)) {
            // Don't reveal if email exists or not (security best practice)
            $_SESSION['success'] = 'If your email exists, you will receive a password reset link';
            header('Location: index.php?action=forgot-password');
            exit;
        }

        // Generate reset token
        $token = $this->createPasswordResetToken($email);

        if ($token) {
            $resetLink = "http://localhost/EcoSolveit/index.php?action=reset-password&token=" . $token;

            // TODO: Send email with reset link
            // For now, display the link (for testing purposes)
            $_SESSION['success'] = "Password reset link: <a href='$resetLink' target='_blank'>Click here</a> (Valid for 1 hour)";

            // In production, send actual email:
            // $this->sendResetEmail($email, $resetLink);
        } else {
            $_SESSION['error'] = 'Failed to generate reset token. Please try again.';
        }

        header('Location: index.php?action=forgot-password');
        exit;
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = 'Invalid reset link';
            header('Location: index.php?action=login-page');
            exit;
        }

        // Verify token is valid
        $user = $this->verifyResetToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Invalid or expired reset link';
            header('Location: index.php?action=forgot-password');
            exit;
        }

        include 'views/FrontOffice/reset_password.php';
    }

    /**
     * Handle reset password form submission
     */
    public function handleResetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login-page');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: index.php?action=reset-password&token=' . urlencode($token));
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: index.php?action=reset-password&token=' . urlencode($token));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: index.php?action=reset-password&token=' . urlencode($token));
            exit;
        }

        // Reset password
        if ($this->resetPassword($token, $password)) {
            $_SESSION['success'] = 'Password reset successfully! You can now login.';
            header('Location: index.php?action=login-page');
        } else {
            $_SESSION['error'] = 'Invalid or expired token';
            header('Location: index.php?action=forgot-password');
        }
        exit;
    }

    // Optional: Send email method (you'll need to configure mail server)
    // private function sendResetEmail(string $email, string $resetLink): void
    // {
    //     $subject = "Password Reset - EcoSolve";
    //     $message = "Click the following link to reset your password: " . $resetLink;
    //     $headers = "From: noreply@ecosolve.com";
    //     mail($email, $subject, $message, $headers);
    // }
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = $this->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // Start session
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['user_type'] = $user->getUserType();

                // Redirect to dashboard via index.php action
                header('Location: /EcoSolveit/index.html');
                exit;

                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                header('Location: /EcoSolveit/views/FrontOffice/login.php');
                exit;

                exit;
            }
        } else {
            header('Location: /EcoSolveit/index.html');
            exit;
        }
    }
}
