<?php
require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../core/database.php';

use Models\Reclamation;


class ReclamationController
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function createReclamation(Reclamation $reclamation)
    {
        try {
            $data = [
                ':user_id' => $reclamation->getUserId(),
                ':titre' => $reclamation->getTitre(),
                ':description' => $reclamation->getDescription(),
                ':type' => $reclamation->getType(),
                ':statut' => $reclamation->getStatut(),
                ':date_reclamation' => $reclamation->getDateReclamation(),
            ];

            $sql = "INSERT INTO reclamation (
                user_id, titre, description, 
                type, statut, date_reclamation
            ) VALUES (
                :user_id, :titre, :description, 
                :type, :statut, :date_reclamation
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);

            return [
                'id' => $this->conn->lastInsertId(),
                'reclamation' => $data
            ];
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAllReclamations()
    {
        try {
            $query = "SELECT * FROM reclamation ORDER BY date_reclamation DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getReclamationById(int $id)
    {
        try {
            $query = "SELECT * FROM reclamation WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function editReclamation(Reclamation $reclamation)
    {
        try {
            $data = [
                ':id' => $reclamation->getId(),
                ':user_id' => $reclamation->getUserId(),
                ':titre' => $reclamation->getTitre(),
                ':description' => $reclamation->getDescription(),
                ':type' => $reclamation->getType(),
                ':statut' => $reclamation->getStatut(),
                ':date_reclamation' => $reclamation->getDateReclamation(),
            ];

            $sql = "UPDATE reclamation SET
                user_id = :user_id,
                titre = :titre,
                description = :description,
                type = :type,
                statut = :statut,
                date_reclamation = :date_reclamation
                WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function deleteReclamation(int $id)
    {
        try {
            $query = "DELETE FROM reclamation WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
