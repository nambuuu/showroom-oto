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
    "SELECT b.id, b.full_name AS customer_name, c.model_name AS car_name, b.preferred_date, b.preferred_time, b.status
     FROM bookings b JOIN cars c ON b.car_id=c.id ORDER BY b.created_at DESC LIMIT 6"
)->fetchAll();

// Recent contacts
$recentContacts = $pdo->query(
    "SELECT id, full_name AS name, email, subject, created_at, is_read FROM contacts ORDER BY created_at DESC LIMIT 6"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard – Showroom Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
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
            <div style="font-size:13px;color:var(--text-muted)">
                <i class="bi bi-calendar3"></i>
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <!-- Stat Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:28px">

            <div class="stat-card" style="--card-accent:linear-gradient(90deg,#d4a843,#a67c2e);--icon-bg:rgba(212,168,67,0.1);--icon-color:#d4a843">
                <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
                <div class="stat-value" data-count="<?= $carTotal ?>"><?= number_format($carTotal) ?></div>
                <div class="stat-label">Tổng số xe</div>
                <span class="stat-change" style="background:rgba(212,168,67,0.1);color:var(--gold)"><i class="bi bi-check-circle"></i> Active</span>
            </div>

            <div class="stat-card" style="--card-accent:linear-gradient(90deg,#f59e0b,#d97706);--icon-bg:rgba(245,158,11,0.1);--icon-color:#f59e0b">
                <div class="stat-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                <div class="stat-value" data-count="<?= $bookingTotal ?>"><?= number_format($bookingTotal) ?></div>
                <div class="stat-label">Lịch chờ duyệt</div>
                <span class="stat-change" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="bi bi-clock"></i> Pending</span>
            </div>

            <div class="stat-card" style="--card-accent:linear-gradient(90deg,#ef4444,#b91c1c);--icon-bg:rgba(239,68,68,0.1);--icon-color:#ef4444">
                <div class="stat-icon"><i class="bi bi-envelope-fill"></i></div>
                <div class="stat-value" data-count="<?= $contactTotal ?>"><?= number_format($contactTotal) ?></div>
                <div class="stat-label">Liên hệ chưa đọc</div>
                <span class="stat-change" style="background:rgba(239,68,68,0.1);color:#ef4444"><i class="bi bi-exclamation-circle"></i> Mới</span>
            </div>

            <div class="stat-card" style="--card-accent:linear-gradient(90deg,#10b981,#059669);--icon-bg:rgba(16,185,129,0.1);--icon-color:#10b981">
                <div class="stat-icon"><i class="bi bi-award-fill"></i></div>
                <div class="stat-value" data-count="<?= $brandTotal ?>"><?= number_format($brandTotal) ?></div>
                <div class="stat-label">Tổng hãng xe</div>
                <span class="stat-change" style="background:rgba(16,185,129,0.1);color:#10b981"><i class="bi bi-graph-up"></i> Brands</span>
            </div>

        </div>

        <!-- Charts row -->
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:28px">

            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-pie-chart-fill me-2"></i> Trạng thái lịch hẹn</h5>
                </div>
                <div class="card-glass-body" style="display:flex;align-items:center;justify-content:center">
                    <canvas id="bookingChart" style="max-height:220px"></canvas>
                </div>
            </div>

            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-bar-chart-fill me-2"></i> Xe theo hãng</h5>
                </div>
                <div class="card-glass-body">
                    <canvas id="brandChart" style="max-height:220px"></canvas>
                </div>
            </div>

        </div>

        <!-- Tables row -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

            <!-- Recent bookings -->
            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-calendar-event me-2"></i> Lịch lái thử gần đây</h5>
                    <a href="bookings.php" class="btn btn-outline-gold btn-sm"> Xem tất cả</a>
                </div>
                <div class="admin-table-wrap" style="border:none;border-radius:0">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Xe</th>
                                <th>Ngày</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($recentDrives as $row): ?>
                            <?php
                            $bdgMap = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','done'=>'info'];
                            $bdg = $bdgMap[$row['status']] ?? 'secondary';
                            $initials = strtoupper(substr($row['customer_name'],0,1));
                            ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="avatar-initial" style="width:30px;height:30px;font-size:11px"><?= $initials ?></div>
                                        <span style="font-size:13px"><?= htmlspecialchars($row['customer_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['car_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['preferred_date'])) ?></td>
                                <td><span class="badge badge-<?= $bdg ?>"><?= ucfirst($row['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent contacts -->
            <div class="card-glass">
                <div class="card-glass-header">
                    <h5><i class="bi bi-envelope-open me-2"></i> Liên hệ gần đây</h5>
                    <a href="contacts.php" class="btn btn-outline-gold btn-sm">Xem tất cả</a>
                </div>
                <div class="admin-table-wrap" style="border:none;border-radius:0">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Họ tên</th>
                                <th>Chủ đề</th>
                                <th>Ngày</th>
                                <th>Đã đọc</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($recentContacts as $c): ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="avatar-initial" style="width:30px;height:30px;font-size:11px;background:linear-gradient(135deg,#3b82f6,#1d4ed8)">
                                            <?= strtoupper(substr($c['name'],0,1)) ?>
                                        </div>
                                        <span style="font-size:13px"><?= htmlspecialchars($c['name']) ?></span>
                                    </div>
                                </td>
                                <td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    <?= htmlspecialchars($c['subject']) ?>
                                </td>
                                <td><?= date('d/m', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <?php if($c['is_read']): ?>
                                        <span class="badge badge-success"><i class="bi bi-check2"></i> Đọc</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="bi bi-dot"></i> Mới</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

// Booking donut
new Chart(document.getElementById('bookingChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($bkLabels) ?>,
        datasets: [{
            data: <?= json_encode($bkData) ?>,
            backgroundColor: ['#f59e0b','#39b38bff','#ef4444','#3b82f6','#6366f1'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } } }
    }
});

// Brand bar chart
new Chart(document.getElementById('brandChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($brandNames) ?>,
        datasets: [{
            label: 'Số xe',
            data: <?= json_encode($brandCnts) ?>,
            backgroundColor: 'rgba(212,168,67,0.7)',
            borderColor: '#d4a843',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.04)' } },
            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1 } }
        }
    }
});
</script>
</body>
</html>
