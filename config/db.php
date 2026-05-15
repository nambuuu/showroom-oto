<?php
session_start();

define('SITE_NAME', 'AutoElite Showroom');
define('BASE_URL',  'http://localhost/showroom-oto/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'car_showroom_db');
define('DB_USER', 'root');
define('DB_PASS', '172005');

define('UPLOAD_PATH', __DIR__ . '/../assets/image/cars/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL. Vui lòng kiểm tra lại cấu hình. " . $e->getMessage());
}
?>
