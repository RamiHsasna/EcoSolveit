<?php
require_once __DIR__ . '/../models/EcoEvent.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../core/database.php';

use Models\EcoEvent;
use Models\Notification;

class EventController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \Database::getInstance()->getConnection();
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

            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            return [
                'id' => $this->db->lastInsertId(),
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
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteEvent($id)
    {
        $query = "DELETE FROM eco_event WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    //update event
    // 
    public function updateEvent($id, $data)
    {
        try {
            $sql = "UPDATE eco_event SET 
                    event_name = :event_name,
                    description = :description,
                    ville = :ville,
                    pays = :pays,
                    event_date = :event_date,
                    status = :status
                WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':event_name', $data[':event_name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':ville', $data['ville']);
            $stmt->bindParam(':pays', $data['pays']);
            $stmt->bindParam(':event_date', $data['event_date']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Lance une exception détaillée pour debugging
            throw new Exception("Erreur lors de la mise à jour de l'événement : " . $e->getMessage());
        }
    }

    // Récupère tous les événements filtrés pour l'API
    public function getFilteredEvents($filters = [])
    {
        $sql = "SELECT e.*, c.category_name
            FROM eco_event e
            LEFT JOIN category c ON e.category_id = c.id
            WHERE 1=1";
        $params = [];

        // Filtre catégories
        if (!empty($filters['category'])) {
            $placeholders = implode(',', array_fill(0, count($filters['category']), '?'));
            $sql .= " AND e.category_id IN ($placeholders)";
            $params = array_merge($params, $filters['category']);
        }

        // Filtre ville
        if (!empty($filters['ville'])) {
            $placeholders = implode(',', array_fill(0, count($filters['ville']), '?'));
            $sql .= " AND e.ville IN ($placeholders)";
            $params = array_merge($params, $filters['ville']);
        }

        // Filtre pays
        if (!empty($filters['pays'])) {
            $sql .= " AND e.pays = ?";
            $params[] = $filters['pays'];
        }

        // Filtre dates
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(e.event_date) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(e.event_date) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY e.event_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les catégories
    public function getCategories()
    {
        $stmt = $this->db->prepare("SELECT id, category_name FROM category ORDER BY category_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les villes
    public function getVilles()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT ville FROM eco_event ORDER BY ville");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Procedural POST handler so this controller file can accept form submissions directly.
if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['ajouter'])) {
    // Start session to access user authentication
    session_start();

    try {
        // First, check if user is authenticated
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // User not logged in - return error
            http_response_code(401);
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Authentication required',
                    'redirect' => '/EcoSolveit/views/FrontOffice/login.php'
                ]);
            } else {
                // Redirect to login page for HTML form submissions
                header('Location: /EcoSolveit/views/FrontOffice/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            }
            exit;
        }

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
        $db = Database::getInstance()->getConnection();
        $categoryId = null;
        if (!empty($_POST['category_id'])) {
            $categoryId = (int)$_POST['category_id'];
        } elseif (!empty($_POST['categorie'])) {
            $cat = trim($_POST['categorie']);
            // try to find existing category by name
            $stmt = $db->prepare('SELECT id FROM category WHERE category_name = :name LIMIT 1');
            $stmt->execute([':name' => $cat]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['id'])) {
                $categoryId = (int)$row['id'];
            } else {
                // create the category to use its id
                $ins = $db->prepare('INSERT INTO category (category_name) VALUES (:name)');
                $ins->execute([':name' => $cat]);
                $categoryId = (int)$db->lastInsertId();
            }
        } else {
            // fallback default category
            $categoryId = 1;
        }
        $event->setCategoryId($categoryId);

        // Get user id from session (authenticated user)
        $userId = intval($_SESSION['user_id']);

        // Verify that the session user still exists in the database
        $stmt = $db->prepare('SELECT id FROM users WHERE id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            // Session user doesn't exist in database - session is invalid
            session_destroy();
            http_response_code(401);
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid session - please login again',
                    'redirect' => '/EcoSolveit/views/FrontOffice/login.php'
                ]);
            } else {
                header('Location: /EcoSolveit/views/FrontOffice/login.php');
            }
            exit;
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
        $notif->setTitle("Un nouveau evenement '{$eventName}' a été créé !");
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
            $localStmt = $db->prepare($localQuery);
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
        // header("Location: ");

        exit;
    } catch (Exception $e) {
        // Simple error output; in production log the error and show friendly message
        http_response_code(500);
        echo 'Error: ' . htmlspecialchars($e->getMessage());
        exit;
    }
}
