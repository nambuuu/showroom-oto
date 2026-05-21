<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

try {
    $stmt = $pdo->query('SELECT * FROM brands ORDER BY name ASC');
    $brands = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $brands,
    ]);
} catch (Exception $e) {
    error_log('Database error in get_brands: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.',
    ]);
}
