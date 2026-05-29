<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu gửi lên không hợp lệ.',
    ]);
    exit;
}

$car_id = isset($data['car_id']) ? (int)$data['car_id'] : 0;
$full_name = isset($data['full_name']) ? trim(strip_tags($data['full_name'])) : '';
$phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
$email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : '';
$preferred_date = isset($data['preferred_date']) ? trim(strip_tags($data['preferred_date'])) : '';
$preferred_time = isset($data['preferred_time']) ? trim(strip_tags($data['preferred_time'])) : '';
$message = isset($data['message']) ? trim(strip_tags($data['message'])) : '';

if (!$car_id || !$full_name || !$phone || !$preferred_date || !$preferred_time) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).',
    ]);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Địa chỉ email không hợp lệ.',
    ]);
    exit;
}

if (strlen($phone) < 9 || strlen($phone) > 15) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Số điện thoại không hợp lệ.',
    ]);
    exit;
}

try {
    // Kiểm tra trùng lịch: cùng xe, cùng ngày, và cách nhau chưa tới 15 phút (900 giây)
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM bookings 
        WHERE car_id = :car_id 
          AND preferred_date = :preferred_date 
          AND status != 'rejected'
          AND ABS(TIME_TO_SEC(TIMEDIFF(preferred_time, :preferred_time))) < 900
    ");
    $checkStmt->execute([
        ':car_id' => $car_id,
        ':preferred_date' => $preferred_date,
        ':preferred_time' => $preferred_time,
    ]);
    $overlapCount = $checkStmt->fetchColumn();

    if ($overlapCount > 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Đặt lịch lái thử thất bại. Xe này đã có người đặt trong khoảng thời gian này. Vui lòng chọn thời gian cách ít nhất 15 phút.',
        ]);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO bookings (car_id, full_name, email, phone, preferred_date, preferred_time, message, status, created_at)
         VALUES (:car_id, :full_name, :email, :phone, :preferred_date, :preferred_time, :message, 'pending', NOW())"
    );
    $stmt->execute([
        ':car_id' => $car_id,
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':preferred_date' => $preferred_date,
        ':preferred_time' => $preferred_time,
        ':message' => $message,
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Đăng ký đặt lịch lái thử thành công! Showroom sẽ liên hệ lại với bạn để xác nhận.',
    ]);
} catch (Exception $e) {
    error_log('Database error in post_booking: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi hệ thống khi lưu lịch hẹn. Vui lòng thử lại sau.',
    ]);
}
