<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/core/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Category.php';

class CategoryC {

    public function afficherCategories(): array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM category ORDER BY id DESC");
            $categories = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $categories[] = new Category(
                    (int)$row['id'], 
                    $row['category_name'], 
                    $row['description'] ?? ''
                );
            }
            return $categories;
        } catch (PDOException $e) {
            error_log("Erreur affichage catégories: ".$e->getMessage());
            return [];
        }
    }

    public function ajouterCategory(Category $category): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO category (category_name, description) VALUES (:category_name, :description)");
            return $stmt->execute([
                'category_name' => $category->getCategoryName(),
                'description' => $category->getDescription()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout catégorie: ".$e->getMessage());
            return false;
        }
    }

    public function supprimerCategory(int $id): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM category WHERE id=:id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression catégorie: ".$e->getMessage());
            return false;
        }
    }

    public function modifierCategory(Category $category): bool {
        if ($category->getId() === null) {
            throw new InvalidArgumentException("ID requis pour modification.");
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE category SET category_name=:category_name, description=:description WHERE id=:id");
            return $stmt->execute([
                'category_name' => $category->getCategoryName(),
                'description' => $category->getDescription(),
                'id' => $category->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur modification catégorie: ".$e->getMessage());
            return false;
        }
    }

    public function getCategoryById(int $id): ?Category {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM category WHERE id=:id");
            $stmt->execute(['id'=>$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Category((int)$row['id'], $row['category_name'], $row['description'] ?? '');
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur fetch catégorie: ".$e->getMessage());
            return null;
        }
    }
}
?>
