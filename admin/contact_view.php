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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết liên hệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .detail-glass {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 0 auto;
        }
        .detail-row {
            display: flex;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            width: 30%;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-value {
            width: 70%;
            color: var(--text);
        }
        .message-box {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            min-height: 120px;
            white-space: pre-wrap;
            color: var(--text-dim);
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">
        <div class="d-flex align-items-center mb-4">
            <a href="contacts.php" class="btn btn-outline-gold me-3"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <h4 class="mb-0 text-gold" style="font-family: 'Orbitron', sans-serif;">Chi tiết yêu cầu #<?php echo $contact['id']; ?></h4>
        </div>

        <div class="detail-glass">
            <div class="mb-4 pb-3 border-bottom" style="border-color: var(--border) !important;">
                <h5 class="text-gold mb-1"><i class="bi bi-person-lines-fill me-2"></i>Thông Tin Khách Hàng</h5>
                <p class="text-muted" style="font-size: 13px;">Chi tiết liên hệ từ website</p>
            </div>

            <div class="detail-row pt-0">
                <div class="detail-label"><i class="bi bi-person text-gold"></i> Họ tên</div>
                <div class="detail-value"><strong><?php echo htmlspecialchars($contact['full_name']); ?></strong></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label"><i class="bi bi-envelope text-gold"></i> Email</div>
                <div class="detail-value">
                    <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="text-info text-decoration-none">
                        <?php echo htmlspecialchars($contact['email']); ?>
                    </a>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label"><i class="bi bi-telephone text-gold"></i> Số điện thoại</div>
                <div class="detail-value"><?php echo htmlspecialchars($contact['phone'] ?: 'Không cung cấp'); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label"><i class="bi bi-calendar-event text-gold"></i> Ngày gửi</div>
                <div class="detail-value"><?php echo date('H:i - d/m/Y', strtotime($contact['created_at'])); ?></div>
            </div>

            <div class="mt-4 pt-3">
                <h6 class="text-gold mb-3"><i class="bi bi-chat-left-text me-2"></i>Nội Dung Tin Nhắn</h6>
                <div class="mb-2"><strong>Chủ đề:</strong> <?php echo htmlspecialchars($contact['subject']); ?></div>
                <div class="message-box"><?php echo nl2br(htmlspecialchars($contact['message'])); ?></div>
            </div>
            
            <div class="mt-4 text-end">
                <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>?subject=Re: <?php echo urlencode($contact['subject']); ?>" class="btn btn-gold">
                    <i class="bi bi-reply-fill"></i> Trả lời qua Email
                </a>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html> 