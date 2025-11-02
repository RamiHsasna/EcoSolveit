<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/core/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/User.php';

class UserC {

    public function afficherUsers(): array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM user ORDER BY id DESC");
            $users = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $users[] = new User(
                    (int)$row['id'],
                    $row['username'],
                    $row['email'],
                    $row['role']
                );
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erreur affichage utilisateurs: " . $e->getMessage());
            return [];
        }
    }

    public function ajouterUser(User $user): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO user (username, email, role) VALUES (:username, :email, :role)");
            return $stmt->execute([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public function supprimerUser(int $id): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM user WHERE id=:id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public function modifierUser(User $user): bool {
        if ($user->getId() === null) {
            throw new InvalidArgumentException("ID requis pour modification.");
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                "UPDATE user 
                 SET username=:username, email=:email, role=:role 
                 WHERE id=:id"
            );
            return $stmt->execute([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'id' => $user->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur modification utilisateur: " . $e->getMessage());
            return false;
        }
    }
}
?>
