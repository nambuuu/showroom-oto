<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Administrators';

if(isset($_GET['del'])){
    $delId=(int)$_GET['del'];
    if($delId!==($_SESSION['admin_id']??0)){
        $pdo->prepare('DELETE FROM admin_users WHERE id=:id')->execute([':id'=>$delId]);
    }
    header('Location: users.php'); exit;
}

$users=$pdo->query('SELECT id, username, full_name, email, role, created_at FROM admin_users ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Administrators – Admin</title>
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
                <div class="page-title">Administrators</div>
                <div class="page-subtitle"><?= count($users) ?> accounts in the system</div>
            </div>
            <a href="users_add.php" class="btn btn-gold">
                <i class="bi bi-person-plus-fill"></i> Add account
            </a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Account</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $i => $u): ?>
                    <?php
                    $isSelf = ($u['id'] === ($_SESSION['admin_id'] ?? 0));
                    $colors = ['#d4a843','#3b82f6','#10b981','#8b5cf6','#ec4899'];
                    $avatarColor = $colors[$i % count($colors)];
                    ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px"><?= $i+1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar-initial" style="background:linear-gradient(135deg,<?= $avatarColor ?>,<?= $avatarColor ?>99);font-size:14px;width:38px;height:38px">
                                    <?= strtoupper(substr($u['full_name'],0,1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px">
                                        <?= htmlspecialchars($u['full_name']) ?>
                                        <?php if($isSelf): ?>
                                            <span class="badge badge-gold" style="margin-left:4px;font-size:9px">YOU</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code style="background:rgba(255,255,255,0.06);padding:3px 8px;border-radius:6px;font-size:12px;color:var(--text-dim)">
                                <?= htmlspecialchars($u['username']) ?>
                            </code>
                        </td>
                        <td style="font-size:13px;color:var(--text-dim)"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if($u['role']==='superadmin'): ?>
                                <span class="badge badge-gold"><i class="bi bi-shield-fill-check"></i> Superadmin</span>
                            <?php elseif($u['role']==='admin'): ?>
                                <span class="badge badge-info"><i class="bi bi-shield-fill"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><i class="bi bi-person-fill"></i> <?= ucfirst($u['role']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted)">
                            <?php if(!empty($u['created_at'])): ?>
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            <?php else: ?>&mdash;<?php endif; ?>
                        </td>
                        <td>
                            <a href="users_edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-gold">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <?php if(!$isSelf): ?>
                            <button class="btn btn-sm btn-danger-outline" onclick="if(confirm('Delete account <?= htmlspecialchars($u['full_name'],ENT_QUOTES) ?>?')){window.location='users.php?del=<?= $u['id'] ?>';}">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-sm" style="background:rgba(100,116,139,0.1);border:1px solid rgba(100,116,139,0.2);color:var(--text-muted);cursor:not-allowed" disabled title="Cannot delete yourself">
                                <i class="bi bi-lock-fill"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
