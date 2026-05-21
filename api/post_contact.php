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

$full_name = $data['full_name'] ?? null;
$email = $data['email'] ?? null;
$phone = $data['phone'] ?? null;
$subject = $data['subject'] ?? null;
$message = $data['message'] ?? null;

if (!$full_name || !$email || !$subject || !$message) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các trường thông tin bắt buộc (*).'
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
        'message' => 'Gửi yêu cầu liên hệ thành công! Chúng tôi sẽ liên hệ lại với bạn sớm.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi khi lưu thông tin liên hệ: ' . $e->getMessage()
    ]);
}
?>
