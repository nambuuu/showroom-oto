<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Missing contact ID');
}

// Đánh dấu đã đọc
$markRead = $pdo->prepare('UPDATE contacts SET is_read = 1 WHERE id = :id');
$markRead->execute([':id' => $id]);

// Lấy chi tiết
$stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = :id');
$stmt->execute([':id' => $id]);
$contact = $stmt->fetch();
if (!$contact) {
    die('Contact not found');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết liên hệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="container py-4">
        <h2 class="text-gold mb-4">Chi tiết yêu cầu #<?php echo $contact['id']; ?></h2>
        <table class="table table-dark table-striped">
            <tr><th>Họ tên</th><td><?php echo htmlspecialchars($contact['full_name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($contact['email']); ?></td></tr>
            <tr><th>Chủ đề</th><td><?php echo htmlspecialchars($contact['subject']); ?></td></tr>
            <tr><th>Nội dung</th><td><?php echo nl2br(htmlspecialchars($contact['message'] ?? '')); ?></td></tr>
            <tr><th>Ngày gửi</th><td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td></tr>
        </table>
        <a href="contacts.php" class="btn btn-outline-gold mt-3">Quay lại danh sách</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
