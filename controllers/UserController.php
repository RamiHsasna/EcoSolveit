<?php

namespace Controllers;


require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';



use Models\User;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \Database::getInstance()->getConnection();
    }

    // Récupérer tous les utilisateurs
    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
            $users = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                // si 'role' est absent ou NULL, on met 'user' par défaut
                $role = $row['role'] ?? 'user';

                $users[] = new User(
                    (int)$row['id'],
                    $row['username'],
                    $row['email'],
                    $role,
                    $row['password'] ?? null
                );
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erreur affichage utilisateurs : " . $e->getMessage());
            return [];
        }
    }

    // Ajouter un utilisateur
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
    public function update($id, $data): bool
    {
        try {
            // Build the SQL query dynamically based on whether password is included
            $sql = "UPDATE users SET username=:username, email=:email, role=:role";
            if (isset($data[':password']) && $data[':password'] !== null) {
                $sql .= ", password=:password";
            }
            $sql .= " WHERE id=:id";

            $stmt = $this->db->prepare($sql);

            // Bind the basic parameters
            $stmt->bindParam(':username', $data[':username']);
            $stmt->bindParam(':email', $data[':email']);
            $stmt->bindParam(':role', $data[':role']);
            $stmt->bindParam(':id', $id);

            // Only bind password if it's included in the update
            if (isset($data[':password']) && $data[':password'] !== null) {
                $stmt->bindParam(':password', $data[':password']);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur modification utilisateur : " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un utilisateur
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id=:id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression utilisateur : " . $e->getMessage());
            return false;
        }
    }

    // Chercher un utilisateur par ID

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return $this->mapRowToUser($row);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->mapRowToUser($row);
    }

    private function mapRowToUser(array $row): User
    {
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

    // ========== PASSWORD RESET ==========

    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);
        if (!$user) return null;

        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user->setResetToken($token);
        $user->setTokenExpire($expire);

        return $this->update($user) ? $token : null;
    }

    public function verifyResetToken(string $token): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE reset_token = ? AND token_expire > NOW()');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ? $this->mapRowToUser($row) : null;
    }

    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->verifyResetToken($token);
        if (!$user) return false;

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->setPassword($hashed);
        $user->setResetToken(null);
        $user->setTokenExpire(null);

        return $this->update($user);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    // ========== FORGOT PASSWORD ==========

    public function handleForgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
            exit;
        }

        if (!$this->emailExists($email)) {
            $_SESSION['success'] = 'If your email exists, you will receive a password reset link.';
            header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
            exit;
        }

        $token = $this->createPasswordResetToken($email);
        if ($token) {
            $resetLink = "http://localhost/EcoSolveit/views/FrontOffice/reset_password.php?token=" . $token;

            // Send via PHPMailer
            $this->sendResetEmail($email, $resetLink);

            $_SESSION['success'] = 'Password reset link sent! Check your email.';
        } else {
            $_SESSION['error'] = 'Failed to generate reset token. Please try again.';
        }

        header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
        exit;
    }

    private function sendResetEmail(string $email, string $resetLink): void
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'khoiadjatesnim@gmail.com'; // <-- change this
            $mail->Password = 'dhwp edjc xnht wxyo'; // <-- use Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('khoiadjatesnim@gmail.com', 'EcoSolveit Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password (valid for 1 hour):<br><a href='$resetLink'>$resetLink</a>";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }
    }

    // ========== RESET PASSWORD FORM HANDLING ==========

    public function handleResetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirm)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /EcoSolveit/views/FrontOffice/reset_password.php?token=' . urlencode($token));
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /EcoSolveit/views/FrontOffice/reset_password.php?token=' . urlencode($token));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: /EcoSolveit/views/FrontOffice/reset_password.php?token=' . urlencode($token));
            exit;
        }

        if ($this->resetPassword($token, $password)) {
            $_SESSION['success'] = 'Password reset successfully! You can now login.';
            header('Location: /EcoSolveit/views/FrontOffice/login.php');
        } else {
            $_SESSION['error'] = 'Invalid or expired token';
            header('Location: /EcoSolveit/views/FrontOffice/forgot_password.php');
        }
        exit;
    }

    // ========== LOGIN ==========

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $user = $this->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['user_type'] = $user->getUserType();
                header('Location: /EcoSolveit/index.html');
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                header('Location: /EcoSolveit/views/FrontOffice/login.php');
            }
            exit;
        }
        header('Location: /EcoSolveit/views/FrontOffice/login.php');
        exit;
    }
}

// Only for direct web access
// Only run this when accessed directly (not included)
if (basename($_SERVER['PHP_SELF']) === 'UserController.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $controller = new \Controllers\UserController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'forgot-password':
            $controller->handleForgotPassword();
            break;
        case 'reset-password':
            $controller->handleResetPassword();
            break;
        default:
            echo "Invalid action";
            break;
    }
}
