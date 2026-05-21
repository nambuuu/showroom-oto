<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

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

// Làm sạch dữ liệu đầu vào
$car_id = isset($data['car_id']) ? (int)$data['car_id'] : null;
$full_name = isset($data['full_name']) ? trim(strip_tags($data['full_name'])) : null;
$phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : null;
$email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : null;
$preferred_date = isset($data['preferred_date']) ? trim(strip_tags($data['preferred_date'])) : null;
$preferred_time = isset($data['preferred_time']) ? trim(strip_tags($data['preferred_time'])) : null;
$message = isset($data['message']) ? trim(strip_tags($data['message'])) : '';

if (!$car_id || !$full_name || !$phone || !$preferred_date || !$preferred_time) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).'
    ]);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Địa chỉ email không hợp lệ.'
    ]);
    exit;
}

// Validate độ dài sđt cơ bản
if (strlen($phone) < 9 || strlen($phone) > 15) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Số điện thoại không hợp lệ.'
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
    error_log("Database error in post_booking: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi hệ thống khi lưu lịch hẹn. Vui lòng thử lại sau.'
    ]);
}
?>
