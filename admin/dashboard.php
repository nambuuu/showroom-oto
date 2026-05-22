<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Dashboard';

// Totals
$carTotal     = $pdo->query('SELECT COUNT(*) FROM cars')->fetchColumn();
$bookingTotal = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$contactTotal = $pdo->query('SELECT COUNT(*) FROM contacts WHERE is_read=0')->fetchColumn();
$brandTotal   = $pdo->query('SELECT COUNT(*) FROM brands')->fetchColumn();

// Booking status breakdown for chart
$bkStatusRows = $pdo->query("SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status")->fetchAll();
$bkLabels = []; $bkData = [];
foreach($bkStatusRows as $r){ $bkLabels[]=$r['status']; $bkData[]=$r['cnt']; }

// Cars by brand for chart
$brandRows = $pdo->query("SELECT b.name, COUNT(c.id) AS cnt FROM brands b LEFT JOIN cars c ON c.brand_id=b.id GROUP BY b.id ORDER BY cnt DESC LIMIT 8")->fetchAll();
$brandNames=[]; $brandCnts=[];
foreach($brandRows as $r){ $brandNames[]=$r['name']; $brandCnts[]=$r['cnt']; }

// Recent bookings
$recentDrives = $pdo->query(
    "SELECT b.id, b.full_name AS customer_name, c.model_name AS car_name, b.preferred_date, b.preferred_time, b.status, b.created_at
     FROM bookings b JOIN cars c ON b.car_id=c.id ORDER BY b.created_at DESC LIMIT 5"
)->fetchAll();

// Recent contacts
$recentContacts = $pdo->query(
    "SELECT id, full_name AS name, email, subject, created_at, is_read FROM contacts ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

// Timeline activities
$activities = [];
foreach($recentDrives as $d) {
    $activities[] = [
        'type' => 'booking',
        'title' => 'Lịch lái thử mới',
        'desc' => $d['customer_name'] . ' muốn lái thử ' . $d['car_name'],
        'time' => $d['created_at'],
        'icon' => 'bi-car-front-fill',
        'color' => '#f59e0b'
    ];
}
foreach($recentContacts as $c) {
    $activities[] = [
        'type' => 'contact',
        'title' => 'Liên hệ mới',
        'desc' => $c['name'] . ' - ' . $c['subject'],
        'time' => $c['created_at'],
        'icon' => 'bi-envelope-fill',
        'color' => '#3b82f6'
    ];
}
usort($activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});
$activities = array_slice($activities, 0, 8); // Top 8 activities

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'năm',
        'm' => 'tháng',
        'w' => 'tuần',
        'd' => 'ngày',
        'h' => 'giờ',
        'i' => 'phút',
        's' => 'giây',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard – Showroom Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* Nâng cấp Stat Cards */
.stat-card {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 16px;
    position: relative;
    overflow: hidden;
}
.stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    box-shadow: inset 0 0 20px var(--card-color);
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
    border-radius: 16px;
}
.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--card-color);
    background: rgba(255, 255, 255, 0.04);
}
.stat-card:hover::after {
    opacity: 0.15;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 24px;
    margin-top: 10px;
}
.timeline::before {
    content: '';
    position: absolute;
    top: 0; bottom: 0; left: 6px;
    width: 2px;
    background: rgba(255, 255, 255, 0.08);
}
.timeline-item {
    position: relative;
    margin-bottom: 24px;
}
.timeline-icon {
    position: absolute;
    left: -24px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--bg-primary);
    border: 2px solid var(--t-color);
    z-index: 1;
    box-shadow: 0 0 8px var(--t-color);
}
.timeline-content {
    background: rgba(255, 255, 255, 0.02);
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.04);
    transition: all 0.3s;
}
.timeline-content:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.1);
}
.timeline-time {
    font-size: 11px;
    color: var(--text-muted);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.timeline-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 2px;
}
.timeline-desc {
    font-size: 12px;
    color: var(--text-dim);
}

/* Tabs */
.admin-tabs {
    display: flex;
    gap: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding: 0 24px;
    background: rgba(0,0,0,0.2);
}
.admin-tab {
    padding: 16px 0;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    position: relative;
    background: transparent;
    border: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: color 0.3s;
}
.admin-tab:hover {
    color: var(--text);
}
.admin-tab.active {
    color: var(--gold);
}
.admin-tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0; right: 0;
    height: 2px;
    background: var(--gold);
    box-shadow: 0 -2px 10px var(--gold-glow);
}
.tab-pane {
    display: none;
    animation: fadeIn 0.4s ease;
}
.tab-pane.active {
    display: block;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 992px) {
    .charts-row { grid-template-columns: 1fr !important; }
    .tables-row { grid-template-columns: 1fr !important; }
}
</style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>

    <div class="page-body">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <div class="page-title">Dashboard</div>
                <div class="page-subtitle">Chào mừng trở lại, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>!</div>
            </div>
            <div style="font-size:13px;color:var(--text-muted); display:flex; align-items:center; gap:6px; background: rgba(255,255,255,0.03); padding: 8px 16px; border-radius:20px; border: 1px solid rgba(255,255,255,0.05);">
                <i class="bi bi-calendar3" style="color:var(--gold);"></i>
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>

        <!-- Stat Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin-bottom:28px">

            <div class="stat-card" style="--card-color:#d4a843;">
                <div class="stat-icon" style="background:rgba(212,168,67,0.1);color:#d4a843;"><i class="bi bi-car-front-fill"></i></div>
                <div class="stat-value" data-count="<?= $carTotal ?>"><?= number_format($carTotal) ?></div>
                <div class="stat-label">Tổng số xe</div>
                <span class="stat-change" style="background:rgba(212,168,67,0.1);color:var(--gold)"><i class="bi bi-check-circle"></i> Active</span>
            </div>

            <div class="stat-card" style="--card-color:#f59e0b;">
                <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;"><i class="bi bi-calendar2-check-fill"></i></div>
                <div class="stat-value" data-count="<?= $bookingTotal ?>"><?= number_format($bookingTotal) ?></div>
                <div class="stat-label">Lịch chờ duyệt</div>
                <span class="stat-change" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="bi bi-clock"></i> Pending</span>
            </div>

            <div class="stat-card" style="--card-color:#ef4444;">
                <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;"><i class="bi bi-envelope-fill"></i></div>
                <div class="stat-value" data-count="<?= $contactTotal ?>"><?= number_format($contactTotal) ?></div>
                <div class="stat-label">Liên hệ chưa đọc</div>
                <span class="stat-change" style="background:rgba(239,68,68,0.1);color:#ef4444"><i class="bi bi-exclamation-circle"></i> Mới</span>
            </div>

            <div class="stat-card" style="--card-color:#10b981;">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="bi bi-award-fill"></i></div>
                <div class="stat-value" data-count="<?= $brandTotal ?>"><?= number_format($brandTotal) ?></div>
                <div class="stat-label">Tổng hãng xe</div>
                <span class="stat-change" style="background:rgba(16,185,129,0.1);color:#10b981"><i class="bi bi-graph-up"></i> Brands</span>
            </div>

        </div>

        <!-- Charts row -->
        <div class="charts-row" style="display:grid;grid-template-columns:1fr 2fr;gap:24px;margin-bottom:28px">

            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-pie-chart-fill me-2"></i> Trạng thái lịch hẹn</h5>
                </div>
                <div class="card-glass-body" style="display:flex;align-items:center;justify-content:center">
                    <div style="position: relative; height:240px; width:100%;">
                        <canvas id="bookingChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-bar-chart-fill me-2"></i> Xe theo hãng</h5>
                </div>
                <div class="card-glass-body">
                    <div style="position: relative; height:240px; width:100%;">
                        <canvas id="brandChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tables & Timeline row -->
        <div class="tables-row" style="display:grid;grid-template-columns:1fr 2.5fr;gap:24px">

            <!-- Timeline -->
            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-activity me-2"></i> Hoạt động gần đây</h5>
                </div>
                <div class="card-glass-body">
                    <div class="timeline">
                        <?php if(empty($activities)): ?>
                            <div style="color:var(--text-muted); font-size:13px; text-align:center; padding: 20px 0;">Không có hoạt động nào.</div>
                        <?php else: ?>
                            <?php foreach($activities as $act): ?>
                            <div class="timeline-item" style="--t-color: <?= $act['color'] ?>;">
                                <div class="timeline-icon"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time"><i class="bi bi-clock"></i> <?= time_elapsed_string($act['time']) ?></div>
                                    <div class="timeline-title" style="color: <?= $act['color'] ?>;"><?= $act['title'] ?></div>
                                    <div class="timeline-desc"><?= htmlspecialchars($act['desc']) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tabs Data -->
            <div class="card-glass" style="display:flex; flex-direction:column;">
                <div class="admin-tabs">
                    <button class="admin-tab active" onclick="switchTab('tab-bookings', this)">
                        <i class="bi bi-calendar-event me-1"></i> Lịch lái thử mới
                    </button>
                    <button class="admin-tab" onclick="switchTab('tab-contacts', this)">
                        <i class="bi bi-envelope-open me-1"></i> Liên hệ mới
                    </button>
                </div>
                
                <div style="flex:1;">
                    <!-- Tab Bookings -->
                    <div id="tab-bookings" class="tab-pane active">
                        <div class="admin-table-wrap" style="border:none;border-radius:0">
                            <table class="admin-table" style="min-width: 600px;">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Xe</th>
                                        <th>Ngày/Giờ hẹn</th>
                                        <th>Trạng thái</th>
                                        <th style="text-align:right">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(empty($recentDrives)): ?>
                                    <tr><td colspan="5" style="text-align:center; padding: 30px;">Không có dữ liệu</td></tr>
                                <?php else: ?>
                                    <?php foreach($recentDrives as $row): ?>
                                        <?php
                                        $bdgMap = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','done'=>'info'];
                                        $bdg = $bdgMap[$row['status']] ?? 'secondary';
                                        $initials = strtoupper(substr($row['customer_name'],0,1));
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px">
                                                    <div class="avatar-initial" style="width:32px;height:32px;font-size:12px;background:rgba(212,168,67,0.15);color:var(--gold);display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:700; border:1px solid rgba(212,168,67,0.3)"><?= $initials ?></div>
                                                    <span style="font-size:13px; font-weight:500;"><?= htmlspecialchars($row['customer_name']) ?></span>
                                                </div>
                                            </td>
                                            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($row['car_name']) ?></td>
                                            <td>
                                                <div style="font-size:13px; color:var(--text);"><i class="bi bi-calendar-day" style="color:var(--text-dim); margin-right:4px;"></i> <?= date('d/m/Y', strtotime($row['preferred_date'])) ?></div>
                                                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;"><i class="bi bi-clock" style="margin-right:4px;"></i> <?= date('H:i', strtotime($row['preferred_time'])) ?></div>
                                            </td>
                                            <td><span class="badge badge-<?= $bdg ?>"><?= ucfirst($row['status']) ?></span></td>
                                            <td style="text-align:right">
                                                <a href="booking_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-gold btn-sm"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="padding: 15px 24px; border-top: 1px solid var(--border); text-align:right;">
                            <a href="bookings.php" class="btn btn-gold btn-sm" style="color:#000;">Xem tất cả lịch hẹn <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Tab Contacts -->
                    <div id="tab-contacts" class="tab-pane">
                        <div class="admin-table-wrap" style="border:none;border-radius:0">
                            <table class="admin-table" style="min-width: 600px;">
                                <thead>
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Chủ đề</th>
                                        <th>Trạng thái</th>
                                        <th style="text-align:right">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(empty($recentContacts)): ?>
                                    <tr><td colspan="5" style="text-align:center; padding: 30px;">Không có dữ liệu</td></tr>
                                <?php else: ?>
                                    <?php foreach($recentContacts as $c): ?>
                                        <tr>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px">
                                                    <div class="avatar-initial" style="width:32px;height:32px;font-size:12px;background:rgba(59,130,246,0.15);color:#3b82f6;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:700; border:1px solid rgba(59,130,246,0.3)">
                                                        <?= strtoupper(substr($c['name'],0,1)) ?>
                                                    </div>
                                                    <span style="font-size:13px; font-weight:500;"><?= htmlspecialchars($c['name']) ?></span>
                                                </div>
                                            </td>
                                            <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:var(--text-muted); font-size:13px; text-decoration:underline;"><?= htmlspecialchars($c['email']) ?></a></td>
                                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; font-weight:500;">
                                                <?= htmlspecialchars($c['subject']) ?>
                                            </td>
                                            <td>
                                                <?php if($c['is_read']): ?>
                                                    <span class="badge badge-success"><i class="bi bi-check2-all"></i> Đã đọc</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="bi bi-envelope-exclamation"></i> Mới</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:right">
                                                <a href="contact_view.php?id=<?= $c['id'] ?>" class="btn btn-info-outline btn-sm"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="padding: 15px 24px; border-top: 1px solid var(--border); text-align:right;">
                            <a href="contacts.php" class="btn btn-info-outline btn-sm">Xem tất cả liên hệ <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Tab Switching logic
function switchTab(tabId, btn) {
    document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// Chart.js Configuration
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(255,255,255,0.04)';
Chart.defaults.font.family = "'Inter', sans-serif";

// Booking donut
new Chart(document.getElementById('bookingChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($bkLabels) ?>,
        datasets: [{
            data: <?= json_encode($bkData) ?>,
            backgroundColor: ['#f59e0b','#10b981','#ef4444','#3b82f6','#8b5cf6'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                position: 'bottom', 
                labels: { padding: 20, font: { size: 12 }, usePointStyle: true } 
            },
            tooltip: {
                backgroundColor: 'rgba(8, 12, 20, 0.9)',
                titleFont: { size: 13 },
                bodyFont: { size: 13 },
                padding: 12,
                borderColor: 'rgba(212, 168, 67, 0.3)',
                borderWidth: 1
            }
        }
    }
});

// Brand bar chart
const brandGradient = document.getElementById('brandChart').getContext('2d').createLinearGradient(0, 0, 0, 400);
brandGradient.addColorStop(0, 'rgba(212,168,67,0.8)');
brandGradient.addColorStop(1, 'rgba(212,168,67,0.1)');

new Chart(document.getElementById('brandChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($brandNames) ?>,
        datasets: [{
            label: 'Số xe',
            data: <?= json_encode($brandCnts) ?>,
            backgroundColor: brandGradient,
            borderColor: '#d4a843',
            borderWidth: 1,
            borderRadius: 6,
            barThickness: 32
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(8, 12, 20, 0.9)',
                padding: 12,
                borderColor: 'rgba(212, 168, 67, 0.3)',
                borderWidth: 1
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: { 
                beginAtZero: true, 
                grid: { color: 'rgba(255,255,255,0.03)' }, 
                ticks: { stepSize: 1 } 
            }
        }
    }
});
</script>
</body>
</html>
