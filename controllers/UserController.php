<?php

namespace Controllers;

use Models\User;
use PDO;
use PDOException;
use InvalidArgumentException;

require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/User.php';

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
    public function create(User $user): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, role, password) 
                 VALUES (:username, :email, :role, :password)"
            );
            return $stmt->execute([
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':role' => $user->getRole() ?: 'user',
                ':password' => $user->getPassword()
                    ? password_hash($user->getPassword(), PASSWORD_BCRYPT)
                    : null
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout utilisateur : " . $e->getMessage());
            return false;
        }
    }

    // Modifier un utilisateur
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

        $role = $row['role'] ?? 'user';

        return new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $role,
            $row['password'] ?? null
        );
    }
}
