<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Tham số ID xe không hợp lệ.',
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'SELECT c.*, b.name AS brand_name
         FROM cars c
         LEFT JOIN brands b ON c.brand_id = b.id
         WHERE c.id = :id'
    );
    $stmt->execute([':id' => $id]);
    $car = $stmt->fetch();

    if (!$car) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy mẫu xe tương ứng.',
        ]);
        exit;
    }

    $stmtSpec = $pdo->prepare('SELECT * FROM car_specifications WHERE car_id = :id');
    $stmtSpec->execute([':id' => $id]);
    $specifications = $stmtSpec->fetch() ?: null;

    $stmtImg = $pdo->prepare(
        'SELECT * FROM car_images WHERE car_id = :id ORDER BY is_main DESC, id ASC'
    );
    $stmtImg->execute([':id' => $id]);
    $images = $stmtImg->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'car' => $car,
            'specifications' => $specifications,
            'images' => $images,
        ],
    ]);
} catch (Exception $e) {
    error_log("Database error in get_car ($id): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.',
    ]);
}
