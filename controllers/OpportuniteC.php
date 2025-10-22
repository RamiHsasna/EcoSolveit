<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/core/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/EcoSolveit/models/Opportunite.php';

class OpportuniteC {
    public function afficherOpportunites(): array {
        try {
            $db = config::getConnexion();
            $stmt = $db->query("SELECT * FROM opportunites ORDER BY id DESC");
            $opportunites = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $opportunites[] = new Opportunite((int)$row['id'], $row['titre'], $row['description']);
            }
            return $opportunites;
        } catch (PDOException $e) {
            error_log("Erreur affichage opportunites: ".$e->getMessage());
            return [];
        }
    }

    public function ajouterOpportunite(Opportunite $op): bool {
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("INSERT INTO opportunites (titre, description) VALUES (:titre, :description)");
            return $stmt->execute([
                'titre'=>$op->getTitre(),
                'description'=>$op->getDescription()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout opportunite: ".$e->getMessage());
            return false;
        }
    }

    public function supprimerOpportunite(int $id): bool {
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("DELETE FROM opportunites WHERE id=:id");
            return $stmt->execute(['id'=>$id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression opportunite: ".$e->getMessage());
            return false;
        }
    }

    public function modifierOpportunite(Opportunite $op): bool {
        if($op->getId()===null) throw new InvalidArgumentException("ID requis pour modification.");
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("UPDATE opportunites SET titre=:titre, description=:description WHERE id=:id");
            return $stmt->execute([
                'titre'=>$op->getTitre(),
                'description'=>$op->getDescription(),
                'id'=>$op->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur modification opportunite: ".$e->getMessage());
            return false;
        }
    }
}
?>
