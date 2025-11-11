<?php

namespace Controllers;

use Models\Category;
use PDO;
use PDOException;
use InvalidArgumentException;

require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \Database::getInstance()->getConnection();
    }

    // Récupérer toutes les catégories
    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM category ORDER BY id DESC");
            $categories = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                // Si description est NULL, mettre une chaîne vide
                $description = $row['description'] ?? '';
                $categories[] = new Category(
                    (int)$row['id'],
                    $row['category_name'],
                    $description
                );
            }
            return $categories;
        } catch (PDOException $e) {
            error_log("Erreur affichage catégories : " . $e->getMessage());
            return [];
        }
    }
    public function getCategories()
    {
        $stmt = $this->db->prepare("SELECT id, category_name FROM category ORDER BY category_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une catégorie
    public function create(Category $category): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO category (category_name, description) 
                 VALUES (:category_name, :description)"
            );
            return $stmt->execute([
                ':category_name' => $category->getCategoryName(),
                ':description' => $category->getDescription() ?: ''
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout catégorie : " . $e->getMessage());
            return false;
        }
    }

    // Modifier une catégorie
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE category SET category_name=:category_name, description=:description WHERE id=:id";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':category_name', $data[':category_name']);
            $stmt->bindParam(':description', $data[':description']);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur modification catégorie : " . $e->getMessage());
            return false;
        }
    }

    // Supprimer une catégorie
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM category WHERE id=:id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression catégorie : " . $e->getMessage());
            return false;
        }
    }

    // Chercher une catégorie par ID
    public function findById(int $id): ?Category
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM category WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            $description = $row['description'] ?? '';
            return new Category(
                (int)$row['id'],
                $row['category_name'],
                $description
            );
        } catch (PDOException $e) {
            error_log("Erreur fetch catégorie : " . $e->getMessage());
            return null;
        }
    }
}
