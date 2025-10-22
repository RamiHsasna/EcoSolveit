<?php

namespace Controllers;

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
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, ville, pays, user_type, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getVille(),
            $user->getPays(),
            $user->getUserType(),
            $user->getStatus(),
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
        return $user;
    }

    // more controller methods (update, delete, findByEmail) can be added here
}
