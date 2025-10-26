<?php
session_start();
require_once __DIR__ . '/../core/database.php';

header('Content-Type: application/json');

$database = Database::getInstance();
$conn = $database->getConnection();

$user_id = $_SESSION['user_id'] ?? 1;  // Fallback
$stmt = $conn->prepare("SELECT id as user_id, ville as user_city FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($user ?: ['user_id' => 1, 'user_city' => 'Monastir']);
?>