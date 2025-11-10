<?php

use Controllers\CategoryController;

require_once("../controllers/CategoryController.php");
header('Content-Type: application/json');

try {
    $controller = new CategoryController();
    $categories = $controller->getCategories();

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
