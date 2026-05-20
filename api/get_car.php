<?php
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Thiếu tham số id']);
        exit;
    }

    // Lấy thông tin cơ bản
    $sql = "SELECT c.*, b.name as brand_name FROM cars c LEFT JOIN brands b ON c.brand_id = b.id WHERE c.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $car = $stmt->fetch();

    if (!$car) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy xe']);
        exit;
    }

    // Lấy thông số kỹ thuật
    $sqlSpec = "SELECT * FROM car_specifications WHERE car_id = :id";
    $stmtSpec = $pdo->prepare($sqlSpec);
    $stmtSpec->execute([':id' => $id]);
    $specifications = $stmtSpec->fetch();

    // Lấy hình ảnh
    $sqlImg = "SELECT * FROM car_images WHERE car_id = :id ORDER BY is_main DESC, id ASC";
    $stmtImg = $pdo->prepare($sqlImg);
    $stmtImg->execute([':id' => $id]);
    $images = $stmtImg->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'car' => $car,
            'specifications' => $specifications,
            'images' => $images
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
