<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../controllers/EventParticipationController.php';

try {
    if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Event ID is required'
        ]);
        exit;
    }

    $eventId = intval($_GET['event_id']);
    $controller = new EventParticipationController();

    $response = ['success' => true, 'is_participating' => false];

    // Check if user is logged in and participating
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $userId = intval($_SESSION['user_id']);
        $response['is_participating'] = $controller->isUserParticipating($userId, $eventId);
    }

    echo json_encode($response);
} catch (Exception $e) {
    error_log('Check participation error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue'
    ]);
}
