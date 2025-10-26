<?php
require_once __DIR__ . '/../controllers/NotificationController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'GET only']);
    exit;
}

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) {
    echo json_encode(['error' => 'User ID required']);
    exit;
}

$controller = new NotificationController();
$data = $controller->getNotificationsByUser($user_id);

echo json_encode($data);
?>