<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/EventController.php';

try {
    $controller = new EventController();
    $events = $controller->getAllEvents();

    // Ensure we have at least 4 events
    while (count($events) < 4) {
        // Create a dummy event for demonstration
        $events[] = [
            'id' => count($events) + 1,
            'event_name' => 'Évènement à venir ' . (count($events) + 1),
            'description' => 'Un nouvel événement écologique sera bientôt disponible. Restez à l\'écoute !',
            'ville' => 'À déterminer',
            'pays' => 'Tunisie',
            'event_date' => date('Y-m-d', strtotime('+' . (count($events) + 1) . ' weeks')),
            'category_name' => 'À venir',
            'status' => 'pending'
        ];
    }

    echo json_encode([
        'success' => true,
        'events' => array_slice($events, 0, max(4, count($events))) // Get at least 4 events
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
