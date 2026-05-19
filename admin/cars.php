<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Quản lý xe';

$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$brandFilter = $_GET['brand'] ?? '';
$searchTerm  = trim($_GET['search'] ?? '');

$sql   = "SELECT c.id, c.model_name AS name, b.name AS brand, c.year, c.price, c.status,
                 ci.image AS thumbnail
          FROM cars c
          JOIN brands b ON c.brand_id = b.id
          LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_main = 1";
$params = [];
$conditions = [];
if ($brandFilter !== '') { $conditions[] = 'b.id = :brand'; $params[':brand'] = $brandFilter; }
if ($searchTerm !== '')  { $conditions[] = '(c.model_name LIKE :search OR b.name LIKE :search)'; $params[':search'] = "%$searchTerm%"; }
if ($conditions) $sql .= ' WHERE ' . implode(' AND ', $conditions);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM ($sql) AS sub");
$countStmt->execute($params);
$totalRows  = $countStmt->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

$sql .= " ORDER BY c.id DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$brandStmt = $pdo->query('SELECT id, name FROM brands ORDER BY name');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Quản lý xe – Admin</title>
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
                <div class="page-title">Quản lý xe</div>
                <div class="page-subtitle"><?= number_format($totalRows) ?> xe trong hệ thống</div>
            </div>
            <a href="cars_add.php" class="btn btn-gold">
                <i class="bi bi-plus-lg"></i> Thêm xe mới
            </a>
        </div>

        <?php if(isset($_GET['deleted'])): ?>
        <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#10b981;font-size:14px">
            <i class="bi bi-check-circle-fill"></i> Xe đã được xóa thành công.
        </div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
        <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#10b981;font-size:14px">
            <i class="bi bi-check-circle-fill"></i> Thêm xe mới thành công!
        </div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#3b82f6;font-size:14px">
            <i class="bi bi-info-circle-fill"></i> Cập nhật thông tin xe thành công.
        </div>
        <?php endif; ?>

        <!-- Filter bar -->
        <div class="filter-bar">
            <form method="GET" action="" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
                <i class="bi bi-funnel-fill" style="color:var(--gold)"></i>
                <select name="brand" class="form-select" style="width:200px">
                    <option value="">-- Tất cả hãng --</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $brandFilter==$b['id']?'selected':'' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm tên xe..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width:260px">
                <button type="submit" class="btn btn-outline-gold"><i class="bi bi-search"></i> Lọc</button>
                <?php if($brandFilter||$searchTerm): ?>
                    <a href="cars.php" class="btn btn-sm" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444">
                        <i class="bi bi-x"></i> Xóa lọc
                    </a>
                <?php endif; ?>
                <span style="margin-left:auto;font-size:12px;color:var(--text-muted)">
                    Hiển thị <?= count($cars) ?>/<?= number_format($totalRows) ?> kết quả
                </span>
            </form>
        </div>

        <!-- Table -->
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tên xe</th>
                        <th>Hãng</th>
                        <th>Năm</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($cars as $i => $car): ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px"><?= $i+1+$offset ?></td>
                        <td>
                            <?php $thumb = !empty($car['thumbnail'])
                                ? '../assets/image/cars/' . $car['thumbnail']
                                : '../assets/image/placeholder.jpg'; ?>
                            <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($car['name']) ?>" class="car-thumb"
                                 style="width:70px;height:50px;object-fit:cover;border-radius:8px;border:1px solid var(--border);display:block;"
                                 onerror="this.src='../assets/image/placeholder.jpg'">
                        </td>
                        <td style="font-weight:600;color:var(--text)"><?= htmlspecialchars($car['name']) ?></td>
                        <td>
                            <span class="badge badge-gold"><i class="bi bi-award"></i> <?= htmlspecialchars($car['brand']) ?></span>
                        </td>
                        <td><?= $car['year'] ?></td>
                        <td style="font-weight:600;color:var(--gold)"><?= number_format($car['price']) ?>₫</td>
                        <td>
                            <?php if($car['status']==='available'): ?>
                                <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:8px"></i> Đang bán</span>
                            <?php elseif($car['status']==='sold_out'): ?>
                                <span class="badge badge-danger"><i class="bi bi-circle-fill" style="font-size:8px"></i> Hết hàng</span>
                            <?php elseif($car['status']==='coming_soon'): ?>
                                <span class="badge badge-warning"><i class="bi bi-clock-fill" style="font-size:8px"></i> Sắp ra mắt</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?= $car['status'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="cars_edit.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-outline-gold">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button class="btn btn-sm btn-danger-outline" onclick="if(confirm('Xác nhận xóa xe này?')){ window.location='cars_delete.php?id=<?= $car['id'] ?>'; }">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($cars)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Không tìm thấy xe nào
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
            <div class="custom-pagination">
                <?php if($page>1): ?>
                    <a href="?page=<?=$page-1?>&brand=<?=$brandFilter?>&search=<?=urlencode($searchTerm)?>"><i class="bi bi-chevron-left"></i></a>
                <?php endif; ?>
                <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                    <?php if($p==$page): ?>
                        <span class="active"><?=$p?></span>
                    <?php else: ?>
                        <a href="?page=<?=$p?>&brand=<?=$brandFilter?>&search=<?=urlencode($searchTerm)?>"><?=$p?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if($page<$totalPages): ?>
                    <a href="?page=<?=$page+1?>&brand=<?=$brandFilter?>&search=<?=urlencode($searchTerm)?>"><i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
