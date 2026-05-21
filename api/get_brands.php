<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM brands ORDER BY name ASC");
    $brands = $stmt->fetchAll();
    echo json_encode([
        'status' => 'success',
        'data' => $brands
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
