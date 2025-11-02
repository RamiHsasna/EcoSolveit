<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/core/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Opportunite.php';

class OpportuniteC {

    public function afficherOpportunites(): array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM opportunite ORDER BY id DESC");
            $opps = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $opps[] = new Opportunite(
                    (int)$row['id'],
                    $row['title'],
                    $row['description'],
                    $row['date']
                );
            }
            return $opps;
        } catch (PDOException $e) {
            error_log("Erreur affichage opportunités: " . $e->getMessage());
            return [];
        }
    }

    public function ajouterOpportunite(Opportunite $opp): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO opportunite (title, description, date) VALUES (:title, :description, :date)");
            return $stmt->execute([
                'title' => $opp->getTitle(),
                'description' => $opp->getDescription(),
                'date' => $opp->getDate()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout opportunité: " . $e->getMessage());
            return false;
        }
    }

    public function supprimerOpportunite(int $id): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM opportunite WHERE id=:id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression opportunité: " . $e->getMessage());
            return false;
        }
    }

    public function modifierOpportunite(Opportunite $opp): bool {
        if ($opp->getId() === null) {
            throw new InvalidArgumentException("ID requis pour modification.");
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                "UPDATE opportunite 
                 SET title=:title, description=:description, date=:date 
                 WHERE id=:id"
            );
            return $stmt->execute([
                'title' => $opp->getTitle(),
                'description' => $opp->getDescription(),
                'date' => $opp->getDate(),
                'id' => $opp->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur modification opportunité: " . $e->getMessage());
            return false;
        }
    }
}
?>
