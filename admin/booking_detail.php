<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

// Get booking ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die('Missing booking ID');
}

// Fetch booking details
$stmt = $pdo->prepare('SELECT b.*, c.model_name AS car_name FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = :id');
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
    echo "<script>showToast('Cập nhật trạng thái thành công', 'success');</script>";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết lịch lái thử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="container py-4">
        <h2 class="text-gold mb-4">Chi tiết lịch lái thử #<?php echo $booking['id']; ?></h2>
        <table class="table table-dark table-striped">
            <tr><th>Khách hàng</th><td><?php echo htmlspecialchars($booking['full_name']); ?></td></tr>
            <tr><th>Xe</th><td><?php echo htmlspecialchars($booking['car_name']); ?></td></tr>
            <tr><th>Ngày hẹn</th><td><?php echo date('d/m/Y', strtotime($booking['preferred_date'])); ?></td></tr>
            <tr><th>Giờ</th><td><?php echo date('H:i', strtotime($booking['preferred_time'])); ?></td></tr>
            <tr><th>Trạng thái</th><td>
                <form method="POST" class="d-inline">
                    <select name="status" class="form-select bg-dark text-light border-gold" style="width:auto; display:inline-block;">
                        <?php
                        $statuses = ['pending','approved','rejected','done'];
                        foreach ($statuses as $s) {
                            $selected = ($booking['status'] === $s) ? 'selected' : '';
                            echo "<option value='$s' $selected>" . ucfirst($s) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm ms-2">Cập nhật</button>
                </form>
            </td></tr>
        </table>
        <a href="bookings.php" class="btn btn-outline-gold mt-3">Quay lại danh sách</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
