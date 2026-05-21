<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    
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
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
