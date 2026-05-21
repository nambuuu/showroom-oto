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

$full_name = isset($data['full_name']) ? trim(strip_tags($data['full_name'])) : '';
$email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : '';
$phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
$subject = isset($data['subject']) ? trim(strip_tags($data['subject'])) : '';
$message = isset($data['message']) ? trim(strip_tags($data['message'])) : '';

if (!$full_name || !$email || !$subject || !$message) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng điền đầy đủ các trường thông tin bắt buộc (*).',
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Địa chỉ email không hợp lệ.',
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO contacts (full_name, email, phone, subject, message, is_read, created_at)
         VALUES (:full_name, :email, :phone, :subject, :message, 0, NOW())"
    );
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':subject' => $subject,
        ':message' => $message,
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Gửi yêu cầu liên hệ thành công! Chúng tôi sẽ phản hồi lại bạn sớm nhất.',
    ]);
} catch (Exception $e) {
    error_log('Database error in post_contact: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Đã xảy ra lỗi hệ thống khi gửi yêu cầu. Vui lòng thử lại sau.',
    ]);
}
