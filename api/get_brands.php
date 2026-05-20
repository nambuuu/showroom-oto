<?php
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT * FROM brands ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $brands = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $brands
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
