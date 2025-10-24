<?php
require_once __DIR__ . '/../controllers/NotificationController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

$controller = new NotificationController();
$user_id = intval($_POST['user_id'] ?? 0);

if (!$user_id) {
    echo json_encode(['error' => 'User ID required']);
    exit;
}

$success = $controller->markAllAsRead($user_id);
echo json_encode(['success' => $success]);
?>