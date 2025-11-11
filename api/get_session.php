<?php
session_start();
require_once __DIR__ . '/../core/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $response = [
            'success' => true,
            'logged_in' => true,
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'user_type' => $_SESSION['user_type'] ?? null,
            'is_admin' => isAdmin()
        ];

        // Include success message if it exists and clear it
        if (isset($_SESSION['success'])) {
            $response['login_message'] = $_SESSION['success'];
            $response['message_type'] = 'success';
            unset($_SESSION['success']);
        }

        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => true,
            'logged_in' => false,
            'user_id' => null
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
