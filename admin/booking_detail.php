<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

// Get booking ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die('Missing booking ID');
}

// Fetch booking details
$stmt = $pdo->prepare('SELECT b.*, c.model_name AS car_name, c.price, ci.image as car_image FROM bookings b JOIN cars c ON b.car_id = c.id LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_main = 1 WHERE b.id = :id');
$stmt->execute([':id' => $id]);
$booking = $stmt->fetch();
if (!$booking) {
    die('Booking not found');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $upd = $pdo->prepare('UPDATE bookings SET status = :status WHERE id = :id');
    $upd->execute([':status' => $newStatus, ':id' => $id]);
    // Refresh data
    $stmt->execute([':id' => $id]);
    $booking = $stmt->fetch();
    $toastMessage = 'Cập nhật trạng thái thành công!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết lịch lái thử</title>
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
            align-items: center;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            width: 35%;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-value {
            width: 65%;
            color: var(--text);
        }
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .car-preview {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">
        <div class="d-flex align-items-center mb-4">
            <a href="bookings.php" class="btn btn-outline-gold me-3"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <h4 class="mb-0 text-gold" style="font-family: 'Orbitron', sans-serif;">Chi tiết Lịch Lái Thử #<?php echo $booking['id']; ?></h4>
        </div>

        <?php if (!empty($toastMessage)): ?>
            <div class="alert alert-success" style="background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); color: #10b981; max-width: 800px; margin: 0 auto 20px;">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($toastMessage) ?>
            </div>
        <?php endif; ?>

        <div class="detail-glass">
            <div class="row">
                <div class="col-md-5">
                    <h5 class="text-gold mb-3"><i class="bi bi-car-front-fill me-2"></i>Xe Quan Tâm</h5>
                    <?php if ($booking['car_image']): ?>
                        <img src="../assets/image/cars/<?php echo htmlspecialchars($booking['car_image']); ?>" class="car-preview" alt="Car">
                    <?php else: ?>
                        <div class="car-preview d-flex align-items-center justify-content-center" style="background: var(--bg-secondary);">
                            <i class="bi bi-image text-muted" style="font-size: 40px;"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="text-white"><?php echo htmlspecialchars($booking['car_name']); ?></h5>
                    <p class="text-gold mb-0 fw-bold"><?php echo number_format($booking['price'], 0, ',', '.'); ?> ₫</p>
                </div>
                
                <div class="col-md-7">
                    <h5 class="text-gold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Thông Tin Khách Hàng</h5>
                    
                    <div class="detail-row pt-0">
                        <div class="detail-label"><i class="bi bi-person text-gold"></i> Họ tên</div>
                        <div class="detail-value"><strong><?php echo htmlspecialchars($booking['full_name']); ?></strong></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label"><i class="bi bi-envelope text-gold"></i> Email</div>
                        <div class="detail-value">
                            <a href="mailto:<?php echo htmlspecialchars($booking['email']); ?>" class="text-info text-decoration-none">
                                <?php echo htmlspecialchars($booking['email']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label"><i class="bi bi-telephone text-gold"></i> Điện thoại</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['phone']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label"><i class="bi bi-calendar-event text-gold"></i> Lịch hẹn</div>
                        <div class="detail-value">
                            <span class="badge badge-info fs-6 px-3 py-2">
                                <?php echo date('H:i', strtotime($booking['preferred_time'])); ?> - <?php echo date('d/m/Y', strtotime($booking['preferred_date'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label"><i class="bi bi-bookmark-check text-gold"></i> Trạng thái</div>
                        <div class="detail-value">
                            <form method="POST" class="status-form">
                                <select name="status" class="form-select bg-dark text-light" style="width:140px; border-color: var(--border);">
                                    <?php
                                    $statuses = [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Đã hủy',
                                        'done' => 'Hoàn tất'
                                    ];
                                    foreach ($statuses as $val => $label) {
                                        $selected = ($booking['status'] === $val) ? 'selected' : '';
                                        echo "<option value='$val' $selected>$label</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" class="btn btn-gold btn-sm"><i class="bi bi-save"></i> Cập nhật</button>
                            </form>
                        </div>
                    </div>

                    <?php if (!empty($booking['message'])): ?>
                    <div class="mt-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px solid var(--border);">
                        <strong class="text-gold mb-2 d-block"><i class="bi bi-chat-left-text me-1"></i> Ghi chú của khách:</strong>
                        <p class="text-dim mb-0"><?php echo nl2br(htmlspecialchars($booking['message'])); ?></p>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
