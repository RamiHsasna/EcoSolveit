<?php
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../core/database.php';

use Models\Notification;

class NotificationController
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function createNotification(Notification $notification)
    {
        try {
            $data = [
                ':type' => $notification->getType(),
                ':user_id' => $notification->getUserId(),
                ':title' => $notification->getTitle(),
                ':message' => $notification->getMessage(),
                ':link' => $notification->getLink(),
                ':is_read' => $notification->isRead() ? 1 : 0,
            ];

            $sql = "INSERT INTO notifications (type, user_id, title, message, link, is_read) VALUES (:type, :user_id, :title, :message, :link, :is_read)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);

            return ['id' => $this->conn->lastInsertId()];
        } catch (PDOException $e) {
            throw new Exception("DB error: " . $e->getMessage());
        }
    }

    public function getNotificationsByUser($userId, $limit = 20)
    {
        $unreadQuery = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = :user_id AND is_read = 0";
        $unreadStmt = $this->conn->prepare($unreadQuery);
        $unreadStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $unreadStmt->execute();
        $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

        // FIX : Change alias 'read' → 'read_status' (évite mot-clé réservé)
        $query = "SELECT id, type, user_id, title, message AS description, link, is_read AS read_status, created_at 
                  FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['unread_count' => $unreadCount, 'notifications' => $notifications];
    }

    public function markAsRead($notifId, $userId)
    {
        $query = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notifId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markAllAsRead($userId)
    {
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>