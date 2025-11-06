<?php
require_once __DIR__ . '/../models/EcoEvent.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../core/database.php';

use Models\EcoEvent;
use Models\Notification;

class EventController
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function createEvent(EcoEvent $event)
    {
        try {
            $data = [
                ':event_name' => $event->getEventName(),
                ':description' => $event->getDescription(),
                ':ville' => $event->getVille(),
                ':pays' => $event->getPays(),
                ':category_id' => $event->getCategoryId(),
                ':user_id' => $event->getUserId(),
                ':event_date' => $event->getEventDate(),
                ':participant_limit' => $event->getParticipantLimit(),
                ':status' => $event->getStatus(),
            ];

            $sql = "INSERT INTO eco_event (
                event_name, description, ville, pays, 
                category_id, user_id, event_date, 
                participant_limit, status
            ) VALUES (
                :event_name, :description, :ville, :pays,
                :category_id, :user_id, :event_date,
                :participant_limit, :status
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);

            return [
                'id' => $this->conn->lastInsertId(),
                'event' => $data
            ];
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAllEvents()
    {
        // Join with category to include category_name for easier use in views
        $query = "SELECT e.*, c.category_name
                  FROM eco_event e
                  LEFT JOIN category c ON e.category_id = c.id
                  ORDER BY e.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteEvent($id)
    {
        $query = "DELETE FROM eco_event WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

   //update event
// 
public function updateEvent(EcoEvent $event)
{
    try {
        $sql = "UPDATE eco_event SET 
                    event_name = :event_name,
                    description = :description,
                    ville = :ville,
                    pays = :pays,
                    category_id = :category_id,
                    event_date = :event_date,
                    participant_limit = :participant_limit,
                    status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        // Gestion de participant_limit null
        $participantLimit = $event->getParticipantLimit();
        if ($participantLimit === '' || $participantLimit === null) {
            $participantLimit = null;
        }

        $stmt->execute([
            ':event_name'        => $event->getEventName(),
            ':description'       => $event->getDescription(),
            ':ville'             => $event->getVille(),
            ':pays'              => $event->getPays(),
            ':category_id'       => $event->getCategoryId(),
            ':event_date'        => $event->getEventDate(),
            ':participant_limit' => $participantLimit,
            ':status'            => $event->getStatus(),
            ':id'                => $event->getId()
        ]);

        return true;

    } catch (PDOException $e) {
        // Lance une exception détaillée pour debugging
        throw new Exception("Erreur lors de la mise à jour de l'événement : " . $e->getMessage());
    }
}


}

// Procedural POST handler so this controller file can accept form submissions directly.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new EventController();

        // Instantiate model and populate via setters
        $event = new EcoEvent();

        // event name: accept 'event_name' or French 'titre'
        $eventName = $_POST['event_name'] ?? $_POST['titre'] ?? null;
        if ($eventName) {
            $event->setEventName($eventName);
        } else {
            throw new Exception('Missing event name');
        }

        $event->setDescription($_POST['description'] ?? null);
        $event->setVille($_POST['ville'] ?? ($_POST['city'] ?? null));
        $event->setPays($_POST['pays'] ?? null);

        // category: prefer numeric category_id, otherwise try to resolve by name (categorie)
        $conn = Database::getInstance()->getConnection();
        $categoryId = null;
        if (!empty($_POST['category_id'])) {
            $categoryId = (int)$_POST['category_id'];
        } elseif (!empty($_POST['categorie'])) {
            $cat = trim($_POST['categorie']);
            // try to find existing category by name
            $stmt = $conn->prepare('SELECT id FROM category WHERE category_name = :name LIMIT 1');
            $stmt->execute([':name' => $cat]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['id'])) {
                $categoryId = (int)$row['id'];
            } else {
                // create the category to use its id
                $ins = $conn->prepare('INSERT INTO category (category_name) VALUES (:name)');
                $ins->execute([':name' => $cat]);
                $categoryId = (int)$conn->lastInsertId();
            }
        } else {
            // fallback default category
            $categoryId = 1;
        }
        $event->setCategoryId($categoryId);

        // user id: validate that user exists in users table
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 1;

        // Check if user exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            // User doesn't exist, try to get first available user
            $stmt = $conn->prepare('SELECT id FROM users ORDER BY id ASC LIMIT 1');
            $stmt->execute();
            $firstUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($firstUser && isset($firstUser['id'])) {
                $userId = (int)$firstUser['id'];
            } else {
                // No users exist, create a default user
                $ins = $conn->prepare('INSERT INTO users (username, email, password, ville, pays, user_type) VALUES (:username, :email, :password, :ville, :pays, :user_type)');
                $ins->execute([
                    ':username' => 'default_user',
                    ':email' => 'default@ecosolve.com',
                    ':password' => password_hash('default123', PASSWORD_BCRYPT),
                    ':ville' => 'Unknown',
                    ':pays' => 'Unknown',
                    ':user_type' => 'user'
                ]);
                $userId = (int)$conn->lastInsertId();
            }
        }

        $event->setUserId($userId);

        // event date: accept event_date or date; fallback to now
        $eventDate = $_POST['event_date'] ?? $_POST['date'] ?? null;
        if (!$eventDate) {
            $eventDate = (new DateTime())->format('Y-m-d H:i:s');
        }
        $event->setEventDate($eventDate);

        // participant limit
        $participantLimit = isset($_POST['participant_limit']) ? (int)$_POST['participant_limit'] : null;
        $event->setParticipantLimit($participantLimit);

        // status
        $event->setStatus($_POST['status'] ?? 'pending');

        // Create event
        $result = $controller->createEvent($event);
// NOUVEAU : Créer notif auto
        $notif = new Notification();
        $notif->setType('event_created');
        $notif->setUserId($userId);
        $notif->setTitle("Votre événement '{$eventName}' a été créé !");
        $notif->setDescription(substr($event->getDescription() ?? '', 0, 100) . '...');
        $notif->setLink("/EcoSolveit/views/FrontOffice/event_detail.php?id=" . $result['id']);

        require_once __DIR__ . '/NotificationController.php';
        $notifController = new NotificationController();
        $notifController->createNotification($notif);

        // Optionnel : Notifs locales
        $localVille = $event->getVille();
        if ($localVille) {
            $localQuery = "INSERT INTO notifications (type, user_id, title, message, link, is_read) 
                           SELECT 'event_created', u.id, :title_local, :msg, :link, 0 
                           FROM users u WHERE u.ville = :ville AND u.id != :user_id LIMIT 5";
            $localStmt = $conn->prepare($localQuery);
            $localStmt->execute([
                ':title_local' => "Nouvelle opportunité à {$localVille} !",
                ':msg' => $notif->getDescription(),
                ':link' => $notif->getLink(),
                ':ville' => $localVille,
                ':user_id' => $userId
            ]);
        }

        // On success, redirect back or output JSON depending on request
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $result['id']]);
            exit;
        }

        // Default redirect back to homepage

        header('Location: /EcoSolveit/index.html');
            //header("Location: Opportunities.php");

        exit;
    } catch (Exception $e) {
        // Simple error output; in production log the error and show friendly message
        http_response_code(500);
        echo 'Error: ' . htmlspecialchars($e->getMessage());
        exit;
    }
}
?>
