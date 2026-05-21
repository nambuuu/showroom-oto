<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tham số ID xe không hợp lệ.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, b.name AS brand_name, s.engine, s.horsepower, s.torque, 
               s.transmission, s.fuel_type, s.fuel_efficiency, s.seating, 
               s.drive_type, s.top_speed, s.acceleration,
               (SELECT image FROM car_images WHERE car_id = c.id AND is_main = 1 LIMIT 1) AS main_image
        FROM cars c
        JOIN brands b ON c.brand_id = b.id
        LEFT JOIN car_specifications s ON c.id = s.car_id
        WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    $car = $stmt->fetch();

    if ($car) {
        echo json_encode([
            'status' => 'success',
            'data' => $car
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy mẫu xe tương ứng.'
        ]);
    }
} catch (Exception $e) {
    error_log("Database error in get_car ($id): " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
    ]);
}
?>
