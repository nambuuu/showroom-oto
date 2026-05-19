<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

// Delete brand if requested (with confirmation via GET param)
if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    // Ensure no cars reference this brand before deletion
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM cars WHERE brand_id = :bid');
    $checkStmt->execute([':bid' => $delId]);
    if ($checkStmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare('DELETE FROM brands WHERE id = :id');
        $stmt->execute([':id' => $delId]);
    }
    header('Location: brands.php');
    exit;
}

// Fetch all brands
$brandStmt = $pdo->query('SELECT id, name FROM brands ORDER BY name');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý hãng xe – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-gold">Quản lý hãng xe</h2>
            <a href="brands_add.php" class="btn btn-primary">Thêm hãng</a>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Tên hãng</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($brands as $i => $b): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($b['name']); ?></td>
                        <td>
                            <a href="brands_edit.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-gold me-2">Sửa</a>
                            <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Xóa hãng này?')){window.location='brands.php?del=<?php echo $b['id']; ?>';}">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
