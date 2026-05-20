<?php
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $car_id = $data['car_id'] ?? '';
    $full_name = $data['full_name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $preferred_date = $data['preferred_date'] ?? '';
    $preferred_time = $data['preferred_time'] ?? '';
    $message = $data['message'] ?? '';

    if (empty($car_id) || empty($full_name) || empty($phone) || empty($preferred_date) || empty($preferred_time)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc']);
        exit;
    }

    $sql = "INSERT INTO bookings (car_id, full_name, email, phone, preferred_date, preferred_time, message, status, created_at) 
            VALUES (:car_id, :full_name, :email, :phone, :preferred_date, :preferred_time, :message, 'pending', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':car_id' => $car_id,
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':preferred_date' => $preferred_date,
        ':preferred_time' => $preferred_time,
        ':message' => $message
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Đặt lịch lái thử thành công. Chúng tôi sẽ liên hệ lại sớm nhất.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
