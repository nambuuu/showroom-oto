<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Chi tiết lịch lái thử';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: bookings.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT b.*, c.model_name AS car_name, c.price,
            br.name AS brand_name, br.country AS brand_country,
            ci.image AS car_image
     FROM bookings b
     JOIN cars c ON b.car_id = c.id
     JOIN brands br ON c.brand_id = br.id
     LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_main = 1
     WHERE b.id = :id"
);
$stmt->execute([':id' => $id]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: bookings.php');
    exit;
}

$toast = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $allowed = ['pending', 'approved', 'rejected', 'done'];
    $ns = $_POST['status'];
    if (in_array($ns, $allowed, true)) {
        $pdo->prepare('UPDATE bookings SET status = :s WHERE id = :id')
            ->execute([':s' => $ns, ':id' => $id]);
        $booking['status'] = $ns;
        $toast = 'success';
    }
}

$statusMap = [
    'pending'  => ['label' => 'Chờ duyệt',   'cls' => 'warning', 'icon' => 'clock-fill'],
    'approved' => ['label' => 'Đã duyệt',    'cls' => 'success', 'icon' => 'check-circle-fill'],
    'rejected' => ['label' => 'Từ chối',     'cls' => 'danger',  'icon' => 'x-circle-fill'],
    'done'     => ['label' => 'Hoàn thành',  'cls' => 'info',    'icon' => 'flag-fill'],
];
$cur = $statusMap[$booking['status']] ?? ['label' => $booking['status'], 'cls' => 'secondary', 'icon' => 'dot'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Chi tiết lịch #<?= $id ?> – Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px;}
.detail-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;}
.detail-card-header{background:linear-gradient(135deg,rgba(212,168,67,.1),rgba(212,168,67,.03));border-bottom:1px solid var(--border);padding:16px 22px;display:flex;align-items:center;gap:12px;}
.detail-card-header i{font-size:18px;color:var(--gold);}
.detail-card-header h3{font-family:'Orbitron',sans-serif;font-size:13px;font-weight:700;color:var(--gold);letter-spacing:.5px;}
.detail-card-body{padding:22px;}
.info-row{display:flex;flex-direction:column;gap:4px;padding:14px 0;border-bottom:1px solid rgba(255,255,255,.04);}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-row:first-child{padding-top:0;}
.info-label{font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;}
.info-value{font-size:14px;color:var(--text);font-weight:500;}
.avatar-lg{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-dark));display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#000;flex-shrink:0;box-shadow:0 0 20px var(--gold-glow);}
.car-preview{width:100%;height:160px;object-fit:cover;border-radius:10px;border:1px solid var(--border);margin-bottom:14px;}
.car-preview-placeholder{width:100%;height:160px;border-radius:10px;border:1px solid var(--border);background:var(--bg-secondary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:36px;margin-bottom:14px;}
.status-bar{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;}
.status-flow{display:flex;align-items:center;gap:0;}
.sf-step{display:flex;flex-direction:column;align-items:center;gap:6px;}
.sf-dot{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid var(--border);background:var(--bg-primary);color:var(--text-muted);transition:all .3s;}
.sf-dot.active{border-color:var(--gold);background:rgba(212,168,67,.15);color:var(--gold);}
.sf-dot.done-step{border-color:var(--success);background:rgba(16,185,129,.12);color:var(--success);}
.sf-label{font-size:10px;color:var(--text-muted);white-space:nowrap;}
.sf-line{width:40px;height:2px;background:var(--border);margin:0 4px;margin-bottom:20px;}
.sf-line.done-line{background:var(--gold);}
.toast-msg{position:fixed;top:80px;right:24px;z-index:9999;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#10b981;padding:14px 20px;border-radius:12px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;backdrop-filter:blur(12px);animation:slideIn .3s ease;box-shadow:0 8px 32px rgba(0,0,0,.3);}
@keyframes slideIn{from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);}}
@media(max-width:768px){.detail-grid{grid-template-columns:1fr;}.status-flow{flex-wrap:wrap;gap:8px;}}
</style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>

    <?php if ($toast === 'success'): ?>
    <div class="toast-msg" id="toastMsg">
        <i class="bi bi-check-circle-fill"></i> Cập nhật trạng thái thành công!
    </div>
    <script>setTimeout(()=>{const t=document.getElementById('toastMsg');if(t)t.remove();},3500);</script>
    <?php endif; ?>

    <div class="page-body">

        <div class="page-header">
            <div>
                <div class="page-title">Chi tiết lịch lái thử</div>
                <div class="page-subtitle">Mã lịch hẹn #<?= str_pad((string)$id, 5, '0', STR_PAD_LEFT) ?> &mdash; Đăng ký lúc <?= date('H:i d/m/Y', strtotime($booking['created_at'])) ?></div>
            </div>
            <a href="bookings.php" class="btn btn-outline-gold">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="status-bar">
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Trạng thái hiện tại</div>
                <span class="badge badge-<?= $cur['cls'] ?>" style="font-size:13px;padding:6px 14px">
                    <i class="bi bi-<?= $cur['icon'] ?>"></i> <?= $cur['label'] ?>
                </span>
            </div>

            <div class="status-flow">
                <?php
                $steps = [
                    ['key' => 'pending',  'icon' => 'clock',         'label' => 'Chờ duyệt'],
                    ['key' => 'approved', 'icon' => 'check-circle',  'label' => 'Đã duyệt'],
                    ['key' => 'done',     'icon' => 'flag',          'label' => 'Hoàn thành'],
                ];
                $order = ['pending' => 0, 'approved' => 1, 'rejected' => 1, 'done' => 2];
                $curOrder = $order[$booking['status']] ?? 0;
                foreach ($steps as $i => $step):
                    $stepOrder = $i;
                    $isDone = $stepOrder < $curOrder;
                    $isActive = ($step['key'] === $booking['status'])
                        || ($booking['status'] !== 'rejected' && $stepOrder === $curOrder);
                    $dotCls = $isDone ? 'done-step' : ($isActive ? 'active' : '');
                ?>
                <div class="sf-step">
                    <div class="sf-dot <?= $dotCls ?>">
                        <i class="bi bi-<?= $isDone ? 'check-lg' : $step['icon'] ?>"></i>
                    </div>
                    <div class="sf-label"><?= $step['label'] ?></div>
                </div>
                <?php if ($i < count($steps) - 1): ?>
                <div class="sf-line <?= $isDone ? 'done-line' : '' ?>"></div>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php if ($booking['status'] === 'rejected'): ?>
                <div style="margin-left:16px;margin-bottom:20px">
                    <span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Đã từ chối</span>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" style="display:flex;align-items:center;gap:10px">
                <select name="status" class="form-select" style="width:160px">
                    <?php foreach ($statusMap as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $booking['status'] === $k ? 'selected' : '' ?>>
                        <?= $v['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg"></i> Cập nhật
                </button>
            </form>
        </div>

        <div class="detail-grid">

            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-person-fill"></i>
                    <h3>Thông tin khách hàng</h3>
                </div>
                <div class="detail-card-body">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                        <div class="avatar-lg"><?= strtoupper(substr($booking['full_name'], 0, 1)) ?></div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:var(--text)"><?= htmlspecialchars($booking['full_name']) ?></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px">Khách hàng đặt lịch</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-telephone" style="color:var(--gold)"></i> Số điện thoại</div>
                        <div class="info-value">
                            <a href="tel:<?= htmlspecialchars($booking['phone']) ?>" style="color:var(--gold)">
                                <?= htmlspecialchars($booking['phone']) ?>
                            </a>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-envelope" style="color:var(--gold)"></i> Email</div>
                        <div class="info-value">
                            <?php if (!empty($booking['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($booking['email']) ?>" style="color:var(--gold)">
                                <?= htmlspecialchars($booking['email']) ?>
                            </a>
                            <?php else: ?>
                            <span style="color:var(--text-muted)">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($booking['message'])): ?>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-chat-left-text" style="color:var(--gold)"></i> Ghi chú / Yêu cầu</div>
                        <div class="info-value" style="color:var(--text-dim);font-style:italic;line-height:1.6">
                            "<?= nl2br(htmlspecialchars($booking['message'])) ?>"
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-calendar2-check-fill"></i>
                    <h3>Thông tin lịch hẹn</h3>
                </div>
                <div class="detail-card-body">
                    <?php if (!empty($booking['car_image'])): ?>
                        <img src="../assets/image/cars/<?= htmlspecialchars($booking['car_image']) ?>" class="car-preview" alt="<?= htmlspecialchars($booking['car_name']) ?>">
                    <?php else: ?>
                        <div class="car-preview-placeholder"><i class="bi bi-car-front"></i></div>
                    <?php endif; ?>

                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-car-front-fill" style="color:var(--gold)"></i> Xe đăng ký lái thử</div>
                        <div class="info-value" style="font-size:16px;font-weight:700;color:var(--gold)">
                            <?= htmlspecialchars($booking['car_name']) ?>
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                            <span class="badge badge-gold"><i class="bi bi-award"></i> <?= htmlspecialchars($booking['brand_name']) ?></span>
                            <?php if (!empty($booking['brand_country'])): ?>
                            &nbsp;<span><?= htmlspecialchars($booking['brand_country']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($booking['price'])): ?>
                        <div style="font-size:14px;color:var(--gold);font-weight:600;margin-top:6px">
                            <?= number_format((float)$booking['price'], 0, ',', '.') ?> ₫
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-calendar2" style="color:var(--gold)"></i> Ngày hẹn</div>
                        <div class="info-value" style="font-size:16px">
                            <?php
                            $d = strtotime($booking['preferred_date']);
                            $days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
                            echo $days[(int)date('w', $d)] . ', ' . date('d/m/Y', $d);
                            ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-clock" style="color:var(--gold)"></i> Giờ hẹn</div>
                        <div class="info-value" style="font-size:20px;font-weight:700;font-family:'Orbitron',sans-serif;color:var(--gold)">
                            <?= date('H:i', strtotime($booking['preferred_time'])) ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-clock-history" style="color:var(--gold)"></i> Thời điểm đăng ký</div>
                        <div class="info-value"><?= date('H:i:s d/m/Y', strtotime($booking['created_at'])) ?></div>
                    </div>
                    <?php
                    $today = strtotime('today');
                    $apptDay = strtotime($booking['preferred_date']);
                    $diffDays = (int)round(($apptDay - $today) / 86400);
                    if ($diffDays > 0 && !in_array($booking['status'], ['done', 'rejected'], true)):
                    ?>
                    <div style="margin-top:12px;background:rgba(212,168,67,.08);border:1px solid rgba(212,168,67,.2);border-radius:10px;padding:10px 14px;font-size:13px;color:var(--gold)">
                        <i class="bi bi-hourglass-split"></i>
                        Còn <strong><?= $diffDays ?> ngày</strong> đến buổi lái thử
                    </div>
                    <?php elseif ($diffDays === 0 && $booking['status'] === 'approved'): ?>
                    <div style="margin-top:12px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;padding:10px 14px;font-size:13px;color:#10b981">
                        <i class="bi bi-calendar-check-fill"></i>
                        <strong>Hôm nay</strong> là ngày lái thử!
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a href="tel:<?= htmlspecialchars($booking['phone']) ?>" class="btn btn-outline-gold">
                <i class="bi bi-telephone-fill"></i> Gọi khách hàng
            </a>
            <?php if (!empty($booking['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($booking['email']) ?>" class="btn btn-info-outline">
                <i class="bi bi-envelope-fill"></i> Gửi email
            </a>
            <?php endif; ?>
            <a href="bookings.php" class="btn btn-outline-gold" style="margin-left:auto">
                <i class="bi bi-arrow-left"></i> Về danh sách
            </a>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
