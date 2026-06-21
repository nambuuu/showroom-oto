<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Test Drive Bookings';

$statusFilter = $_GET['status'] ?? '';
$searchTerm   = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage= 20;
$offset = ($page-1)*$perPage;

$sql = "SELECT b.id, b.full_name AS customer_name, c.model_name AS car_name,
               b.preferred_date, b.preferred_time, b.status, b.phone
        FROM bookings b JOIN cars c ON b.car_id=c.id";
$params=[]; $conditions=[];
if($statusFilter!==''){$conditions[]='b.status=:status';$params[':status']=$statusFilter;}
if($searchTerm!==''){$conditions[]='(b.full_name LIKE :s OR c.model_name LIKE :s)';$params[':s']="%$searchTerm%";}
if($conditions) $sql.=' WHERE '.implode(' AND ',$conditions);

$countStmt=$pdo->prepare("SELECT COUNT(*) FROM ($sql) AS sub");
$countStmt->execute($params);
$totalRows=(int)$countStmt->fetchColumn();
$totalPages=(int)ceil($totalRows/$perPage);

$sql.=" ORDER BY b.id DESC LIMIT $perPage OFFSET $offset";
$stmt=$pdo->prepare($sql);$stmt->execute($params);
$bookings=$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Test Drive Bookings – Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">

        <div class="page-header">
            <div>
                <div class="page-title">Test Drive Bookings</div>
                <div class="page-subtitle"><?= number_format($totalRows) ?> appointments</div>
            </div>
        </div>

        <!-- Stat mini row -->
        <?php
        $allStatus = $pdo->query("SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status")->fetchAll();
        $statusMap = [];
        foreach($allStatus as $r) $statusMap[$r['status']] = $r['cnt'];
        $statuses = [
            ['pending',  'Pending',  'warning', 'clock'],
            ['approved', 'Approved',   'success', 'check-circle-fill'],
            ['rejected', 'Rejected',    'danger',  'x-circle-fill'],
            ['done',     'Done', 'info',    'flag-fill'],
        ];
        ?>
        <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
            <?php foreach($statuses as [$key,$label,$cls,$icon]): ?>
            <a href="?status=<?=$key?>" style="text-decoration:none;flex:1;min-width:120px">
                <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:14px 16px;text-align:center;transition:all .3s;<?= $statusFilter===$key?'border-color:var(--border-gold);background:rgba(212,168,67,0.05)':'' ?>">
                    <i class="bi bi-<?=$icon?>" style="font-size:20px;display:block;margin-bottom:6px"></i>
                    <div style="font-size:20px;font-weight:700;color:var(--text)"><?= $statusMap[$key] ?? 0 ?></div>
                    <div style="font-size:11px;color:var(--text-muted)"><?=$label?></div>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if($statusFilter): ?>
                <a href="bookings.php" class="btn btn-sm btn-danger-outline" style="align-self:center;white-space:nowrap">
                    <i class="bi bi-x"></i> Clear filter
                </a>
            <?php endif; ?>
        </div>

        <!-- Filter -->
        <div class="filter-bar">
            <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;width:100%">
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
                <i class="bi bi-search" style="color:var(--gold)"></i>
                <input type="text" name="search" class="form-control" placeholder="Search customer or car..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width:280px">
                <button type="submit" class="btn btn-outline-gold"><i class="bi bi-search"></i> Search</button>
                <span style="margin-left:auto;font-size:12px;color:var(--text-muted)"><?= count($bookings) ?>/<?= number_format($totalRows) ?> results</span>
            </form>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Registered car</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($bookings as $i => $b): ?>
                    <?php
                    $bdgMap = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','done'=>'info'];
                    $bdg = $bdgMap[$b['status']] ?? 'secondary';
                    $lblMap = ['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','done'=>'Done'];
                    $lbl = $lblMap[$b['status']] ?? ucfirst($b['status']);
                    ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px"><?= $i+1+$offset ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="avatar-initial" style="width:32px;height:32px;font-size:12px">
                                    <?= strtoupper(substr($b['customer_name'],0,1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($b['customer_name']) ?></div>
                                    <?php if(!empty($b['phone'])): ?>
                                    <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($b['phone']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight:500"><?= htmlspecialchars($b['car_name']) ?></td>
                        <td>
                            <i class="bi bi-calendar2" style="color:var(--gold);margin-right:4px"></i>
                            <?= date('d/m/Y', strtotime($b['preferred_date'])) ?>
                        </td>
                        <td>
                            <i class="bi bi-clock" style="color:var(--text-muted);margin-right:4px"></i>
                            <?= date('H:i', strtotime($b['preferred_time'])) ?>
                        </td>
                        <td><span class="badge badge-<?= $bdg ?>"><?= $lbl ?></span></td>
                        <td>
                            <a href="booking_detail.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-gold">
                                <i class="bi bi-eye-fill"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($bookings)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        No appointments found
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if($totalPages>1): ?>
            <div class="custom-pagination">
                <?php if($page>1): ?><a href="?page=<?=$page-1?>&status=<?=$statusFilter?>&search=<?=urlencode($searchTerm)?>"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
                <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                    <?php if($p==$page): ?><span class="active"><?=$p?></span>
                    <?php else: ?><a href="?page=<?=$p?>&status=<?=$statusFilter?>&search=<?=urlencode($searchTerm)?>"><?=$p?></a><?php endif; ?>
                <?php endfor; ?>
                <?php if($page<$totalPages): ?><a href="?page=<?=$page+1?>&status=<?=$statusFilter?>&search=<?=urlencode($searchTerm)?>"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
