<?php
require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../core/database.php';

use Models\Reclamation;

session_start();


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
                ':user_name' => $reclamation->getUserName(),
                ':email' => $reclamation->getEmail(),
                ':subject' => $reclamation->getSubject(),
                ':message' => $reclamation->getMessage(),
                ':statut' => $reclamation->getStatut(),
                ':date_reclamation' => $reclamation->getDateReclamation(),
            ];

            $sql = "INSERT INTO reclamation (
                user_id, user_name, email, subject, message, statut, date_reclamation
            ) VALUES (
                :user_id, :user_name, :email, :subject, :message, :statut, :date_reclamation
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

    public function editReclamationStatus(int $id, string $newStatus)
{
    try {
        $sql = "UPDATE reclamation SET statut = :statut WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':statut', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
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
    // Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Make sure user is logged in
    if (!isset($_SESSION['user_id'])) {
        die(" Veuillez vous connecter pour soumettre une réclamation");
    }

    $reclamation = new Reclamation();
    $reclamation->setUserId($_SESSION['user_id']);
    $reclamation->setUserName($_POST['name'] ?? ''); // Default to empty string if 'name' is missing
    $reclamation->setEmail($_POST['email'] ?? ''); // Default to empty string if 'email' is missing
    $reclamation->setSubject($_POST['subject'] ?? ''); // Default to empty string if 'subject' is missing
    $reclamation->setMessage($_POST['message'] ?? ''); // Default to empty string if 'message' is missing
    $reclamation->setStatut($_POST['statut'] ?? 'pending'); // Default to 'pending'
    $reclamation->setDateReclamation(date('Y-m-d H:i:s')); // current timestamp

    $controller = new ReclamationController();

    try {
        $result = $controller->createReclamation($reclamation);
        echo "Votre message a été envoyé. Merci !";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

