<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/core/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/User.php';

class UserC {
    public function afficherUsers(): array {
        try {
            $db = config::getConnexion();
            $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
            $users = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $users[] = new User((int)$row['id'], $row['name'], $row['email']);
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erreur affichage users: ".$e->getMessage());
            return [];
        }
    }

    public function ajouterUser(User $user): bool {
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            return $stmt->execute([
                'name'=>$user->getName(),
                'email'=>$user->getEmail()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout user: ".$e->getMessage());
            return false;
        }
    }

    public function supprimerUser(int $id): bool {
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("DELETE FROM users WHERE id=:id");
            return $stmt->execute(['id'=>$id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression user: ".$e->getMessage());
            return false;
        }
    }

    public function modifierUser(User $user): bool {
        if($user->getId()===null) throw new InvalidArgumentException("ID requis pour modification.");
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("UPDATE users SET name=:name, email=:email WHERE id=:id");
            return $stmt->execute([
                'name'=>$user->getName(),
                'email'=>$user->getEmail(),
                'id'=>$user->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur modification user: ".$e->getMessage());
            return false;
        }
    }
}
?>
