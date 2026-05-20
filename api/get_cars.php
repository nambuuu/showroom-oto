<?php
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;

    $where = [];
    $params = [];

    if ($brand_id) {
        $where[] = "c.brand_id = :brand_id";
        $params[':brand_id'] = $brand_id;
    }

    if ($category) {
        $where[] = "c.category = :category";
        $params[':category'] = $category;
    }

    if ($search) {
        $where[] = "c.model_name LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    // Count total
    $countSql = "SELECT COUNT(id) as total FROM cars c $whereClause";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($params);
    $total = $stmtCount->fetch()['total'];

    // Get data
    $sql = "
        SELECT c.*, b.name as brand_name, img.image as main_image 
        FROM cars c 
        LEFT JOIN brands b ON c.brand_id = b.id
        LEFT JOIN car_images img ON c.id = img.car_id AND img.is_main = 1
        $whereClause
        ORDER BY c.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $cars,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
