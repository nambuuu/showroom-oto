<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Nhận dữ liệu JSON từ body request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu gửi lên không hợp lệ.'
    ]);
    exit;
}

$car_id = $data['car_id'] ?? null;
$full_name = $data['full_name'] ?? null;
$phone = $data['phone'] ?? null;
$email = $data['email'] ?? null;
$preferred_date = $data['preferred_date'] ?? null;
$preferred_time = $data['preferred_time'] ?? null;
$message = $data['message'] ?? null;

if (!$car_id || !$full_name || !$phone || !$preferred_date || !$preferred_time) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).'
    ]);
    exit;
}

try {
    // Lưu lịch hẹn lái thử vào bảng bookings
    $stmt = $pdo->prepare("
        INSERT INTO bookings (car_id, full_name, email, phone, preferred_date, preferred_time, message, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$car_id, $full_name, $email, $phone, $preferred_date, $preferred_time, $message]);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Đăng ký đặt lịch lái thử thành công! Showroom sẽ liên hệ lại với bạn để xác nhận.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi khi lưu lịch hẹn: ' . $e->getMessage()
    ]);
}
?>
