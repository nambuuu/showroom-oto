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
        // Also delete the logo file if exists
        $logoStmt = $pdo->prepare('SELECT logo FROM brands WHERE id = :id');
        $logoStmt->execute([':id' => $delId]);
        $logo = $logoStmt->fetchColumn();
        
        $stmt = $pdo->prepare('DELETE FROM brands WHERE id = :id');
        if ($stmt->execute([':id' => $delId])) {
            if ($logo && file_exists('../assets/image/brands/' . $logo)) {
                unlink('../assets/image/brands/' . $logo);
            }
        }
    } else {
        $_SESSION['error'] = 'Cannot delete this brand because there are still cars belonging to it.';
    }
    header('Location: brands.php');
    exit;
}

// Fetch all brands with car count
$brandStmt = $pdo->query('
    SELECT b.id, b.name, b.logo, b.country, COUNT(c.id) AS car_count 
    FROM brands b 
    LEFT JOIN cars c ON b.id = c.brand_id 
    GROUP BY b.id, b.name, b.logo, b.country 
    ORDER BY b.name
');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .brand-logo-preview {
            width: 50px;
            height: 50px;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 5px;
            border: 1px solid var(--border);
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">
        
        <div class="page-header">
            <div>
                <div class="page-title">Brand Management</div>
                <div class="page-subtitle">Add, edit, delete car brands in the system</div>
            </div>
            <a href="brands_add.php" class="btn btn-gold"><i class="bi bi-plus-lg"></i> Add Brand</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #ef4444;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card-glass">
            <div class="card-glass-header">
                <h5><i class="bi bi-award-fill me-2"></i>Brand List</h5>
                <span class="badge badge-gold"><?= count($brands) ?> brands</span>
            </div>
            <div class="admin-table-wrap" style="border: none; border-radius: 0;">
                <?php if (empty($brands)): ?>
                    <div class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        <h5 class="text-gold">No brands found</h5>
                        <p class="text-muted">Please add the first brand to the system.</p>
                        <a href="brands_add.php" class="btn btn-outline-gold mt-2">Add now</a>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Brand name</th>
                            <th>Country</th>
                            <th>Number of cars</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($brands as $b): ?>
                            <tr>
                                <td>
                                    <?php if ($b['logo']): ?>
                                        <img src="../assets/image/brands/<?= htmlspecialchars($b['logo']) ?>" alt="Logo" class="brand-logo-preview">
                                    <?php else: ?>
                                        <div class="brand-logo-preview d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.02);">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-light"><?= htmlspecialchars($b['name']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($b['country']): ?>
                                        <span class="badge badge-secondary"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($b['country']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= $b['car_count'] ?> cars</span>
                                </td>
                                <td class="text-end">
                                    <a href="brands_edit.php?id=<?= $b['id']; ?>" class="btn btn-sm btn-outline-gold me-2" title="Edit"><i class="bi bi-pencil"></i> Edit</a>
                                    <button class="btn btn-sm btn-danger-outline" onclick="deleteBrand(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['name'])) ?>')" title="Delete"><i class="bi bi-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<script>
function deleteBrand(id, name) {
    if (confirm(`Are you sure you want to delete the brand "${name}"? This action cannot be undone.`)) {
        window.location.href = `brands.php?del=${id}`;
    }
}
</script>
</body>
</html>
