<?php

require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/EventParticipation.php';

use Models\EventParticipation;

class EventParticipationController
{

    private PDO $db;

    public function __construct()
    {
        $this->db = \Database::getInstance()->getConnection();
    }

    public function createEventParticipation(EventParticipation $eventParticipation)
    {
        try {
            $data = [
                ':event_id' => $eventParticipation->getEventId(),
                ':user_id' => $eventParticipation->getUserId(),
            ];
            $sql = "INSERT INTO event_participation (event_id, user_id) VALUES (:event_id, :user_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            return ['id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            throw new Exception('Database error:' . $e->getMessage());
        }
    }

    /**
     * Check if a user is already participating in an event
     */
    public function isUserParticipating(int $userId, int $eventId): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM event_participation WHERE user_id = :user_id AND event_id = :event_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':event_id' => $eventId
            ]);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Check if an event can accept more participants
     */
    public function canParticipate(int $eventId): bool
    {
        try {
            // Get event participant limit
            $sql = "SELECT participant_limit FROM eco_event WHERE id = :event_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                return false; // Event doesn't exist
            }

            // If no limit is set, allow participation
            if ($event['participant_limit'] === null || $event['participant_limit'] <= 0) {
                return true;
            }

            // Count current participants
            $sql = "SELECT COUNT(*) FROM event_participation WHERE event_id = :event_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);
            $currentCount = $stmt->fetchColumn();

            return $currentCount < $event['participant_limit'];
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get all participants for an event
     */
    public function getEventParticipants(int $eventId): array
    {
        try {
            $sql = "SELECT ep.*, u.username, u.email 
                    FROM event_participation ep 
                    JOIN users u ON ep.user_id = u.id 
                    WHERE ep.event_id = :event_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Remove a user's participation from an event
     */
    public function removeParticipation(int $userId, int $eventId): bool
    {
        try {
            $sql = "DELETE FROM event_participation WHERE user_id = :user_id AND event_id = :event_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':event_id' => $eventId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
}
