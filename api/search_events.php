<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/EventController.php';

$controller = new EventController();

try {
    if (isset($_GET['get'])) {
        if ($_GET['get'] === 'categories') {
            echo json_encode($controller->getCategories());
        } elseif ($_GET['get'] === 'villes') {
            echo json_encode($controller->getVilles());
        }
        exit;
    }

    $filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : [];
    $events = $controller->getFilteredEvents($filters);

    echo json_encode(['events' => $events]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
