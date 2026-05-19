<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Xóa ảnh liên quan trước (car_images)
    $imgStmt = $pdo->prepare('SELECT image FROM car_images WHERE car_id = :id');
    $imgStmt->execute([':id' => $id]);
    $images = $imgStmt->fetchAll();

    foreach ($images as $img) {
        $filePath = __DIR__ . '/../assets/image/cars/' . $img['image'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // Xóa car_images
    $pdo->prepare('DELETE FROM car_images WHERE car_id = :id')->execute([':id' => $id]);

    // Xóa car_specifications
    $pdo->prepare('DELETE FROM car_specifications WHERE car_id = :id')->execute([':id' => $id]);

    // Xóa xe
    $pdo->prepare('DELETE FROM cars WHERE id = :id')->execute([':id' => $id]);
}

header('Location: cars.php?deleted=1');
exit;
