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

$full_name = isset($data['full_name']) ? trim(strip_tags($data['full_name'])) : null;
$email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : null;
$phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : null;
$subject = isset($data['subject']) ? trim(strip_tags($data['subject'])) : null;
$message = isset($data['message']) ? trim(strip_tags($data['message'])) : null;

if (!$full_name || !$email || !$subject || !$message) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các trường thông tin bắt buộc (*).'
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

try {
    // Lưu thông tin liên hệ vào bảng contacts
    $stmt = $pdo->prepare("
        INSERT INTO contacts (full_name, email, phone, subject, message, is_read)
        VALUES (?, ?, ?, ?, ?, 0)
    ");
    $stmt->execute([$full_name, $email, $phone, $subject, $message]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Gửi yêu cầu liên hệ thành công! Chúng tôi sẽ phản hồi lại bạn sớm nhất.'
    ]);
} catch (Exception $e) {
    error_log("Database error in post_contact: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi hệ thống khi gửi yêu cầu. Vui lòng thử lại sau.'
    ]);
}
?>
