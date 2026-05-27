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
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-bottom:32px;}
.detail-card{background:rgba(255,255,255,0.02);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.06);border-radius:20px;overflow:hidden;position:relative;transition:all 0.4s cubic-bezier(0.4,0,0.2,1);box-shadow:0 8px 32px rgba(0,0,0,0.2);}
.detail-card:hover{border-color:rgba(212,168,67,0.3);box-shadow:0 12px 40px rgba(0,0,0,0.3),0 0 20px rgba(212,168,67,0.1);transform:translateY(-4px);}
.detail-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(212,168,67,0.5),transparent);opacity:0.5;transition:opacity 0.4s;}
.detail-card:hover::before{opacity:1;}
.detail-card-header{background:linear-gradient(135deg,rgba(212,168,67,0.08),transparent);border-bottom:1px solid rgba(255,255,255,0.06);padding:20px 28px;display:flex;align-items:center;gap:16px;}
.detail-card-header i{font-size:22px;color:var(--gold);filter:drop-shadow(0 0 8px rgba(212,168,67,0.4));}
.detail-card-header h3{font-family:'Orbitron',sans-serif;font-size:15px;font-weight:700;color:var(--gold);letter-spacing:1px;text-transform:uppercase;margin:0;}
.detail-card-body{padding:28px;}
.info-row{display:flex;flex-direction:column;gap:6px;padding:16px 0;border-bottom:1px dashed rgba(255,255,255,0.06);transition:background 0.3s;}
.info-row:hover{background:rgba(255,255,255,0.015);border-radius:8px;padding-left:12px;margin-left:-12px;padding-right:12px;margin-right:-12px;}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-row:first-child{padding-top:0;}
.info-label{font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;gap:8px;}
.info-value{font-size:15px;color:var(--text);font-weight:500;}
.avatar-lg{width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-dark));display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:#000;flex-shrink:0;box-shadow:0 0 24px rgba(212,168,67,0.3);border:2px solid rgba(255,255,255,0.15);}
.car-preview-wrap{position:relative;border-radius:16px;overflow:hidden;margin-bottom:24px;border:1px solid rgba(255,255,255,0.08);box-shadow:0 8px 24px rgba(0,0,0,0.3);}
.car-preview-wrap::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(8,12,20,0.9),transparent);pointer-events:none;}
.car-preview{width:100%;height:220px;object-fit:cover;display:block;transition:transform 0.6s cubic-bezier(0.4,0,0.2,1);}
.car-preview-wrap:hover .car-preview{transform:scale(1.08);}
.car-preview-info{position:absolute;bottom:0;left:0;right:0;padding:20px;z-index:2;pointer-events:none;}
.car-preview-placeholder{width:100%;height:220px;background:var(--bg-secondary);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.1);font-size:64px;}
.status-bar{background:rgba(255,255,255,0.02);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:32px;margin-bottom:32px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;box-shadow:0 12px 40px rgba(0,0,0,0.2);position:relative;overflow:hidden;}
.status-bar::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:var(--gold);box-shadow:0 0 20px var(--gold);}
.status-flow{display:flex;align-items:center;gap:0;flex:1;justify-content:center;min-width:320px;max-width:600px;margin:0 auto;}
.sf-step{display:flex;flex-direction:column;align-items:center;gap:10px;position:relative;z-index:2;width:80px;}
.sf-dot{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;border:2px solid rgba(255,255,255,0.1);background:var(--bg-secondary);color:var(--text-muted);transition:all 0.4s cubic-bezier(0.4,0,0.2,1);}
.sf-dot.active{border-color:var(--gold);background:rgba(212,168,67,0.15);color:var(--gold);box-shadow:0 0 24px rgba(212,168,67,0.3);transform:scale(1.15);}
.sf-dot.done-step{border-color:var(--success);background:rgba(16,185,129,0.15);color:var(--success);box-shadow:0 0 16px rgba(16,185,129,0.2);}
.sf-label{font-size:11px;font-weight:600;color:var(--text-muted);white-space:nowrap;text-transform:uppercase;letter-spacing:0.5px;transition:color 0.3s;}
.sf-dot.active + .sf-label{color:var(--gold);filter:drop-shadow(0 0 4px rgba(212,168,67,0.5));}
.sf-dot.done-step + .sf-label{color:var(--success);}
.sf-line{flex:1;height:3px;background:rgba(255,255,255,0.08);margin:0 4px;margin-bottom:26px;border-radius:2px;transition:background 0.4s;position:relative;overflow:hidden;}
.sf-line.done-line{background:rgba(16,185,129,0.6);box-shadow:0 0 8px rgba(16,185,129,0.3);}
.sf-line.active-line{background:linear-gradient(90deg,rgba(16,185,129,0.6),rgba(212,168,67,0.6));}
.sf-line.active-line::after{content:'';position:absolute;top:0;left:-100%;width:50%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.8),transparent);animation:shimmer 2s infinite;}
@keyframes shimmer{100%{left:200%;}}
.toast-msg{position:fixed;top:80px;right:28px;z-index:9999;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#10b981;padding:16px 24px;border-radius:12px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:12px;backdrop-filter:blur(16px);animation:slideIn 0.4s cubic-bezier(0.175,0.885,0.32,1.275) forwards;box-shadow:0 12px 40px rgba(0,0,0,0.4);}
@keyframes slideIn{from{opacity:0;transform:translateX(50px) scale(0.9);}to{opacity:1;transform:translateX(0) scale(1);}}
.action-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-top:24px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.06);}
.action-buttons{display:flex;gap:12px;flex-wrap:wrap;}
@media(max-width:992px){.detail-grid{grid-template-columns:1fr;}.status-bar{flex-direction:column;align-items:stretch;}.status-flow{margin:24px 0;}}
</style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>

    <?php if ($toast === 'success'): ?>
    <div class="toast-msg" id="toastMsg">
        <i class="bi bi-check-circle-fill" style="font-size: 18px; filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.5));"></i> 
        Cập nhật trạng thái thành công!
    </div>
    <script>setTimeout(()=>{const t=document.getElementById('toastMsg');if(t){t.style.animation='slideIn 0.4s reverse forwards';setTimeout(()=>t.remove(),400);}},3500);</script>
    <?php endif; ?>

    <div class="page-body">

        <div class="page-header">
            <div>
                <div class="page-title">Chi tiết lịch lái thử</div>
                <div class="page-subtitle" style="display: flex; align-items: center; gap: 8px;">
                    <span style="color: var(--gold); font-weight: 600;">#<?= str_pad((string)$id, 5, '0', STR_PAD_LEFT) ?></span>
                    <span style="color: rgba(255,255,255,0.2);">|</span>
                    <i class="bi bi-calendar-event"></i> Đăng ký lúc <?= date('H:i d/m/Y', strtotime($booking['created_at'])) ?>
                </div>
            </div>
            <a href="bookings.php" class="btn btn-outline-gold" style="padding: 10px 20px;">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="status-bar">
            <div style="display: flex; flex-direction: column; gap: 8px; min-width: 180px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted);">Trạng thái hiện tại</div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="badge badge-<?= $cur['cls'] ?>" style="font-size: 14px; padding: 8px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <i class="bi bi-<?= $cur['icon'] ?>"></i> <?= $cur['label'] ?>
                    </span>
                    <?php if ($booking['status'] === 'rejected'): ?>
                        <span style="font-size: 13px; color: var(--danger); font-weight: 500;"><i class="bi bi-exclamation-triangle"></i> Đã từ chối</span>
                    <?php endif; ?>
                </div>
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
                    $isActive = ($step['key'] === $booking['status']) || ($booking['status'] !== 'rejected' && $stepOrder === $curOrder);
                    $isNextActive = ($booking['status'] !== 'rejected' && $stepOrder + 1 === $curOrder);
                    
                    $dotCls = $isDone ? 'done-step' : ($isActive ? 'active' : '');
                ?>
                <div class="sf-step">
                    <div class="sf-dot <?= $dotCls ?>">
                        <i class="bi bi-<?= $isDone ? 'check-lg' : $step['icon'] ?>"></i>
                    </div>
                    <div class="sf-label"><?= $step['label'] ?></div>
                </div>
                <?php if ($i < count($steps) - 1): 
                    $lineCls = $isDone ? ($isNextActive ? 'active-line' : 'done-line') : '';
                ?>
                <div class="sf-line <?= $lineCls ?>"></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <form method="POST" style="display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.3); padding: 12px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); box-shadow: inset 0 2px 8px rgba(0,0,0,0.2);">
                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500; white-space: nowrap;">Cập nhật:</div>
                <select name="status" class="form-select" style="width: 140px; background: var(--bg-primary); border-color: rgba(255,255,255,0.1); color: var(--text); box-shadow: none;">
                    <?php foreach ($statusMap as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $booking['status'] === $k ? 'selected' : '' ?>>
                        <?= $v['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-gold" style="padding: 8px 16px;">
                    <i class="bi bi-check2-circle" style="font-size: 15px;"></i> Lưu
                </button>
            </form>
        </div>

        <div class="detail-grid">

            <!-- Cột 1: Thông tin khách hàng -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-person-bounding-box"></i>
                    <h3>Thông tin khách hàng</h3>
                </div>
                <div class="detail-card-body">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; background: rgba(255,255,255,0.02); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.04);">
                        <div class="avatar-lg"><?= strtoupper(substr($booking['full_name'], 0, 1)) ?></div>
                        <div>
                            <div style="font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 0.5px;"><?= htmlspecialchars($booking['full_name']) ?></div>
                            <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--gold); margin-top: 6px; background: rgba(212,168,67,0.1); padding: 4px 10px; border-radius: 12px; border: 1px solid rgba(212,168,67,0.2);">
                                <i class="bi bi-person-badge"></i> Khách hàng đặt lịch
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-telephone" style="color: var(--gold); font-size: 14px;"></i> Số điện thoại</div>
                        <div class="info-value">
                            <a href="tel:<?= htmlspecialchars($booking['phone']) ?>" style="color: #fff; font-size: 16px; font-weight: 600; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($booking['phone']) ?>
                            </a>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="bi bi-envelope" style="color: var(--gold); font-size: 14px;"></i> Email</div>
                        <div class="info-value">
                            <?php if (!empty($booking['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($booking['email']) ?>" style="color: var(--text-dim);">
                                <?= htmlspecialchars($booking['email']) ?>
                            </a>
                            <?php else: ?>
                            <span style="color: rgba(255,255,255,0.2); font-style: italic;">Chưa cung cấp</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($booking['message'])): ?>
                    <div class="info-row" style="border-bottom: none;">
                        <div class="info-label"><i class="bi bi-chat-right-quote" style="color: var(--gold); font-size: 14px;"></i> Ghi chú / Yêu cầu</div>
                        <div class="info-value" style="background: rgba(0,0,0,0.2); padding: 16px; border-radius: 12px; border-left: 3px solid var(--gold); color: var(--text-dim); font-style: italic; line-height: 1.6; margin-top: 8px;">
                            "<?= nl2br(htmlspecialchars($booking['message'])) ?>"
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột 2: Thông tin lịch hẹn -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-calendar2-week"></i>
                    <h3>Thông tin lịch hẹn</h3>
                </div>
                <div class="detail-card-body">
                    <div class="car-preview-wrap">
                        <?php if (!empty($booking['car_image'])): ?>
                            <img src="../assets/image/cars/<?= htmlspecialchars($booking['car_image']) ?>" class="car-preview" alt="<?= htmlspecialchars($booking['car_name']) ?>">
                        <?php else: ?>
                            <div class="car-preview-placeholder"><i class="bi bi-car-front"></i></div>
                        <?php endif; ?>
                        
                        <div class="car-preview-info">
                            <div style="font-family: 'Orbitron', sans-serif; font-size: 20px; font-weight: 700; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.8); margin-bottom: 4px;">
                                <?= htmlspecialchars($booking['car_name']) ?>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="badge badge-gold" style="background: rgba(212,168,67,0.3); backdrop-filter: blur(4px); border: none; color: #fff;"><i class="bi bi-award"></i> <?= htmlspecialchars($booking['brand_name']) ?></span>
                                </div>
                                <?php if (!empty($booking['price'])): ?>
                                <div style="font-size: 16px; color: var(--gold); font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                                    <?= number_format((float)$booking['price'], 0, ',', '.') ?> ₫
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="info-row" style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.03);">
                            <div class="info-label" style="justify-content: center;"><i class="bi bi-calendar-event" style="color: var(--gold);"></i> Ngày hẹn</div>
                            <div class="info-value" style="font-size: 16px; text-align: center; color: #fff; margin-top: 4px;">
                                <?php
                                $d = strtotime($booking['preferred_date']);
                                $days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
                                echo $days[(int)date('w', $d)] . '<br><span style="font-weight:700; font-size:18px;">' . date('d/m/Y', $d) . '</span>';
                                ?>
                            </div>
                        </div>
                        
                        <div class="info-row" style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.03);">
                            <div class="info-label" style="justify-content: center;"><i class="bi bi-clock-history" style="color: var(--gold);"></i> Giờ hẹn</div>
                            <div class="info-value" style="font-size: 24px; text-align: center; font-weight: 700; font-family: 'Orbitron', sans-serif; color: var(--gold); margin-top: 4px; text-shadow: 0 0 12px rgba(212,168,67,0.3);">
                                <?= date('H:i', strtotime($booking['preferred_time'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $today = strtotime('today');
                    $apptDay = strtotime($booking['preferred_date']);
                    $diffDays = (int)round(($apptDay - $today) / 86400);
                    if ($diffDays > 0 && !in_array($booking['status'], ['done', 'rejected'], true)):
                    ?>
                    <div style="margin-top: 20px; background: linear-gradient(90deg, rgba(212,168,67,0.1), rgba(212,168,67,0.02)); border-left: 3px solid var(--gold); border-radius: 8px; padding: 12px 16px; font-size: 14px; color: var(--gold); display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-hourglass-split" style="font-size: 18px; animation: pulse 2s infinite;"></i>
                        <div>Còn <strong style="font-size: 16px;"><?= $diffDays ?> ngày</strong> đến buổi lái thử</div>
                    </div>
                    <?php elseif ($diffDays === 0 && $booking['status'] === 'approved'): ?>
                    <div style="margin-top: 20px; background: linear-gradient(90deg, rgba(16,185,129,0.15), rgba(16,185,129,0.02)); border-left: 3px solid #10b981; border-radius: 8px; padding: 12px 16px; font-size: 14px; color: #10b981; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-calendar-check-fill" style="font-size: 18px; filter: drop-shadow(0 0 8px rgba(16,185,129,0.5));"></i>
                        <div><strong style="font-size: 16px;">Hôm nay</strong> là ngày lái thử!</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="action-buttons">
                <a href="tel:<?= htmlspecialchars($booking['phone']) ?>" class="btn btn-outline-gold" style="padding: 10px 20px; font-size: 14px;">
                    <i class="bi bi-telephone-fill"></i> Gọi khách hàng
                </a>
                <?php if (!empty($booking['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($booking['email']) ?>" class="btn btn-info-outline" style="padding: 10px 20px; font-size: 14px;">
                    <i class="bi bi-envelope-fill"></i> Gửi email
                </a>
                <?php endif; ?>
            </div>
            <a href="bookings.php" class="btn btn-outline-gold" style="margin-left: auto; padding: 10px 20px; font-size: 14px;">
                <i class="bi bi-arrow-left"></i> Về danh sách
            </a>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html> 
