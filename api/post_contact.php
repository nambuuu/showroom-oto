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
    
    $full_name = $data['full_name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $subject = $data['subject'] ?? '';
    $message = $data['message'] ?? '';

    if (empty($full_name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc']);
        exit;
    }

    $sql = "INSERT INTO contacts (full_name, email, phone, subject, message, is_read, created_at) 
            VALUES (:full_name, :email, :phone, :subject, :message, 0, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Gửi yêu cầu liên hệ thành công.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
