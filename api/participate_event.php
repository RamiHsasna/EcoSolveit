<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../controllers/EventParticipationController.php';
require_once __DIR__ . '/../models/EventParticipation.php';

use Models\EventParticipation;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not authenticated',
            'require_login' => true
        ]);
        exit;
    }

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['event_id']) || empty($input['event_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Event ID is required'
        ]);
        exit;
    }

    $eventId = intval($input['event_id']);
    $userId = intval($_SESSION['user_id']);

    // Check if user is already participating in this event
    $controller = new EventParticipationController();
    if ($controller->isUserParticipating($userId, $eventId)) {
        echo json_encode([
            'success' => false,
            'error' => 'Vous participez déjà à cet événement'
        ]);
        exit;
    }

    // Check if event has reached participant limit
    if (!$controller->canParticipate($eventId)) {
        echo json_encode([
            'success' => false,
            'error' => 'L\'événement a atteint sa limite de participants'
        ]);
        exit;
    }

    // Create participation
    $participation = new EventParticipation();
    $participation->setEventId($eventId);
    $participation->setUserId($userId);

    $result = $controller->createEventParticipation($participation);

    echo json_encode([
        'success' => true,
        'message' => 'Participation enregistrée avec succès',
        'participation_id' => $result['id']
    ]);
} catch (Exception $e) {
    error_log('Event participation error: ' . $e->getMessage());
    error_log('Event participation stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue lors de l\'enregistrement de votre participation',
        'debug' => $e->getMessage() // Add this for debugging, remove in production
    ]);
}
