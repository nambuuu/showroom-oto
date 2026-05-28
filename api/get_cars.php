<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

try {
    // Chế độ đơn giản (booking dropdown): chỉ limit, không page
    if (!isset($_GET['page'])) {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        if ($limit <= 0 || $limit > 1000) {
            $limit = 100;
        }

        $stmt = $pdo->prepare(
            "SELECT c.id, c.model_name, b.name AS brand_name, c.price, c.category, c.year, c.status,
                    img.image AS main_image, cs.seating
             FROM cars c
             JOIN brands b ON c.brand_id = b.id
             LEFT JOIN car_images img ON c.id = img.car_id AND img.is_main = 1
             LEFT JOIN car_specifications cs ON c.id = cs.car_id
             WHERE c.status IN ('available', 'coming_soon')
             ORDER BY b.name ASC, c.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $cars = $stmt->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data' => $cars,
        ]);
        exit;
    }

    // Chế độ phân trang (trang danh sách xe)
    $page = max(1, (int)$_GET['page']);
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    if ($limit <= 0 || $limit > 100) {
        $limit = 10;
    }
    $offset = ($page - 1) * $limit;

    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
    $seating  = isset($_GET['seating'])  ? (int)$_GET['seating']  : 0;
    $max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
    $year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

    $where = [];
    $params = [];

    if ($brand_id > 0) {
        $where[] = 'c.brand_id = :brand_id';
        $params[':brand_id'] = $brand_id;
    }
    if ($category !== '') {
        $where[] = 'c.category = :category';
        $params[':category'] = $category;
    }
    if ($search !== '') {
        $where[] = 'c.model_name LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    if ($seating > 0) {
        $where[] = 'cs.seating = :seating';
        $params[':seating'] = $seating;
    }
    if ($max_price > 0) {
        $where[] = 'c.price <= :max_price';
        $params[':max_price'] = $max_price;
    }
    if ($year > 0) {
        $where[] = 'c.year = :year';
        $params[':year'] = $year;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmtCount = $pdo->prepare("
        SELECT COUNT(c.id) AS total
        FROM cars c
        LEFT JOIN car_specifications cs ON c.id = cs.car_id
        $whereClause
    ");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetch()['total'];

    $sql = "
        SELECT c.*, b.name AS brand_name, img.image AS main_image, cs.seating
        FROM cars c
        LEFT JOIN brands b ON c.brand_id = b.id
        LEFT JOIN car_images img ON c.id = img.car_id AND img.is_main = 1
        LEFT JOIN car_specifications cs ON c.id = cs.car_id
        $whereClause
        ORDER BY b.name ASC, c.created_at DESC
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
            'total_pages' => $limit > 0 ? (int)ceil($total / $limit) : 0,
        ],
    ]);
} catch (Exception $e) {
    error_log('Database error in get_cars: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.',
    ]);
}
