<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Liên hệ';

if(isset($_GET['del'])){
    $delId=(int)$_GET['del'];
    $pdo->prepare('DELETE FROM contacts WHERE id=:id')->execute([':id'=>$delId]);
    header('Location: contacts.php'); exit;
}

$search=$_GET['search'] ?? '';
$isRead=$_GET['is_read'] ?? '';
$page=max(1,(int)($_GET['page']??1));
$perPage=20; $offset=($page-1)*$perPage;

$sql="SELECT id, full_name AS name, email, subject, created_at, is_read FROM contacts";
$params=[]; $conditions=[];
if(trim($search)!==''){
    $conditions[]="(full_name LIKE :s OR email LIKE :s OR subject LIKE :s)";
    $params[':s']="%".trim($search)."%";
}
if($isRead!==''){$conditions[]='is_read=:ir';$params[':ir']=$isRead;}
if($conditions) $sql.=' WHERE '.implode(' AND ',$conditions);

$countStmt=$pdo->prepare("SELECT COUNT(*) FROM ($sql) AS sub");
$countStmt->execute($params);
$totalRows=(int)$countStmt->fetchColumn();
$totalPages=(int)ceil($totalRows/$perPage);
$sql.=" ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt=$pdo->prepare($sql);$stmt->execute($params);
$contacts=$stmt->fetchAll();

$unread=(int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Liên hệ – Admin</title>
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
                <div class="page-title">Yêu cầu liên hệ</div>
                <div class="page-subtitle">
                    <?= number_format($totalRows) ?> liên hệ
                    <?php if($unread>0): ?>
                        &nbsp;·&nbsp; <span style="color:#ef4444;font-weight:600"><?= $unread ?> chưa đọc</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick filter tabs -->
        <div style="display:flex;gap:8px;margin-bottom:20px">
            <a href="contacts.php" class="btn btn-sm <?= $isRead===''?'btn-gold':'btn-outline-gold' ?>">Tất cả</a>
            <a href="contacts.php?is_read=0" class="btn btn-sm <?= $isRead==='0'?'btn-gold':'btn-outline-gold' ?>">
                <i class="bi bi-envelope-fill"></i> Chưa đọc
                <?php if($unread>0): ?><span style="background:#ef4444;color:#fff;border-radius:10px;padding:0 6px;font-size:10px;margin-left:4px"><?=$unread?></span><?php endif; ?>
            </a>
            <a href="contacts.php?is_read=1" class="btn btn-sm <?= $isRead==='1'?'btn-gold':'btn-outline-gold' ?>">
                <i class="bi bi-envelope-open-fill"></i> Đã đọc
            </a>
        </div>

        <!-- Filter -->
        <div class="filter-bar">
            <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;width:100%">
                <input type="hidden" name="is_read" value="<?= htmlspecialchars($isRead) ?>">
                <i class="bi bi-search" style="color:var(--gold)"></i>
                <input type="text" name="search" class="form-control" placeholder="Tìm tên, email, chủ đề..." value="<?= htmlspecialchars($search) ?>" style="max-width:300px">
                <button type="submit" class="btn btn-outline-gold"><i class="bi bi-search"></i> Tìm</button>
                <?php if($search): ?>
                    <a href="contacts.php?is_read=<?=$isRead?>" class="btn btn-sm btn-danger-outline"><i class="bi bi-x"></i> Xóa lọc</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Chủ đề</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($contacts as $i => $c): ?>
                    <tr style="<?= !$c['is_read']?'background:rgba(239,68,68,0.03)':'' ?>">
                        <td style="color:var(--text-muted);font-size:12px"><?= $i+1+$offset ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="avatar-initial" style="width:32px;height:32px;font-size:12px;<?= !$c['is_read']?'background:linear-gradient(135deg,#ef4444,#b91c1c)':'' ?>">
                                    <?= strtoupper(substr($c['name'],0,1)) ?>
                                </div>
                                <span style="font-weight:<?= !$c['is_read']?'700':'500' ?>"><?= htmlspecialchars($c['name']) ?></span>
                            </div>
                        </td>
                        <td style="font-size:13px;color:var(--text-dim)"><?= htmlspecialchars($c['email']) ?></td>
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:<?= !$c['is_read']?'600':'400' ?>">
                            <?= htmlspecialchars($c['subject']) ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted)">
                            <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                        </td>
                        <td>
                            <?php if($c['is_read']): ?>
                                <span class="badge badge-success"><i class="bi bi-check2-circle"></i> Đã đọc</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><i class="bi bi-envelope-fill"></i> Chưa đọc</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="contact_view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-eye-fill"></i></a>
                            <button class="btn btn-sm btn-danger-outline" onclick="if(confirm('Xóa liên hệ này?')){window.location='contacts.php?del=<?= $c['id'] ?>';}">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($contacts)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Không có liên hệ nào
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if($totalPages>1): ?>
            <div class="custom-pagination">
                <?php if($page>1): ?><a href="?page=<?=$page-1?>&search=<?=urlencode($search)?>&is_read=<?=$isRead?>"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
                <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                    <?php if($p==$page): ?><span class="active"><?=$p?></span>
                    <?php else: ?><a href="?page=<?=$p?>&search=<?=urlencode($search)?>&is_read=<?=$isRead?>"><?=$p?></a><?php endif; ?>
                <?php endfor; ?>
                <?php if($page<$totalPages): ?><a href="?page=<?=$page+1?>&search=<?=urlencode($search)?>&is_read=<?=$isRead?>"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
