<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: cars.php');
    exit;
}

// Fetch car data
$stmt = $pdo->prepare('SELECT c.*, ci.image AS thumbnail FROM cars c LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_main = 1 WHERE c.id = :id');
$stmt->execute([':id' => $id]);
$car = $stmt->fetch();
if (!$car) {
    header('Location: cars.php?err=notfound');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $name     = trim($_POST['model_name'] ?? '');
    $brand_id = $_POST['brand_id'] ?? '';
    $year     = (int)($_POST['year'] ?? 0);
    $price    = (float)($_POST['price'] ?? 0);
    $status   = $_POST['status'] ?? 'available';
    if ($name === '' || $brand_id === '' || $year <= 0 || $price <= 0) {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin hợp lệ.';
    }
    // Handle optional new thumbnail
    $thumbnailPath = $car['thumbnail'] ?? '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['thumbnail']['type'], $allowed)) {
            $errors[] = 'Định dạng ảnh không hợp lệ (jpg/png/webp).';
        }
        if ($_FILES['thumbnail']['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Kích thước ảnh không được vượt quá 5MB.';
        }
        if (empty($errors)) {
            $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('car_') . '.' . $ext;
            $dest = UPLOAD_PATH . $newName;
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                $errors[] = 'Lỗi khi lưu ảnh.';
            } else {
                $thumbnailPath = 'assets/image/cars/' . $newName;
            }
        }
    }
    if (empty($errors)) {
        $upd = $pdo->prepare('UPDATE cars SET model_name = :model_name, brand_id = :brand_id, year = :year, price = :price, status = :status WHERE id = :id');
        $upd->execute([
            ':model_name' => $name,
            ':brand_id' => $brand_id,
            ':year'     => $year,
            ':price'    => $price,
            ':status'   => $status,
            ':id'       => $id
        ]);

        // Nếu có ảnh mới → cập nhật/thêm vào car_images
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK && $thumbnailPath !== '') {
            $imgName = basename($thumbnailPath);
            // Xóa ảnh main cũ
            $pdo->prepare('DELETE FROM car_images WHERE car_id = :id AND is_main = 1')->execute([':id' => $id]);
            // Thêm ảnh main mới
            $imgStmt = $pdo->prepare('INSERT INTO car_images (car_id, image, is_main) VALUES (:car_id, :image, 1)');
            $imgStmt->execute([
                ':car_id' => $id,
                ':image'  => $imgName
            ]);
        }

        header('Location: cars.php?msg=updated');
        exit;
    }
}

// Brand list for dropdown
$brandStmt = $pdo->query('SELECT id, name FROM brands ORDER BY name');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Xe – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="container py-4">
        <h2 class="text-gold mb-4">Chỉnh sửa Xe</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="action" value="edit">
            <div class="col-md-6">
                <label class="form-label">Tên xe</label>
                <input type="text" name="model_name" class="form-control" value="<?php echo htmlspecialchars($car['model_name']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hãng</label>
                <select name="brand_id" class="form-select" required>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?php echo $b['id']; ?>"<?php if ($car['brand_id'] == $b['id']) echo ' selected'; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Năm</label>
                <input type="number" name="year" class="form-control" value="<?php echo $car['year']; ?>" min="1900" max="2099" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Giá (VND)</label>
                <input type="number" name="price" class="form-control" value="<?php echo $car['price']; ?>" min="0" step="1000" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select" required>
                    <option value="available"<?php if($car['status']==='available') echo ' selected'; ?>>Đang bán</option>
                    <option value="sold_out"<?php if($car['status']==='sold_out') echo ' selected'; ?>>Hết hàng</option>
                    <option value="coming_soon"<?php if($car['status']==='coming_soon') echo ' selected'; ?>>Sắp ra mắt</option>
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label">Thumbnail hiện tại</label><br>
                <?php if (!empty($car['thumbnail'])): ?>
                    <img src="../assets/image/cars/<?php echo htmlspecialchars($car['thumbnail']); ?>" alt="thumb" style="width:120px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border);margin-bottom:10px;display:block;">
                <?php endif; ?>
                <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">Nếu không chọn, thumbnail sẽ giữ nguyên.</small>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="cars.php" class="btn btn-outline-gold ms-2">Hủy</a>
            </div>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
