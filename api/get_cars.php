<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    if ($limit <= 0 || $limit > 1000) {
        $limit = 100;
    }
    
    // Lấy danh sách xe cùng tên hãng xe tương ứng
    $stmt = $pdo->prepare("
        SELECT c.id, c.model_name, b.name AS brand_name, c.price, c.category, c.status
        FROM cars c
        JOIN brands b ON c.brand_id = b.id
        WHERE c.status = 'available' OR c.status = 'coming_soon'
        ORDER BY b.name ASC, c.model_name ASC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $cars = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'data' => $cars
    ]);
} catch (Exception $e) {
    error_log("Database error in get_cars: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
    ]);
}
?>
